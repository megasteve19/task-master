<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth')->group(function() {
    // ** Dashboard route
    Route::get('/', DashboardController::class)->name('dashboard');

    // ** Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ** Project routes
    Route::resource('projects', ProjectController::class)->only('index', 'show', 'store', 'update', 'destroy');

    // ** Project task routes
    Route::resource('projects.tasks', ProjectTaskController::class)->only('index', 'store', 'update', 'destroy')->scoped();

    // ** Sub-routes for project and project tasks
    Route::prefix('projects/{project}')->name('projects.')->group(function() {
        // ** Project routes
        Route::controller(ProjectController::class)->group(function() {
            Route::put('archive', 'archive')->name('archive');
            Route::put('restore-archive', 'restoreArchive')->name('restore-archive');
            Route::put('restore-delete', 'restoreDelete')->name('restore-delete')->withTrashed();
            Route::delete('destroy-permanently', 'destroyPermanently')->name('destroy-permanently')->withTrashed();
        });

        // ** Project task routes
        Route::controller(ProjectTaskController::class)->group(function() {
            Route::put('tasks/{task}/archive', 'archive')->name('tasks.archive');
            Route::put('tasks/{task}/restore-archive', 'restoreArchive')->name('tasks.restore-archive');
            Route::put('tasks/{task}/restore-delete', 'restoreDelete')->name('tasks.restore-delete')->withTrashed();
            Route::delete('tasks/{task}/destroy-permanently', 'destroyPermanently')->name('tasks.destroy-permanently')->withTrashed();
            Route::put('tasks/{task}/swap-priority', 'swapPriority')->name('tasks.swap-priority');
            Route::put('tasks/{task}/status', 'updateStatus')->name('tasks.update-status');
        });
    });

    // ** User routes
    Route::resource('users', UserController::class)->only('index');

    // ** Setting routes
    Route::singleton('settings', SettingController::class);

    // ** About route
    Route::get('/about', AboutController::class)->name('about');
});

require __DIR__ . '/auth.php';
