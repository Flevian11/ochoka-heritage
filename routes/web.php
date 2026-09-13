<?php

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
