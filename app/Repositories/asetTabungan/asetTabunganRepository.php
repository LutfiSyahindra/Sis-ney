<?php

namespace App\Repositories\asetTabungan;

use App\Models\asetTabunganModel;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class asetTabunganRepository
{
    public function getAllAset()
    {
        return asetTabunganModel::all();
    }

    public function getTabunganMasuk(){
        return asetTabunganModel::where('keterangan', 'Masuk')
        ->where('jenis_tabungan', 'Tabungan')
        ->get();
    }

    public function createAset(array $data){
        return asetTabunganModel::create($data);
    }

    public function findAset($id){
        return asetTabunganModel::findOrFail($id);
    }
    

}
