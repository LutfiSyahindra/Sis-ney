<?php

namespace App\Services\Users;

use App\Repositories\Users\rolesRepository;

class rolesService
{
    protected $rolesRepository;

    public function __construct(rolesRepository $rolesRepository)
    {
        $this->rolesRepository = $rolesRepository;
    }


    public function getRolesData()
    {
        return $this->rolesRepository->getAllRoles();
    }

    public function createRoles($name)
    {
        return $this->rolesRepository->createRoles($name);
    }

    public function findRoles($id)
    {
        return $this->rolesRepository->findRoles($id);
    }

}
