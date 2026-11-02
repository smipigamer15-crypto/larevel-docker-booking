<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiLog;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class ApiTokenController extends Controller
{
    public function index(Request $request)
    {
        $tokens = PersonalAccessToken::with('tokenable')
            ->when($request->user_id, fn($q) => $q->where('tokenable_id', $request->user_id))
            ->latest()
            ->paginate(20);

        $logs = ApiLog::with('user')
            ->latest()
            ->limit(50)
            ->get();

        $stats = [
            'total_tokens' => PersonalAccessToken::count(),
            'active_tokens' => PersonalAccessToken::where('last_used_at', '>=', now()->subDays(7))->count(),
            'total_requests' => ApiLog::count(),
            'requests_today' => ApiLog::whereDate('created_at', today())->count(),
        ];

        return view('admin.api-tokens.index', compact('tokens', 'logs', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'abilities' => 'array',
            'abilities.*' => 'string|in:bookings:read,bookings:write,services:read,profile:read,profile:write',
        ]);

        $user = User::findOrFail($data['user_id']);

        $token = $user->createToken($data['name'], $data['abilities'] ?? ['bookings:read', 'services:read', 'profile:read']);

        return back()->with('success', 'Token created: ' . $token->plainTextToken)
            ->with('new_token', $token->plainTextToken);
    }

    public function destroy(PersonalAccessToken $token)
    {
        $token->delete();

        return back()->with('success', 'Token revoked.');
    }

    public function revokeAll(User $user)
    {
        $user->tokens()->delete();

        return back()->with('success', "All tokens revoked for {$user->name}.");
    }
}
