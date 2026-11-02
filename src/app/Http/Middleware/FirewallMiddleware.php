<?php

namespace App\Http\Middleware;

use App\Models\FirewallLog;
use App\Models\FirewallRule;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class FirewallMiddleware
{
    protected array $attackPatterns = [
        'sql_injection' => [
            '/(\bunion\b.*\bselect\b)/i',
            '/(\bselect\b.*\bfrom\b.*\bwhere\b)/i',
            '/(\bdrop\b.*\btable\b)/i',
            '/(\binsert\b.*\binto\b)/i',
            '/(\bdelete\b.*\bfrom\b)/i',
            '/(\bor\b\s+\d+\s*=\s*\d+)/i',
            '/(--|\#|\/\*)/i',
        ],
        'xss' => [
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript:/i',
            '/on(load|error|click|mouseover)\s*=/i',
            '/<iframe[^>]*>/i',
            '/<embed[^>]*>/i',
            '/<object[^>]*>/i',
        ],
        'path_traversal' => [
            '/\.\.[\/\\\\]/',
            '/(etc\/passwd)/i',
            '/(windows\/system32)/i',
            '/(proc\/self)/i',
        ],
        'command_injection' => [
            '/(;|\||`|\$\().*(cat|ls|wget|curl|bash|sh|nc|rm)\b/i',
        ],
        'php_injection' => [
            '/(<\?php)/i',
            '/(base64_decode)/i',
            '/(eval\s*\()/i',
            '/(system\s*\()/i',
            '/(exec\s*\()/i',
        ],
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!config('firewall.enabled', true)) {
            return $next($request);
        }

        $ip = $request->ip();

        if ($this->isWhitelisted($ip)) {
            return $next($request);
        }

        if ($rule = $this->isBlacklisted($ip)) {
            $this->logBlock($request, 'blacklist', [
                'rule_id' => $rule->id,
                'rule_value' => $rule->value,
                'reason' => $rule->reason,
            ]);

            return $this->blockResponse('Your IP has been blocked.');
        }

        if ($this->isRateLimited($request)) {
            $this->logBlock($request, 'rate_limit', [
                'limit' => config('firewall.rate_limit', 100),
                'window' => '1 minute',
            ]);

            return $this->blockResponse('Too many requests. Please slow down.');
        }

        if ($attack = $this->detectAttack($request)) {
            $this->logBlock($request, 'attack_pattern', [
                'type' => $attack['type'],
                'payload' => substr($attack['payload'], 0, 500),
                'location' => $attack['location'],
            ]);

            $this->maybeAutoBan($ip);

            return $this->blockResponse('Malicious request detected.');
        }

        if (config('firewall.country_blocking', false)) {
            if ($this->isCountryBlocked($request)) {
                $this->logBlock($request, 'country_block', [
                    'country' => $this->getCountry($ip),
                ]);

                return $this->blockResponse('Access from your country is not allowed.');
            }
        }

        return $next($request);
    }

    protected function isWhitelisted(string $ip): bool
    {
        if (in_array($ip, config('firewall.always_allow', ['127.0.0.1', '::1']), true)) {
            return true;
        }

        return FirewallRule::active()
            ->whitelist()
            ->get()
            ->contains(fn($rule) => $rule->matchesIp($ip));
    }

    protected function isBlacklisted(string $ip): ?FirewallRule
    {
        return FirewallRule::active()
            ->blacklist()
            ->get()
            ->first(fn($rule) => $rule->matchesIp($ip));
    }

    protected function isRateLimited(Request $request): bool
    {
        if (!config('firewall.rate_limiting', true)) {
            return false;
        }

        $ip = $request->ip();
        $limit = config('firewall.rate_limit', 100);
        $key = 'firewall_rate:' . $ip;

        $count = Cache::get($key, 0);

        if ($count >= $limit) {
            return true;
        }

        Cache::put($key, $count + 1, now()->addMinute());

        return false;
    }

    protected function detectAttack(Request $request): ?array
    {
        $url = $request->fullUrl();
        foreach ($this->attackPatterns as $type => $patterns) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $url)) {
                    return [
                        'type' => $type,
                        'payload' => $url,
                        'location' => 'url',
                    ];
                }
            }
        }

        $inputs = array_merge($request->query->all(), $request->request->all());

        foreach ($inputs as $key => $value) {
            if (!is_string($value)) {
                continue;
            }

            foreach ($this->attackPatterns as $type => $patterns) {
                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        return [
                            'type' => $type,
                            'payload' => "{$key} = {$value}",
                            'location' => 'input',
                        ];
                    }
                }
            }
        }

        return null;
    }

    protected function maybeAutoBan(string $ip): void
    {
        if (!config('firewall.auto_ban', true)) {
            return;
        }

        $threshold = config('firewall.auto_ban_threshold', 3);
        $duration = config('firewall.auto_ban_duration', 24);
        $key = 'firewall_attacks:' . $ip;

        $count = Cache::get($key, 0) + 1;
        Cache::put($key, $count, now()->addHour());

        if ($count >= $threshold) {
            FirewallRule::create([
                'type' => 'blacklist',
                'value' => $ip,
                'reason' => 'Auto-banned after ' . $count . ' attack attempts',
                'is_active' => true,
                'expires_at' => now()->addHours($duration),
            ]);

            Cache::forget($key);
        }
    }

    protected function isCountryBlocked(Request $request): bool
    {
        $blocked = config('firewall.blocked_countries', []);
        if (empty($blocked)) {
            return false;
        }

        $country = $this->getCountry($request->ip());

        return $country && in_array($country, $blocked, true);
    }

    protected function getCountry(string $ip): ?string
    {
        return Cache::remember("firewall_country:{$ip}", now()->addDay(), function () use ($ip) {
            try {
                $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=countryCode");
                if ($response) {
                    $data = json_decode($response, true);
                    return $data['countryCode'] ?? null;
                }
            } catch (\Throwable $e) {
                Log::warning('Firewall: country lookup failed', [
                    'ip' => $ip,
                    'error' => $e->getMessage(),
                ]);
            }

            return null;
        });
    }

    protected function logBlock(Request $request, string $reason, array $details = []): void
    {
        try {
            FirewallLog::create([
                'ip' => $request->ip(),
                'method' => $request->method(),
                'url' => substr($request->fullUrl(), 0, 1000),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'referer' => substr((string) $request->header('referer'), 0, 500),
                'reason' => $reason,
                'details' => json_encode($details),
                'user_id' => auth()->id(),
                'blocked_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Firewall: failed to log block', ['error' => $e->getMessage()]);
        }
    }

    protected function blockResponse(string $message): Response
    {
        if (request()->expectsJson()) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => $message,
            ], 403);
        }

        abort(403, $message);
    }
}
