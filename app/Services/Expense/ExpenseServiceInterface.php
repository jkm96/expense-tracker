<?php

namespace App\Services\Expense;

use App\Models\Expense;

interface ExpenseServiceInterface
{
    public function fetchByUser(int $userId,array $params);

    public function addOrUpdate(int $userId,array $data, ?int $expenseId): Expense;

    public function delete(int $userId, int $expenseId): bool;

    public function find($expenseId);
}
