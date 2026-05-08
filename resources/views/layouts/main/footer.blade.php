<footer class="site-footer">
    <nav class="footer-links" aria-label="Footer links">
        <a href="{{ route('policy') }}">Privacy Policy</a>
        <a href="{{ route('terms') }}">Terms & Rules</a>
        <a href="{{ route('about') }}">About Us</a>
    </nav>
    <p class="copyright">&copy; {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.</p>
</footer>
