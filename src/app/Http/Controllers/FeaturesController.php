<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Service;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\BookingCalendar;
use App\Services\FeatureWorkspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class FeaturesController extends Controller
{
    public function index()
    {
        $features = collect(config('feature_catalog'))->reject(fn($entry) => $entry['category'] === 'resources');

        return view('features.index', compact('features'));
    }

    public function show(Request $request, FeatureWorkspace $workspace, string $page)
    {
        $catalog = config('feature_catalog');
        abort_unless(isset($catalog[$page]) && $page !== 'security', 404);
        $feature = $catalog[$page] + ['slug' => $page];
        $settings = $workspace->settings($request->user()?->id ?? 0);
        $services = Service::where('is_active', true)->orderBy('title')->get();
        $popular = $feature['tool'] === 'popular' ? Service::where('is_active', true)->withCount('bookings')->orderByDesc('bookings_count')->limit(6)->get() : collect();
        $reviews = $feature['tool'] === 'stories' ? Review::where('is_approved', true)->with(['user', 'service'])->latest()->limit(12)->get() : collect();
        $tickets = $request->user() && $feature['tool'] === 'support' ? SupportTicket::where('user_id', $request->user()->id)->latest()->limit(10)->get() : collect();
        $threads = $tickets->mapWithKeys(fn($ticket) => [$ticket->id => $workspace->messages($ticket->id)]);
        $calendarUrl = $request->user() && $settings['calendar_token'] ? URL::signedRoute('features.calendar.feed', ['user' => $request->user()->id, 'token' => $settings['calendar_token']]) : null;
        $period = $request->validate(['days' => ['nullable', 'integer', 'in:7,30,90']]);
        $days = (int) ($period['days'] ?? 30);
        $analytics = null;
        if ($request->user()?->hasActiveBookingSubscription() && $feature['tool'] === 'analytics') {
            $bookings = $request->user()->bookings()->with('service')->whereBetween('created_at', [now()->subDays($days - 1)->startOfDay(), now()->endOfDay()])->get();
            $analytics = [
                'total' => $bookings->count(),
                'completed' => $bookings->where('status', 'completed')->count(),
                'cancelled' => $bookings->where('status', 'cancelled')->count(),
                'bookings' => $bookings->sortByDesc('created_at')->take(20),
            ];
        }

        return view('features.tool', compact('feature', 'settings', 'services', 'popular', 'reviews', 'tickets', 'threads', 'calendarUrl', 'days', 'analytics'));
    }

    public function save(Request $request, FeatureWorkspace $workspace)
    {
        $tool = $request->validate(['tool' => 'required|in:branding,templates,emails,zapier,calendar'])['tool'];
        $rules = match ($tool) {
            'branding' => [
                'business_name' => 'required|string|max:100',
                'welcome' => 'required|string|max:300',
                'accent' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'published' => 'sometimes|boolean',
            ],
            'templates' => ['template' => 'required|in:studio,minimal,classic'],
            'emails' => [
                'email_subject' => ['required', 'string', 'max:150', 'not_regex:/[\r\n]/'],
                'email_body' => 'required|string|max:5000',
            ],
            'zapier' => [
                'zapier_url' => [
                    'nullable',
                    'required_if:zapier_enabled,1',
                    'string',
                    'max:500',
                    'regex:~\Ahttps://hooks\.zapier\.com/hooks/catch/[A-Za-z0-9/_-]+/?\z~',
                ],
                'zapier_enabled' => 'sometimes|boolean',
            ],
            'calendar' => [],
        };
        $values = $request->validate($rules);
        if ($tool === 'branding') {
            $values['published'] = $request->boolean('published');
        }
        if ($tool === 'zapier') {
            $values['zapier_enabled'] = $request->boolean('zapier_enabled');
            $values['zapier_url'] = $values['zapier_url'] ?? '';
        }
        if ($tool === 'calendar') {
            $values['calendar_token'] = Str::random(64);
        }
        $workspace->save($request->user()->id, $values);

        return back()->with('success', 'Your settings have been saved.');
    }

    public function previewEmail(Request $request, FeatureWorkspace $workspace)
    {
        $settings = $workspace->settings($request->user()->id);
        $values = [
            '{booking_id}' => '123',
            '{name}' => $request->user()->name,
            '{service}' => 'Example appointment',
            '{date}' => now()->addDay()->toDateString(),
            '{time}' => '10:00',
        ];
        Mail::raw(strtr($settings['email_body'] ?: 'Your appointment on {date} at {time} is confirmed.', $values), function ($mail) use ($request, $settings, $values) {
            $mail->to($request->user()->email)->subject(preg_replace('/[\r\n]+/', ' ', strtr($settings['email_subject'], $values)));
        });

        return back()->with('success', 'Preview sent to your verified email address.');
    }

    public function testZapier(Request $request, FeatureWorkspace $workspace, \App\Services\BookingAutomation $automation)
    {
        $settings = $workspace->settings($request->user()->id);
        if (!$settings['zapier_enabled'] || !$settings['zapier_url']) {
            return back()->withErrors(['zapier_url' => 'Save and enable your Zapier hook first.']);
        }
        try {
            $automation->send($request->user()->id, ['event' => 'connection.test', 'message' => 'Your Booking connection is ready.']);
        } catch (\Throwable $exception) {
            return back()->withErrors(['zapier_url' => 'Zapier did not accept the test. Check your Catch Hook URL.']);
        }

        return back()->with('success', 'Test event delivered to Zapier.');
    }

    public function calendar(Request $request, FeatureWorkspace $workspace, BookingCalendar $calendar, ?User $user = null, ?string $token = null)
    {
        if ($user) {
            $expected = $workspace->settings($user->id)['calendar_token'];
            abort_unless($expected && $token && hash_equals($expected, $token), 404);
        } else {
            $user = $request->user();
        }
        $content = $calendar->render($user->bookings()->with('service')->whereDate('booking_date', '>=', now()->subYear())->orderBy('booking_date')->get());

        return response($content)->withHeaders([
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="my-bookings.ics"',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function bookingPage(User $user, FeatureWorkspace $workspace)
    {
        $settings = $workspace->settings($user->id);
        abort_unless($settings['published'] && $user->hasVerifiedEmail(), 404);
        $services = Service::where('is_active', true)->orderBy('title')->get();

        return view('features.booking-page', compact('settings', 'services'));
    }

    public function messages(Request $request, SupportTicket $ticket, FeatureWorkspace $workspace)
    {
        abort_unless($ticket->user_id === $request->user()->id, 404);
        if ($request->isMethod('post')) {
            $data = $request->validate(['message' => 'required|string|max:5000']);
            $workspace->addMessage($ticket->id, $data['message'], false);
            $ticket->update(['status' => 'open']);

            return back()->with('success', 'Your message has been added.');
        }

        return response()->json(['messages' => $workspace->messages($ticket->id)])->header('Cache-Control', 'private, no-store');
    }
}
