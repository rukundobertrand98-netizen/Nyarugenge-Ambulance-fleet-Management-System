<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        $this->validate();
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember)) {

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Invalid email or password.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $user = Auth::user();

        // ROLE BASED REDIRECT
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'dispatcher' => redirect()->route('dispatcher.dashboard'),
            'driver' => redirect()->route('driver.dashboard'),
            'patient' => redirect()->route('patient.dashboard'),
            default => tap(Auth::logout(), function () {
                throw ValidationException::withMessages([
                    'email' => 'User role not recognized.',
                ]);
            }),
        };
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => "Too many login attempts. Try again in {$seconds} seconds.",
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->email) . '|' . request()->ip()
        );
    }
};

?>

<!-- ================= LOGIN UI ================= -->

<div class="login-wrapper">

    <div class="login-card">

        <!-- HEADER -->
        <div class="login-header">

            <div class="login-logo">
                <i class="fa-solid fa-truck-medical"></i>
            </div>

            <h1 class="login-title">Nyarugenge Hospital</h1>

            <p class="login-subtitle">
                Ambulance Fleet Management System
            </p>

        </div>

        <!-- STATUS -->
        @if (session('status'))
            <div class="alert-success">
                {{ session('status') }}
            </div>
        @endif

        <!-- FORM -->
        <form wire:submit="login" class="login-form">

            <!-- EMAIL -->
            <div class="form-group">

                <label class="form-label">Email Address</label>

                <div class="input-wrapper">
                    <i class="fa-solid fa-envelope"></i>

                    <input
                        type="email"
                        wire:model="email"
                        class="form-input"
                        placeholder="Enter your email"
                        required
                        autofocus
                    >
                </div>

                @error('email')
                    <div class="input-error">{{ $message }}</div>
                @enderror

            </div>

            <!-- PASSWORD -->
            <div class="form-group">

                <label class="form-label">Password</label>

                <div class="input-wrapper">
                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        wire:model="password"
                        class="form-input"
                        placeholder="Enter your password"
                        required
                    >
                </div>

                @error('password')
                    <div class="input-error">{{ $message }}</div>
                @enderror

            </div>

            <!-- OPTIONS -->
            <div class="login-options">

                <label class="remember-box">
                    <input type="checkbox" wire:model="remember">
                    Remember Me
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">
                        Forgot Password?
                    </a>
                @endif

            </div>

            <!-- BUTTON -->
            <button type="submit" class="login-btn">
                Login
            </button>

        </form>

        <!-- FOOTER -->
        <div class="login-footer">
            Don’t have an account?
            <a href="{{ route('register') }}">Create Account</a>
        </div>

    </div>

</div>