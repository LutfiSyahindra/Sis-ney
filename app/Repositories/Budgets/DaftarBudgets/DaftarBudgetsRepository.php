<?php

namespace App\Repositories\Budgets\DaftarBudgets;

use App\Models\DaftarBudgetModel;
use App\Models\TransaksiModel;
use App\Models\TransferModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class DaftarBudgetsRepository
{
    public function createDaftarBudget(array $data)
    {
        return DaftarBudgetModel::create($data);
    }

    public function getAllDaftarBudget()
    {
        return DaftarBudgetModel::with('user')->get();
    }

    public function getDaftarBudget($start, $end)
    {
        $start = date('Y-m', strtotime($start)); // '2025-07'
        $end= date('Y-m', strtotime($end));     // '2025-07'

        return DaftarBudgetModel::with('user')->get();
    }
    

    public function findDaftarBudget($id){
        return DaftarBudgetModel::findOrFail($id);
    }

    public function updateDaftarBudget(string $id, array $data)
    {
        $daftarBudget = DaftarBudgetModel::findOrFail($id);
        $daftarBudget->update($data);
        return $daftarBudget;
    }

    public function updateTotalBudget(string $id, $total)
    {
        Log::info("🧪 [Repo] update budget ID: $id to total: $total");
    
        $result = DaftarBudgetModel::where('id', $id)->update(['total' => $total]);
    
        Log::info("🧪 [Repo] result update: " . $result);
    
        return $result;
    }

        
}
