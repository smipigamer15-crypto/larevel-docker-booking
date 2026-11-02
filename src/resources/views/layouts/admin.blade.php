<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark" data-admin data-style-page="@yield('style-page')">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Admin') · {{ config('app.name', 'Booking System') }}</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css" rel="stylesheet">
        @vite('resources/css/admin.css')
        @stack('styles')
    </head>
    <body class="admin-shell">
        <div id="admin-app" class="admin-workspace">
            <aside class="admin-sidebar" aria-label="Administration">
                <a class="admin-brand" href="{{ route('admin.dashboard') }}">
                    <span class="admin-brand-icon"><i class="fas fa-layer-group"></i></span>
                    <span>Booking<span class="admin-brand-caption">ADMIN CONSOLE</span></span>
                </a>
                <button class="btn btn-outline-secondary d-lg-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavigation" aria-expanded="false" aria-controls="adminNavigation">
                    <i class="fas fa-bars"></i> Menu
                </button>
                <div class="collapse d-lg-flex admin-sidebar-content" id="adminNavigation">
                    <nav class="admin-navigation" aria-label="Admin sections">
                        @foreach([
                            ['admin.dashboard', 'Dashboard', 'chart-line', 'admin.dashboard'],
                            ['admin.bookings.index', 'Bookings', 'calendar-check', 'admin.bookings.*'],
                            ['admin.calendar', 'Calendar', 'calendar-alt', 'admin.calendar*'],
                            ['admin.subscriptions.index', 'Subscriptions', 'crown', 'admin.subscriptions.*'],
                            ['admin.services.index', 'Services', 'concierge-bell', 'admin.services.*'],
                            ['admin.categories.index', 'Categories', 'tags', 'admin.categories.*'],
                            ['admin.users.index', 'Users', 'users', 'admin.users.*'],
                            ['admin.security.index', 'Security', 'shield-alt', 'admin.security.*'],
                            ['admin.monitoring.index', 'Monitoring', 'eye', 'admin.monitoring.*'],
                            ['admin.firewall.index', 'Firewall', 'shield-virus', 'admin.firewall.*'],
                            ['admin.vulnerability.index', 'Vulnerabilities', 'bug', 'admin.vulnerability.*'],
                            ['admin.backups.index', 'Backups', 'database', 'admin.backups.*'],
                            ['admin.api-tokens.index', 'API access', 'key', 'admin.api-tokens.*'],
                            ['admin.custom-rules.index', 'Custom rules', 'sliders-h', 'admin.custom-rules.*'],
                            ['admin.sms-logs.index', 'SMS logs', 'sms', 'admin.sms-logs.*'],
                            ['admin.support.index', 'Support', 'headset', 'admin.support.*'],
                            ] as [$route, $label, $icon, $pattern])
                            <a href="{{ route($route) }}" class="admin-nav-link {{ request()->routeIs($pattern) ? 'is-active' : '' }}" @if(request()->routeIs($pattern)) aria-current="page" @endif>
                                <i class="fas fa-{{ $icon }}" aria-hidden="true"></i><span>{{ $label }}</span>
                            </a>
                        @endforeach
                    </nav>
                    <div class="admin-account">
                        <span class="admin-account-name">{{ auth()->user()->name }}</span>
                        <a class="admin-nav-link" href="{{ route('home') }}"><i class="fas fa-arrow-left"></i> Back to site</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="admin-nav-link"><i class="fas fa-sign-out-alt"></i> Sign out</button>
                        </form>
                    </div>
                </div>
            </aside>
            <main class="admin-content" id="admin-content">
                @yield('content')
            </main>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @vite('resources/js/app.js')
        @stack('scripts')
        @include('partials.notifications')
    </body>
</html>
