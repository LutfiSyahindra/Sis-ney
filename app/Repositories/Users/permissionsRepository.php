<?php

namespace App\Repositories\Users;

use App\Models\User;
use Spatie\Permission\Models\Permission;

class permissionsRepository
{
    public function getAllPermissions()
    {
        return Permission::all();
    }

    public function createPermissions($name){
        return Permission::create([
            'name' => $name,
        ]);
    }

    public function findPermissions($id){
        return Permission::findOrFail($id);
    }
    

}
