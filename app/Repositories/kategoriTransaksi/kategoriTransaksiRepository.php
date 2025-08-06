<?php

namespace App\Repositories\kategoriTransaksi;

use App\Models\KategoriTransaksiModel;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class kategoriTransaksiRepository
{
    public function getAllKategori()
    {
        return KategoriTransaksiModel::all();
    }

    public function getKategori()
    {
        return KategoriTransaksiModel::select('id', 'nama_kategori', 'tipe')
            ->orderBy('tipe')
            ->orderBy('nama_kategori')
            ->get();
    }

    public function getKategoriPengeluaran()
    {
        return KategoriTransaksiModel::select('id', 'nama_kategori', 'tipe')
            ->where('tipe', 'pengeluaran')
            ->orderBy('tipe')
            ->orderBy('nama_kategori')
            ->get();
    }

    public function createKategori(array $data){
        return KategoriTransaksiModel::create($data);
    }

    public function findKategori($id){
        return KategoriTransaksiModel::findOrFail($id);
    }
    

}
