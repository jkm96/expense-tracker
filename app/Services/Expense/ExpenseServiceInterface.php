<?php

namespace App\Services\Expense;

use App\Models\Expense;

interface ExpenseServiceInterface
{
    public function fetchByUser(array $params);

    public function addOrUpdate(array $data, ?int $expenseId): Expense;

    public function delete(int $expenseId): bool;

    public function find($expenseId);
}
