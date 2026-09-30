@extends('layouts.app')

@section('page-title', 'Edit Supplier')

@section('breadcrumb')
    <nav class="flex text-sm text-gray-500" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center hover:text-gray-900">
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('supplier.index') }}" class="ml-1 md:ml-2 hover:text-gray-900">Data Supplier</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 md:ml-2 text-gray-400">Edit</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="p-6">
        <form action="{{ route('supplier.update', $supplier) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="kode_supplier" class="block mb-2 text-sm font-medium text-gray-900">Kode Supplier</label>
                    <input type="text" id="kode_supplier" value="{{ $supplier->kode_supplier }}" class="bg-gray-100 border border-gray-300 text-gray-600 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed" disabled>
                </div>

                <div>
                    <label for="nama_supplier" class="block mb-2 text-sm font-medium text-gray-900">Nama Supplier <span class="text-red-500">*</span></label>
                    <input type="text" id="nama_supplier" name="nama_supplier" value="{{ old('nama_supplier', $supplier->nama_supplier) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" placeholder="Nama Perusahaan / Supplier" required>
                    @error('nama_supplier')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="kontak_person" class="block mb-2 text-sm font-medium text-gray-900">Kontak Person</label>
                    <input type="text" id="kontak_person" name="kontak_person" value="{{ old('kontak_person', $supplier->kontak_person) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" placeholder="Nama PIC">
                    @error('kontak_person')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="telepon" class="block mb-2 text-sm font-medium text-gray-900">Telepon</label>
                    <input type="text" id="telepon" name="telepon" value="{{ old('telepon', $supplier->telepon) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" placeholder="Nomor Telepon">
                    @error('telepon')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $supplier->email) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" placeholder="Alamat Email">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="kota" class="block mb-2 text-sm font-medium text-gray-900">Kota</label>
                    <input type="text" id="kota" name="kota" value="{{ old('kota', $supplier->kota) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" placeholder="Nama Kota">
                    @error('kota')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label for="alamat" class="block mb-2 text-sm font-medium text-gray-900">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" placeholder="Alamat Lengkap">{{ old('alamat', $supplier->alamat) }}</textarea>
                    @error('alamat')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="is_aktif" class="block mb-2 text-sm font-medium text-gray-900">Status <span class="text-red-500">*</span></label>
                    <select id="is_aktif" name="is_aktif" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" required>
                        <option value="1" {{ old('is_aktif', $supplier->is_aktif) == 1 ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_aktif', $supplier->is_aktif) == 0 ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('is_aktif')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="flex space-x-3 mt-8">
                <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Simpan Perubahan</button>
                <a href="{{ route('supplier.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
