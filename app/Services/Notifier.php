<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Notifier
{
    /**
     * Send dynamic notification
     *
     * @param string $email
     * @param Mailable $notification
     * @throws \Exception
     */
    public function sendNotification(string $email, Mailable $notification, string $channel)
    {
        $cacheKey = $channel . '_' . $email;

        if (Cache::has($cacheKey)) {
            Log::channel($channel)->info("Alert skipped for {$email} due to recent notification");
            return;
        }

        try {
            Mail::to($email)->send($notification);
            Log::channel($channel)->info("Email sent to {$email} at " . now());
            Cache::put($cacheKey, true, 300); //5min
        } catch (\Exception $e) {
            Log::channel($channel)->error("Failed to send email to {$email}: " . $e->getMessage());
        }
    }
}
