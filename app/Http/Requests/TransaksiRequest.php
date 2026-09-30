<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransaksiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'jenis' => 'required|in:MASUK,KELUAR,RETUR,ADJUSTMENT',
            'supplier_id' => 'nullable|required_if:jenis,MASUK|exists:supplier,id',
            'gudang_id' => 'required|exists:gudang,id',
            'keterangan' => 'nullable|string|max:1000',
            'detail' => 'required|array|min:1',
            'detail.*.produk_id' => 'required|exists:produk,id',
            'detail.*.qty' => 'required|integer|min:1',
            'detail.*.harga_satuan' => 'required|numeric|min:0',
            'detail.*.catatan' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'jenis.required' => 'Jenis transaksi wajib dipilih.',
            'supplier_id.required_if' => 'Supplier wajib dipilih untuk transaksi MASUK.',
            'gudang_id.required' => 'Gudang wajib dipilih.',
            'detail.required' => 'Detail transaksi wajib diisi.',
            'detail.min' => 'Minimal 1 detail transaksi.',
            'detail.*.produk_id.required' => 'Produk wajib dipilih pada setiap baris detail.',
            'detail.*.qty.required' => 'Quantity wajib diisi pada setiap baris detail.',
            'detail.*.qty.min' => 'Quantity minimal 1 pada setiap baris detail.',
            'detail.*.harga_satuan.required' => 'Harga satuan wajib diisi pada setiap baris detail.',
            'detail.*.harga_satuan.min' => 'Harga satuan minimal 0.',
        ];
    }
}
