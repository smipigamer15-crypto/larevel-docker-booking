<?php

namespace App\Services;

use App\Models\BlockedDate;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingAvailability
{
    public function create(User $user, array $data): Booking
    {
        return DB::transaction(function () use ($user, $data) {
            $owner = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $service = Service::whereKey($data['service_id'])->lockForUpdate()->firstOrFail();

            if (! $service->is_active || $service->duration <= 0) {
                throw ValidationException::withMessages(['service_id' => __('This service is not available.')]);
            }
            if (! $owner->canCreateBooking()) {
                throw ValidationException::withMessages(['service_id' => __('You have reached your monthly limit of 10 bookings. Upgrade to Pro for unlimited bookings.')]);
            }

            $start = Carbon::parse($data['booking_date'].' '.$data['start_time']);
            $end = $start->copy()->addMinutes($service->duration);

            if ($start->isPast() || $start->format('H:i') < '09:00' || $end->format('Y-m-d') !== $data['booking_date']
                || $end->format('H:i') > '21:00' || $start->minute % 30 !== 0) {
                throw ValidationException::withMessages(['start_time' => __('Choose an available time during opening hours.')]);
            }
            if ($data['end_time'] !== $end->format('H:i')) {
                throw ValidationException::withMessages(['end_time' => __('The end time must match the service duration.')]);
            }
            if (BlockedDate::whereDate('date', $data['booking_date'])->exists()) {
                throw ValidationException::withMessages(['booking_date' => __('This day is blocked for booking.')]);
            }
            if ($this->conflicts($service, $owner, $data['booking_date'])
                ->where('start_time', '<', $end->format('H:i:s'))
                ->where('end_time', '>', $start->format('H:i:s'))->exists()) {
                throw ValidationException::withMessages(['start_time' => __('This time slot is already booked.')])->status(409);
            }

            return Booking::create([
                'user_id' => $owner->id,
                'service_id' => $service->id,
                'booking_date' => $data['booking_date'],
                'start_time' => $start->format('H:i'),
                'end_time' => $end->format('H:i'),
                'notes' => $data['notes'] ?? null,
                'status' => 'upcoming',
            ]);
        }, 3);
    }

    public function slots(Service $service, User $user, string $date): array
    {
        if (! $service->is_active || $service->duration <= 0 || BlockedDate::whereDate('date', $date)->exists()) {
            return [];
        }

        $bookings = $this->conflicts($service, $user, $date)->get();
        $start = Carbon::parse($date.' 09:00');
        $closing = Carbon::parse($date.' 21:00');
        $slots = [];

        while ($start < $closing) {
            $end = $start->copy()->addMinutes($service->duration);
            $conflict = $bookings->contains(fn (Booking $booking) => $start < Carbon::parse($date.' '.$booking->end_time)
                && $end > Carbon::parse($date.' '.$booking->start_time)
            );

            if (! $start->isPast() && $end <= $closing && ! $conflict) {
                $slots[] = ['start' => $start->format('H:i'), 'end' => $end->format('H:i')];
            }
            $start->addMinutes(30);
        }

        return $slots;
    }

    private function conflicts(Service $service, User $user, string $date): Builder
    {
        return Booking::whereDate('booking_date', $date)
            ->where('status', 'upcoming')
            ->where(fn (Builder $query) => $query->where('service_id', $service->id)->orWhere('user_id', $user->id));
    }
}
