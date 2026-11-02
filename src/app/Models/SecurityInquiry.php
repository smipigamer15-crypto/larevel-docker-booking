<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SecurityInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'message',
        'package',
        'duration',
        'duration_days',
        'status',
        'admin_notes',
        'contacted_at',
        'access_token',
        'access_activated_at',
        'expires_at',
        'last_reminder_sent_at',
        'reminder_count',
        'is_active',
        'stripe_customer_id',
        'stripe_subscription_id',
        'stripe_price_id',
        'subscription_status',
        'subscription_ends_at',
        'cancel_at_period_end',
    ];

    protected $casts = [
        'contacted_at' => 'datetime',
        'access_activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_reminder_sent_at' => 'datetime',
        'is_active' => 'boolean',
        'subscription_ends_at' => 'datetime',
        'cancel_at_period_end' => 'boolean',
    ];

    public function getIsSubscribedAttribute()
    {
        return $this->subscription_status === 'active' || $this->subscription_status === 'trialing';
    }

    public function getPackageStripePriceIdAttribute()
    {
        return [
            'pro' => config('services.stripe.prices.security_pro'),
            'enterprise' => config('services.stripe.prices.security_enterprise'),
        ][$this->package] ?? null;
    }

    public static function getPackageFeatures($package)
    {
        $features = [
            'basic' => [
                'ssl_encryption' => true,
                'two_factor_auth' => false,
                'monitoring' => false,
                'daily_backups' => false,
                'advanced_firewall' => false,
                'ddos_protection' => false,
                'vulnerability_scan' => false,
                'priority_support' => false,
                'api_access' => false,
                'custom_rules' => false,
            ],
            'pro' => [
                'ssl_encryption' => true,
                'two_factor_auth' => true,
                'monitoring' => true,
                'daily_backups' => true,
                'advanced_firewall' => false,
                'ddos_protection' => true,
                'vulnerability_scan' => true,
                'priority_support' => false,
                'api_access' => false,
                'custom_rules' => false,
            ],
            'enterprise' => [
                'ssl_encryption' => true,
                'two_factor_auth' => true,
                'monitoring' => true,
                'daily_backups' => true,
                'advanced_firewall' => true,
                'ddos_protection' => true,
                'vulnerability_scan' => true,
                'priority_support' => true,
                'api_access' => true,
                'custom_rules' => true,
            ],
            'custom' => [
                'ssl_encryption' => true,
                'two_factor_auth' => true,
                'monitoring' => true,
                'daily_backups' => true,
                'advanced_firewall' => false,
                'ddos_protection' => false,
                'vulnerability_scan' => false,
                'priority_support' => false,
                'api_access' => false,
                'custom_rules' => false,
            ],
        ];

        return $features[$package] ?? $features['basic'];
    }

    public function getFeaturesAttribute()
    {
        return self::getPackageFeatures($this->package);
    }

    public function getActiveFeaturesCountAttribute()
    {
        return collect($this->features)->filter()->count();
    }

    public function getTotalFeaturesCountAttribute()
    {
        return count($this->features);
    }

    public function getPackageLabelAttribute()
    {
        return [
            'basic' => 'Basic',
            'pro' => 'Pro',
            'enterprise' => 'Enterprise',
            'custom' => 'Custom',
        ][$this->package] ?? $this->package;
    }

    public function getPackageDescriptionAttribute()
    {
        return [
            'basic' => 'Essential security for small businesses',
            'pro' => 'Advanced security for growing businesses',
            'enterprise' => 'Complete security for large enterprises',
            'custom' => 'Tailored security solution',
        ][$this->package] ?? '';
    }

    public function getPackagePriceAttribute()
    {
        return [
            'basic' => 0,
            'pro' => 49,
            'enterprise' => 199,
            'custom' => 'Custom',
        ][$this->package] ?? 0;
    }

    public function getStatusLabelAttribute()
    {
        return [
            'pending' => 'Pending',
            'contacted' => 'Contacted',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'deactivated' => 'Deactivated',
            'expired' => 'Expired',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'contacted' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            'deactivated' => 'secondary',
            'expired' => 'secondary',
        ][$this->status] ?? 'secondary';
    }

    public function getDurationDays($duration)
    {
        return match ($duration) {
            '1_month' => 30,
            '3_months' => 90,
            '6_months' => 180,
            '1_year' => 365,
            'custom' => $this->duration_days ?? 30,
            default => 30,
        };
    }

    public function getDurationLabelAttribute()
    {
        return match ($this->duration) {
            '1_month' => '1 Month',
            '3_months' => '3 Months',
            '6_months' => '6 Months',
            '1_year' => '1 Year',
            'custom' => 'Custom',
            'monthly' => 'Monthly Subscription',
            default => 'N/A',
        };
    }

    public function getDaysRemainingAttribute()
    {
        if (! $this->expires_at) {
            return 0;
        }

        if ($this->expires_at->isPast()) {
            return 0;
        }

        return max(0, (int) ceil(now()->diffInDays($this->expires_at, false)));
    }

    public function getIsExpiredAttribute()
    {
        if (! $this->expires_at) {
            return false;
        }

        return now()->greaterThan($this->expires_at);
    }

    public function getExpirationStatusAttribute()
    {
        if (! $this->expires_at) {
            return 'inactive';
        }

        if ($this->is_expired) {
            return 'expired';
        }

        $days = $this->days_remaining;
        if ($days <= 3) {
            return 'expiring_soon';
        }

        return 'active';
    }

    public function getExpirationBadgeAttribute()
    {
        return match ($this->expiration_status) {
            'expired' => ['class' => 'danger', 'label' => 'Expired'],
            'expiring_soon' => ['class' => 'warning', 'label' => 'Expiring Soon'],
            'active' => ['class' => 'success', 'label' => 'Active'],
            default => ['class' => 'secondary', 'label' => 'N/A'],
        };
    }

    public function generateAccessToken()
    {
        $this->access_token = Str::random(32);
        $this->save();

        return $this->access_token;
    }

    public function activateAccess($duration = '1_year')
    {
        $this->is_active = true;
        $this->access_activated_at = now();
        $this->status = 'approved';

        $this->duration = $duration;
        $this->duration_days = $this->getDurationDays($duration);
        $this->expires_at = now()->addDays($this->duration_days);

        $this->save();

        return $this;
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')->where('expires_at', '<', now());
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now());
    }

    public function scopeExpiringSoon($query, $days = 3)
    {
        return $query->where('is_active', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<', now()->addDays($days));
    }
}
