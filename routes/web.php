<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    //dashboard 
    Route::get('/dashboard', function () {
        return redirect()->route('posts.index');
    })->name('dashboard');


    //profile

    // Profil anzeigen (Route Model Binding über benutzername)
    Route::get('/u/{user:benutzername}', [ProfileController::class, 'show'])
        ->name('profile.show');

    // Eigenes Profil bearbeiten
    Route::get('/profil', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    // Profil speichern (PUT oder PATCH)
    Route::put('/profil', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::patch('/profil', [ProfileController::class, 'update']);

    // Profil löschen
    Route::delete('/profil', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    //posts

    Route::get('/posts', [PostController::class, 'index'])
        ->name('posts.index');

    Route::post('/posts', [PostController::class, 'store'])
        ->name('posts.store');

    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])
        ->name('posts.edit');

    Route::patch('/posts/{post}', [PostController::class, 'update'])
        ->name('posts.update');

    Route::delete('/posts/{post}', [PostController::class, 'destroy'])
        ->name('posts.destroy');

    Route::post('/posts/{post}/like', [PostController::class, 'toggleLike'])
        ->name('posts.like');


    //Kommentare
   

    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])
        ->name('comments.store');

    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
        ->name('comments.destroy');


    
    //Startseite

    Route::get('/', function () {
        return redirect()->route('posts.index');
    })->name('startseite');
});


require __DIR__.'/auth.php';
