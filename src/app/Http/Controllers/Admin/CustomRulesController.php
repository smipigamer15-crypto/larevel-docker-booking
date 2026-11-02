<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecuritySetting;
use App\Models\User;
use Illuminate\Http\Request;

class CustomRulesController extends Controller
{
    public function index()
    {
        $users = User::with('securitySetting')
            ->orderBy('name')
            ->paginate(20);

        $stats = [
            'total_users' => User::count(),
            'enabled_rules' => SecuritySetting::where('is_enabled', true)->count(),
            'total_rules' => SecuritySetting::count(),
        ];

        return view('admin.custom-rules.index', compact('users', 'stats'));
    }

    public function edit(User $user)
    {
        $settings = $user->securitySetting ?? new SecuritySetting([
            'user_id' => $user->id,
            'max_login_attempts' => 5,
            'login_lockout_minutes' => 15,
            'session_timeout_minutes' => 120,
            'is_enabled' => false,
        ]);

        return view('admin.custom-rules.edit', compact('user', 'settings'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'ip_whitelist' => 'nullable|string',
            'ip_blacklist' => 'nullable|string',
            'blocked_countries' => 'nullable|string',
            'max_login_attempts' => 'required|integer|min:1|max:20',
            'login_lockout_minutes' => 'required|integer|min:1|max:1440',
            'session_timeout_minutes' => 'required|integer|min:5|max:10080',
            'require_2fa' => 'boolean',
            'allowed_hours' => 'nullable|array',
            'allowed_hours.*' => 'integer|min:0|max:23',
            'allowed_days' => 'nullable|array',
            'allowed_days.*' => 'integer|min:1|max:7',
            'is_enabled' => 'boolean',
        ]);

        $data['ip_whitelist'] = $this->parseList($request->input('ip_whitelist'));
        $data['ip_blacklist'] = $this->parseList($request->input('ip_blacklist'));
        $data['blocked_countries'] = $this->parseList($request->input('blocked_countries'));

        $data['require_2fa'] = $request->boolean('require_2fa');
        $data['is_enabled'] = $request->boolean('is_enabled');
        $data['allowed_hours'] = $request->input('allowed_hours', []);
        $data['allowed_days'] = $request->input('allowed_days', []);

        SecuritySetting::updateOrCreate(['user_id' => $user->id], $data);

        return redirect()->route('admin.custom-rules.edit', $user)
            ->with('success', 'Security rules saved successfully.');
    }

    public function reset(User $user)
    {
        $user->securitySetting?->delete();

        return redirect()->route('admin.custom-rules.edit', $user)
            ->with('success', 'Security rules reset to defaults.');
    }

    protected function parseList(?string $input): array
    {
        if (!$input) {
            return [];
        }

        $items = preg_split('/[\s,]+/', trim($input), -1, PREG_SPLIT_NO_EMPTY);

        return array_values(array_unique(array_map('trim', $items)));
    }
}
