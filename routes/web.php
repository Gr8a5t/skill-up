<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FitlifeController;


Route::get('/', [FitlifeController::class, 'index'])->name('home');
Route::get('/about', [FitlifeController::class, 'about'])->name('about');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/user-dashboard', [FitlifeController::class, 'dashboard'])->name('dashboard');
    Route::get('/chats', [FitlifeController::class, 'chats'])->name('dashboard.chats');


    Route::get('/paths', [FitlifeController::class, 'paths'])->name('paths');
    Route::get('/courses', [FitlifeController::class, 'courses'])->name('courses');
    Route::get('/courses/{slug}', [FitlifeController::class, 'showCourse'])->name('courses.show');
    Route::get('/paths/{slug}/learn', [FitlifeController::class, 'learn'])->name('paths.learn');
    Route::get('/courses/{slug}/learn', [FitlifeController::class, 'courseLearn'])->name('courses.learn');
    Route::post('/api/progress', [FitlifeController::class, 'updateProgress'])->name('paths.progress');
    
});
