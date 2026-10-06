<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'home'])->name('dashboard');
    Route::get('/cancel', [TransactionController::class, 'cancel'])->name('transaction.cancel');
    Route::post('/mission/claim', [TransactionController::class, 'claimMonthlyMission'])->name('mission.claim');
    Route::get('/mission-validation/{missionValidation}', [TransactionController::class, 'missionValidation'])->name('mission.validation');
    Route::get('/social-share/confirmation', [TransactionController::class, 'ConfirmationSocialShareMission'])->name('social.share.confirmation');
    Route::post('/social-share/submit', [TransactionController::class, 'SubmitSocialShareMission'])->name('social.share.submit');
    Route::get('/rewards', [\App\Http\Controllers\RewardController::class, 'index'])->name('rewards');
    Route::get('/rewards/{reward}/redeem', [TransactionController::class, 'redeemConfirmation'])->name('reward.redeem');
    Route::post('/rewards/{reward}/redeem/confirm', [TransactionController::class, 'redeemConfirmed'])->name('reward.redeem.confirm');
    Route::get('/activity', [\App\Http\Controllers\ActivityController::class, 'index'])->name('activity');
    Route::get('/admin/dashboard', [\App\Http\Controllers\AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/users/{user}/transactions', [\App\Http\Controllers\AdminDashboardController::class, 'showUserTransactions'])->name('admin.user.transactions');
    Route::post('/admin/dashboard', [\App\Http\Controllers\AdminDashboardController::class, 'store'])->name('admin.dashboard.store');
    Route::get('/save-points/{business_token}', [TransactionController::class, 'showPointsFormValidation'])->name('save.points.validation');
    Route::post('/logout', function () {
        auth()->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});


// 
Route::middleware(['auth', 'business.token']) ->name('transaction.')->group(function () {
    Route::get('/save-points', [TransactionController::class, 'showPointsForm'])->name('save.points.form');
    Route::post('/save-points', [TransactionController::class, 'submit'])->name('save.points');
    Route::get('/confirmation', [TransactionController::class, 'confirmationView'])->name('confirmation');
   
    Route::get('/social-share/claim', [TransactionController::class, 'claimSocialShareMission'])->name('social.share.claim');
});
  Route::get('/confirmed', [TransactionController::class, 'confirmed'])->name('confirmed');
  Route::get('/confirmed/{}', [TransactionController::class, 'confirmed'])->name('confirmed');