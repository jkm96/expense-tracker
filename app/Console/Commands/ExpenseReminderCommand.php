<?php

namespace App\Console\Commands;

use App\Events\ExpenseReminderEvent;
use App\Models\User;
use App\Notifications\ExpenseReminderNotification;
use App\Utils\Enums\NotificationType;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpenseReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-missing-expenses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a notification if a user has not added expenses for a few days.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logger = Log::channel('commandlog');

        $logger->info('Started expense check!');

        $threshold = Carbon::now()->subDays(1);

        $users = User::whereDoesntHave('expenses', function ($query) use ($threshold) {
            $query->where('created_at', '>=', $threshold);
        })->get();

        foreach ($users as $user) {
            $message = "Hello {$user->name}, just a polite reminder. Remember to log your expenses!";

            // Send and save the notification
            $user->notify(new ExpenseReminderNotification($message, NotificationType::REMINDER));

            //send realtime event
            event(new ExpenseReminderEvent($user->id, $message,NotificationType::REMINDER));

            $logger->info("Notification sent to: {$user->email}");
        }

        $logger->info('Expense check completed!');
    }
}
