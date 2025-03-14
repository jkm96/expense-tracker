<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\RecurringExpense;
use App\Utils\Enums\ExpenseFrequency;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessRecurringExpenses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-recurring-expenses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle recurring expenses and generate expense records.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // Fetch all active recurring expenses
        $recurringExpenses = RecurringExpense::with('expense')
            ->where('is_active', true)
            ->get();

        $recurringExpenses->each(function ($recurring) use ($now) {
            Log::info("Recurring Expense: {$recurring->expense->name}");
            $lastProcessed = Carbon::parse($recurring->last_processed_at);
            Log::info("Last Processed: {$lastProcessed}");

            $shouldProcess = $this->isDueForProcessing($recurring->frequency, $lastProcessed, $now);
            Log::info("Should Process: {$shouldProcess}");

            if ($shouldProcess) {
                // Create a new expense record
                Expense::create([
                    'user_id'    => $recurring->user_id,
                    'amount'     => $recurring->amount,
                    'category'   => $recurring->expense->category,
                    'note'       => $recurring->expense->note,
                    'date'       => $now->toDateString(),
                ]);

                // Update last_processed_at
                $recurring->update(['last_processed_at' => $now->toDateTimeString()]);
                Log::info("Processed recurring expense: {$recurring->expense->note}");
            }
        });

        Log::info('Finished processing Recurring expenses!');
    }

    /**
     * Determine if the recurring expense is due for processing.
     */
    private function isDueForProcessing(ExpenseFrequency $frequency, Carbon $lastProcessed, Carbon $now): bool
    {
        return match ($frequency) {
            ExpenseFrequency::DAILY   => $lastProcessed->lt($now->startOfDay()),
            ExpenseFrequency::WEEKLY  => $lastProcessed->diffInDays($now) >= 7,
            ExpenseFrequency::MONTHLY => $lastProcessed->diffInMonths($now) >= 1,
            ExpenseFrequency::YEARLY  => $lastProcessed->diffInYears($now) >= 1
        };
    }
}
