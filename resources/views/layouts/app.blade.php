<!doctype html>
<html lang="{{ app()->getLocale() }}" data-bs-theme="{{ auth()->user()->theme ?? 'light' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'IT Support Management')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --page-bg: #f1f4f8;
            --ink: #202a36;
            --muted: #778392;
            --brand: #0862df;
            --line: #e2e8ef;
        }
        html[data-bs-theme="dark"] {
            color-scheme: dark;
            --page-bg: #111827;
            --ink: #e5e7eb;
            --muted: #9ca3af;
            --line: #374151;
            --surface: #1f2937;
        }
        body { background:var(--page-bg); color:var(--ink); }
        html[lang="km"] body,
        html[lang="km"] button,
        html[lang="km"] input,
        html[lang="km"] select,
        html[lang="km"] textarea {
            font-family:'Noto Sans Khmer','Khmer OS System','Leelawadee UI',sans-serif;
            font-size:1.02rem;
        }
        .sidebar { width:250px; min-height:100vh; flex:0 0 250px; background:#fff; border-right:1px solid var(--line); }
        .sidebar-brand { color:#263343; }
        .sidebar-brand-icon { display:inline-grid; width:36px; height:36px; place-items:center; border-radius:10px; background:#e6f1ff; color:var(--brand); }
        .sidebar .nav-link { color:#586779; border-radius:9px; margin:3px 0; padding:10px 12px; font-size:.92rem; font-weight:600; }
        .sidebar .nav-link:hover { background:#f2f6fb; color:var(--brand); }
        .sidebar .nav-link.active { background:var(--brand); color:#fff; box-shadow:0 5px 12px #0862df2b; }
        .sidebar .nav-link i { width:20px; display:inline-block; text-align:center; }
        .sidebar-section { color:#99a4b1; letter-spacing:.08em; }
        .main { min-width:0; }
        .topbar { margin:16px 16px 0; min-height:106px; border-radius:14px; background:linear-gradient(112deg,#172433 0%,#202e42 55%,#3937bd 100%); color:#fff; }
        .topbar .navbar-brand { color:#fff; }
        .topbar-subtitle { color:#b8c4d2; font-size:.9rem; }
        .topbar .btn-outline-light { --bs-btn-hover-color:#172433; }
        .profile-button { background:#fff; color:#263343; border:0; border-radius:30px; padding:.45rem .9rem; }
        .profile-button:hover { background:#edf3fa; color:#263343; }
        .stat-card,.table-card,.form-card { border:1px solid #e8edf3; border-radius:12px; }
        .stat-card { box-shadow:0 2px 4px #1c2b3a0a !important; }
        .stat-card .card-body { padding:1.15rem; }
        .stat-icon { width:38px; height:38px; display:grid; place-items:center; border-radius:9px; font-size:1.15rem; }
        .table-card { overflow:hidden; }
        .table-card .card-header { border-bottom:1px solid var(--line); }
        .table > :not(caption) > * > * { padding:.85rem 1rem; }
        .page-loader {
            position:fixed;
            inset:0;
            z-index:2000;
            display:grid;
            place-items:center;
            background:rgb(255 255 255 / 92%);
            backdrop-filter:blur(2px);
            opacity:0;
            visibility:hidden;
            transition:opacity .18s ease, visibility .18s ease;
        }
        .page-loader.is-active { opacity:1; visibility:visible; }
        .page-loader-spinner {
            position:relative;
            width:72px;
            aspect-ratio:1;
            border:2px solid #e8edf3;
            border-top-color:#263343;
            border-bottom-color:#1684f8;
            border-radius:50%;
            animation:page-loader-spin .9s linear infinite;
        }
        .page-loader-spinner::before {
            position:absolute;
            inset:12px;
            border:2px solid #e8edf3;
            border-bottom-color:#cbd5e1;
            border-radius:50%;
            content:"";
        }
        .page-loader-spinner::after {
            position:absolute;
            top:50%;
            left:50%;
            width:12px;
            aspect-ratio:1;
            border-radius:50%;
            background:#1684f8;
            box-shadow:0 0 12px rgb(22 132 248 / 40%);
            content:"";
            transform:translate(-50%, -50%);
        }
        html[data-bs-theme="dark"] body { background:var(--page-bg); color:var(--ink); }
        html[data-bs-theme="dark"] .sidebar { background:var(--surface); }
        html[data-bs-theme="dark"] .sidebar-brand { color:var(--ink); }
        html[data-bs-theme="dark"] .sidebar-brand-icon { background:#1e3a5f; }
        html[data-bs-theme="dark"] .sidebar .nav-link { color:#cbd5e1; }
        html[data-bs-theme="dark"] .sidebar .nav-link:hover { background:#374151; color:#93c5fd; }
        html[data-bs-theme="dark"] .sidebar .nav-link.active { background:var(--brand); color:#fff; }
        html[data-bs-theme="dark"] .sidebar-section { color:#9ca3af; }
        html[data-bs-theme="dark"] .stat-card,
        html[data-bs-theme="dark"] .table-card,
        html[data-bs-theme="dark"] .form-card { border-color:var(--line); }
        html[data-bs-theme="dark"] .bg-white,
        html[data-bs-theme="dark"] .bg-light { background-color:var(--surface) !important; }
        html[data-bs-theme="dark"] .text-dark { color:var(--ink) !important; }
        html[data-bs-theme="dark"] .table-light {
            --bs-table-bg:var(--surface);
            --bs-table-color:var(--ink);
            --bs-table-border-color:var(--line);
        }
        html[data-bs-theme="dark"] .profile-button { background:#374151; color:var(--ink); }
        html[data-bs-theme="dark"] .profile-button:hover { background:#4b5563; color:#fff; }
        html[data-bs-theme="dark"] .page-loader { background:rgb(17 24 39 / 92%); }
        html[data-bs-theme="dark"] .page-loader-spinner { border-color:#374151; border-top-color:#93c5fd; border-bottom-color:#1684f8; }
        html[data-bs-theme="dark"] .page-loader-spinner::before { border-color:#374151; border-bottom-color:#6b7280; }
        @keyframes page-loader-spin { to { transform:rotate(360deg); } }
        @media(prefers-reduced-motion:reduce) {
            .page-loader { transition:none; }
            .page-loader-spinner { animation-duration:2.4s; }
        }
        @media(max-width:991px) {
            .sidebar { width:100%; min-height:auto; flex-basis:auto; border-right:0; border-bottom:1px solid var(--line); }
            .sidebar nav { display:grid; grid-template-columns:repeat(auto-fit,minmax(125px,1fr)); }
            .topbar { margin:12px 12px 0; }
        }
        @media(max-width:575px) {
            .topbar { align-items:flex-start !important; }
            .topbar .container-fluid { align-items:flex-start; gap:12px; flex-direction:column; }
        }
    </style>
</head>
<body>
<div class="page-loader" id="page-loader" role="status" aria-live="polite" aria-hidden="true">
    <span class="page-loader-spinner" aria-hidden="true"></span>
    <span class="visually-hidden">{{ __('Loading page') }}</span>
</div>
<div class="d-flex flex-column flex-lg-row min-vh-100">
    @include('layouts.sidebar')
    <div class="main flex-grow-1">
        @include('layouts.navbar')
        <main class="container-fluid p-3 p-lg-4">
            @include('partials.alerts')
            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (() => {
        const loader = document.getElementById('page-loader');

        const showLoader = () => {
            loader.classList.add('is-active');
            loader.setAttribute('aria-hidden', 'false');
        };

        const hideLoader = () => {
            loader.classList.remove('is-active');
            loader.setAttribute('aria-hidden', 'true');
        };

        document.addEventListener('click', (event) => {
            if (!(event.target instanceof Element) || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            const link = event.target.closest('a[href]');

            if (!link || link.hasAttribute('download') || link.target && link.target !== '_self' || link.dataset.noLoading !== undefined) {
                return;
            }

            const destination = new URL(link.href, window.location.href);

            if (destination.origin !== window.location.origin || destination.pathname + destination.search === window.location.pathname + window.location.search) {
                return;
            }

            showLoader();
        });

        document.addEventListener('submit', (event) => {
            const form = event.target;

            if (!(form instanceof HTMLFormElement) || form.dataset.noLoading !== undefined || form.target && form.target !== '_self' || form.method.toLowerCase() === 'dialog') {
                return;
            }

            showLoader();
        });

        window.addEventListener('pageshow', hideLoader);
    })();
</script>
@stack('scripts')
</body>
</html>
