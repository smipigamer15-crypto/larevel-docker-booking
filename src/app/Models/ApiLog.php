<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ApiLog extends Model
{
    protected $fillable = [
        'user_id',
        'token_id',
        'method',
        'endpoint',
        'status_code',
        'ip',
        'user_agent',
        'response_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeRecent(Builder $query, int $hours = 24): Builder
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    public function getStatusColorAttribute(): string
    {
        return match (true) {
            $this->status_code >= 500 => 'danger',
            $this->status_code >= 400 => 'warning',
            $this->status_code >= 200 && $this->status_code < 300 => 'success',
            default => 'secondary',
        };
    }
}
