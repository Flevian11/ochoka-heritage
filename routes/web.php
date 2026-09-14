<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MemberController;
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
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
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
});