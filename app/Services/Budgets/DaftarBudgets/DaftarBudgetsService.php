<?php

namespace App\Services\Budgets\DaftarBudgets;

use App\Repositories\Budgets\DaftarBudgets\DaftarBudgetsRepository; 

class DaftarBudgetsService
{
    protected $DaftarBudgetsRepository;

    public function __construct(DaftarBudgetsRepository $DaftarBudgetsRepository)
    {
        $this->DaftarBudgetsRepository = $DaftarBudgetsRepository;
    }

    public function createDaftarBudget(array $data)
    {
        return $this->DaftarBudgetsRepository->createDaftarBudget($data);
    }

    public function getAllDaftarBudget()
    {
        return $this->DaftarBudgetsRepository->getAllDaftarBudget();
    }

    public function findDaftarBudget($id)
    {
        return $this->DaftarBudgetsRepository->findDaftarBudget($id);
    }

    public function updateDaftarBudget(string $id, array $data)
    {
        return $this->DaftarBudgetsRepository->updateDaftarBudget($id, $data);
    }

    public function updateTotalBudget($id, $total)
    {
        return $this->DaftarBudgetsRepository->updateTotalBudget($id, $total);
    }
}
