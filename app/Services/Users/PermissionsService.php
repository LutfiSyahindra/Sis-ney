<?php

namespace App\Services\Users;

use App\Repositories\Users\permissionsRepository;

class PermissionsService
{
    protected $permissionsRepository;

    public function __construct(permissionsRepository $permissionsRepository)
    {
        $this->permissionsRepository = $permissionsRepository;
    }


    public function getPermissionsData()
    {
        return $this->permissionsRepository->getAllPermissions();
    }

    public function createPermissions($name)
    {
        return $this->permissionsRepository->createPermissions($name);
    }

    public function findPermissions($id)
    {
        return $this->permissionsRepository->findPermissions($id);
    }

}
