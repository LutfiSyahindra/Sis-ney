<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiModel extends Model
{
    use HasFactory;

    protected $table = 'transaksi_keuangan';
    protected $guarded = ['id'];

    public function kategori()
    {
        return $this->belongsTo(KategoriTransaksiModel::class, 'kategori_id');
    }

    public function aset()
    {
        return $this->belongsTo(asetTabunganModel::class, 'aset_tabungan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
