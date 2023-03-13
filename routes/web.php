<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AuthController;

Route::get('/', [AuthController::class, 'login']);

Route::get('asset', [HomeController::class, 'getFile'])->name('asset');
