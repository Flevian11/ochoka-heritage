<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\ExecutivePositionController;
use App\Http\Controllers\Admin\ExecutiveAppointmentController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Home'))->name('home');
Route::get('/about', fn () => Inertia::render('About'))->name('about');
Route::get('/leadership', fn () => Inertia::render('Leadership'))->name('leadership');
Route::get('/membership', fn () => Inertia::render('Membership'))->name('membership');
Route::get('/welfare', fn () => Inertia::render('Welfare'))->name('welfare');
Route::get('/governance', fn () => Inertia::render('Governance'))->name('governance');
Route::get('/contact', fn () => Inertia::render('Contact'))->name('contact');
Route::get('/faq', fn () => Inertia::render('FAQ'))->name('faq');
Route::get('/terms', fn () => Inertia::render('Terms'))->name('terms');
Route::get('/privacy', fn () => Inertia::render('Privacy'))->name('privacy');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:login')->name('login.store');

    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegistration'])->name('register.store');

    Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'updatePassword'])->name('password.update');
});

Route::get('/email/verify', [AuthController::class, 'verificationNotice'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::post('/logout', [AuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)
        ->middleware('permission:members.view')
        ->name('dashboard');

    Route::middleware('permission:members.view')->group(function () {
        Route::get('/members', [MemberController::class, 'index'])->name('members.index');
        Route::get('/members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit');
    });

    Route::middleware('permission:members.manage')->group(function () {
        Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
        Route::post('/members', [MemberController::class, 'store'])->name('members.store');
        Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
        Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
    });

    Route::prefix('governance')->name('governance.')->middleware('permission:governance.view')->group(function () {
        Route::get('/positions', [ExecutivePositionController::class, 'index'])->name('positions.index');
        Route::get('/appointments', [ExecutiveAppointmentController::class, 'index'])->name('appointments.index');

        Route::middleware('permission:governance.manage')->group(function () {
            Route::get('/positions/create', [ExecutivePositionController::class, 'create'])->name('positions.create');
            Route::post('/positions', [ExecutivePositionController::class, 'store'])->name('positions.store');
            Route::get('/positions/{position}/edit', [ExecutivePositionController::class, 'edit'])->name('positions.edit');
            Route::put('/positions/{position}', [ExecutivePositionController::class, 'update'])->name('positions.update');
            Route::delete('/positions/{position}', [ExecutivePositionController::class, 'destroy'])->name('positions.destroy');

            Route::get('/appointments/create', [ExecutiveAppointmentController::class, 'create'])->name('appointments.create');
            Route::post('/appointments', [ExecutiveAppointmentController::class, 'store'])->name('appointments.store');
            Route::post('/appointments/{appointment}/revoke', [ExecutiveAppointmentController::class, 'revoke'])->name('appointments.revoke');
        });
    });

    Route::prefix('settings')->name('settings.')->middleware('permission:system.manage')->group(function () {
        Route::get('/', [SystemSettingController::class, 'index'])->name('index');
        Route::post('/', [SystemSettingController::class, 'store'])->name('store');
        Route::put('/{setting}', [SystemSettingController::class, 'update'])->name('update');
        Route::delete('/{setting}', [SystemSettingController::class, 'destroy'])->name('destroy');
    });
});