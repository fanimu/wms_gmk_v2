<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GudangRequest extends FormRequest
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
            'nama_gudang' => 'required|string|max:100',
            'lokasi' => 'required|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:20',
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
            'nama_gudang.required' => 'Nama gudang wajib diisi.',
            'nama_gudang.max' => 'Nama gudang maksimal 100 karakter.',
            'lokasi.required' => 'Lokasi wajib diisi.',
            'lokasi.max' => 'Lokasi maksimal 255 karakter.',
            'penanggung_jawab.max' => 'Penanggung jawab maksimal 100 karakter.',
            'telepon.max' => 'Nomor telepon maksimal 20 karakter.',
        ];
    }
}
