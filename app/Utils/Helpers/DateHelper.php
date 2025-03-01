<?php
namespace App\Utils\Helpers;
use Carbon\Carbon;

class DateHelper
{
    public static function formatTimestamp($time)
    {
        $time = Carbon::parse($time); // Ensure it's a Carbon instance
        $now = Carbon::now();

        // If it's today
        if ($time->isToday()) {
            $diffInMinutes = floor(abs($now->diffInMinutes($time)));

            // Less than 1 hour ago: Relative time
            if ($diffInMinutes < 60) {
                if ($diffInMinutes < 1) {
                    return 'Just now';
                }
                return $diffInMinutes . ' mins ago';
            }

            // More than 1 hour ago: "Today, 2:00 PM"
            return 'Today, ' . $time->format('g:i A');
        }

        // If it's yesterday
        if ($time->isYesterday()) {
            return 'Yesterday, ' . $time->format('g:i A');
        }

        // Older than yesterday: "Aug 20, 2:00 PM"
        return $time->format('M j, g:i A');
    }
}
