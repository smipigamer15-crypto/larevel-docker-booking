<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FeatureWorkspace
{
    public function settings(int $userId): array
    {
        return $this->read('users/' . $userId) + [
            'business_name' => 'Booking',
            'welcome' => 'Choose your next appointment.',
            'accent' => '#2563EB',
            'template' => 'studio',
            'published' => false,
            'email_subject' => 'Booking confirmation #{booking_id}',
            'email_body' => '',
            'zapier_url' => '',
            'zapier_enabled' => false,
            'calendar_token' => '',
        ];
    }

    public function save(int $userId, array $values): void
    {
        $this->mutate('users/' . $userId, fn($current) => array_replace($current, $values));
    }

    public function messages(int $ticketId): array
    {
        return $this->read('tickets/' . $ticketId)['messages'] ?? [];
    }

    public function addMessage(int $ticketId, string $message, bool $admin): void
    {
        $this->mutate('tickets/' . $ticketId, function ($current) use ($message, $admin) {
            $messages = $current['messages'] ?? [];
            $messages[] = ['body' => $message, 'admin' => $admin, 'at' => now()->toIso8601String()];

            return ['messages' => array_slice($messages, -200)];
        });
    }

    public function render(string $text, Booking $booking): string
    {
        return strtr($text, [
            '{booking_id}' => (string) $booking->id,
            '{name}' => $booking->user->name,
            '{service}' => $booking->service->title,
            '{date}' => $booking->booking_date->format('Y-m-d'),
            '{time}' => substr($booking->start_time, 0, 5),
        ]);
    }

    private function read(string $key): array
    {
        $disk = Storage::disk('local');
        $path = 'feature-workspace/' . $key . '.json';

        return $disk->exists($path) ? json_decode(Crypt::decryptString($disk->get($path)), true, 512, JSON_THROW_ON_ERROR) : [];
    }

    private function mutate(string $key, callable $update): void
    {
        $path = Storage::disk('local')->path('feature-workspace/' . $key . '.json');
        File::ensureDirectoryExists(dirname($path));
        $lock = fopen($path . '.lock', 'c');
        if (!$lock || !flock($lock, LOCK_EX)) {
            throw new \RuntimeException('Unable to save feature settings.');
        }

        try {
            $values = $update($this->read($key));
            File::replace($path, Crypt::encryptString(json_encode($values, JSON_THROW_ON_ERROR)));
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }
}
