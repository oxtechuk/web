<?php

use App\Http\Controllers\Api\Store\AboutController;
use App\Http\Controllers\Api\Store\BlogController;
use App\Http\Controllers\Api\Store\BookingController;
use App\Http\Controllers\Api\Store\CalculatorController;
use App\Http\Controllers\Api\Store\CarController;
use App\Http\Controllers\Api\Store\GalleryController;
use App\Http\Controllers\Api\Store\HomeController;
use App\Http\Controllers\Api\Store\NewsletterController;
use App\Http\Controllers\Api\Store\OfferController;
use App\Http\Controllers\Api\Store\SettingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('store')->name('store.api.')->group(function () {
    // Car Catalog (US1)
    Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
    Route::get('/cars/search', [CarController::class, 'search'])->name('cars.search');
    Route::get('/cars/compare', [CarController::class, 'compare'])->name('cars.compare');
    Route::get('/cars/{slug}', [CarController::class, 'show'])->name('cars.show');
    Route::get('/brands', [CarController::class, 'brands'])->name('brands.index');

    // Booking & Calculator (US2)
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::post('/calculator/lead', [CalculatorController::class, 'saveLead'])->name('calculator.lead');
    Route::post('/calculator/otp/send', [CalculatorController::class, 'sendOtp'])->name('calculator.otp.send');
    Route::post('/calculator/otp/verify', [CalculatorController::class, 'verifyOtp'])->name('calculator.otp.verify');

    // Content & Engagement (US3)
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
    Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');
    Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');

    // Home & Static (US4)
    Route::get('/home', [HomeController::class, '__invoke'])->name('home');
    Route::get('/about', [AboutController::class, '__invoke'])->name('about');

    // Settings & Gallery (US5)
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/gallery', [GalleryController::class, '__invoke'])->name('gallery');
});
