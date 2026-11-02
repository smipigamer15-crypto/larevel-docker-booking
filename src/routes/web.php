<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FeaturesController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\BookingSubscriptionController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\BookingSubscriptionController as AdminBookingSubscriptionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\FirewallController;
use App\Http\Controllers\Admin\BlockedDateController;
use App\Http\Controllers\Admin\SecurityController as AdminSecurityController;
use App\Http\Controllers\Admin\SmsLogController;
use Illuminate\Support\Facades\Route;

Route::post('/language', [\App\Http\Controllers\LocaleController::class, 'update'])
    ->middleware('throttle:strict')
    ->name('locale.update');

Route::middleware(['throttle:public'])->group(function () {
    Route::get('/', function () {
        $services = App\Models\Service::where('is_active', true)->limit(6)->get();
        return view('home', compact('services'));
    })->name('home');

    Route::get('/pricing', [App\Http\Controllers\PricingController::class, 'index'])->name('pricing');
    Route::get('/features', [FeaturesController::class, 'index'])->name('features');
    Route::get('/enterprise', [App\Http\Controllers\EnterpriseController::class, 'index'])->name('enterprise');

    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');

    Route::get('/features/security', [SecurityController::class, 'index'])->name('security');
    Route::get('/features/{page}', [FeaturesController::class, 'show'])
        ->whereIn('page', array_keys(array_filter(config('feature_catalog'), fn($entry) => $entry['category'] !== 'resources' && $entry['tool'] !== 'security')))
        ->name('features.show');
    Route::get('/resources/{page}', [FeaturesController::class, 'show'])
        ->whereIn('page', array_keys(array_filter(config('feature_catalog'), fn($entry) => $entry['category'] === 'resources')))
        ->name('resources.show');
    Route::get('/book/{user}', [FeaturesController::class, 'bookingPage'])->whereNumber('user')->name('features.booking-page');
    Route::get('/calendar/{user}/{token}.ics', [FeaturesController::class, 'calendar'])
        ->middleware('signed')->whereNumber('user')->where('token', '[A-Za-z0-9]{64}')->name('features.calendar.feed');

    Route::get('/about', function () {
        return view('resources.about');
    })->name('about');

    Route::get('/partners', function () {
        return view('resources.partners');
    })->name('partners');

    Route::get('/affiliate', function () {
        return view('resources.affiliate');
    })->name('affiliate');
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class, 'throttle:public'])
    ->name('stripe.webhook');

Route::post('/features/security', [SecurityController::class, 'store'])->middleware('throttle:contact')->name('security.store');
Route::get('/security/checkout/success/{inquiry}', [SecurityController::class, 'checkoutSuccess'])->middleware(['auth', 'verified'])->name('security.checkout.success');
Route::get('/security/status/{token}', [SecurityController::class, 'clientStatus'])->name('security.client.status');
Route::get('/security/activate/{token}', [SecurityController::class, 'activateAccess'])->name('security.client.activate');
Route::post('/security/check-status', [SecurityController::class, 'checkStatus'])->middleware('throttle:strict')->name('security.check.status');
Route::get('/security/report/{token}', [App\Http\Controllers\SecurityReportController::class, 'download'])->name('security.report.download');

Route::middleware(['auth'])->group(function () {
    Route::middleware('verified')->group(function () {
        Route::post('/features/settings', [FeaturesController::class, 'save'])->middleware('throttle:strict')->name('features.settings');
        Route::post('/features/emails/preview', [FeaturesController::class, 'previewEmail'])->middleware('throttle:strict')->name('features.email-preview');
        Route::get('/features/calendar/export', [FeaturesController::class, 'calendar'])->name('features.calendar.export');
        Route::post('/features/zapier/test', [FeaturesController::class, 'testZapier'])->middleware('throttle:strict')->name('features.zapier-test');
    });
    Route::get('/support/{ticket}/messages', [FeaturesController::class, 'messages'])->middleware('throttle:60,1')->name('features.support.messages');
    Route::post('/support/{ticket}/messages', [FeaturesController::class, 'messages'])->middleware('throttle:strict')->name('features.support.reply');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.remove-avatar');

    Route::post('/profile/generate-api-token', [ProfileController::class, 'generateApiToken'])->name('profile.generate-api-token');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->middleware('throttle:booking-create')->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->middleware('throttle:strict')->name('bookings.cancel');

    Route::get('/booking/slots', [BookingController::class, 'getAvailableSlots'])->name('booking.slots');
    Route::get('/booking/calendar', [BookingController::class, 'getCalendarData'])->name('booking.calendar');

    Route::get('/bookings/{booking}/review', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/bookings/{booking}/review', [ReviewController::class, 'store'])->middleware('throttle:strict')->name('reviews.store');

    Route::middleware('verified')->group(function () {
        Route::get('/booking/checkout/{package}', [BookingSubscriptionController::class, 'checkoutPage'])->name('booking.subscription.checkout.page');
        Route::post('/booking/subscribe/{package}', [BookingSubscriptionController::class, 'checkout'])->middleware('throttle:strict')->name('booking.subscription.checkout');

        Route::get('/booking/subscription/manage/{token}', [BookingSubscriptionController::class, 'manage'])->name('booking.subscription.manage');
        Route::post('/booking/subscription/cancel/{token}', [BookingSubscriptionController::class, 'cancelSubscription'])->middleware('throttle:strict')->name('booking.subscription.cancel.subscription');
        Route::post('/booking/subscription/resume/{token}', [BookingSubscriptionController::class, 'resumeSubscription'])->middleware('throttle:strict')->name('booking.subscription.resume');
        Route::get('/booking/subscription/report/{token}', [BookingSubscriptionController::class, 'downloadReport'])->middleware('throttle:strict')->name('booking.subscription.report');
        Route::get('/booking/subscription/success/{subscription}', [BookingSubscriptionController::class, 'success'])->name('booking.subscription.success');
        Route::get('/booking/subscription/cancel', [BookingSubscriptionController::class, 'cancel'])->name('booking.subscription.cancel');

        Route::get('/security/checkout/{package}', [SecurityController::class, 'checkoutPage'])->name('security.checkout');
        Route::post('/security/checkout/{package}', [SecurityController::class, 'checkout'])->middleware('throttle:strict')->name('security.checkout.post');
        Route::post('/security/subscription/cancel/{token}', [SecurityController::class, 'cancelSubscription'])->middleware('throttle:strict')->name('security.subscription.cancel');
        Route::post('/security/subscription/resume/{token}', [SecurityController::class, 'resumeSubscription'])->middleware('throttle:strict')->name('security.subscription.resume');
    });

    Route::post('/two-factor/enable', [App\Http\Controllers\TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::post('/two-factor/disable', [App\Http\Controllers\TwoFactorController::class, 'disable'])->name('2fa.disable');

    Route::prefix('support')->name('support.')->group(function () {
        Route::post('/ticket', [\App\Http\Controllers\SupportTicketController::class, 'store'])->name('ticket.store');
    });
});

Route::middleware(['guest', 'throttle:auth'])->group(function () {
    Route::get('/two-factor', [App\Http\Controllers\TwoFactorController::class, 'show'])->name('2fa.show');
    Route::post('/two-factor/verify', [App\Http\Controllers\TwoFactorController::class, 'verify'])->name('2fa.verify');
    Route::post('/two-factor/resend', [App\Http\Controllers\TwoFactorController::class, 'resend'])->name('2fa.resend');
});

Route::get('/admin', fn() => redirect()->route('admin.dashboard'))->middleware(['auth', 'admin'])->name('admin');

Route::middleware(['auth', 'admin', 'throttle:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/export', [AdminDashboardController::class, 'export'])->name('export');

    Route::resource('services', AdminServiceController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('users', UserController::class);
    Route::resource('bookings', AdminBookingController::class);

    Route::get('calendar', [BlockedDateController::class, 'index'])->name('calendar');
    Route::post('calendar/block', [BlockedDateController::class, 'block'])->name('calendar.block');
    Route::delete('calendar/unblock', [BlockedDateController::class, 'unblock'])->name('calendar.unblock');

    Route::get('/sms-logs', [SmsLogController::class, 'index'])->name('sms-logs.index');

    Route::prefix('api-tokens')->name('api-tokens.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ApiTokenController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\ApiTokenController::class, 'store'])->name('store');
        Route::delete('/{token}', [\App\Http\Controllers\Admin\ApiTokenController::class, 'destroy'])->name('destroy');
        Route::delete('/user/{user}/revoke-all', [\App\Http\Controllers\Admin\ApiTokenController::class, 'revokeAll'])->name('revoke-all');
    });

    Route::prefix('custom-rules')->name('custom-rules.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CustomRulesController::class, 'index'])->name('index');
        Route::get('/{user}/edit', [\App\Http\Controllers\Admin\CustomRulesController::class, 'edit'])->name('edit');
        Route::put('/{user}', [\App\Http\Controllers\Admin\CustomRulesController::class, 'update'])->name('update');
        Route::delete('/{user}/reset', [\App\Http\Controllers\Admin\CustomRulesController::class, 'reset'])->name('reset');
    });

    Route::prefix('security')->name('security.')->group(function () {
        Route::get('/', [AdminSecurityController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminSecurityController::class, 'show'])->name('show');
        Route::put('/{id}', [AdminSecurityController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminSecurityController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/cancel-subscription', [AdminSecurityController::class, 'cancelSubscription'])->name('cancel-subscription');
        Route::put('/{id}/regenerate-token', [AdminSecurityController::class, 'regenerateToken'])->name('regenerate-token');
        Route::post('/{id}/resend-link', [AdminSecurityController::class, 'resendLink'])->name('resend-link');
        Route::put('/{id}/activate-client', [AdminSecurityController::class, 'activateClient'])->name('activate-client');
        Route::put('/{id}/deactivate-client', [AdminSecurityController::class, 'deactivateClient'])->name('deactivate-client');
        Route::put('/{id}/update-duration', [AdminSecurityController::class, 'updateDuration'])->name('update-duration');
    });

    Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
        Route::get('/', [AdminBookingSubscriptionController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminBookingSubscriptionController::class, 'show'])->name('show');
        Route::put('/{id}', [AdminBookingSubscriptionController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminBookingSubscriptionController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/activate', [AdminBookingSubscriptionController::class, 'activate'])->name('activate');
        Route::post('/{id}/deactivate', [AdminBookingSubscriptionController::class, 'deactivate'])->name('deactivate');
        Route::post('/{id}/regenerate-token', [AdminBookingSubscriptionController::class, 'regenerateToken'])->name('regenerate-token');
        Route::post('/{id}/extend', [AdminBookingSubscriptionController::class, 'extend'])->name('extend');
        Route::post('/{id}/cancel', [AdminBookingSubscriptionController::class, 'cancelSubscription'])->name('cancel');
    });

    Route::prefix('vulnerability')->name('vulnerability.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\VulnerabilityController::class, 'index'])->name('index');
        Route::get('/{scan}', [\App\Http\Controllers\Admin\VulnerabilityController::class, 'show'])->name('show');
        Route::post('/run', [\App\Http\Controllers\Admin\VulnerabilityController::class, 'run'])->name('run');
        Route::delete('/{scan}', [\App\Http\Controllers\Admin\VulnerabilityController::class, 'destroy'])->name('destroy');
        Route::post('/clear-old', [\App\Http\Controllers\Admin\VulnerabilityController::class, 'clearOld'])->name('clear-old');
    });

    Route::get('/monitoring', [\App\Http\Controllers\Admin\MonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/{id}', [\App\Http\Controllers\Admin\MonitoringController::class, 'show'])->name('monitoring.show');
    Route::post('/monitoring/clear', [\App\Http\Controllers\Admin\MonitoringController::class, 'clear'])->name('monitoring.clear');
    Route::delete('/monitoring/clear-all', [\App\Http\Controllers\Admin\MonitoringController::class, 'clearAll'])->name('monitoring.clear-all');

    Route::prefix('firewall')->name('firewall.')->group(function () {
        Route::get('/', [FirewallController::class, 'index'])->name('index');
        Route::get('/logs', [FirewallController::class, 'logs'])->name('logs');
        Route::post('/rules', [FirewallController::class, 'store'])->name('rules.store');
        Route::put('/rules/{rule}', [FirewallController::class, 'update'])->name('rules.update');
        Route::delete('/rules/{rule}', [FirewallController::class, 'destroy'])->name('rules.destroy');
        Route::post('/block-ip', [FirewallController::class, 'blockIp'])->name('block-ip');
        Route::delete('/unblock/{rule}', [FirewallController::class, 'unblockIp'])->name('unblock');
        Route::post('/clear-logs', [FirewallController::class, 'clearLogs'])->name('clear-logs');
    });

    Route::prefix('backups')->name('backups.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('index');
        Route::post('/create', [\App\Http\Controllers\Admin\BackupController::class, 'create'])->name('create');
        Route::get('/download/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('download');
        Route::delete('/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'delete'])->name('delete');
        Route::post('/clean', [\App\Http\Controllers\Admin\BackupController::class, 'clean'])->name('clean');
    });

    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SupportTicketController::class, 'index'])->name('index');
        Route::get('/{ticket}/messages', [\App\Http\Controllers\Admin\SupportTicketController::class, 'messages'])->name('messages');
        Route::post('/{id}/resolve', [\App\Http\Controllers\Admin\SupportTicketController::class, 'resolve'])->name('resolve');
        Route::post('/{id}/reply', [\App\Http\Controllers\Admin\SupportTicketController::class, 'reply'])->name('reply');
    });
});

require __DIR__ . '/auth.php';
