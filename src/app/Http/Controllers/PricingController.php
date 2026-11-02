<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function index()
    {
        $plans = [
            [
                'id' => 'basic',
                'name' => 'Basic',
                'price' => 0,
                'period' => 'month',
                'description' => 'Perfect for getting started',
                'features' => [
                    'Up to 10 bookings per month',
                    'Basic configuration',
                    'Email notifications',
                    'Calendar access',
                    'Email support',
                ],
                'popular' => false,
                'button_text' => 'Get Started',
                'button_class' => 'btn-outline-primary',
            ],
            [
                'id' => 'pro',
                'name' => 'Pro',
                'price' => 29,
                'period' => 'month',
                'description' => 'Best for growing businesses',
                'features' => [
                    'Unlimited bookings',
                    'SMS notifications',
                    '24/7 support',
                    'Additional integrations',
                    'Analytics & reports',
                ],
                'popular' => true,
                'button_text' => 'Subscribe Now',
                'button_class' => 'btn-primary',
            ],
            [
                'id' => 'business',
                'name' => 'Business',
                'price' => 99,
                'period' => 'month',
                'description' => 'For large businesses',
                'features' => [
                    'All Pro features',
                    'API access',
                    'Multiple administrators',
                    'Priority support',
                    'White-label branding',
                ],
                'popular' => false,
                'button_text' => 'Contact Sales',
                'button_class' => 'btn-outline-primary',
            ],
        ];

        $faqs = [
            [
                'question' => 'Can I change my plan later?',
                'answer' => 'Yes, you can upgrade or downgrade your plan at any time.',
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept all major credit cards, PayPal, and bank transfers.',
            ],
            [
                'question' => 'Is there a free trial?',
                'answer' => 'Yes, we offer a 14-day free trial on all paid plans.',
            ],
        ];

        return view('pricing', compact('plans', 'faqs'));
    }
}
