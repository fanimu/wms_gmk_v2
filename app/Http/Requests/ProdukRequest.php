<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProdukRequest extends FormRequest
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
        $produk = $this->route('produk');
        $id = $produk ? ($produk instanceof \App\Models\Produk ? $produk->id : $produk) : null;

        return [
            'sku' => 'required|string|max:50|unique:produk,sku,' . $id,
            'nama_produk' => 'required|string|max:150',
            'kategori_id' => 'nullable|exists:kategori,id',
            'gudang_id' => 'nullable|exists:gudang,id',
            'gender' => 'nullable|in:PRIA,WANITA,UNISEX',
            'merk' => 'nullable|string|max:100',
            'warna' => 'nullable|string|max:50',
            'ukuran' => 'nullable|string|max:20',
            'urutan_ukuran' => 'nullable|integer|min:1|max:10',
            'grup_produk' => 'nullable|string|max:100',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok_minimum' => 'nullable|integer|min:0',
            'gambar' => 'nullable|image|max:2048',
            'deskripsi' => 'nullable|string|max:2000',
            'is_active' => 'sometimes|boolean',
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
            'sku.required' => 'SKU wajib diisi.',
            'sku.unique' => 'SKU sudah digunakan.',
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'harga_beli.required' => 'Harga beli wajib diisi.',
            'harga_beli.min' => 'Harga beli minimal 0.',
            'harga_jual.required' => 'Harga jual wajib diisi.',
            'harga_jual.min' => 'Harga jual minimal 0.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
