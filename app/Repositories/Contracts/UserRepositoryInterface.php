<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function createCustomer(array $payload): User;

    public function findByEmail(string $email): ?User;
}
