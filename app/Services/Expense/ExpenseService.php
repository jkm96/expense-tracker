<?php

namespace App\Services\Expense;

use App\Models\Expense;
use App\Utils\Enums\ExpenseCategory;
use Illuminate\Support\Facades\Auth;

class ExpenseService implements ExpenseServiceInterface
{
    public function fetchByUser(int $userId, array $params)
    {
        $filter = $params["filter"];
        $search = $params["search"];
        $page = $params["current_page"];
        $perPage = $params["per_page"];
        $query = Expense::where('user_id', $userId);

        if ($filter !== 'all') {
            $query->where('category', '=', ExpenseCategory::from($filter));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        return $query->orderByDesc('date')->orderByDesc('created_at')->paginate($perPage, ['*'], 'page', $page);

    }

    public function addOrUpdate(int $userId, array $data, ?int $expenseId): Expense
    {
        $expenseData = array_merge($data, [
            'user_id' => $userId,
        ]);

        if ($expenseId) {
            $expense = Expense::where('id', $expenseId)->where('user_id', $userId)->firstOrFail();
            $expense->update($expenseData);
            return $expense;
        }

        return Expense::create($expenseData);
    }

    public function delete(int $userId, int $expenseId): bool
    {
        $expense = Expense::where('id', $expenseId)->where('user_id', $userId)->first();
        return $expense ? $expense->delete() : false;
    }

    public function find($expenseId)
    {
        return Expense::where('id', $expenseId)->where('user_id', Auth::id())->first();
    }
}
