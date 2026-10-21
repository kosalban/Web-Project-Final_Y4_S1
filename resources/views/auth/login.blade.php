<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Login - KOSAL IT-Room') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8fafc;
            overflow-x: hidden;
        }
        main {
            flex: 1;
        }

        /* 1. Running Light Border Wrapper */
        .card-light-wrapper {
            position: relative;
            border-radius: 1.25rem;
            padding: 3px; /* Border thickness */
            /* background: linear-gradient(90deg, #0d6efd, #be1a1a, #0d6efd, #be1a1a); */
            background-size: 300% 300%;
            animation: runningLight 4s linear infinite, fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(25px);
            box-shadow: 0 10px 30px rgba(13, 110, 253, 0.2);
        }

   
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Card Inner Container */
        .login-card {
            border: none;
            background-color: #ffffff;
            border-radius: 1.15rem;
        }

        .info-box {
            background-color: #f0f4f9;
            border-left: 5px solid #0d6efd;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .info-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.08);
        }

        /* 2. Interactive Input Group Elevate & Active Blue Highlight */
        .form-control-lg, .input-group-text {
            font-size: 1.05rem;
            padding: 0.75rem 1rem;
            transition: all 0.25s ease-in-out;
        }

        .input-group {
            transition: transform 0.25s ease-in-out;
        }

        .input-group:focus-within {
            transform: translateY(-2px);
        }

        .input-group:focus-within .input-group-text {
            background-color: #e7f0ff !important;
            color: #0d6efd !important;
            border-color: #86b7fe;
        }

        .input-group:focus-within .form-control {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.2);
        }

        /* 3. Button Hover Elevation & Light Beam Sweep */
        .btn-lg-custom {
            font-size: 1.1rem;
            padding: 0.85rem 1rem;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        /* Running light beam across button */
        .btn-lg-custom::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -60%;
            width: 40%;
            height: 200%;
            background: linear-gradient(
                60deg, 
                transparent, 
                rgba(255, 255, 255, 0.4), 
                transparent
            );
            transform: rotate(25deg);
            animation: lightSweep 3.5s infinite;
        }

        @keyframes lightSweep {
            0% { left: -60%; }
            20% { left: 140%; }
            100% { left: 140%; }
        }

        .btn-lg-custom:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(13, 110, 253, 0.35);
        }

        .btn-lg-custom:active:not(:disabled) {
            transform: translateY(0);
        }

        /* 4. Card Shake Effect for Empty Fields */
        .shake {
            animation: shake 0.4s ease-in-out !important;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-8px); }
            40%, 80% { transform: translateX(8px); }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm py-2">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('about') }}">
                <img src="{{ asset('images/Profile_youtube.jpg') }}" alt="Logo" height="50" width="50" class="rounded-circle object-fit-cover">
                <span>KOSAL IT-Room</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <form method="POST" action="{{ route('settings.language') }}">
                    @csrf
                    <input type="hidden" name="locale" value="{{ app()->getLocale() === 'km' ? 'en' : 'km' }}">
                    <button class="btn btn-outline-secondary px-3 py-2" type="submit">
                        <i class="bi bi-translate me-1"></i>{{ app()->getLocale() === 'km' ? 'English' : 'ខ្មែរ' }}
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="d-flex align-items-center py-5">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    
                    <!-- Running Light Border Wrapper -->
                    <div class="card-light-wrapper" id="cardContainer">
                        <div class="card login-card">
                            <div class="card-body p-4 p-sm-5">
                                
                                <!-- Logo & Title -->
                                <div class="text-center mb-4">                             
                                    <h3 class="fw-bold mb-2">{{ __('KOSAL IT-Room') }}</h3>
                                    <p class="text-muted fs-6 mb-0">{{ __('Hardware, Software & Repair Management System') }}</p>
                                </div>


                                <!-- Alert Errors -->
                                @if($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show fs-6 mb-4 shake" role="alert">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first() }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
                                    </div>
                                @endif

                                <!-- Login Form -->
                                <form id="loginForm" method="POST" action="{{ route('login.store') }}" novalidate>
                                    @csrf
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold fs-6">{{ __('Email Address') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope fs-5"></i></span>
                                            <input type="email" name="email" id="emailInput" value="{{ old('email') }}" class="form-control form-control-lg" placeholder="{{ __('Enter email') }}" required autofocus>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold fs-6">{{ __('Password') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted"><i class="bi bi-lock fs-5"></i></span>
                                            <input type="password" name="password" id="passwordInput" class="form-control form-control-lg" placeholder="{{ __('Enter password') }}" required>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember" style="width: 1.2em; height: 1.2em;">
                                            <label class="form-check-label text-secondary fs-6 ms-1" for="remember">{{ __('Remember me') }}</label>
                                        </div>
                                        <a href="#" class="fs-6 text-decoration-none text-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#contactAdminModal">
                                        
                                            <i class="bi bi-telegram me-1"></i>{{ __('Forgot password ?') }}
                                        </a>
                                    </div>

                                    <!-- Submit Button with Light Sweep and Loading Animation -->
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-lg-custom w-100 fw-bold rounded-3">
                                        <span id="btnText">{{ __('Sign In') }}</span>
                                        <span id="btnSpinner" class="d-none">
                                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                            {{ __('Signing in...') }}
                                        </span>
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-top py-3 mt-auto">
        <div class="container text-center text-md-between d-md-flex align-items-center gap-2 fs-6 text-muted">
            <div>&copy; {{ now()->year }} KOSAL IT-Room. {{ __('All rights reserved.') }}</div>
            <div class="mt-2 mt-md-0">
                <a href="{{ route('about') }}" class="text-muted text-decoration-none me-3">{{ __('About') }}</a>
                <a href="#" class="text-muted text-decoration-none" data-bs-toggle="modal" data-bs-target="#contactAdminModal">{{ __('Support') }}</a>
            </div>
        </div>
    </footer>

    <!-- Contact Admin Modal -->
    <div class="modal fade" id="contactAdminModal" tabindex="-1" aria-labelledby="contactAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold fs-5" id="contactAdminModalLabel">{{ __('Contact Admin Support') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body text-center pt-2 pb-4 px-4">
                    <p class="text-muted fs-6 mb-4">{{ __('Need password reset or account help? Reach out on Telegram:') }}</p>
                    <div class="d-grid gap-3">
                        <button class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2 py-3 rounded-3 fs-6 fw-semibold" onclick="openTelegram('Bankosal_SETEC')">
                            <i class="bi bi-telegram fs-4"></i> {{ __('Admin (Kosal)') }}
                        </button>
                        <button class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2 py-3 rounded-3 fs-6 fw-semibold" onclick="openTelegram('Chanboramey_Ban')">
                            <i class="bi bi-telegram fs-4"></i> {{ __('Support (Boramey)') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Libraries & Custom Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openTelegram(username) {
            if (!username) return;
            window.open('https://t.me/' + username, '_blank');
        }

        // Form submit handler for Shake and Loading state
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            const email = document.getElementById('emailInput');
            const password = document.getElementById('passwordInput');
            const cardContainer = document.getElementById('cardContainer');

            // Trigger Shake Animation if inputs are empty
            if (!email.value.trim() || !password.value.trim()) {
                e.preventDefault();
                cardContainer.classList.remove('shake');
                void cardContainer.offsetWidth; // Force CSS reflow
                cardContainer.classList.add('shake');
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');

            submitBtn.disabled = true;
            btnText.classList.add('d-none');
            btnSpinner.classList.remove('d-none');
        });
    </script>
</body>
</html>