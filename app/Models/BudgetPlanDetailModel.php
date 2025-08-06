<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetPlanDetailModel extends Model
{
    use HasFactory;

    protected $table = 'budget_plan_details';
    protected $fillable = [
        'budget_plan_id',
        'nominal',
        'keterangan',
        'aset_tabungan_id',
    ];

    public function BudgetPlan()
    {
        return $this->belongsTo(BudgetPlanModel::class, 'budget_plan_id');
    }

    public function Aset()
    {
        return $this->belongsTo(asetTabunganModel::class, 'aset_tabungan_id');
    }
}
