<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - {{ config('app.name', 'Inventory OCR') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 1000px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            overflow: hidden;
            display: flex;
            margin: 20px;
        }

        /* Left Side: Branding / Gradient */
        .login-brand {
            flex: 1;
            background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%);
            padding: 3rem;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        /* Animated background shapes */
        .login-brand::before, .login-brand::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: pulse 6s infinite alternate;
        }
        .login-brand::before {
            width: 300px;
            height: 300px;
            top: -100px;
            right: -100px;
        }
        .login-brand::after {
            width: 200px;
            height: 200px;
            bottom: -50px;
            left: -50px;
            animation-delay: 2s;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.1; }
            100% { transform: scale(1.1); opacity: 0.2; }
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 700;
            z-index: 1;
        }
        
        .brand-logo i {
            font-size: 2rem;
        }

        .brand-content {
            z-index: 1;
            margin-top: auto;
            margin-bottom: auto;
        }

        .brand-content h1 {
            font-weight: 800;
            font-size: 2.5rem;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
            padding: 1.5rem;
            margin-top: 2rem;
            z-index: 1;
        }

        /* Right Side: Form */
        .login-form-wrapper {
            flex: 1;
            padding: 4rem 3rem;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        
        .form-control:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
        }

        .form-label {
            font-weight: 600;
            color: #475569;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(to right, #0ea5e9, #3b82f6);
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(14, 165, 233, 0.3);
        }

        .input-group-text {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #64748b;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            .login-brand {
                padding: 2.5rem;
            }
            .login-form-wrapper {
                padding: 2.5rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <!-- Brand Section -->
        <div class="login-brand">
            <div class="brand-logo">
                <i class="ph-fill ph-aperture"></i>
                Inventory OCR
            </div>
            
            <div class="brand-content">
                <h1>Smart Inventory Management</h1>
                <p class="fs-5 opacity-75">Automate your stock taking with advanced OCR technology. Scan receipts, manage debts, and track everything effortlessly.</p>
                
                <div class="glass-card mt-5">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <i class="ph-fill ph-check-circle fs-4 text-white"></i>
                        <span class="fw-semibold fs-5">Fast & Accurate</span>
                    </div>
                    <p class="mb-0 opacity-75 small">Extract data from supplier receipts in seconds without manual entry.</p>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="login-form-wrapper">
            <div class="mb-4">
                <h3 class="fw-bold text-dark mb-1">Welcome back</h3>
                <p class="text-muted">Please enter your credentials to access your account.</p>
            </div>

            <!-- Session Status -->
            @if(session('status'))
                <div class="alert alert-success rounded-3 mb-4">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-4">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text border-end-0"><i class="ph ph-envelope"></i></span>
                        <input id="email" type="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@example.com">
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1 fw-medium">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label mb-0">Password</label>
                        @if (Route::has('password.request'))
                            <a class="text-decoration-none small text-primary fw-medium" href="{{ route('password.request') }}">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <div class="input-group">
                        <span class="input-group-text border-end-0"><i class="ph ph-lock-key"></i></span>
                        <input id="password" type="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="••••••••">
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1 fw-medium">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="mb-4 form-check">
                    <input class="form-check-input shadow-none" type="checkbox" name="remember" id="remember_me">
                    <label class="form-check-label text-muted small" for="remember_me">
                        Remember for 30 days
                    </label>
                </div>

                <div class="d-grid mt-5">
                    <button type="submit" class="btn btn-primary btn-lg">
                        Sign In <i class="ph-bold ph-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
