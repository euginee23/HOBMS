<?php

use App\Mail\PasswordResetCode;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Forgot password')] #[Layout('layouts.auth.simple')] class extends Component {
    public int $step = 1;

    public string $email = '';
    public string $code = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function sendCode(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $this->email)->first();

        if (! $user) {
            $this->addError('email', 'We could not find an account with that email address.');

            return;
        }

        $this->dispatchResetCode();
        $this->step = 2;
    }

    public function verifyCode(): void
    {
        $this->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $cacheKey = 'password_reset_'.md5($this->email);
        $cachedCode = Cache::get($cacheKey);

        if (! $cachedCode || $cachedCode !== $this->code) {
            $this->addError('code', 'The code is invalid or has expired.');

            return;
        }

        $this->step = 3;
    }

    public function resetPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string', Password::default(), 'confirmed'],
        ]);

        $cacheKey = 'password_reset_'.md5($this->email);
        $cachedCode = Cache::get($cacheKey);

        if (! $cachedCode || $cachedCode !== $this->code) {
            $this->addError('code', 'The code has expired. Please request a new one.');
            $this->step = 1;

            return;
        }

        $user = User::where('email', $this->email)->firstOrFail();
        $user->forceFill(['password' => Hash::make($this->password)])->save();

        Cache::forget($cacheKey);

        session()->flash('status', 'Your password has been reset successfully.');

        $this->redirect(route('login'), navigate: true);
    }

    public function resendCode(): void
    {
        $resendKey = 'password_resend_'.md5($this->email);

        if (Cache::has($resendKey)) {
            $this->addError('code', 'Please wait before requesting a new code.');

            return;
        }

        $this->dispatchResetCode();

        session()->flash('code_resent', 'A new code has been sent to your email.');
    }

    public function goBack(): void
    {
        $this->step = max(1, $this->step - 1);
        $this->resetErrorBag();
    }

    private function dispatchResetCode(): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $cacheKey = 'password_reset_'.md5($this->email);

        Cache::put($cacheKey, $code, now()->addMinutes(10));
        Cache::put('password_resend_'.md5($this->email), true, now()->addSeconds(60));

        Mail::to($this->email)->send(new PasswordResetCode($code));
    }
}
?>

<div class="flex flex-col gap-6">
    @if ($step === 1)
        <x-auth-header :title="__('Forgot password')" :description="__('Enter your email to receive a password reset code')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form wire:submit="sendCode" class="flex flex-col gap-6">
            <flux:input
                wire:model="email"
                label="{{ __('Email address') }}"
                type="email"
                required
                autofocus
                placeholder="email@example.com"
            />

            <flux:button variant="primary" type="submit" class="w-full">
                {{ __('Send reset code') }}
            </flux:button>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
            <span>{{ __('Or, return to') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('log in') }}</flux:link>
        </div>
    @elseif ($step === 2)
        <div class="text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-violet-100 dark:bg-violet-900/30">
                <flux:icon name="envelope" class="size-7 text-violet-600 dark:text-violet-400" />
            </div>
            <x-auth-header :title="__('Enter verification code')" :description="__('We sent a 6-digit code to') . ' ' . $email" />
        </div>

        @if (session('code_resent'))
            <div class="rounded-lg bg-green-50 p-3 text-center text-sm text-green-700 dark:bg-green-900/20 dark:text-green-400">
                {{ session('code_resent') }}
            </div>
        @endif

        <form wire:submit="verifyCode" class="flex flex-col gap-6">
            <flux:otp wire:model="code" length="6" submit="auto" class="mx-auto" />

            @error('code')
                <p class="text-center text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            <flux:button variant="primary" type="submit" class="w-full">
                {{ __('Verify code') }}
            </flux:button>
        </form>

        <div class="flex items-center justify-between text-sm">
            <button wire:click="goBack" type="button" class="text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300">
                &larr; {{ __('Back') }}
            </button>
            <button wire:click="resendCode" type="button" class="text-violet-600 hover:text-violet-800 dark:text-violet-400 dark:hover:text-violet-300">
                {{ __('Resend code') }}
            </button>
        </div>
    @elseif ($step === 3)
        <x-auth-header :title="__('Set new password')" :description="__('Enter your new password below')" />

        <form wire:submit="resetPassword" class="flex flex-col gap-6">
            <flux:input
                wire:model="password"
                label="{{ __('New password') }}"
                type="password"
                required
                autocomplete="new-password"
                placeholder="{{ __('Password') }}"
                viewable
            />

            <flux:input
                wire:model="password_confirmation"
                label="{{ __('Confirm password') }}"
                type="password"
                required
                autocomplete="new-password"
                placeholder="{{ __('Confirm password') }}"
                viewable
            />

            <flux:button variant="primary" type="submit" class="w-full">
                {{ __('Reset password') }}
            </flux:button>
        </form>

        <div class="text-sm">
            <button wire:click="goBack" type="button" class="text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300">
                &larr; {{ __('Back to verification') }}
            </button>
        </div>
    @endif
</div>

