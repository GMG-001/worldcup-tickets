<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        $model = $this->getModel();

        return $model->create($data);
    }

    public function findByEmail(string $email): ?User
    {
        $model = $this->getModel();

        return$model->where('email', $email)->first();
    }

    public function getModel(): User
    {
        return new User();
    }
}
