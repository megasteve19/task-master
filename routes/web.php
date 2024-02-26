<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TaskController;
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
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('projects', ProjectController::class)->only('index', 'store', 'update', 'destroy');

    Route::controller(ProjectController::class)->prefix('projects/{project}')->name('projects.')->group(function() {
        Route::put('archive', 'archive')->name('archive');
        Route::put('restore-archive', 'restoreArchive')->name('restore-archive');
        Route::put('restore-delete', 'restoreDelete')->name('restore-delete')->withTrashed();
        Route::delete('destroy-permanently', 'destroyPermanently')->name('destroy-permanently')->withTrashed();
    });

    Route::resource('tasks', TaskController::class)->only('index', 'store', 'update', 'destroy');

    Route::resource('users', UserController::class)->only('index');

    Route::singleton('settings', SettingController::class);

    Route::get('/about', AboutController::class)->name('about');
});

require __DIR__ . '/auth.php';
