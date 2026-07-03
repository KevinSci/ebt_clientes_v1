@auth
    <div class="fixed-top bg-white border-bottom d-flex d-lg-none align-items-center px-3 shadow-sm ebt-mobile-navbar" style="height: 56px; z-index: 1030;">
        <a href="{{ route('login') }}" class="d-flex align-items-center">
            <img src="{{ asset('img/logo.svg') }}" alt="EBT Logo" style="height: 36px;">
        </a>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navbar = document.querySelector('.ebt-mobile-navbar');
            if (!navbar) return;

            let lastScrollY = window.scrollY;

            window.addEventListener('scroll', () => {
                const currentScrollY = window.scrollY;

                // Evitar trigger en rebotes (como en iOS)
                if (currentScrollY < 0) return;

                if (currentScrollY > lastScrollY && currentScrollY > 56) {
                    // Scroll hacia abajo - ocultar navbar
                    navbar.classList.add('nav-hidden');
                } else {
                    // Scroll hacia arriba - mostrar navbar
                    navbar.classList.remove('nav-hidden');
                }
                lastScrollY = currentScrollY;
            });
        });
    </script>
    @endpush
@endauth
