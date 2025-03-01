<?php

namespace App\Console\Commands;

use App\Events\ExpenseNotificationEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifyMissingExpensesCommand extends Command
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
        Log::info('Started expense check!');

        $threshold = Carbon::now()->subDays(1); // Adjust the days as needed

        $users = User::whereDoesntHave('expenses', function ($query) use ($threshold) {
            $query->where('created_at', '>=', $threshold);
        })->get();

        foreach ($users as $user) {
            $message = "Hello {$user->name}, do not forget to log your expenses!";
            event(new ExpenseNotificationEvent($user->id, $message));
            Log::info("Notification sent to: {$user->email}");
        }

        Log::info('Expense check completed!');
    }
}
