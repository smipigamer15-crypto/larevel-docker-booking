<?php

namespace App\Services;

use App\Models\SmsLog;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public static function send($phone, $message)
    {
        if (app()->environment('local', 'testing')) {
            SmsLog::create([
                'phone' => $phone,
                'message' => $message,
                'status' => 'sent',
            ]);
            return true;
        }

        try {
            $sid = env('TWILIO_SID');
            $token = env('TWILIO_TOKEN');
            $from = env('TWILIO_FROM');

            if ($sid && $token && $from) {
                $twilio = new \Twilio\Rest\Client($sid, $token);
                $twilio->messages->create($phone, [
                    'from' => $from,
                    'body' => $message,
                ]);
                return true;
            }
        } catch (\Exception $e) {
            Log::error('SMS error: ' . $e->getMessage());
            SmsLog::create([
                'phone' => $phone,
                'message' => $message,
                'status' => 'failed',
            ]);
        }

        return false;
    }
}
