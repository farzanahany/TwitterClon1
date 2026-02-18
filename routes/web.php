<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {

    // ✅ Dashboard soll Feed sein

    Route::get('/dashboard', function () {
    return redirect()->route('posts.index');
    })->middleware(['auth'])->name('dashboard');


    //user profile

    Route::get('/u/{user:username}', [ProfileController::class, 'show'])
    ->name('profile.show');

    Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
});


    // Startseite -> Dashboard
    Route::get('/', function () {
        return redirect()->route('dashboard');
    })->name('startseite');

    // Profil
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/u/{user:benutzername}', [ProfileController::class, 'show'])->name('profile.show');

    // Posts (optional weiterhin erreichbar)
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::post('/posts/{post}/like', [PostController::class, 'toggleLike'])->name('posts.like');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::patch('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::get('/', [PostController::class, 'index'])->name('posts.index');



});

require __DIR__.'/auth.php';
