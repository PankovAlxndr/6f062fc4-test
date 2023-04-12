<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('/{user}', [UserController::class, 'update'])->name('users.update');
    Route::get('/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/', [UserController::class, 'store'])->name('users.store');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

Route::prefix('groups')->group(function () {
    Route::get('/', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');
    Route::patch('/{group}', [GroupController::class, 'update'])->name('groups.update');
    Route::get('/create', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/', [GroupController::class, 'store'])->name('groups.store');
    Route::delete('/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');
});

Route::prefix('tags')->group(function () {
    Route::get('/', [TagController::class, 'index'])->name('tags.index');
    Route::get('/{tag}/edit', [TagController::class, 'edit'])->name('tags.edit');
    Route::patch('/{tag}', [TagController::class, 'update'])->name('tags.update');
    Route::get('/create', [TagController::class, 'create'])->name('tags.create');
    Route::post('/', [TagController::class, 'store'])->name('tags.store');
    Route::delete('/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');
});
