<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return redirect('/admin');
})->name('home');

Route::get('/my-qr-image', function () {
    $user = \Illuminate\Support\Facades\Auth::user();
    if (!$user) abort(403);

    $token = $user->qr_token;
    if (empty($token)) {
        // Return transparent 1x1 pixel
        return response(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='))
            ->header('Content-Type', 'image/png');
    }

    $options = new \chillerlan\QRCode\QROptions([
        'version'    => 5,
        'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
        'eccLevel'   => \chillerlan\QRCode\QRCode::ECC_L,
        'scale'      => 10,
        'imageBase64' => false,
    ]);

    $qrcode = (new \chillerlan\QRCode\QRCode($options))->render($token);

    return response($qrcode)->header('Content-Type', 'image/png');
})->middleware(['auth', 'verified'])->name('my-qr-image');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
