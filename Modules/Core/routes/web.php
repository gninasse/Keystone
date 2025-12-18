<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Core\Http\Controllers\AuthController;

Route::resource('cores', CoreController::class)->names('core');
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
// Route::middleware(['auth', 'verified'])->group(function () {
// });
