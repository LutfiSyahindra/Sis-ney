<?php

namespace App\Repositories\Transfer;

use App\Models\TransferModel;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class TransferRepository
{
    public function getAllTransfer()
    {
        return TransferModel::with(['asal', 'tujuan'])->get();
    }

    public function createTransfer(array $data){
        return TransferModel::create($data);
    }

    public function findTransfer($id){
        return TransferModel::findOrFail($id);
    }

    public function findMutasiMasuk($asetId, $startDate = null, $endDate = null)
    {
        $query = TransferModel::where('tujuan_aset_id', $asetId)
            ->with(['asal', 'tujuan']);
    
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_transfer', [$startDate, $endDate]);
        }
    
        return $query->get();
    }
    
    public function findMutasiKeluar($asetId, $startDate = null, $endDate = null)
    {
        $query = TransferModel::where('asal_aset_id', $asetId)
            ->with(['asal', 'tujuan']);
    
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_transfer', [$startDate, $endDate]);
        }
    
        return $query->get();
    }
    
    
}
