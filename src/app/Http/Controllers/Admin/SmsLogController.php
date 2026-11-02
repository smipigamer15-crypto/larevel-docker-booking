<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsLog;

class SmsLogController extends Controller
{
    public function index()
    {
        $logs = SmsLog::latest()->paginate(20);
        return view('admin.sms-logs.index', compact('logs'));
    }
}
