<?php

use App\Http\Controllers\Admin\DashboardController as AdminController;
use App\Http\Controllers\Admin\TrainingController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\NewsCheckController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [NewsCheckController::class, 'home'])->name('home');
Route::post('/check', [NewsCheckController::class, 'check'])->name('news.check');
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

Route::get('/pricing', [PaymentController::class, 'showPlans'])->name('pricing');
Route::post('/payment/checkout', [PaymentController::class, 'createCheckout'])->middleware('auth')->name('payment.checkout');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');

Route::view('/how-it-works', 'news.how-it-works')->name('how-it-works');
Route::view('/faq', 'news.faq')->name('faq');
Route::view('/contact', 'news.contact')->name('contact');
Route::view('/about', 'news.about')->name('about');

Route::get('/dashboard', function () {
    if (auth()->user()?->isAdmin()) {
        return redirect()->route('admin.overview');
    }

    return redirect()->route('history.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::delete('/history/{prediction}', [HistoryController::class, 'destroy'])->name('history.destroy');
    Route::get('/history/{prediction}/recheck', [HistoryController::class, 'recheck'])->name('history.recheck');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->middleware(['auth', 'verified', 'admin'])->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'overview'])->name('overview');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/toggle', [AdminController::class, 'toggleUser'])->name('users.toggle');
    Route::get('/predictions', [AdminController::class, 'predictions'])->name('predictions');
    Route::get('/audit', [AdminController::class, 'auditLogs'])->name('audit');
    Route::get('/datasets', [AdminController::class, 'datasets'])->name('datasets');
    Route::post('/datasets', [AdminController::class, 'storeDataset'])->name('datasets.store');
    Route::post('/datasets/{dataset}/train', [AdminController::class, 'trainDataset'])->name('datasets.train');
    Route::delete('/datasets/{dataset}', [AdminController::class, 'destroyDataset'])->name('datasets.destroy');
    Route::get('/training', [TrainingController::class, 'index'])->name('training');
    Route::post('/training/upload', [TrainingController::class, 'upload'])->name('training.upload');
    Route::post('/training/start', [TrainingController::class, 'start'])->name('training.start');
    Route::get('/training/status', [TrainingController::class, 'status'])->name('training.status');
});

require __DIR__.'/auth.php';
