<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Nyarugenge Hospital</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .login-header { text-align: center; margin-bottom: 32px; }

        .login-logo {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
            font-size: 28px; color: #fff;
        }

        .login-title { font-size: 22px; font-weight: 700; color: #1a1a2e; margin-bottom: 6px; }
        .login-subtitle { font-size: 13px; color: #666; }

        .alert-error {
            background: #fef2f2; border: 1px solid #fca5a5;
            color: #dc2626; border-radius: 8px;
            padding: 10px 14px; margin-bottom: 20px; font-size: 14px;
        }

        .alert-success {
            background: #f0fdf4; border: 1px solid #86efac;
            color: #16a34a; border-radius: 8px;
            padding: 10px 14px; margin-bottom: 20px; font-size: 14px;
        }

        .login-form { display: flex; flex-direction: column; gap: 18px; }

        .form-group { display: flex; flex-direction: column; gap: 6px; }

        .form-label { font-size: 13px; font-weight: 600; color: #374151; }

        .input-wrapper {
            position: relative;
            display: flex; align-items: center;
        }

        .input-wrapper i {
            position: absolute; left: 14px;
            color: #9ca3af; font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px; color: #111827;
            outline: none;
            transition: border-color .2s;
        }

        .form-input:focus { border-color: #e74c3c; }

        .input-error { font-size: 12px; color: #dc2626; margin-top: 2px; }

        .login-options {
            display: flex; align-items: center;
            justify-content: space-between; font-size: 13px;
        }

        .remember-box { display: flex; align-items: center; gap: 6px; color: #374151; cursor: pointer; }

        .forgot-link { color: #e74c3c; text-decoration: none; font-weight: 500; }
        .forgot-link:hover { text-decoration: underline; }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: #fff; border: none; border-radius: 8px;
            font-size: 15px; font-weight: 600; cursor: pointer;
            transition: opacity .2s;
        }

        .login-btn:hover { opacity: .9; }

        .login-footer { text-align: center; margin-top: 24px; font-size: 13px; color: #6b7280; }
        .login-footer a { color: #e74c3c; font-weight: 600; text-decoration: none; }
        .login-footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">

        <div class="login-header">
            <div class="login-logo">
                <i class="fa-solid fa-truck-medical"></i>
            </div>
            <h1 class="login-title">Nyarugenge Hospital</h1>
            <p class="login-subtitle">Ambulance Fleet Management System</p>
        </div>

        {{-- Session status --}}
        @if (session('status'))
            <div class="alert-success">{{ session('status') }}</div>
        @endif

        {{-- Validation errors --}}
        @if ($errors->isNotEmpty())
            <div class="alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf

            {{-- Email --}}
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-envelope"></i>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        placeholder="Enter your email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                    >
                </div>
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock"></i>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="Enter your password"
                        required
                    >
                </div>
            </div>

            {{-- Options --}}
            <div class="login-options">
                <label class="remember-box">
                    <input type="checkbox" name="remember"> Remember Me
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
                @endif
            </div>

            <button type="submit" class="login-btn">Login</button>
        </form>

        <div class="login-footer">
            Don't have an account? <a href="{{ route('register') }}">Create Account</a>
        </div>

    </div>
</div>

</body>
</html>
