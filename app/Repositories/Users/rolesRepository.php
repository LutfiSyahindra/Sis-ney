<?php

namespace App\Repositories\Users;

use App\Models\User;
use Spatie\Permission\Models\Role;

class rolesRepository
{
    public function getAllRoles()
    {
        return Role::all();
    }

    public function createRoles($name){
        return Role::create([
            'name' => $name,
        ]);
    }

    public function findRoles($id){
        return Role::findOrFail($id);
    }
    

}
