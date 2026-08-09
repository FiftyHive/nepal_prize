<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Nepal Prize Checker') — IRD Lottery Coupon Check</title>
    <meta name="description"
        content="@yield('meta_description', 'Check whether your IRD taxpayer incentive prize coupon has been allotted. Fast, simple, and reliable coupon checker for the Government of Nepal prize program.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- Open Graph --}}
    <meta property="og:title" content="@yield('title', 'Nepal Prize Checker')">
    <meta property="og:description"
        content="@yield('meta_description', 'Check your IRD lottery coupon result instantly.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    {{-- reCAPTCHA --}}
    @if(config('services.recaptcha.site_key'))
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --brand: #dc2626;
            --brand-dark: #b91c1c;
            --brand-light: #fee2e2;
            --page-bg: #271E45;
            /* deep indigo — MeroShare vibe */
            --card-bg: #ffffff;
            --card-border: #e5e7eb;
            --text: #111827;
            --text-muted: #6b7280;
            --text-subtle: #9ca3af;
            --border: #d1d5db;
            --input-focus: rgba(220, 38, 38, .18);
            --success-bg: #f0fdf4;
            --success-border: #16a34a;
            --success-text: #15803d;
            --danger-bg: #fff1f2;
            --danger-border: #dc2626;
            --danger-text: #b91c1c;
            --radius: 8px;
            --shadow: 0 4px 24px rgba(0, 0, 0, .18);
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--page-bg);
            color: var(--text);
            font-size: 15px;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* ── LOGO ── */
        .logo {
            text-decoration: none;
            display: inline-block;
            line-height: 1;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 900;
            letter-spacing: -.03em;
            color: var(--text);
        }

        .logo-text .brand {
            color: var(--brand);
        }

        /* ── NAV (top bar above card for blog link) ── */
        .top-nav {
            width: 100%;
            display: flex;
            justify-content: flex-end;
            padding: 1rem 1.5rem 0;
            max-width: 480px;
        }

        .top-nav a {
            color: rgba(255, 255, 255, .65);
            text-decoration: none;
            font-size: .85rem;
            font-weight: 500;
            margin-left: 1.25rem;
            transition: color .15s;
        }

        .top-nav a:hover {
            color: #fff;
        }

        /* ── MAIN CARD ── */
        main {
            flex: 1;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
        }

        .card {
            background: var(--card-bg);
            border-radius: 14px;
            box-shadow: var(--shadow);
            padding: 2.5rem 2.25rem 2rem;
            width: 100%;
            max-width: 460px;
        }

        /* ── CARD HEADER ── */
        .card-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .card-logo-text {
            font-size: 2rem;
            font-weight: 900;
            letter-spacing: -.04em;
            line-height: 1;
            color: var(--text);
        }

        .card-logo-text .brand {
            color: var(--brand);
        }

        .card-subtitle {
            margin-top: .45rem;
            font-size: .9rem;
            color: var(--text-muted);
            font-weight: 500;
            letter-spacing: .005em;
        }

        /* ── FORM ── */
        .form-group {
            margin-bottom: 1.1rem;
        }

        label {
            display: block;
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: .35rem;
        }

        select,
        input[type="text"] {
            width: 100%;
            padding: .65rem .85rem;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            font-family: inherit;
            font-size: .95rem;
            color: var(--text);
            background: #fff;
            transition: border-color .15s, box-shadow .15s;
            appearance: none;
        }

        select:focus,
        input:focus {
            outline: none;
            border-color: var(--brand);
            box-shadow: 0 0 0 3px var(--input-focus);
        }

        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right .75rem center;
            padding-right: 2.5rem;
        }

        .form-hint {
            font-size: .78rem;
            color: var(--text-subtle);
            margin-top: .28rem;
        }

        /* ── BUTTON ── */
        .btn {
            display: block;
            width: 100%;
            padding: .75rem 1.5rem;
            border: none;
            border-radius: var(--radius);
            font-family: inherit;
            font-size: .95rem;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            transition: background .15s, transform .1s;
        }

        .btn-brand {
            background: var(--brand);
            color: #fff;
            letter-spacing: .02em;
        }

        .btn-brand:hover {
            background: var(--brand-dark);
        }

        .btn-brand:active {
            transform: scale(.98);
        }

        .btn-brand:disabled {
            background: #f87171;
            cursor: not-allowed;
        }

        /* ── ALERT ── */
        .alert {
            padding: .7rem .9rem;
            border-radius: var(--radius);
            font-size: .85rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
        }

        .alert-danger {
            background: var(--danger-bg);
            color: var(--danger-text);
            border-color: var(--danger-border);
        }

        .alert-success {
            background: var(--success-bg);
            color: var(--success-text);
            border-color: var(--success-border);
        }

        /* ── LOADING ── */
        .loading-indicator {
            display: flex;
            align-items: center;
            gap: .55rem;
            color: var(--text-muted);
            font-size: .88rem;
            padding: .6rem 0;
        }

        .spinner {
            width: 17px;
            height: 17px;
            border: 2px solid #e5e7eb;
            border-top-color: var(--brand);
            border-radius: 50%;
            animation: spin .65s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── RESULT CARDS ── */
        .results-section {
            margin-top: 1.5rem;
        }

        .results-heading {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: .65rem;
        }

        .result-card {
            display: flex;
            align-items: flex-start;
            gap: .8rem;
            padding: .9rem 1rem;
            border-radius: var(--radius);
            border: 1.5px solid;
            margin-bottom: .55rem;
        }

        .result-card.allotted {
            background: var(--success-bg);
            border-color: #bbf7d0;
        }

        .result-card.not-allotted {
            background: var(--danger-bg);
            border-color: #fecaca;
        }

        .result-icon {
            font-size: 1.25rem;
            flex-shrink: 0;
            line-height: 1.45;
        }

        .result-coupon {
            font-size: .97rem;
            font-weight: 700;
            letter-spacing: .04em;
            font-variant-numeric: tabular-nums;
        }

        .result-card.allotted .result-coupon {
            color: var(--success-text);
        }

        .result-card.not-allotted .result-coupon {
            color: var(--danger-text);
        }

        .result-label {
            font-size: .83rem;
            margin-top: .1rem;
        }

        .result-card.allotted .result-label {
            color: #166534;
        }

        .result-card.not-allotted .result-label {
            color: #991b1b;
        }

        /* ── FOOTER ── */
        footer {
            width: 100%;
            max-width: 460px;
            text-align: center;
            padding: 1.25rem 1.25rem 1.75rem;
        }

        .footer-links a {
            color: rgba(255, 255, 255, .45);
            text-decoration: none;
            font-size: .8rem;
            margin: 0 .5rem;
            transition: color .15s;
        }

        .footer-links a:hover {
            color: rgba(255, 255, 255, .8);
        }

        .footer-disclaimer {
            font-size: .72rem;
            color: rgba(255, 255, 255, .35);
            margin-top: .5rem;
            line-height: 1.5;
        }

        .footer-disclaimer a {
            color: rgba(255, 255, 255, .5);
        }

        .footer-copy {
            font-size: .72rem;
            color: rgba(255, 255, 255, .3);
            margin-top: .35rem;
        }

        /* ── BLOG STYLES ── */
        .page-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 1.4rem;
        }

        .post-list {
            list-style: none;
        }

        .post-item {
            border-bottom: 1px solid var(--border);
            padding: 1.2rem 0;
        }

        .post-item:last-child {
            border-bottom: none;
        }

        .post-title a {
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--text);
            text-decoration: none;
        }

        .post-title a:hover {
            color: var(--brand);
        }

        .post-meta {
            font-size: .78rem;
            color: var(--text-muted);
            margin-top: .2rem;
        }

        .post-excerpt {
            margin-top: .45rem;
            font-size: .88rem;
            color: var(--text-muted);
        }

        .read-more {
            display: inline-block;
            margin-top: .55rem;
            font-size: .83rem;
            font-weight: 600;
            color: var(--brand);
            text-decoration: none;
        }

        .read-more:hover {
            text-decoration: underline;
        }

        .article-title {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .article-meta {
            font-size: .8rem;
            color: var(--text-muted);
            margin-top: .35rem;
        }

        .article-featured-img {
            width: 100%;
            border-radius: var(--radius);
            margin: 1.25rem 0;
            object-fit: cover;
            max-height: 360px;
        }

        .article-content {
            line-height: 1.75;
        }

        .article-content h2,
        .article-content h3 {
            margin: 1.2rem 0 .45rem;
            font-weight: 600;
        }

        .article-content p {
            margin-bottom: .9rem;
        }

        .article-content a {
            color: var(--brand);
        }

        /* Blog card style (white card on dark bg) */
        .white-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: var(--shadow);
            padding: 2rem 2.25rem;
            width: 100%;
            max-width: 700px;
        }

        @media (max-width: 540px) {

            .card,
            .white-card {
                padding: 1.75rem 1.25rem 1.5rem;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    <div class="top-nav" aria-label="Site navigation">
        <a href="{{ route('blog.index') }}" @class(['active' => request()->routeIs('blog.*')])>Blog</a>
        @auth <a href="{{ route('filament.admin.pages.dashboard') }}">Admin</a> @endauth
    </div>

    <main>
        @yield('content')
    </main>

    <footer>
        <div class="footer-links">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('blog.index') }}">Blog</a>
        </div>
        <p class="footer-disclaimer">
            This website provides a convenient way to check taxpayer incentive prize coupon information.
            For official information, please refer to the
            <a href="https://prize.ird.gov.np/" target="_blank" rel="noopener noreferrer">Inland Revenue Department</a>.
            This is not an official Government of Nepal website.
        </p>
        <p class="footer-copy">© {{ date('Y') }} Nepal Prize Checker</p>
    </footer>

    @stack('scripts')
</body>

</html>