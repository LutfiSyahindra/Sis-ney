<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriBudgetModel extends Model
{
    use HasFactory;

    protected $table = 'budget_items';
    protected $fillable = [
        'budget_id',
        'kategori_id',
        'jumlah',
    ];

    public function budgets()
    {
        return $this->belongsTo(DaftarBudgetModel::class, 'budget_id');
    }

    public function kategori_transaksi()
    {
        return $this->belongsTo(KategoriTransaksiModel::class, 'kategori_id');
    }
}
