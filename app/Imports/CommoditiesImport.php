<?php

namespace App\Imports;

use App\Commodity;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class CommoditiesImport implements ToModel, WithHeadingRow, WithUpserts
{
    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Commodity([
            'item_code' => $row['kode_barang'],
            'name' => $row['nama_barang'],
            'brand' => $row['merek'],
            'material' => $row['bahan'],
            'year_of_purchase' => $row['tahun_pembelian'],
            'condition' => $this->translateConditionNameToNumber($row['kondisi']),
            'quantity' => $row['kuantitas'],
            'price' => $row['harga'],
            'price_per_item' => $row['harga_satuan'],
            'note' => $row['keterangan'],
        ]);
    }

    /**
     * Translate condition name to the corresponding number.
     */
    public function translateConditionNameToNumber($conditionName)
    {
        return match ($conditionName) {
            'Baik' => 1,
            'Kurang Baik' => 2,
            'Rusak Berat' => 3,
        };
    }

    /**
     * Specify the unique column used for upsert operations.
     */
    public function uniqueBy()
    {
        return 'item_code';
    }
}
