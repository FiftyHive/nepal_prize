<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Nepal Prize Checker — IRD Taxpayer Lottery')</title>
    <meta name="description"
        content="@yield('meta_description', 'Check whether your IRD taxpayer incentive prize coupon has been allotted. Fast, simple, and reliable coupon checker for the Government of Nepal prize program.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- Open Graph & Twitter Cards --}}
    <meta property="og:title" content="@yield('title', 'Nepal Prize Checker')">
    <meta property="og:description"
        content="@yield('meta_description', 'Check your IRD taxpayer lottery coupon results instantly.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap"
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
            --brand: #e11d48;
            --brand-hover: #be123c;
            --brand-light: #ffe4e6;
            --brand-subtle: #fff1f2;
            --page-bg: #0f172a;
            --header-bg: rgba(15, 23, 42, 0.85);
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --text-light: #94a3b8;
            --border-color: #e2e8f0;
            --border-focus: #e11d48;
            --input-focus-ring: rgba(225, 29, 72, 0.15);
            --success-bg: #f0fdf4;
            --success-border: #86efac;
            --success-text: #15803d;
            --danger-bg: #fff1f2;
            --danger-border: #fecdd3;
            --danger-text: #be123c;
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.12);
            --shadow-xl: 0 20px 40px rgba(0, 0, 0, 0.25);
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--page-bg);
            color: var(--text-main);
            font-size: 15px;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        /* ── HEADER / NAVIGATION ── */
        .site-header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: var(--header-bg);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            width: 100%;
        }

        .header-inner {
            max-width: 1120px;
            margin: 0 auto;
            padding: 0.85rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .site-logo {
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .logo-badge {
            background: linear-gradient(135deg, #e11d48, #f43f5e);
            color: #fff;
            font-weight: 800;
            font-size: 0.85rem;
            padding: 0.35rem 0.55rem;
            border-radius: var(--radius-sm);
            box-shadow: 0 2px 8px rgba(225, 29, 72, 0.4);
            letter-spacing: 0.02em;
        }

        .logo-text {
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: #ffffff;
        }

        .logo-text span {
            color: #fb7185;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-item {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            padding: 0.45rem 0.85rem;
            border-radius: var(--radius-sm);
            transition: all 0.15s ease;
        }

        .nav-item:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.06);
        }

        .nav-item.active {
            color: #ffffff;
            background: rgba(225, 29, 72, 0.18);
            color: #fda4af;
        }

        .nav-cta {
            background: var(--brand);
            color: #ffffff !important;
            font-weight: 700;
            padding: 0.45rem 1rem;
            border-radius: var(--radius-sm);
            transition: all 0.15s ease;
        }

        .nav-cta:hover {
            background: var(--brand-hover) !important;
            transform: translateY(-1px);
        }

        /* ── MAIN CONTENT CONTAINER ── */
        main {
            flex: 1;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2.5rem 1.25rem 3.5rem;
        }

        /* ── HOMEPAGE CARD ── */
        .card {
            background: var(--card-bg);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            padding: 2.5rem 2.25rem 2.25rem;
            width: 100%;
            max-width: 480px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .card-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .card-logo-text {
            font-size: 1.85rem;
            font-weight: 800;
            letter-spacing: -0.035em;
            /* line-height: 1.1; */
            color: var(--text-main);
        }

        .card-logo-text .brand {
            color: var(--brand);
        }

        .card-subtitle {
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ── FORM ELEMENTS ── */
        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #475569;
            margin-bottom: 0.4rem;
        }

        select,
        input[type="text"] {
            width: 100%;
            padding: 0.72rem 0.95rem;
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-md);
            font-family: inherit;
            font-size: 0.95rem;
            color: var(--text-main);
            background: #ffffff;
            transition: all 0.15s ease;
            appearance: none;
        }

        select:focus,
        input[type="text"]:focus {
            outline: none;
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px var(--input-focus-ring);
        }

        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.85rem center;
            padding-right: 2.5rem;
        }

        .form-hint {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-top: 0.35rem;
        }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: var(--radius-md);
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-brand {
            background: var(--brand);
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(225, 29, 72, 0.3);
        }

        .btn-brand:hover {
            background: var(--brand-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(225, 29, 72, 0.4);
        }

        .btn-brand:active {
            transform: translateY(0);
        }

        .btn-brand:disabled {
            background: #fda4af;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #334155;
            font-weight: 600;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        /* ── ALERTS & STATUS ── */
        .alert {
            padding: 0.8rem 1rem;
            border-radius: var(--radius-md);
            font-size: 0.88rem;
            margin-bottom: 1.25rem;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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

        /* ── SPINNER / LOADING ── */
        .loading-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.65rem;
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 0.85rem 0;
        }

        .spinner {
            width: 18px;
            height: 18px;
            border: 2px solid #e2e8f0;
            border-top-color: var(--brand);
            border-radius: 50%;
            animation: spin 0.65s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── RESULT CARDS ── */
        .results-section {
            margin-top: 1.75rem;
            border-top: 1px solid #f1f5f9;
            padding-top: 1.5rem;
        }

        .results-heading {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .result-card {
            display: flex;
            align-items: flex-start;
            gap: 0.85rem;
            padding: 1rem 1.15rem;
            border-radius: var(--radius-md);
            border: 1.5px solid;
            margin-bottom: 0.65rem;
            transition: transform 0.15s ease;
        }

        .result-card.allotted {
            background: var(--success-bg);
            border-color: var(--success-border);
        }

        .result-card.not-allotted {
            background: var(--danger-bg);
            border-color: var(--danger-border);
        }

        .result-icon {
            font-size: 1.3rem;
            flex-shrink: 0;
            line-height: 1.3;
        }

        .result-coupon {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: 0.03em;
        }

        .result-card.allotted .result-coupon {
            color: var(--success-text);
        }

        .result-card.not-allotted .result-coupon {
            color: var(--danger-text);
        }

        .result-label {
            font-size: 0.85rem;
            margin-top: 0.15rem;
        }

        .result-card.allotted .result-label {
            color: #166534;
        }

        .result-card.not-allotted .result-label {
            color: #991b1b;
        }

        .result-prize-tag {
            display: inline-block;
            background: #dcfce7;
            color: #15803d;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            margin-top: 0.35rem;
        }

        /* ── BLOG LAYOUT & CARDS ── */
        .blog-container {
            width: 100%;
            max-width: 980px;
            margin: 0 auto;
        }

        .blog-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .blog-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(225, 29, 72, 0.15);
            color: #fda4af;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            margin-bottom: 0.85rem;
            border: 1px solid rgba(225, 29, 72, 0.3);
        }

        .blog-header h1 {
            color: #ffffff;
            font-size: 2.25rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            line-height: 1.2;
            margin-bottom: 0.75rem;
        }

        .blog-header p {
            color: #94a3b8;
            font-size: 1.05rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Search Bar */
        .search-bar-wrap {
            max-width: 520px;
            margin: 1.75rem auto 0;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 0.85rem 1.15rem 0.85rem 2.75rem;
            background: #1e293b;
            border: 1.5px solid #334155;
            border-radius: 9999px;
            color: #ffffff;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .search-input::placeholder {
            color: #64748b;
        }

        .search-input:focus {
            outline: none;
            border-color: #fb7185;
            background: #0f172a;
            box-shadow: 0 0 0 4px rgba(225, 29, 72, 0.2);
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 1rem;
            pointer-events: none;
        }

        /* Grid */
        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.75rem;
            margin-bottom: 2.5rem;
        }

        .blog-card {
            background: #ffffff;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            display: flex;
            flex-direction: column;
            transition: all 0.25s ease;
            border: 1px solid rgba(255, 255, 255, 0.08);
            text-decoration: none;
            color: inherit;
        }

        .blog-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.35);
        }

        .blog-card-img-wrap {
            height: 190px;
            width: 100%;
            position: relative;
            background: linear-gradient(135deg, #1e293b, #334155);
            overflow: hidden;
        }

        .blog-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.35s ease;
        }

        .blog-card:hover .blog-card-img {
            transform: scale(1.04);
        }

        .blog-card-img-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e1b4b, #312e81);
            color: #c7d2fe;
            font-size: 2.5rem;
        }

        .blog-card-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .blog-card-meta {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 0.75rem;
        }

        .blog-pill {
            background: var(--brand-subtle);
            color: var(--brand);
            padding: 0.2rem 0.55rem;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .blog-card-title {
            font-size: 1.2rem;
            font-weight: 700;
            line-height: 1.35;
            color: var(--text-main);
            margin-bottom: 0.6rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            transition: color 0.15s ease;
        }

        .blog-card:hover .blog-card-title {
            color: var(--brand);
        }

        .blog-card-excerpt {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.55;
            margin-bottom: 1.25rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }

        .blog-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid #f1f5f9;
            padding-top: 1rem;
            margin-top: auto;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--brand);
        }

        .blog-card-arrow {
            transition: transform 0.15s ease;
        }

        .blog-card:hover .blog-card-arrow {
            transform: translateX(4px);
        }

        /* ── SINGLE ARTICLE (SHOW) ── */
        .article-wrap {
            width: 100%;
            max-width: 820px;
            margin: 0 auto;
        }

        .article-back-nav {
            margin-bottom: 1.5rem;
        }

        .article-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            padding: 0.4rem 0.85rem;
            border-radius: var(--radius-sm);
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.15s ease;
        }

        .article-back-btn:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(-2px);
        }

        .article-card {
            background: #ffffff;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            padding: 2.75rem 2.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .article-header {
            margin-bottom: 2rem;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 1.75rem;
        }

        .article-title {
            font-size: 2.15rem;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.03em;
            color: var(--text-main);
            margin: 0.85rem 0 1rem;
        }

        .article-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            color: var(--text-muted);
            font-size: 0.88rem;
        }

        .article-meta-details {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .article-featured-image {
            width: 100%;
            border-radius: var(--radius-md);
            margin: 1.5rem 0 2.25rem;
            max-height: 440px;
            object-fit: cover;
            box-shadow: var(--shadow-md);
        }

        /* ── PROSE ARTICLE TYPOGRAPHY ── */
        .article-content {
            font-size: 1.05rem;
            line-height: 1.8;
            color: #334155;
        }

        .article-content p {
            margin-bottom: 1.35rem;
        }

        .article-content h2 {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: var(--text-main);
            margin: 2.25rem 0 0.85rem;
            padding-bottom: 0.4rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .article-content h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 1.75rem 0 0.65rem;
        }

        .article-content ul,
        .article-content ol {
            margin: 0 0 1.35rem 1.5rem;
            padding-left: 0.5rem;
        }

        .article-content li {
            margin-bottom: 0.5rem;
        }

        .article-content blockquote {
            border-left: 4px solid var(--brand);
            background: var(--brand-subtle);
            padding: 1rem 1.25rem;
            border-radius: 0 var(--radius-md) var(--radius-md) 0;
            margin: 1.75rem 0;
            font-style: italic;
            color: #475569;
        }

        .article-content a {
            color: var(--brand);
            text-decoration: underline;
            text-underline-offset: 3px;
            font-weight: 600;
        }

        .article-content a:hover {
            color: var(--brand-hover);
        }

        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: var(--radius-md);
            margin: 1.5rem 0;
        }

        .article-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.75rem 0;
            font-size: 0.95rem;
        }

        .article-content th,
        .article-content td {
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            text-align: left;
        }

        .article-content th {
            background: #f8fafc;
            font-weight: 700;
            color: var(--text-main);
        }

        /* ── ARTICLE CTA BANNER ── */
        .article-cta-box {
            margin-top: 3rem;
            background: linear-gradient(135deg, #1e1b4b, #312e81);
            border-radius: var(--radius-lg);
            padding: 2rem;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .article-cta-box h3 {
            font-size: 1.3rem;
            font-weight: 800;
            margin-bottom: 0.35rem;
        }

        .article-cta-box p {
            color: #cbd5e1;
            font-size: 0.92rem;
            margin: 0;
        }

        .article-cta-btn {
            background: var(--brand);
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-md);
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.15s ease;
            box-shadow: 0 4px 12px rgba(225, 29, 72, 0.4);
        }

        .article-cta-btn:hover {
            background: var(--brand-hover);
            transform: translateY(-2px);
        }

        /* ── SOCIAL SHARE BAR ── */
        .share-bar {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f1f5f9;
            flex-wrap: wrap;
        }

        .share-label {
            font-size: 0.85rem;
            font-weight: 700;
            color: #64748b;
            margin-right: 0.4rem;
        }

        .share-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 0.85rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-sm);
            color: #475569;
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .share-btn:hover {
            background: #f1f5f9;
            color: var(--text-main);
            border-color: #cbd5e1;
        }

        /* ── FOOTER ── */
        footer {
            width: 100%;
            background: #090d16;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            color: #64748b;
            padding: 2.5rem 1.25rem 2rem;
            text-align: center;
        }

        .footer-inner {
            max-width: 760px;
            margin: 0 auto;
        }

        .footer-nav {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 1rem;
        }

        .footer-nav a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            transition: color 0.15s ease;
        }

        .footer-nav a:hover {
            color: #ffffff;
        }

        .footer-disclaimer {
            font-size: 0.78rem;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 0.75rem;
        }

        .footer-disclaimer a {
            color: #94a3b8;
            text-decoration: underline;
        }

        .footer-copy {
            font-size: 0.75rem;
            color: #475569;
        }

        /* ── RESPONSIVENESS ── */
        @media (max-width: 640px) {
            main {
                padding: 1.5rem 1rem 2.5rem;
            }

            .card,
            .article-card {
                padding: 1.75rem 1.25rem 1.5rem;
                border-radius: var(--radius-lg);
            }

            .blog-header h1 {
                font-size: 1.75rem;
            }

            .article-title {
                font-size: 1.65rem;
            }

            .blog-grid {
                grid-template-columns: 1fr;
            }

            .header-inner {
                padding: 0.75rem 1rem;
            }

            .article-cta-box {
                flex-direction: column;
                align-items: flex-start;
            }

            .article-cta-btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    {{-- Top Navigation Header --}}
    <header class="site-header" role="banner">
        <div class="header-inner">
            {{-- <a href="{{ route('home') }}" class="site-logo" aria-label="Nepal Prize Checker Home">
                <div class="logo-badge">IRD</div>
                <div class="logo-text">Nepal<span>Prize</span>Checker</div>
            </a> --}}

            <nav class="nav-links" aria-label="Main Navigation">
                <a href="{{ route('home') }}" @class(['nav-item', 'active' => request()->routeIs('home')])>Home</a>
                <a href="{{ route('blog.index') }}" @class(['nav-item', 'active' => request()->routeIs('blog.*')])>Blog</a>
                @auth
                    <a href="{{ route('filament.admin.pages.dashboard') }}" class="nav-item">Admin</a>
                @endauth
                {{-- <a href="{{ route('home') }}" class="nav-cta">Check Coupon</a> --}}
            </nav>
        </div>
    </header>

    <main role="main">
        @yield('content')
    </main>

    <footer>
        <div class="footer-inner">
            <div class="footer-nav">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('blog.index') }}">Blog &amp; Guides</a>
                <a href="https://prize.ird.gov.np/" target="_blank" rel="noopener noreferrer">Official IRD Portal</a>
                @auth
                    <a href="{{ route('filament.admin.pages.dashboard') }}">Admin Dashboard</a>
                @endauth
            </div>
            <p class="footer-disclaimer">
                This website provides a fast and convenient interface to check taxpayer incentive prize coupon information.
                Official draws and claims are conducted by the <a href="https://prize.ird.gov.np/" target="_blank" rel="noopener noreferrer">Inland Revenue Department (IRD)</a>, Government of Nepal.
                This is an independent community tool and not an official government portal.
            </p>
            <p class="footer-copy">© {{ date('Y') }} Nepal Prize Checker. All rights reserved.</p>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>