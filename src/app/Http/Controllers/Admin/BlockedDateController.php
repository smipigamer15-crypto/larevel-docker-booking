<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedDate;
use Illuminate\Http\Request;

class BlockedDateController extends Controller
{
    public function index()
    {
        return view('admin.calendar', ['blockedDates' => BlockedDate::orderBy('date')->paginate(30)]);
    }

    public function block(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $blockedDate = BlockedDate::whereDate('date', $data['date'])->first()
            ?? new BlockedDate(['date' => $data['date']]);
        $blockedDate->reason = $data['reason'] ?? null;
        $blockedDate->save();

        return redirect()->route('admin.calendar')->with('success', 'Date blocked.');
    }

    public function unblock(Request $request)
    {
        $data = $request->validate(['id' => ['required', 'integer', 'exists:blocked_dates,id']]);
        BlockedDate::findOrFail($data['id'])->delete();

        return redirect()->route('admin.calendar')->with('success', 'Date unblocked.');
    }
}
