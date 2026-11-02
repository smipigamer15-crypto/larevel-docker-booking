<?php

return [
    'enabled' => env('FIREWALL_ENABLED', true),
    'rate_limiting' => env('FIREWALL_RATE_LIMITING', true),
    'rate_limit' => env('FIREWALL_RATE_LIMIT', 100),
    'auto_ban' => env('FIREWALL_AUTO_BAN', true),
    'auto_ban_threshold' => env('FIREWALL_AUTO_BAN_THRESHOLD', 3),
    'auto_ban_duration' => env('FIREWALL_AUTO_BAN_DURATION', 24),
    'country_blocking' => env('FIREWALL_COUNTRY_BLOCKING', false),
    'blocked_countries' => array_filter(explode(',', env('FIREWALL_BLOCKED_COUNTRIES', ''))),
    'always_allow' => [
        '127.0.0.1',
        '::1',
    ],
];
