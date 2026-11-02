<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'level',
        'event',
        'description',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getLevelColorAttribute()
    {
        return match ($this->level) {
            'info' => 'info',
            'warning' => 'warning',
            'error' => 'danger',
            'critical' => 'danger',
            default => 'secondary',
        };
    }

    public function getLevelIconAttribute()
    {
        return match ($this->level) {
            'info' => 'fa-info-circle',
            'warning' => 'fa-exclamation-triangle',
            'error' => 'fa-times-circle',
            'critical' => 'fa-skull-crossbones',
            default => 'fa-circle',
        };
    }

    public function scopeCritical($query)
    {
        return $query->whereIn('level', ['error', 'critical']);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }
}
