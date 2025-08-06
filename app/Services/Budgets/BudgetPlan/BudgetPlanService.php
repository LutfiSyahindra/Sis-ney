<?php

namespace App\Services\Budgets\BudgetPlan;

use App\Repositories\Budgets\BudgetPlan\BudgetPlanRepository; 

class BudgetPlanService
{
    protected $BudgetPlanRepository;

    public function __construct(BudgetPlanRepository $BudgetPlanRepository)
    {
        $this->BudgetPlanRepository = $BudgetPlanRepository;
    }

    public function createBudgetPlan(array $data)
    {
        return $this->BudgetPlanRepository->createBudgetPlan($data);
    }

    public function getAllBudgetPlan()
    {
        return $this->BudgetPlanRepository->getAllBudgetPlan();
    }

    public function findBudgetPlan($id)
    {
        return $this->BudgetPlanRepository->findBudgetPlan($id);
    }

    public function updateBudgetPlan(string $id, array $data)
    {
        return $this->BudgetPlanRepository->updateBudgetPlan($id, $data);
    }

    public function createBudgetPlanDetail(array $data)
    {
        return $this->BudgetPlanRepository->createBudgetPlanDetail($data);
    }

    public function getBudgetPlanDetail($start = null, $end = null)
    {
        return $this->BudgetPlanRepository->getBudgetPlanDetail($start, $end);
    }

    public function getBudgetPlanDetailById($id)
    {
        return $this->BudgetPlanRepository->getBudgetPlanDetailById($id);
    }

    public function findBudgetPlanDetail($id)
    {
        return $this->BudgetPlanRepository->findBudgetPlanDetail($id);
    }

    public function updateBudgetPlanDetail(string $id, array $data)
    {
        return $this->BudgetPlanRepository->updateBudgetPlanDetail($id, $data);
    }
}
