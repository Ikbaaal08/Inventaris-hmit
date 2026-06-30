<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = User::query();

        $query->when(request()->filled('role_id'), function ($q) {
            return $q->whereRelation('roles', 'id', '=', request('role_id'));
        });

        $users = $query->get()->except(auth()->id());
        $roles = Role::all();
        $admin_count = User::role('Administrator')->count();
        $user_count = User::count();

        return view('users.index', compact('users', 'roles', 'admin_count', 'user_count'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $role = Role::findById($validated['role_id']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);
        $user->assignRole($role);

        return to_route('pengguna.index')->with('success', 'Data berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();
        $role = Role::findById($validated['role_id']);

        $credentials = [];
        if ($validated['password'] !== null) {
            $credentials['password'] = bcrypt($validated['password']);
        } else {
            $credentials = collect($validated)->except('password', 'password_confirmation')->toArray();
        }

        $user->update($credentials);
        $user->syncRoles($role);

        return redirect()->route('pengguna.index')->with('success', 'Data berhasil diubah!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return to_route('pengguna.index')->with('success', 'Data berhasil dihapus!');
    }

    /**
     * Generate new passwords for selected users.
     */
    public function generatePasswords(Request $request)
    {
        $this->authorize('update', User::class);

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $userIds = $request->user_ids;
        $generated = [];

        foreach ($userIds as $id) {
            $user = User::find($id);
            if ($user && $user->id !== auth()->id()) {
                $plainPassword = 'HMIT-' . rand(100, 999) . '-' . strtolower(\Illuminate\Support\Str::random(4));
                $user->update([
                    'password' => bcrypt($plainPassword),
                ]);
                $generated[] = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $plainPassword,
                ];
            }
        }

        return response()->json([
            'code' => 200,
            'message' => 'Password berhasil di-generate!',
            'data' => $generated,
        ]);
    }

    /**
     * Generate member accounts based on NIMs.
     */
    public function generateByNim(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'nims' => 'required|string',
            'email_suffix' => 'required|string',
            'name_prefix' => 'nullable|string',
            'password_type' => 'required|in:random,nim,custom',
            'custom_password' => 'required_if:password_type,custom|nullable|string|min:3',
            'role_id' => 'required|exists:roles,id',
        ]);

        $rawNims = preg_split('/[\s,]+/', $request->nims);
        $nims = array_filter(array_map('trim', $rawNims));

        $emailSuffix = trim($request->email_suffix);
        if (!\Illuminate\Support\Str::startsWith($emailSuffix, '@')) {
            $emailSuffix = '@' . $emailSuffix;
        }

        $namePrefix = trim($request->name_prefix ?? '');
        $passwordType = $request->password_type;
        $customPassword = $request->custom_password;
        
        $role = Role::findById($request->role_id);
        
        $created = [];
        $skipped = [];

        foreach ($nims as $nim) {
            if (empty($nim)) continue;

            $email = $nim . $emailSuffix;

            if (User::where('email', $email)->exists()) {
                $skipped[] = [
                    'nim' => $nim,
                    'email' => $email,
                    'reason' => 'Email sudah terdaftar',
                ];
                continue;
            }

            $name = ($namePrefix ? $namePrefix . ' ' : '') . $nim;

            if ($passwordType === 'random') {
                $plainPassword = 'HMIT-' . rand(100, 999) . '-' . strtolower(\Illuminate\Support\Str::random(4));
            } elseif ($passwordType === 'nim') {
                $plainPassword = $nim;
            } else {
                $plainPassword = $customPassword;
            }

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($plainPassword),
            ]);

            $user->assignRole($role);

            $created[] = [
                'name' => $name,
                'email' => $email,
                'password' => $plainPassword,
            ];
        }

        return response()->json([
            'code' => 200,
            'message' => 'Proses generate selesai!',
            'data' => [
                'created' => $created,
                'skipped' => $skipped,
            ]
        ]);
    }
}
