<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\AiChatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Public & Landing Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/fields/{slug}', [FieldController::class, 'show'])->name('fields.show');

// AI Assistant Chatbot Endpoint
Route::post('/ai/chat', [AiChatController::class, 'chat'])->name('ai.chat');

// Booking Routes
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/bookings/{bookingCode}', [BookingController::class, 'show'])->name('bookings.show');
Route::post('/bookings/{bookingCode}/pay', [BookingController::class, 'pay'])->name('bookings.pay');
Route::post('/bookings/{bookingCode}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
Route::get('/my-bookings', [BookingController::class, 'history'])->name('bookings.history');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Quick Demo Login Switchers (Khusus Pengujian Prototype)
Route::get('/demo/admin', [AuthController::class, 'loginAsAdmin'])->name('demo.admin');
Route::get('/demo/customer', [AuthController::class, 'loginAsCustomer'])->name('demo.customer');

// Admin Panel Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('/bookings/{bookingCode}/status', [AdminDashboardController::class, 'updateStatus'])->name('bookings.updateStatus');
});
