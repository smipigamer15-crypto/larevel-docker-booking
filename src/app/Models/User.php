<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements \Illuminate\Contracts\Auth\MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'avatar',
        'two_factor_enabled',
        'two_factor_code',
        'two_factor_expires_at',
        'two_factor_verified_at',
        'api_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code',
        'api_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'two_factor_enabled' => 'boolean',
        'two_factor_expires_at' => 'datetime',
        'two_factor_verified_at' => 'datetime',
    ];

    public function generateTwoFactorCode()
    {
        $this->two_factor_code = random_int(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(1);
        $this->save();

        return $this->two_factor_code;
    }

    public function verifyTwoFactorCode($code)
    {
        if (!$this->two_factor_code || !hash_equals((string) $this->two_factor_code, (string) $code)) {
            return false;
        }

        if (!$this->two_factor_expires_at || $this->two_factor_expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function getTwoFactorSecondsRemainingAttribute()
    {
        if (!$this->two_factor_expires_at) {
            return 0;
        }

        $remaining = now()->diffInSeconds($this->two_factor_expires_at, false);
        return max(0, (int) $remaining);
    }

    public function canResendTwoFactorCode()
    {
        if (!$this->two_factor_expires_at) {
            return true;
        }

        if ($this->two_factor_expires_at->isPast()) {
            return true;
        }

        $codeCreatedAt = $this->two_factor_expires_at->copy()->subMinute();

        return now()->greaterThanOrEqualTo($codeCreatedAt->copy()->addSeconds(60));
    }

    public function resetTwoFactorCode()
    {
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }

    public function canUseTwoFactor()
    {
        if (!$this->hasVerifiedEmail()) {
            return false;
        }

        $securityInquiry = \App\Models\SecurityInquiry::where('email', $this->email)
            ->where('is_active', true)
            ->where('status', 'approved')
            ->first();

        if (!$securityInquiry || $securityInquiry->is_expired) {
            return false;
        }

        return $securityInquiry->features['two_factor_auth'] ?? false;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function securitySetting()
    {
        return $this->hasOne(SecuritySetting::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function hasActiveBookingSubscription(): bool
    {
        return $this->activeBookingSubscription() !== null;
    }

    public function activeBookingSubscription()
    {
        if (!$this->hasVerifiedEmail()) {
            return null;
        }

        return BookingSubscription::where('email', $this->email)
            ->where('is_active', true)
            ->where('status', 'approved')
            ->where(fn($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->where(fn($query) => $query->whereNull('stripe_subscription_id')->orWhereIn('subscription_status', ['active', 'trialing']))
            ->latest()
            ->first();
    }

    public function bookingsThisMonth(): int
    {
        return \App\Models\Booking::where('user_id', $this->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function monthlyBookingLimit(): ?int
    {
        return $this->hasActiveBookingSubscription() ? null : 10;
    }

    public function canCreateBooking(): bool
    {
        $limit = $this->monthlyBookingLimit();

        if ($limit === null) {
            return true;
        }

        return $this->bookingsThisMonth() < $limit;
    }

    public function bookingsRemaining(): ?int
    {
        $limit = $this->monthlyBookingLimit();

        if ($limit === null) {
            return null;
        }

        return max(0, $limit - $this->bookingsThisMonth());
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return null;
    }
}
