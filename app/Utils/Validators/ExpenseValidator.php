<?php

namespace App\Utils\Validators;

use App\Utils\Enums\ExpenseCategory;
use App\Utils\Enums\ExpenseFrequency;
use Illuminate\Validation\Rule;

class ExpenseValidator
{
    public static function expenseRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category' => ['required', Rule::in(ExpenseCategory::cases())],
            'notes' => 'nullable|string',
        ];
    }

    public static function expenseMessages(): array
    {
        return [
            'name.required' => 'The expense name is required.',
            'name.string' => 'The expense name must be a valid string.',
            'name.max' => 'The expense name cannot exceed 255 characters.',
            'amount.required' => 'The amount is required.',
            'amount.numeric' => 'The amount must be a valid number.',
            'amount.min' => 'The amount must be at least 0.',
            'date.required' => 'The date is required.',
            'date.date' => 'The date must be a valid date format.',
            'category.required' => 'The category is required.',
            'category.in' => 'The selected category is invalid.',
            'notes.string' => 'The notes must be a string.',
        ];
    }

    public static function recurringExpenseRules($frequency)
    {
        return [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'category' => ['required', Rule::in(ExpenseCategory::cases())],
            'frequency' => ['required', Rule::in(ExpenseFrequency::cases())],
            'days' => $frequency === ExpenseFrequency::DAILY->value ? 'required|array|min:1' : 'nullable',
            'dayOfWeek' => $frequency === ExpenseFrequency::WEEKLY->value ? 'required|string|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday' : 'nullable',
            'dayOfMonth' => $frequency === ExpenseFrequency::MONTHLY->value ? 'required|integer|min:1|max:31' : 'nullable',
        ];
    }

    public static function recurringExpenseMessages()
    {
        return [
            'name.required' => 'The expense name is required.',
            'name.max' => 'The expense name should not exceed 255 characters.',

            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'The amount must be at least 0.',

            'start_date.required' => 'Please provide a start date.',
            'start_date.date' => 'The start date must be a valid date.',

            'category.required' => 'Please select an expense category.',
            'category.in' => 'Invalid category selected.',

            'frequency.required' => 'Please select an expense frequency.',
            'frequency.in' => 'Invalid frequency selected.',

            'days.required' => 'You must select at least one day for daily expenses.',
            'days.array' => 'Days must be an array.',
            'days.min' => 'You must select at least one day.',

            'dayOfWeek.required' => 'Please select a day of the week for weekly expenses.',
            'dayOfWeek.in' => 'Invalid day of the week selected.',

            'dayOfMonth.required' => 'Please specify a day of the month for monthly expenses.',
            'dayOfMonth.integer' => 'The day of the month must be a number.',
            'dayOfMonth.min' => 'The day of the month must be at least 1.',
            'dayOfMonth.max' => 'The day of the month cannot be greater than 31.',
        ];
    }

    public static function exportRules(): array
    {
        return [
            'exportFields.startDate' => 'required|date',
            'exportFields.endDate' => 'required|date|after_or_equal:exportFields.startDate',
        ];
    }

    public static function exportMessages(): array
    {
        return [
            'exportFields.startDate.required' => 'The start date is required.',
            'exportFields.startDate.date' => 'The start date must be a valid date.',
            'exportFields.endDate.required' => 'The end date is required.',
            'exportFields.endDate.date' => 'The end date must be a valid date.',
            'exportFields.endDate.after_or_equal' => 'The end date must be after or equal to the start date.',
        ];
    }
}
