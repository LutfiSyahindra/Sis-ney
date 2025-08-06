<?php

namespace App\Services\Budgets\KategoriBudgets;

use App\Repositories\Budgets\KategoriBudgets\KategoriBudgetsRepository; 

class KategoriBudgetsService
{
    protected $KategoriBudgetsRepository;

    public function __construct(KategoriBudgetsRepository $KategoriBudgetsRepository)
    {
        $this->KategoriBudgetsRepository = $KategoriBudgetsRepository;
    }

    public function createKategoriBudget(array $data)
    {
        return $this->KategoriBudgetsRepository->createKategoriBudget($data);
    }

    public function getAllKategoriBudget()
    {
        return $this->KategoriBudgetsRepository->getAllKategoriBudget();
    }

    public function getKategoriBudgetMY($start, $end)
    {
        return $this->KategoriBudgetsRepository->getKategoriBudgetMY($start, $end);
    }

    public function findKategoriBudget($id)
    {
        return $this->KategoriBudgetsRepository->findKategoriBudget($id);
    }

    public function updateKategoriBudget(string $id, array $data)
    {
        return $this->KategoriBudgetsRepository->updateKategoriBudget($id, $data);
    }

    public function getTotalJumlahByBudgetId($budgetId, $excludeId = null)
    {
        return $this->KategoriBudgetsRepository->getTotalJumlahByBudgetId($budgetId, $excludeId);
    }
}
