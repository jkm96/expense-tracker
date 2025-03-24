<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\RecurringExpense;
use App\Models\User;
use App\Notifications\ExpenseReminderNotification;
use App\Utils\Constants\AppEventListener;
use App\Utils\Enums\NotificationType;
use App\Utils\Helpers\ExpenseHelper;
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
        Log::info('Started processing recurring expenses!');

        $now = Carbon::now();

        $recurringExpenses = RecurringExpense::where('is_active', true)
            ->where('next_process_at', '<=', $now)
            ->get();

        $recurringExpenses->each(function ($recurring) use ($now) {
            Log::info("Processing: {$recurring->name}");

            Expense::create([
                'user_id' => $recurring->user_id,
                'recurring_expense_id' => $recurring->id,
                'name' => $recurring->name,
                'amount' => $recurring->amount,
                'category' => $recurring->category,
                'notes' => $recurring->notes,
                'date' => $now->toDateString(),
            ]);

            $nextProcessTime = ExpenseHelper::calculateNextProcessTime($recurring->frequency, $now);

            $recurring->update([
                'last_processed_at' => $now->toDateTimeString(),
                'next_process_at' => $nextProcessTime->toDateTimeString(),
            ]);

            $user = User::findOrFail($recurring->user_id);
            $message = "Your recurring expense: {$recurring->name} has been processed successfully at: {$recurring->last_processed_at->format('Y-m-d h:i A')}";
            $user->notify(new ExpenseReminderNotification($message, NotificationType::ALERT));

            Log::info("Processed: {$recurring->name}");
        });

        Log::info('Finished processing recurring expenses!');
    }
}
