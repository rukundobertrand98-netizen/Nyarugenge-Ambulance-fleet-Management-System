<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role']     = 'patient'; // default role for self-registered users

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirect(route('patient.dashboard', absolute: false), navigate: true);
    }
}; ?>

<!-- ================= REGISTER UI ================= -->

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
        <form wire:submit="register" class="login-form">

            <!-- NAME -->
            <div class="form-group">

                <label class="form-label">Full Name</label>

                <div class="input-wrapper">
                    <i class="fa-solid fa-user"></i>

                    <input
                        type="text"
                        wire:model="name"
                        class="form-input"
                        placeholder="Enter your full name"
                        required
                        autofocus
                        autocomplete="name"
                    >
                </div>

                @error('name')
                    <div class="input-error">{{ $message }}</div>
                @enderror

            </div>

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
                        autocomplete="email"
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
                        placeholder="Create a password"
                        required
                        autocomplete="new-password"
                    >
                </div>

                @error('password')
                    <div class="input-error">{{ $message }}</div>
                @enderror

            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="form-group">

                <label class="form-label">Confirm Password</label>

                <div class="input-wrapper">
                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        wire:model="password_confirmation"
                        class="form-input"
                        placeholder="Confirm your password"
                        required
                        autocomplete="new-password"
                    >
                </div>

                @error('password_confirmation')
                    <div class="input-error">{{ $message }}</div>
                @enderror

            </div>

            <!-- BUTTON -->
            <button type="submit" class="login-btn">
                Create Account
            </button>

        </form>

        <!-- FOOTER -->
        <div class="login-footer">
            Already have an account?
            <a href="{{ route('login') }}">Login</a>
        </div>

    </div>

</div>
