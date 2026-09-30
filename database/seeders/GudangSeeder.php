<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gudang;
use App\Services\KodeGeneratorService;

class GudangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(KodeGeneratorService $kodeGenerator): void
    {
        $gudangs = [
            [
                'kode_gudang' => 'GDG-001',
                'nama_gudang' => 'Gudang Utama',
                'lokasi' => 'Jl. Industri No. 1, Tangerang',
                'is_active' => true,
            ],
            [
                'kode_gudang' => 'GDG-002',
                'nama_gudang' => 'Gudang Bahan Baku',
                'lokasi' => 'Jl. Industri No. 2, Tangerang',
                'is_active' => true,
            ],
            [
                'kode_gudang' => 'GDG-003',
                'nama_gudang' => 'Gudang Finished Goods',
                'lokasi' => 'Jl. Raya Serpong No. 10, Serpong',
                'is_active' => true,
            ],
        ];

        foreach ($gudangs as $gudang) {
            Gudang::create($gudang);
        }
    }
}
