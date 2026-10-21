<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ __('Learn about KOSAL-IT-Room, the IT support and ticket management system.') }}">
    <title>{{ __('About KOSAL-IT-Room') }} | KOSAL-IT-Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --about-ink: #152744;
            --about-muted: #64748b;
            --about-primary: #2563eb;
            --about-surface: #f5f8ff;
        }

        body {
            color: var(--about-ink);
            background: #fff;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
        }

        .about-nav {
            background: rgb(255 255 255 / 94%);
            backdrop-filter: blur(12px);
        }

        .about-logo {
            width: 42px;
            height: 42px;
            object-fit: cover;
        }

        .about-hero {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(ellipse at 90% 10%, rgb(96 165 250 / 26%), transparent 35%),
                linear-gradient(135deg, #10213e 0%, #173a70 58%, #1d4ed8 100%);
        }

        .about-hero::after {
            position: absolute;
            right: -100px;
            bottom: -220px;
            width: 430px;
            height: 430px;
            border: 1px solid rgb(255 255 255 / 15%);
            border-radius: 50%;
            content: "";
        }

        .hero-label {
            color: #bfdbfe;
            letter-spacing: .14em;
        }

        .hero-copy {
            max-width: 660px;
            color: #dbeafe;
        }

        .hero-visual {
            position: relative;
            z-index: 1;
            max-width: 390px;
            border: 1px solid rgb(255 255 255 / 18%);
            background: rgb(255 255 255 / 10%);
            box-shadow: 0 24px 70px rgb(4 14 35 / 28%);
            backdrop-filter: blur(10px);
        }

        .feature-card {
            height: 100%;
            border: 1px solid #e7edf6;
            border-radius: 1rem;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 1rem 2.5rem rgb(21 39 68 / 9%);
        }

        .feature-icon {
            display: inline-grid;
            width: 48px;
            height: 48px;
            place-items: center;
            border-radius: 14px;
            color: var(--about-primary);
            background: #eff6ff;
            font-size: 1.3rem;
        }

        .workflow-section {
            background: var(--about-surface);
        }

        .step-number {
            display: inline-grid;
            width: 42px;
            height: 42px;
            place-items: center;
            border-radius: 50%;
            color: #fff;
            background: var(--about-primary);
            font-weight: 700;
        }

        .about-footer {
            color: var(--about-muted);
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body>
    <nav class="about-nav navbar navbar-expand py-3 border-bottom">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('about') }}">
                <img class="about-logo rounded-circle" src="{{ asset('images/Profile_youtube.jpg') }}" alt="">
                <span>KOSAL-IT-Room</span>
            </a>
            <div class="d-flex align-items-center gap-2">
                <form method="POST" action="{{ route('settings.language') }}">
                    @csrf
                    <input type="hidden" name="locale" value="{{ app()->getLocale() === 'km' ? 'en' : 'km' }}">
                    <button class="btn btn-outline-secondary btn-sm" type="submit" aria-label="{{ app()->getLocale() === 'km' ? __('Switch to English') : __('Switch to Khmer') }}">
                        <i class="bi bi-translate me-1" aria-hidden="true"></i>{{ app()->getLocale() === 'km' ? 'English' : 'ខ្មែរ' }}
                    </button>
                </form>
                <a class="btn btn-primary btn-sm px-3" href="{{ route('login') }}">{{ __('Sign In') }}</a>
            </div>
        </div>
    </nav>

    <main>
        <section class="about-hero text-white py-5">
            <div class="container py-lg-5">
                <div class="row align-items-center g-5">
                    <div class="col-lg-7 position-relative" style="z-index:1">
                        <div class="hero-label text-uppercase fw-semibold small mb-3">{{ __('IT support, made easier') }}</div>
                        <h1 class="display-4 fw-bold lh-sm mb-4">{{ __('Support requests, organized from start to finish.') }}</h1>
                        <p class="hero-copy fs-5 mb-4">{{ __('KOSAL-IT-Room helps people request IT help and gives the support team one clear place to manage each issue, share updates, and follow it through to resolution.') }}</p>
                        <a class="btn btn-light btn-lg text-primary fw-semibold px-4" href="{{ route('login') }}">
                            {{ __('Go to Sign In') }} <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="col-lg-5 d-flex justify-content-lg-end">
                        <div class="hero-visual rounded-4 p-4 p-md-5 w-100">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <span class="feature-icon bg-white"><i class="bi bi-headset" aria-hidden="true"></i></span>
                                <div>
                                    <div class="small text-white-50">{{ __('Your IT support workspace') }}</div>
                                    <div class="fw-semibold fs-5">KOSAL-IT-Room</div>
                                </div>
                            </div>
                            <div class="rounded-3 bg-white text-dark p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <div class="small text-secondary">{{ __('Support request') }}</div>
                                        <div class="fw-semibold">{{ __('Report and follow IT issues') }}</div>
                                    </div>
                                    <i class="bi bi-check-circle-fill text-success fs-5" aria-hidden="true"></i>
                                </div>
                                <div class="progress mt-3" role="presentation" style="height:6px">
                                    <div class="progress-bar" style="width:72%"></div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-white-50 small">
                                <i class="bi bi-shield-check" aria-hidden="true"></i>
                                <span>{{ __('A clear view for requesters and support staff') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container py-lg-4">
                <div class="text-center mx-auto mb-5" style="max-width:680px">
                    <div class="text-primary text-uppercase fw-semibold small mb-2">{{ __('What the system does') }}</div>
                    <h2 class="display-6 fw-bold mb-3">{{ __('Everything needed to keep support work moving.') }}</h2>
                    <p class="text-secondary mb-0">{{ __('From a request’s first report to the team’s final update, the important details stay together.') }}</p>
                </div>

                <div class="row g-4">
                    <div class="col-md-6 col-xl-3">
                        <article class="feature-card card shadow-sm p-4">
                            <span class="feature-icon mb-4"><i class="bi bi-inbox" aria-hidden="true"></i></span>
                            <h3 class="h5 fw-bold">{{ __('Organized support tickets') }}</h3>
                            <p class="text-secondary mb-0">{{ __('Record the issue, category, priority, and useful device details in one request.') }}</p>
                        </article>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <article class="feature-card card shadow-sm p-4">
                            <span class="feature-icon mb-4"><i class="bi bi-person-check" aria-hidden="true"></i></span>
                            <h3 class="h5 fw-bold">{{ __('Clear assignment and status') }}</h3>
                            <p class="text-secondary mb-0">{{ __('Support staff can assign technicians and update status as work progresses.') }}</p>
                        </article>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <article class="feature-card card shadow-sm p-4">
                            <span class="feature-icon mb-4"><i class="bi bi-chat-dots" aria-hidden="true"></i></span>
                            <h3 class="h5 fw-bold">{{ __('Updates in context') }}</h3>
                            <p class="text-secondary mb-0">{{ __('Keep comments, activity history, and ticket notifications connected to the issue.') }}</p>
                        </article>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <article class="feature-card card shadow-sm p-4">
                            <span class="feature-icon mb-4"><i class="bi bi-graph-up-arrow" aria-hidden="true"></i></span>
                            <h3 class="h5 fw-bold">{{ __('Useful reporting') }}</h3>
                            <p class="text-secondary mb-0">{{ __('Review ticket summaries and trends by category or technician, with date filters.') }}</p>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section class="workflow-section py-5">
            <div class="container py-lg-4">
                <div class="row align-items-center g-5">
                    <div class="col-lg-5">
                        <div class="text-primary text-uppercase fw-semibold small mb-2">{{ __('A simple workflow') }}</div>
                        <h2 class="display-6 fw-bold mb-3">{{ __('From issue to resolution, with everyone on the same page.') }}</h2>
                        <p class="text-secondary mb-0">{{ __('Each request has a visible place in the support process, so teams can coordinate and requesters can follow progress.') }}</p>
                    </div>
                    <div class="col-lg-7">
                        <div class="d-grid gap-4">
                            <div class="d-flex gap-3">
                                <span class="step-number flex-shrink-0">1</span>
                                <div>
                                    <h3 class="h5 fw-bold mb-1">{{ __('Submit a support request') }}</h3>
                                    <p class="text-secondary mb-0">{{ __('Describe the issue and include the details that help the team understand it.') }}</p>
                                </div>
                            </div>
                            <div class="d-flex gap-3">
                                <span class="step-number flex-shrink-0">2</span>
                                <div>
                                    <h3 class="h5 fw-bold mb-1">{{ __('The team assigns and works on it') }}</h3>
                                    <p class="text-secondary mb-0">{{ __('Technicians and support staff coordinate through ticket status and updates.') }}</p>
                                </div>
                            </div>
                            <div class="d-flex gap-3">
                                <span class="step-number flex-shrink-0">3</span>
                                <div>
                                    <h3 class="h5 fw-bold mb-1">{{ __('Follow progress and resolution') }}</h3>
                                    <p class="text-secondary mb-0">{{ __('Check the ticket history and see the latest information in one place.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container">
                <div class="rounded-4 bg-primary text-white text-center p-4 p-md-5">
                    <h2 class="h2 fw-bold mb-2">{{ __('Need IT support?') }}</h2>
                    <p class="text-white-50 mb-4">{{ __('Sign in to submit a request or continue managing your support tickets.') }}</p>
                    <a class="btn btn-light text-primary fw-semibold px-4" href="{{ route('login') }}">{{ __('Go to Sign In') }}</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="about-footer border-top py-4">
        <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 small">
            <span>&copy; {{ now()->year }} KOSAL-IT-Room</span>
            <span>{{ __('IT support, made easier') }}</span>
        </div>
    </footer>
</body>
</html>
