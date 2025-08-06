<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarBudgetModel extends Model
{
    use HasFactory;

    protected $table = 'budgets';
    protected $fillable = [
        'user_id',
        'bulan',
        'tahun',
        'total',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
