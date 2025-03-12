<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\RecurringExpense;
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
        $today = Carbon::today();

        // Fetch all active recurring expenses
        $recurringExpenses = RecurringExpense::with('expense')
            ->where('is_active', true)
            ->get();

        $recurringExpenses->each(function ($recurring) use ($today) {
            $lastProcessed = Carbon::parse($recurring->last_processed_at);
            $shouldProcess = $this->isDueForProcessing($recurring->frequency, $lastProcessed, $today);

            if ($shouldProcess) {
                // Create a new expense record
                Expense::create([
                    'user_id'    => $recurring->user_id,
                    'amount'     => $recurring->amount,
                    'category'   => $recurring->expense->category,
                    'note'       => $recurring->expense->note,
                    'date'       => $today->toDateString(),
                ]);

                // Update last_processed_at
                $recurring->update(['last_processed_at' => $today]);
                Log::info("Processed recurring expense: {$recurring->expense->note}");
            }
        });

        Log::info('Recurring expenses processed successfully!');
    }

    /**
     * Determine if the recurring expense is due for processing.
     */
    private function isDueForProcessing(string $frequency, Carbon $lastProcessed, Carbon $today): bool
    {
        return match ($frequency) {
            'daily'   => $lastProcessed->lt($today),
            'weekly'  => $lastProcessed->diffInDays($today) >= 7,
            'monthly' => $lastProcessed->diffInMonths($today) >= 1,
            'yearly'  => $lastProcessed->diffInYears($today) >= 1,
            default   => false,
        };
    }
}
