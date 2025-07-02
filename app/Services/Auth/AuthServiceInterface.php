<?php

namespace App\Services\Auth;

use App\Models\User;

interface AuthServiceInterface
{
    public function registerUser(array $data): User;

    public function verifyUserEmail(string $token): array;

    public function login(string $identifier, string $password): bool;

    public function logoutUser(): void;

    public function resetPassword(array $data): bool;

    public function sendPasswordResetLink(string $email): void;
}
