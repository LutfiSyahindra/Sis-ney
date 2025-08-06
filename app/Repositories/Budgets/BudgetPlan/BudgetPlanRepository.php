<?php

namespace App\Repositories\Budgets\BudgetPlan;

use App\Models\BudgetPlanDetailModel;
use App\Models\BudgetPlanModel;
use App\Models\DaftarBudgetModel;
use App\Models\TransaksiModel;
use App\Models\TransferModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class BudgetPlanRepository
{
    public function createBudgetPlan(array $data)
    {
        return BudgetPlanModel::create($data);
    }

    public function getAllBudgetPlan()
    {
        return BudgetPlanModel::with('user')->get();
    }

    public function findBudgetPlan($id){
        return BudgetPlanModel::findOrFail($id);
    }

    public function updateBudgetPlan(string $id, array $data)
    {
        $BudgetPlan = BudgetPlanModel::findOrFail($id);
        $BudgetPlan->update($data);
        return $BudgetPlan;
    }

    public function createBudgetPlanDetail(array $data)
    {
        return BudgetPlanDetailModel::create($data);
    }

    public function getBudgetPlanDetail($start = null, $end = null){
        $query = BudgetPlanDetailModel::with(['BudgetPlan','Aset']);

        if ($start && $end) {
            if ($start === $end) {
                // Kalau hanya 1 tanggal (flatpickr belum lengkap)
                $query->whereDate('created_at', $start);
            } else {
                // Kalau 2 tanggal, filter range
                $query->whereBetween('created_at', [$start, $end]);
            }
        } elseif ($start) {
            $query->whereDate('created_at', $start);
        }

        return $query->get();
    }

    public function getBudgetPlanDetailById($id){
        return BudgetPlanDetailModel::with(['BudgetPlan','Aset'])
            ->where('budget_plan_id', $id)
            ->get();
    }

    public function findBudgetPlanDetail($id){
        return BudgetPlanDetailModel::findOrFail($id);
    }

    public function updateBudgetPlanDetail(string $id, array $data)
    {
        $BudgetPlanDetail = BudgetPlanDetailModel::findOrFail($id);
        $BudgetPlanDetail->update($data);
        return $BudgetPlanDetail;
    }

        
}
