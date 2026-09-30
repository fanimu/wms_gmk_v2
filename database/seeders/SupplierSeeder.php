<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'kode_supplier' => 'SUP-001',
                'nama_supplier' => 'PT Tekstil Nusantara',
                'kota' => 'Bandung',
                'is_active' => true,
            ],
            [
                'kode_supplier' => 'SUP-002',
                'nama_supplier' => 'CV Benang Emas',
                'kota' => 'Solo',
                'is_active' => true,
            ],
            [
                'kode_supplier' => 'SUP-003',
                'nama_supplier' => 'UD Kain Makmur',
                'kota' => 'Surabaya',
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
