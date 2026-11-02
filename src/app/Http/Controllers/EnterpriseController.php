<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EnterpriseController extends Controller
{
    public function index()
    {
        $features = [
            [
                'icon' => 'fa-building',
                'title' => 'Multi-Branch Management',
                'description' => 'Manage multiple locations, departments, and teams from one centralized dashboard. Perfect for franchises and chains.',
                'color' => 'primary',
            ],
            [
                'icon' => 'fa-shield-alt',
                'title' => 'Advanced Security',
                'description' => 'Enterprise-grade security with SSO integration, role-based access control, and comprehensive audit logs.',
                'color' => 'success',
            ],
            [
                'icon' => 'fa-chart-bar',
                'title' => 'Advanced Analytics',
                'description' => 'Deep insights with custom reports, predictive analytics, and real-time business intelligence dashboards.',
                'color' => 'info',
            ],
            [
                'icon' => 'fa-headset',
                'title' => 'Dedicated Support',
                'description' => '24/7 priority support with a dedicated account manager, SLA guarantees, and on-call engineering team.',
                'color' => 'warning',
            ],
            [
                'icon' => 'fa-plug',
                'title' => 'Custom Integrations',
                'description' => 'Seamless integration with your existing systems including CRM, ERP, HRIS, and custom business applications.',
                'color' => 'danger',
            ],
            [
                'icon' => 'fa-database',
                'title' => 'White-Label Solutions',
                'description' => 'Fully customizable platform with your branding, custom domain, and tailored user experience for your business.',
                'color' => 'purple',
            ],
        ];

        $benefits = [
            [
                'number' => '99.9%',
                'label' => 'Uptime Guarantee',
                'description' => 'Reliable infrastructure with SLA-backed uptime',
            ],
            [
                'number' => '24/7',
                'label' => 'Support',
                'description' => 'Round-the-clock dedicated support team',
            ],
            [
                'number' => '500+',
                'label' => 'Integrations',
                'description' => 'Connect with your favorite business tools',
            ],
            [
                'number' => '1M+',
                'label' => 'Bookings',
                'description' => 'Process millions of bookings with ease',
            ],
        ];

        $faqs = [
            [
                'question' => 'What is the minimum commitment for enterprise?',
                'answer' => 'We offer flexible annual contracts with options for multi-year commitments for better pricing.',
            ],
            [
                'question' => 'Can we customize the platform?',
                'answer' => 'Yes! Enterprise plans include full customization including branding, workflows, and custom features.',
            ],
            [
                'question' => 'What security certifications do you have?',
                'answer' => 'We are SOC 2 Type II certified, GDPR compliant, and offer HIPAA compliance for healthcare businesses.',
            ],
            [
                'question' => 'Do you offer on-premise deployment?',
                'answer' => 'Yes, we offer hybrid and on-premise deployment options for enterprises with strict data requirements.',
            ],
        ];

        return view('enterprise', compact('features', 'benefits', 'faqs'));
    }
}
