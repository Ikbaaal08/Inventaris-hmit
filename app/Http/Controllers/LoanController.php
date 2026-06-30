<?php

namespace App\Http\Controllers;

use App\Commodity;
use App\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole(['Administrator', 'Ketua Himpunan'])) {
            $loans = Loan::with(['user', 'commodity'])->latest()->get();
        } else {
            $loans = Loan::with(['commodity'])->where('user_id', $user->id)->latest()->get();
        }

        return view('loans.index', compact('loans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'commodity_id' => 'required|exists:commodities,id',
            'quantity' => 'required|integer|min:1',
            'borrow_date' => 'required|date',
        ]);

        $commodity = Commodity::findOrFail($request->commodity_id);

        if ($commodity->isBorrowed()) {
            return back()->with('error', 'Barang ini sedang dipinjam oleh orang lain dan belum dikembalikan!');
        }

        if ($commodity->quantity < $request->quantity) {
            return back()->with('error', 'Stok barang tidak mencukupi untuk dipinjam!');
        }

        DB::transaction(function () use ($request, $commodity) {
            Loan::create([
                'user_id' => auth()->id(),
                'commodity_id' => $request->commodity_id,
                'quantity' => $request->quantity,
                'borrow_date' => $request->borrow_date,
                'status' => 'dipinjam',
            ]);

            $commodity->decrement('quantity', $request->quantity);
        });

        return back()->with('success', 'Barang berhasil dipinjam!');
    }

    /**
     * Update the specified resource in storage (Return the commodity).
     */
    public function update(Request $request, Loan $peminjaman)
    {
        if ($peminjaman->user_id != auth()->id()) {
            abort(403, 'Hanya peminjam yang dapat mengembalikan barang ini.');
        }

        if ($peminjaman->status === 'dikembalikan') {
            return back()->with('error', 'Barang sudah dikembalikan sebelumnya!');
        }

        $request->validate([
            'return_photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'return_date' => 'required|date|after_or_equal:' . $peminjaman->borrow_date->format('Y-m-d'),
        ]);

        $returnPhotoPath = null;
        if ($request->hasFile('return_photo')) {
            $file = $request->file('return_photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $directory = public_path('assets/img/returns');
            
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            $file->move($directory, $filename);
            $returnPhotoPath = 'assets/img/returns/' . $filename;
        }

        DB::transaction(function () use ($peminjaman, $returnPhotoPath, $request) {
            $peminjaman->update([
                'status' => 'dikembalikan',
                'return_date' => $request->return_date,
                'return_photo' => $returnPhotoPath,
            ]);

            $peminjaman->commodity->increment('quantity', $peminjaman->quantity);
        });

        return back()->with('success', 'Barang berhasil dikembalikan!');
    }
}
