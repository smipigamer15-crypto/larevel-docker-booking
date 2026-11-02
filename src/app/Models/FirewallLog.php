<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FirewallLog extends Model
{
    protected $fillable = [
        'ip',
        'method',
        'url',
        'user_agent',
        'referer',
        'country',
        'reason',
        'details',
        'user_id',
        'blocked_at',
    ];

    protected $casts = [
        'blocked_at' => 'datetime',
    ];

    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('blocked_at', '>=', now()->subDays($days));
    }

    public function scopeForIp(Builder $query, string $ip): Builder
    {
        return $query->where('ip', $ip);
    }

    public function scopeReason(Builder $query, string $reason): Builder
    {
        return $query->where('reason', $reason);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getReasonLabelAttribute(): string
    {
        return match ($this->reason) {
            'blacklist' => 'Blacklisted IP',
            'rate_limit' => 'Rate Limit',
            'attack_pattern' => 'Attack Pattern',
            'country_block' => 'Country Block',
            'auto_ban' => 'Auto Ban',
            default => ucfirst($this->reason),
        };
    }

    public function getReasonColorAttribute(): string
    {
        return match ($this->reason) {
            'blacklist' => 'danger',
            'rate_limit' => 'warning',
            'attack_pattern' => 'danger',
            'country_block' => 'purple',
            'auto_ban' => 'dark',
            default => 'secondary',
        };
    }
}
