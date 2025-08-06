<?php

namespace App\Repositories\Budgets\KategoriBudgets;

use App\Models\KategoriBudgetModel;
use App\Models\TransaksiModel;
use App\Models\TransferModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class KategoriBudgetsRepository
{
    public function createKategoriBudget(array $data)
    {
        return KategoriBudgetModel::create($data);
    }

    public function getAllKategoriBudget()
    {
        return KategoriBudgetModel::with('budgets', 'kategori_transaksi')->get();
    }

    public function getKategoriBudgetMY($start, $end)
    {
        $startMonth = date('Y-m', strtotime($start)); // '2025-07'
        $endMonth = date('Y-m', strtotime($end));     // '2025-07'

        return KategoriBudgetModel::with(['budgets', 'kategori_transaksi'])
            ->whereHas('budgets', function ($query) use ($startMonth, $endMonth) {
                $query->whereRaw("CONCAT(tahun, '-', LPAD(bulan, 2, '0')) BETWEEN ? AND ?", [$startMonth, $endMonth]);
            })
            ->get();
    }


    public function findKategoriBudget($id){
        return KategoriBudgetModel::findOrFail($id);
    }

    public function updateKategoriBudget(string $id, array $data)
    {
        $KategoriBudget = KategoriBudgetModel::findOrFail($id);
        $KategoriBudget->update($data);
        return $KategoriBudget;
    }

    public function getTotalJumlahByBudgetId($budgetId, $excludeId = null)
    {
        $query = KategoriBudgetModel::where('budget_id', $budgetId);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->sum('jumlah');
    }


        
}
