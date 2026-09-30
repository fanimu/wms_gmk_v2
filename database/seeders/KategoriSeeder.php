<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            ['nama_kategori' => 'PAKAIAN / POLO SHIRT'],
            ['nama_kategori' => 'PAKAIAN / KAOS'],
            ['nama_kategori' => 'PAKAIAN / KEMEJA'],
            ['nama_kategori' => 'PAKAIAN / CELANA'],
            ['nama_kategori' => 'PAKAIAN / JAKET'],
            ['nama_kategori' => 'AKSESORIS / TOPI'],
            ['nama_kategori' => 'AKSESORIS / TAS'],
        ];

        foreach ($kategoris as $kategori) {
            Kategori::create($kategori);
        }
    }
}
