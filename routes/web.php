<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\AdminRegisterController;
use App\Http\Controllers\Auth\loginController;
use Illuminate\Support\Facades\Route;
use PhpParser\Node\Scalar\MagicConst\Dir;


Route::get('/login', [loginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
Route::get('/admin/register', [AdminRegisterController::class, 'admin_register'])->name('admin.register');
Route::post('/admin/register', [AdminRegisterController::class, 'store'])->name('admin.register.store');


require __DIR__.'/prof.php';
require __DIR__.'/student.php';
require __DIR__.'/admin.php';