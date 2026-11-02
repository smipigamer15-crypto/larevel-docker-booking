<?php

namespace App\Http\Controllers;

use App\Models\SecurityInquiry;
use Barryvdh\DomPDF\Facade\Pdf;

class SecurityReportController extends Controller
{
    public function download($token)
    {
        $inquiry = SecurityInquiry::where('access_token', $token)->firstOrFail();

        $features = $inquiry->features;
        $labels = [
            'ssl_encryption' => 'SSL Encryption',
            'two_factor_auth' => '2FA Authentication',
            'monitoring' => '24/7 Monitoring',
            'daily_backups' => 'Daily Backups',
            'advanced_firewall' => 'Advanced Firewall',
            'ddos_protection' => 'DDoS Protection',
            'vulnerability_scan' => 'Vulnerability Scan',
            'priority_support' => 'Priority Support',
            'api_access' => 'API Access',
            'custom_rules' => 'Custom Rules',
        ];

        $pdf = Pdf::loadView('security.report-pdf', compact('inquiry', 'features', 'labels'));
        $pdf->setPaper('A4', 'portrait');

        $filename = 'security-report-' . $inquiry->id . '-' . date('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
