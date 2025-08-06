<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class asetTabunganModel extends Model
{
    use HasFactory;

    protected $table = 'aset_tabungan';
    protected $guarded = ['id'];
}
