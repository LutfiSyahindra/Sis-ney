<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetPlanModel extends Model
{
    use HasFactory;

    protected $table = 'budget_plan';
    protected $fillable = [
        'user_id',
        'nama',
        'target',
        'terkumpul',
        'progres',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
