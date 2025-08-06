<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferModel extends Model
{
    use HasFactory;

    protected $table = 'transfer_aset';
    protected $guarded = ['id'];

    public function asal()
    {
        return $this->belongsTo(asetTabunganModel::class, 'asal_aset_id');
    }

    public function tujuan()
    {
        return $this->belongsTo(asetTabunganModel::class, 'tujuan_aset_id');
    }
}
