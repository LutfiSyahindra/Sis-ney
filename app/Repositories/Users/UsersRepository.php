<?php

namespace App\Repositories\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersRepository
{
    public function getAllUsers()
    {
        return User::all();
    }

    public function createUsers($name, $email, $password){
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password), // Pastikan password di-hash!
        ]);
    }

    public function findUser($id){
        return User::find($id); // Cara paling sederhana
    }


}
