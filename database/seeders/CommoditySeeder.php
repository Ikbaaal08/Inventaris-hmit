<?php

namespace Database\Seeders;

use App\CommodityLocation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommoditySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $commodities = collect([
            'Laptop Inventaris HMIF',
            'Proyektor Epson',
            'Printer Canon Pixma',
            'Sound System Portable',
            'Papan Tulis Whiteboard',
            'Bendera HMIF',
            'Kamera DSLR Canon',
            'Dispenser Miyako',
            'Stopkontak / Kabel Rol',
            'Lemari Arsip Dokumen',
            'Modem WiFi Router',
            'Meja Rapat',
            'Kursi Lipat Chitose',
            'Kipas Angin Dinding',
            'Air Conditioner (AC)',
            'Papan Pengumuman HMIF',
        ]);

        $brands = collect([
            'IKEA',
            'Livien',
            'iFurnholic',
            'Red Sun',
            'JYSXK',
            'Olympic',
            'Informa',
            "Dove's",
            'Funika',
            'Atria',
            'Vivere',
        ]);

        $materials = collect([
            'Kayu Solid',
            'Kayu Lapis (Plywood/Multipleks)',
            'Blockboard',
            'MDF (Medium Density Fibreboard)',
            'Melaminto',
            'Partikel',
            'Rotan',
        ]);

        $conditions = collect([1, 2, 3]);

        $data = $commodities->map(fn ($commodity) => [
            'item_code' => 'BRG-'.mt_rand(1000, 9999).mt_rand(100, 999),
            'name' => $commodity,
            'brand' => $brands->random(),
            'material' => $materials->random(),
            'year_of_purchase' => mt_rand(2010, date('Y')),
            'condition' => $conditions->random(),
            'quantity' => $quantity = mt_rand(50, 200),
            'price' => $quantity * ($pricePerItem = mt_rand(2500, 150000)),
            'price_per_item' => $pricePerItem,
            'note' => 'Keterangan barang',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('commodities')->insert($data->toArray());
    }
}
