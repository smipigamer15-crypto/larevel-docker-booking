@vite('resources/css/footer.css')
<footer class="footer py-5">
    <div class="container">
        <div class="row g-4">

            <div class="col-lg-4 col-md-6">
                <div class="footer-brand">
                    <a class="navbar-brand fw-bold fs-4" href="{{ route('home') }}">
                        <i class="fas fa-calendar-check text-primary me-2"></i>Booking
                    </a>
                    <p class="text-secondary mt-3" data-view-style="partials-footer-1">
                        {{ __('Simple, fast and convenient booking system for your services. Choose a service, pick a time, and confirm.') }}
                    </p>
                    <div class="footer-socials d-flex gap-3 mt-4">
                        <a href="#" class="social-link" aria-label="{{ __('Facebook') }}">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="{{ __('Twitter') }}">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="{{ __('Instagram') }}">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="{{ __('LinkedIn') }}">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="{{ __('YouTube') }}">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <h6 class="footer-title fw-bold mb-3">{{ __('Quick Links') }}</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('services.index') }}">{{ __('Services') }}</a></li>
                    <li><a href="{{ route('bookings.create') }}">{{ __('Book Now') }}</a></li>
                    <li><a href="{{ route('pricing') }}">{{ __('Pricing') }}</a></li>
                    <li><a href="{{ route('enterprise') }}">{{ __('Enterprise') }}</a></li>
                    <li><a href="{{ route('features') }}">{{ __('Features') }}</a></li>
                    <li><a href="{{ route('security') }}">{{ __('Security') }}</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6">
                <h6 class="footer-title fw-bold mb-3">{{ __('Account') }}</h6>
                <ul class="list-unstyled footer-links">
                    @auth
                        <li><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li><a href="{{ route('bookings.index') }}">{{ __('My Bookings') }}</a></li>
                        <li><a href="{{ route('profile.edit') }}">{{ __('My Profile') }}</a></li>
                        @if(auth()->user()->isAdmin())
                            <li><a href="{{ route('admin.dashboard') }}">{{ __('Admin Panel') }}</a></li>
                        @endif
                    @else
                        <li><a href="{{ route('login') }}">{{ __('Login') }}</a></li>
                        <li><a href="{{ route('register') }}">{{ __('Sign Up') }}</a></li>
                        <li><a href="{{ route('pricing') }}">{{ __('Choose a Plan') }}</a></li>
                    @endauth
                </ul>
            </div>

            <div class="col-lg-4 col-md-6">
                <h6 class="footer-title fw-bold mb-3">{{ __('Stay Updated') }}</h6>
                <p class="text-secondary" data-view-style="partials-footer-2">
                    {{ __('Subscribe to our newsletter for updates and tips.') }}
                </p>
                <form class="footer-newsletter d-flex gap-2" action="#" method="POST">
                    @csrf
                    <input type="email" class="form-control rounded-pill" placeholder="{{ __('Enter your email') }}" required data-view-style="partials-footer-3">
                    <button type="submit" class="btn btn-primary rounded-pill px-4" data-view-style="partials-footer-4">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
                <div class="footer-contact mt-4">
                    <p class="text-secondary mb-1" data-view-style="partials-footer-5">
                        <i class="fas fa-envelope me-2 text-primary"></i>{{ __('support') }}@booking{{ __('.com') }}
                    </p>
                    <p class="text-secondary mb-0" data-view-style="partials-footer-6">
                        <i class="fas fa-phone me-2 text-primary"></i>+1 (555) 123-4567
                    </p>
                </div>
            </div>
        </div>

        <div class="footer-bottom mt-5 pt-4" data-view-style="partials-footer-7">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-secondary mb-0" data-view-style="partials-footer-8">
                        © {{ date('Y') }} {{ __('Booking System. All rights reserved.') }}
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="{{ route('pricing') }}" class="text-secondary me-3" data-view-style="partials-footer-9">{{ __('Pricing') }}</a>
                    <a href="{{ route('features') }}" class="text-secondary me-3" data-view-style="partials-footer-10">{{ __('Features') }}</a>
                    <a href="{{ route('security') }}" class="text-secondary" data-view-style="partials-footer-11">{{ __('Security') }}</a>
                </div>
            </div>
        </div>
    </div>
</footer>
