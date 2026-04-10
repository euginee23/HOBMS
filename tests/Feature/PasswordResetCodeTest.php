<?php

use App\Mail\PasswordResetCode;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

beforeEach(function () {
    Mail::fake();
    $this->user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('old-password'),
    ]);
});

test('submitting email sends password reset code and advances to step 2', function () {
    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'test@example.com')
        ->call('sendCode')
        ->assertSet('step', 2)
        ->assertHasNoErrors();

    Mail::assertSent(PasswordResetCode::class, function ($mail) {
        return $mail->hasTo('test@example.com');
    });
});

test('submitting non-existent email shows error', function () {
    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'nobody@example.com')
        ->call('sendCode')
        ->assertSet('step', 1)
        ->assertHasErrors(['email']);

    Mail::assertNothingSent();
});

test('wrong verification code is rejected', function () {
    Cache::put('password_reset_'.md5('test@example.com'), '123456', now()->addMinutes(10));

    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'test@example.com')
        ->set('step', 2)
        ->set('code', '999999')
        ->call('verifyCode')
        ->assertSet('step', 2)
        ->assertHasErrors(['code']);
});

test('correct verification code advances to step 3', function () {
    Cache::put('password_reset_'.md5('test@example.com'), '123456', now()->addMinutes(10));

    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'test@example.com')
        ->set('step', 2)
        ->set('code', '123456')
        ->call('verifyCode')
        ->assertSet('step', 3)
        ->assertHasNoErrors();
});

test('password is reset successfully with valid code', function () {
    Cache::put('password_reset_'.md5('test@example.com'), '123456', now()->addMinutes(10));

    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'test@example.com')
        ->set('code', '123456')
        ->set('step', 3)
        ->set('password', 'new-secure-password')
        ->set('password_confirmation', 'new-secure-password')
        ->call('resetPassword')
        ->assertRedirect(route('login'));

    $this->user->refresh();
    expect(Hash::check('new-secure-password', $this->user->password))->toBeTrue();
    expect(Cache::has('password_reset_'.md5('test@example.com')))->toBeFalse();
});

test('resend code sends a new email', function () {
    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'test@example.com')
        ->set('step', 2)
        ->call('resendCode')
        ->assertHasNoErrors();

    Mail::assertSent(PasswordResetCode::class);
});

test('resend code is rate limited', function () {
    Cache::put('password_resend_'.md5('test@example.com'), true, now()->addSeconds(60));

    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'test@example.com')
        ->set('step', 2)
        ->call('resendCode')
        ->assertHasErrors(['code']);

    Mail::assertNothingSent();
});
