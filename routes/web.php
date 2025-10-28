<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TreeController;
use App\Http\Controllers\TreeCategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;

// Redirect root to login or dashboard depending on auth
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Auth routes (login / register / logout)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard common (redirects by role)
Route::middleware('auth')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Public routes (list & show) used by layout or guest users
Route::get('/trees', [TreeController::class, 'index'])->name('trees.index');
// constrain {tree} to numeric ids so URIs like /trees/create won't be captured by this route
Route::get('/trees/{tree}', [TreeController::class, 'show'])->whereNumber('tree')->name('trees.show');
Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
Route::get('/locations/{location}', [LocationController::class, 'show'])->whereNumber('location')->name('locations.show');

// Public categories routes (added to avoid RouteNotFound when layout calls categories.index/show)
Route::get('/categories', [TreeCategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [TreeCategoryController::class, 'show'])->whereNumber('category')->name('categories.show');

// ADMIN routes
Route::middleware(['auth', \App\Http\Middleware\RoleMiddleware::class . ':admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('trees', TreeController::class);
    // removed categories and locations resource management (handled within tree creation)
    // Admin users resource
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
});

// Admin-named aliases for templates that call un-prefixed route names
// These routes simply map to the same controller actions but are protected
// so templates calling route('trees.create') etc. won't fail.
Route::middleware(['auth', \App\Http\Middleware\RoleMiddleware::class . ':admin'])->group(function () {
    // Trees create/store aliases
    Route::get('/trees/create', [TreeController::class, 'create'])->name('trees.create');
    Route::post('/trees', [TreeController::class, 'store'])->name('trees.store');

    // Removed categories and locations create/store aliases (management removed)
});

// Admin aliases for edit/update/destroy so templates calling unprefixed names work
Route::middleware(['auth', \App\Http\Middleware\RoleMiddleware::class . ':admin'])->group(function () {
    // Trees edit/update/destroy aliases
    Route::get('/trees/{tree}/edit', [TreeController::class, 'edit'])->name('trees.edit');
    Route::match(['put', 'patch'], '/trees/{tree}', [TreeController::class, 'update'])->name('trees.update');
    Route::delete('/trees/{tree}', [TreeController::class, 'destroy'])->name('trees.destroy');

    // Removed categories and locations edit/update/destroy aliases (management removed)
});

// STAFF routes
Route::middleware(['auth', \App\Http\Middleware\RoleMiddleware::class . ':staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    Route::resource('trees', TreeController::class)->only(['index','show','edit','update']);
    Route::resource('locations', LocationController::class)->only(['index','show']);
});

// STUDENT / GUEST routes
Route::middleware(['auth', \App\Http\Middleware\RoleMiddleware::class . ':student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('trees', [TreeController::class, 'index'])->name('trees.index');
    Route::get('trees/{tree}', [TreeController::class, 'show'])->name('trees.show');
    Route::get('locations', [LocationController::class, 'index'])->name('locations.index');
    Route::get('locations/{location}', [LocationController::class, 'show'])->name('locations.show');
});

// Fallback for undefined routes
Route::fallback(function () {
    abort(404);
});