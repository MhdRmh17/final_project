<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\DashboardController as ApiDashboardController;
use App\Http\Controllers\ProjectFormController;
use App\Http\Controllers\UserController;


/*
|--------------------------------------------------------------------------
| Public API
|--------------------------------------------------------------------------
*/
Route::get('ping', fn() => response()->json(['pong' => true]));
Route::post('register', [AuthController::class, 'register'])->name('api.register');
Route::post('login',    [AuthController::class, 'login'])   ->name('api.login');

/*
|--------------------------------------------------------------------------
| Protected by Sanctum
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // تسجيل خروج
    Route::post('logout', [AuthController::class, 'logout'])->name('api.logout');

    // بيانات المستخدم الحالي
    Route::get('user', fn(Request $r) => $r->user())->name('api.user');

    /*
    |--------------------------------------------------------------------------
    | Project Forms (أي مستخدم موثّق)
    |--------------------------------------------------------------------------
    */

    // CRUD كامل على ProjectForm
    Route::apiResource('project-forms', ProjectFormController::class);

    // مشاريعي فقط
    Route::get('my-projects', [ProjectFormController::class, 'myProjects'])
         ->name('project-forms.myProjects');

    /*
    |--------------------------------------------------------------------------
    | تحديث حالة المشروع (Admin فقط) – المسار الصحيح
    |--------------------------------------------------------------------------
    */
 // تأكد من أن هذا المسار هو الذي تستخدمه في الجافاسكربت:
Route::patch('project-forms/{project_form}/status', [ApiDashboardController::class, 'updateStatus'])
    ->middleware([AdminMiddleware::class])
    ->name('project-forms.updateStatus');


    /*
    |--------------------------------------------------------------------------
    | Profile Endpoints
    |--------------------------------------------------------------------------
    */

    // CRUD Profile
Route::apiResource('users', UserController::class)->only(['show','update']);
Route::middleware('auth:sanctum')
     ->patch('user/profile', [UserController::class, 'updateProfile']);




    /*
    |--------------------------------------------------------------------------
    | Dashboard & User Management (Admin only)
    |--------------------------------------------------------------------------
    */
    Route::middleware(AdminMiddleware::class)->group(function () {

        // جلب بيانات الداشبورد
        Route::get('dashboard', [ApiDashboardController::class, 'index'])
             ->name('api.dashboard');

        // تغيير كلمة مرور أي مستخدم
        Route::post('users/{user}/password',
             [ApiDashboardController::class, 'updatePassword'])
             ->name('api.users.updatePassword');
    });
});
