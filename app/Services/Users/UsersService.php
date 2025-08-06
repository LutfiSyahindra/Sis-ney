<?php

namespace App\Services\Users;

use App\Repositories\Users\UsersRepository;

class UsersService
{
    protected $usersRepository;

    public function __construct(UsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    public function getUsersData()
    {
        return $this->usersRepository->getAllUsers();
    }

    public function createUsers($name, $email, $password)
    {
        return $this->usersRepository->createUsers($name, $email, $password);
    }

    public function findUser($id)
    {
        return $this->usersRepository->findUser($id);
    }

}
