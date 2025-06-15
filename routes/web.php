<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;      // ← استوردها هنا

use Illuminate\Support\Facades\Route;

// صفحة تسجيل الدخول (Blade view). فقط للضيوف.
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
});

// صفحة لوحة التحكم. محمية بتوكن Sanctum وبالـ AdminMiddleware.
Route::view('/dashboard', 'dashboard')
     ->middleware('auth');


//////
Route::view('/api-login', 'login-api')->name('api.login.page');
Route::view('/dashboard-page', 'dashboard-api')->name('api.dashboard.page');
/////



Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);    // ← هذا السطر مفقود عندك
});

Route::post('/logout', [AuthController::class, 'logout'])
     ->middleware('auth:sanctum')
     ->name('logout');

