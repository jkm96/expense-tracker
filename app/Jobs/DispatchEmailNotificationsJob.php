<?php

namespace App\Jobs;

use App\Mail\UserForgotPasswordMail;
use App\Mail\UserVerificationMail;
use App\Utils\Constants\AppEmailType;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DispatchEmailNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $jobPayload;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct($jobPayload)
    {
        $this->jobPayload = $jobPayload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $emailData = $this->jobPayload;
        try {
            Log::info("Email type " . $emailData['type']);
            Log::info($emailData);
            $email = null;
            switch ($emailData['type']) {
                case AppEmailType::USER_VERIFICATION:
                    $email = new UserVerificationMail($emailData);
                    break;
                case AppEmailType::USER_FORGOT_PASSWORD:
                    $email = new UserForgotPasswordMail($emailData);
                    break;
            }
            Log::info("Sent email to " . $emailData['recipientEmail']);
            Mail::to($emailData['recipientEmail'])->send($email);
        } catch (Exception $e) {
            Log::error('Error sending email: ' . $e->getMessage());
        }
    }
}
