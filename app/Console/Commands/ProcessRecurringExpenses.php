<?php

namespace App\Console\Commands;

use App\Events\ExpenseReminderEvent;
use App\Models\AuditLog;
use App\Models\Expense;
use App\Models\RecurringExpense;
use App\Models\User;
use App\Notifications\ExpenseReminderNotification;
use App\Utils\Constants\AppEventListener;
use App\Utils\Enums\AuditAction;
use App\Utils\Enums\NotificationType;
use App\Utils\Helpers\ExpenseHelper;
use Carbon\Carbon;
use Exception;
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
        $logger = Log::channel('commandlog');

        $logger->info('Started processing recurring expenses!');

        $now = Carbon::now();

        $recurringExpenses = RecurringExpense::where('is_active', true)
            ->where('next_process_at', '<=', $now)
            ->get();

        $recurringExpenses->each(function ($recurring) use ($now,$logger) {
            try {
                $logger->info("Processing: {$recurring->name}");

                Expense::create([
                    'user_id' => $recurring->user_id,
                    'recurring_expense_id' => $recurring->id,
                    'name' => $recurring->name,
                    'amount' => $recurring->amount,
                    'category' => $recurring->category,
                    'notes' => $recurring->notes,
                    'date' => $now->toDateString(),
                ]);

                $scheduleConfig = json_decode($recurring->schedule_config, true) ?? [];
                $lastProcessed = $recurring->last_processed_at ?? $recurring->start_date ?? now();
                $nextProcessTime = ExpenseHelper::calculateNextProcessTime($recurring->frequency, $lastProcessed, $scheduleConfig);

                $recurring->update([
                    'last_processed_at' => $now->toDateTimeString(),
                    'next_process_at' => $nextProcessTime->toDateTimeString(),
                ]);

                $user = User::findOrFail($recurring->user_id);
                if ($user) {
                    AuditLog::log(
                        AuditAction::COMMAND_EXECUTION,
                        'System',
                        $user->id,
                        "Recurring expense processed successfully",
                        'RecurringExpense',
                        $recurring->id,
                        ['name' => $recurring->name, 'amount' => $recurring->amount]
                    );

                    $message = "Your recurring expense: {$recurring->name} has been processed successfully at: {$now->format('Y-m-d h:i A')}";
                    $user->notify(new ExpenseReminderNotification($message, NotificationType::ALERT));

                    // Send real-time event
                    event(new ExpenseReminderEvent($user->id, $message, NotificationType::REMINDER));
                }

                $logger->info("Processed: {$recurring->name}");
            } catch (Exception $e) {
                $logger->error("Failed to process {$recurring->name}: " . $e->getMessage());
            }
        });

        $logger->info('Finished processing recurring expenses!');
    }
}
