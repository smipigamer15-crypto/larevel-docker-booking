<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FirewallRule extends Model
{
    protected $fillable = [
        'type',
        'value',
        'reason',
        'is_active',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeWhitelist(Builder $query): Builder
    {
        return $query->where('type', 'whitelist');
    }

    public function scopeBlacklist(Builder $query): Builder
    {
        return $query->where('type', 'blacklist');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isWhitelist(): bool
    {
        return $this->type === 'whitelist';
    }

    public function isBlacklist(): bool
    {
        return $this->type === 'blacklist';
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function matchesIp(string $ip): bool
    {
        $value = trim($this->value);

        if ($value === $ip) {
            return true;
        }

        if (str_contains($value, '/')) {
            return $this->ipInCidr($ip, $value);
        }

        if (str_contains($value, '*')) {
            $pattern = '/^' . str_replace('\*', '.*', preg_quote($value, '/')) . '$/';
            return (bool) preg_match($pattern, $ip);
        }

        return false;
    }

    private function ipInCidr(string $ip, string $cidr): bool
    {
        [$subnet, $mask] = explode('/', $cidr);
        $mask = (int) $mask;

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);

        if ($ipLong === false || $subnetLong === false) {
            return false;
        }

        $maskLong = -1 << 32 - $mask;

        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }
}
