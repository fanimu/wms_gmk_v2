<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class KodeGeneratorService
{
    /**
     * Returns next GDG-XXX
     */
    public function generateKodeGudang(): string
    {
        return DB::transaction(function () {
            $seq = $this->getNextSequence('GDG', 'gudang', 'kode_gudang');
            return 'GDG-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Returns next SUP-XXX
     */
    public function generateKodeSupplier(): string
    {
        return DB::transaction(function () {
            $seq = $this->getNextSequence('SUP', 'supplier', 'kode_supplier');
            return 'SUP-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Returns TRX-{JENIS}-{YYYYMMDD}-{XXX} e.g. TRX-MASUK-20240101-001
     */
    public function generateKodeTransaksi(string $jenis): string
    {
        $date = now()->format('Ymd');
        $prefix = "TRX-{$jenis}-{$date}";
        
        return DB::transaction(function () use ($prefix) {
            $lastRecord = DB::table('transaksi')
                ->where('no_transaksi', 'like', "{$prefix}-%")
                ->lockForUpdate()
                ->orderBy('no_transaksi', 'desc')
                ->first();

            $sequence = 1;
            if ($lastRecord) {
                $lastSequence = (int) substr($lastRecord->no_transaksi, -3);
                $sequence = $lastSequence + 1;
            }

            return $prefix . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Returns ORD-{YYYYMMDD}-{XXXX}
     */
    public function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = "ORD-{$date}";

        return DB::transaction(function () use ($prefix) {
            $lastRecord = DB::table('orders')
                ->where('order_number', 'like', "{$prefix}-%")
                ->lockForUpdate()
                ->orderBy('order_number', 'desc')
                ->first();

            $sequence = 1;
            if ($lastRecord) {
                $lastSequence = (int) substr($lastRecord->order_number, -4);
                $sequence = $lastSequence + 1;
            }

            return $prefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Private helper to get next sequence based on highest existing ID
     */
    private function getNextSequence(string $prefix, string $table, string $column): int
    {
        $lastRecord = DB::table($table)
            ->where($column, 'like', "{$prefix}-%")
            ->lockForUpdate()
            ->orderBy($column, 'desc')
            ->first();

        if (!$lastRecord) {
            return 1;
        }

        $lastSequence = (int) substr($lastRecord->$column, -3);
        return $lastSequence + 1;
    }
}
