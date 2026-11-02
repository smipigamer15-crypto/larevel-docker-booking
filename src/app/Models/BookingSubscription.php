<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSubscription extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'package',
        'status',
        'access_token',
        'stripe_customer_id',
        'stripe_subscription_id',
        'subscription_status',
        'subscription_ends_at',
        'cancel_at_period_end',
        'is_active',
        'access_activated_at',
        'expires_at',
    ];

    protected $casts = [
        'subscription_ends_at' => 'datetime',
        'access_activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancel_at_period_end' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'plan_label',
        'days_remaining',
    ];

    public function isActive(): bool
    {
        return $this->is_active && $this->subscription_status === 'active' && (!$this->expires_at || $this->expires_at->isFuture());
    }

    public function hasActiveSubscription(): bool
    {
        return $this->is_active && $this->subscription_status === 'active';
    }

    public function getPlanLabelAttribute(): string
    {
        return match ($this->package) {
            'pro' => 'Pro',
            'business', 'enterprise' => 'Business',
            default => ucfirst($this->package ?? 'Basic'),
        };
    }

    public function getDaysRemainingAttribute(): int
    {
        if (!$this->subscription_ends_at) {
            return 0;
        }

        return max(0, (int) ceil(now()->diffInDays($this->subscription_ends_at, false)));
    }
}
