<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecuritySetting extends Model
{
    protected $fillable = [
        'user_id',
        'ip_whitelist',
        'ip_blacklist',
        'blocked_countries',
        'max_login_attempts',
        'login_lockout_minutes',
        'session_timeout_minutes',
        'require_2fa',
        'allowed_hours',
        'allowed_days',
        'is_enabled',
    ];

    protected $casts = [
        'ip_whitelist' => 'array',
        'ip_blacklist' => 'array',
        'blocked_countries' => 'array',
        'allowed_hours' => 'array',
        'allowed_days' => 'array',
        'require_2fa' => 'boolean',
        'is_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isIpWhitelisted(string $ip): bool
    {
        if (empty($this->ip_whitelist)) {
            return true;

        }

        foreach ($this->ip_whitelist as $rule) {
            if ($this->ipMatches($ip, $rule)) {
                return true;
            }
        }

        return false;
    }

    public function isIpBlacklisted(string $ip): bool
    {
        if (empty($this->ip_blacklist)) {
            return false;
        }

        foreach ($this->ip_blacklist as $rule) {
            if ($this->ipMatches($ip, $rule)) {
                return true;
            }
        }

        return false;
    }

    protected function ipMatches(string $ip, string $rule): bool
    {
        $rule = trim($rule);

        if ($rule === $ip) {
            return true;
        }

        if (str_contains($rule, '/')) {
            [$subnet, $mask] = explode('/', $rule);
            $ipLong = ip2long($ip);
            $subnetLong = ip2long($subnet);
            if ($ipLong === false || $subnetLong === false) {
                return false;
            }
            $maskLong = -1 << 32 - (int) $mask;
            return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
        }

        if (str_contains($rule, '*')) {
            $pattern = '/^' . str_replace('\*', '.*', preg_quote($rule, '/')) . '$/';
            return (bool) preg_match($pattern, $ip);
        }

        return false;
    }

    public function isHourAllowed(): bool
    {
        if (empty($this->allowed_hours)) {
            return true;
        }

        return in_array((int) now()->format('G'), $this->allowed_hours, true);
    }

    public function isDayAllowed(): bool
    {
        if (empty($this->allowed_days)) {
            return true;
        }

        return in_array((int) now()->format('N'), $this->allowed_days, true);
    }

    public function getAllowedHoursLabelAttribute(): string
    {
        if (empty($this->allowed_hours)) {
            return 'Any time';
        }

        $hours = collect($this->allowed_hours)->sort()->values();
        return $hours->first() . ':00 — ' . ($hours->last() + 1) . ':00';
    }

    public function getAllowedDaysLabelAttribute(): string
    {
        if (empty($this->allowed_days)) {
            return 'Any day';
        }

        $days = [
            1 => 'Mon',
            2 => 'Tue',
            3 => 'Wed',
            4 => 'Thu',
            5 => 'Fri',
            6 => 'Sat',
            7 => 'Sun',
        ];

        $labels = collect($this->allowed_days)->sort()->map(fn($d) => $days[$d] ?? '?');
        return $labels->implode(', ');
    }
}
