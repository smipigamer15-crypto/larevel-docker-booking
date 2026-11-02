@vite('resources/css/navbar.css')
<nav class="navbar navbar-expand-xxl navbar-custom sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="{{ route('home') }}">
            <i class="fas fa-calendar-check text-primary me-2"></i>Booking
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto gap-3">
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="{{ route('bookings.create') }}">
                        {{ __('Schedule') }}
                    </a>
                </li>

                <li class="nav-item dropdown mega-dropdown">
                    <button type="button" class="nav-link dropdown-toggle fw-semibold" id="functionsDropdown" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                        {{ __('Features') }}
                    </button>
                    <div class="dropdown-menu mega-menu w-100 border-0 shadow-lg rounded-0">
                        <div class="container py-4">
                            <div class="row g-4">
                                <div class="col-md-3">
                                    <h6 class="text-uppercase fw-bold text-primary mb-3">{{ __('FEATURES') }}</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('security') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-shield-alt text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Security') }}</div>
                                                    <small class="text-secondary">{{ __('Your data is safe and secure') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('features.show', 'voice-booking') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-microphone text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('AI Voice Booking') }}</div>
                                                    <small class="text-secondary">{{ __('Turn spoken or typed requests into a booking draft.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('features.show', 'social-media') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-share-alt text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Social Media & Ads') }}</div>
                                                    <small class="text-secondary">{{ __('Create booking links for your social profiles and ads.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('features.show', 'marketing') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-chart-line text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Marketing Tools') }}</div>
                                                    <small class="text-secondary">{{ __('Build campaign links with source and campaign tracking tags.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mt-2">
                                            <a class="dropdown-item fw-semibold p-2 rounded-3" href="{{ route('features') }}" data-view-style="partials-navbar-4">
                                                <span>→</span> {{ __('All Features') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="col-md-3">
                                    <h6 class="text-uppercase fw-bold text-primary mb-3">{{ __('INTEGRATIONS') }}</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('features.show', 'calendar-sync') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-calendar-alt text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Calendar Sync') }}</div>
                                                    <small class="text-secondary">{{ __('Subscribe to your booking calendar or download an ICS file.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('features.show', 'payments') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-credit-card text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Payment Processing') }}</div>
                                                    <small class="text-secondary">{{ __('Manage your Stripe plans and payment status.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('features.show', 'facebook-instagram') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fab fa-facebook text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Facebook & Instagram Booking') }}</div>
                                                    <small class="text-secondary">{{ __('Add your booking link to a Facebook page or Instagram bio.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('features.show', 'zapier') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-bolt text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Zapier') }}</div>
                                                    <small class="text-secondary">{{ __('Send booking events to your Zapier Catch Hook.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mt-2">
                                            <a class="dropdown-item fw-semibold p-2 rounded-3" href="{{ route('features', ['category' => 'integrations']) }}" data-view-style="partials-navbar-9">
                                                <span>→</span> {{ __('All Integrations') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="col-md-3">
                                    <h6 class="text-uppercase fw-bold text-primary mb-3">{{ __('SETTINGS') }}</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('features.show', 'apps') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-mobile-alt text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Apps') }}</div>
                                                    <small class="text-secondary">{{ __('Install the booking website as an app on your device.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('features.show', 'templates') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-palette text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Site Templates') }}</div>
                                                    <small class="text-secondary">{{ __('Publish a booking page using a layout you choose.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('features.show', 'customization') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-paint-brush text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Full Customization') }}</div>
                                                    <small class="text-secondary">{{ __('Customize your booking page name, colors and welcome message.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('features.show', 'emails') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-envelope text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Customizable Emails') }}</div>
                                                    <small class="text-secondary">{{ __('Personalize the confirmation emails for your bookings.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mt-2">
                                            <a class="dropdown-item fw-semibold p-2 rounded-3" href="{{ route('features', ['category' => 'settings']) }}" data-view-style="partials-navbar-14">
                                                <span>→</span> {{ __('About Settings') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="col-md-3">
                                    <h6 class="text-uppercase fw-bold text-primary mb-3">{{ __('EXTRA') }}</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('features.show', 'popular') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-star text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Popular') }}</div>
                                                    <small class="text-secondary">{{ __('Explore the most booked services and customer favorites.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('features.show', 'premium') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-gem text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Premium') }}</div>
                                                    <small class="text-secondary">{{ __('Compare plans and review your current membership.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('features.show', 'new') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-rocket text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('New') }}</div>
                                                    <small class="text-secondary">{{ __('Explore the latest tools available on this website.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('features.show', 'help') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-question-circle text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Help') }}</div>
                                                    <small class="text-secondary">{{ __('Find answers and get help with your bookings.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mt-2">
                                            <a class="dropdown-item fw-semibold p-2 rounded-3" href="{{ route('features', ['category' => 'extra']) }}" data-view-style="partials-navbar-19">
                                                <span>→</span> {{ __('All Extra Features') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="{{ route('pricing') }}">
                        {{ __('Pricing') }}
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="{{ route('enterprise') }}">
                        {{ __('Enterprise') }}
                    </a>
                </li>

                <li class="nav-item dropdown mega-dropdown">
                    <button type="button" class="nav-link dropdown-toggle fw-semibold" id="resourcesDropdown" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                        {{ __('Resources') }}
                    </button>
                    <div class="dropdown-menu mega-menu w-100 border-0 shadow-lg rounded-0">
                        <div class="container py-4">
                            <div class="row g-4">
                                <div class="col-md-3">
                                    <h6 class="text-uppercase fw-bold text-primary mb-3">{{ __('GET HELP') }}</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('resources.show', 'how-it-works') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-play-circle text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('How It Works') }}</div>
                                                    <small class="text-secondary">{{ __('Follow the steps from selecting a service to managing a booking.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('resources.show', 'help-center') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-life-ring text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Help Center') }}</div>
                                                    <small class="text-secondary">{{ __('Search answers about accounts, bookings and subscriptions.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('resources.show', 'live-chat') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-comment-dots text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Live Chat Support') }}</div>
                                                    <small class="text-secondary">{{ __('Start a support conversation and read replies on the website.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="col-md-3">
                                    <h6 class="text-uppercase fw-bold text-primary mb-3">{{ __('LEARN MORE') }}</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('resources.show', 'customer-stories') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-users text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Customer Stories') }}</div>
                                                    <small class="text-secondary">{{ __('Read approved feedback from real customers.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('resources.show', 'analytics') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-chart-bar text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Analytics') }}</div>
                                                    <small class="text-secondary">{{ __('See booking trends for your own account.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('resources.show', 'admin-demo') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-desktop text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Admin Demo') }}</div>
                                                    <small class="text-secondary">{{ __('Explore an interactive, read-only administration preview.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="col-md-3">
                                    <h6 class="text-uppercase fw-bold text-primary mb-3">{{ __('LEARNING') }}</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('resources.show', 'youtube') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fab fa-youtube text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('YouTube Channel') }}</div>
                                                    <small class="text-secondary">{{ __('Follow guided lessons and find related videos on YouTube.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('resources.show', 'blog') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-newspaper text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Blog') }}</div>
                                                    <small class="text-secondary">{{ __('Practical guides to planning, sharing and managing bookings.') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="col-md-3">
                                    <h6 class="text-uppercase fw-bold text-primary mb-3">{{ __('COMMUNITY') }}</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('about') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-info-circle text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('About Us') }}</div>
                                                    <small class="text-secondary">{{ __('Learn more about our team') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('partners') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-handshake text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Partners') }}</div>
                                                    <small class="text-secondary">{{ __('Become our partner') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a class="dropdown-item d-flex align-items-start p-2 rounded-3" href="{{ route('affiliate') }}">
                                                <div class="me-3 mt-1">
                                                    <i class="fas fa-coins text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ __('Affiliate Program') }}</div>
                                                    <small class="text-secondary">{{ __('Earn by referring clients') }}</small>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>

            <ul class="navbar-nav align-items-center">
                <li class="nav-item language-nav-item me-2">
                    @include('partials.language-switcher', ['placement' => 'nav'])
                </li>
                @auth
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-primary btn-sm rounded-pill px-3" href="{{ route('admin.dashboard') }}" target="_blank" rel="noopener noreferrer">
                                <i class="fas fa-crown me-1"></i>{{ __('Admin') }}
                            </a>
                        </li>
                    @endif
                @endauth

                @guest
                    <li class="nav-item me-2">
                        <a class="nav-link fw-semibold" href="{{ route('login') }}">
                            {{ __('Login') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary rounded-pill px-4" href="{{ route('register') }}">
                            {{ __('Sign Up') }}
                        </a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown" data-view-style="partials-navbar-28">
                            @if(auth()->user()->avatar && file_exists(public_path('storage/' . auth()->user()->avatar)))
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                    class="rounded-circle"
                                    width="32"
                                    height="32"
                                    alt="{{ auth()->user()->name }}"
                                    data-view-style="partials-navbar-29">
                            @else
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                        data-view-style="partials-navbar-30">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                            @endif
                            <span class="fw-semibold">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 py-2">
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user-circle me-2 text-primary"></i>{{ __('My Profile') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('dashboard') }}">
                                    <i class="fas fa-chart-pie me-2 text-primary"></i>{{ __('Dashboard') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('bookings.index') }}">
                                    <i class="fas fa-calendar-check me-2 text-primary"></i>{{ __('My Bookings') }}
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>{{ __('Logout') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
