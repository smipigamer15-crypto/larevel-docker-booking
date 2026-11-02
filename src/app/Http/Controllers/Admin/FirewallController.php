<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FirewallLog;
use App\Models\FirewallRule;
use Illuminate\Http\Request;

class FirewallController extends Controller
{
    public function index(Request $request)
    {
        $query = FirewallRule::query()->latest();
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $rules = $query->paginate(20)->withQueryString();
        
        $stats = [
            'total' => FirewallRule::count(),
            'whitelist' => FirewallRule::where('type', 'whitelist')->count(),
            'blacklist' => FirewallRule::where('type', 'blacklist')->count(),
            'blocked_today' => FirewallLog::whereDate('blocked_at', today())->count(),
        ];
        
        return view('admin.firewall.index', compact('rules', 'stats'));
    }
    
    public function logs(Request $request)
    {
        $query = FirewallLog::query()->latest('blocked_at');
        
        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }
        
        if ($request->filled('ip')) {
            $query->where('ip', 'like', '%' . $request->input('ip') . '%');
        }
        
        $logs = $query->paginate(30)->withQueryString();
        
        $stats = [
            'total' => FirewallLog::count(),
            'today' => FirewallLog::whereDate('blocked_at', today())->count(),
            'week' => FirewallLog::where('blocked_at', '>=', now()->subWeek())->count(),
            'unique_ips' => FirewallLog::distinct('ip')->count('ip'),
        ];
        
        return view('admin.firewall.logs', compact('logs', 'stats'));
    }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:whitelist,blacklist',
            'value' => 'required|string|max:255',
            'reason' => 'nullable|string|max:500',
            'expires_at' => 'nullable|date|after:now',
        ]);
        
        $data['is_active'] = true;
        $data['created_by'] = auth()->id();
        
        FirewallRule::create($data);
        
        return back()->with('success', 'Rule added successfully.');
    }
    
    public function update(Request $request, FirewallRule $rule)
    {
        $data = $request->validate([
            'reason' => 'nullable|string|max:500',
            'is_active' => 'sometimes|boolean',
            'expires_at' => 'nullable|date',
        ]);
        
        $rule->update($data);
        
        return back()->with('success', 'Rule updated successfully.');
    }
    
    public function destroy(FirewallRule $rule)
    {
        $rule->delete();
        
        return back()->with('success', 'Rule deleted successfully.');
    }
    
    public function clearLogs()
    {
        FirewallLog::where('blocked_at', '<', now()->subDays(30))->delete();
        
        return back()->with('success', 'Old logs cleared.');
    }
    
    public function blockIp(Request $request)
    {
        $data = $request->validate([
            'ip' => 'required|string|max:45',
            'reason' => 'nullable|string|max:500',
        ]);
        
        FirewallRule::create([
            'type' => 'blacklist',
            'value' => $data['ip'],
            'reason' => $data['reason'] ?? 'Manually blocked by admin',
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);
        
        return back()->with('success', 'IP blocked successfully.');
    }
    
    public function unblockIp(FirewallRule $rule)
    {
        $rule->delete();
        
        return back()->with('success', 'IP unblocked.');
    }
}
