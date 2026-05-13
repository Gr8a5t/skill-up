<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FitlifeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;

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
    Route::get('/forum', [FitlifeController::class, 'forum'])->name('dashboard.forum');
    
    // Profile Routing
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');

    Route::get('/paths', [FitlifeController::class, 'paths'])->name('paths');
    Route::get('/courses', [FitlifeController::class, 'courses'])->name('courses');
    Route::get('/courses/{slug}', [FitlifeController::class, 'showCourse'])->name('courses.show');
    Route::get('/paths/{slug}/learn', [FitlifeController::class, 'learn'])->name('paths.learn');
    Route::get('/courses/{slug}/learn', [FitlifeController::class, 'courseLearn'])->name('courses.learn');
    Route::post('/api/progress', [FitlifeController::class, 'updateProgress'])->name('paths.progress');
    
    // Admin Routes
    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/courses', [AdminController::class, 'courses'])->name('admin.courses');
        Route::get('/admin/courses/create', [AdminController::class, 'createCourse'])->name('admin.courses.create');
        Route::post('/admin/courses', [AdminController::class, 'storeCourse'])->name('admin.courses.store');
        Route::get('/admin/courses/{course}/edit', [AdminController::class, 'editCourse'])->name('admin.courses.edit');
        Route::put('/admin/courses/{course}', [AdminController::class, 'updateCourse'])->name('admin.courses.update');
        Route::delete('/admin/courses/{course}', [AdminController::class, 'deleteCourse'])->name('admin.courses.delete');
    });
});
