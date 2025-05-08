<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeCommentController;
use App\Http\Controllers\LikePostController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UpdatePasswordController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

// ---------------------- Access - Users --------------------------- //
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('forgot-password.store');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('reset-password.store');
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout.destroy');
});

// ------------------- CRUD - User -----------------------=--------- //
Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware(['verify-user'])->group(function () {
        Route::get('/user', [UserController::class, 'index'])->name('user.index');
    });
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile.show');
    Route::get('/user/{id}', [UserController::class, 'show'])->name('user.show');
    Route::put('/user', [UserController::class, 'update'])->name('user.update');
    Route::put('/newpassword', [UpdatePasswordController::class, 'updatePassword'])->name('password.update');
    Route::delete('/user', [UserController::class, 'destroy'])->name('user.destroy');
});

// ------------------ CRUD - Post --------------------------=------ //
Route::get('/post', [PostController::class, 'index'])->name('post.index');
Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware(['verify-user'])->group(function () {
        Route::post('/post', [PostController::class, 'store'])->name('post.store');
        Route::put('/post/{id}', [PostController::class, 'update'])->name('post.update');
        Route::delete('/post/{id}', [PostController::class, 'destroy'])->name('post.destroy');
    });
    Route::get('/post/{id}', [PostController::class, 'show'])->name('post.show');
});

// ----------------- CRUD - Chapter -------------------------------- //
Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware(['verify-user'])->group(function () {
        Route::post('/chapter', [ChapterController::class, 'store'])->name('chapter.store');
        Route::put('/chapter/{id}', [ChapterController::class, 'update'])->name('chapter.store');
        Route::delete('/chapter/{id}', [ChapterController::class, 'destroy'])->name('chapter.store');
    });
    Route::get('/chapter', [ChapterController::class, 'index'])->name('chapter.store');
    Route::get('/chapter/{id}', [ChapterController::class, 'show'])->name('chapter.store');
});

// ----------------- CRUD - Comment -------------------------------- //
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/comment', [CommentController::class, 'store'])->name('comment.store');
    Route::get('/comment', [CommentController::class, 'index'])->name('comment.index');
    Route::get('/comment/{id}', [CommentController::class, 'show'])->name('comment.show');
    Route::put('/comment/{id}', [CommentController::class, 'update'])->name('comment.update');
    Route::delete('/comment/{id}', [CommentController::class, 'destroy'])->name('comment.destroy');
});

// --------------- LIKES - Post/Comment --------------------------- //
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/like_post', [LikePostController::class, 'store'])->name('like_post.store');
    Route::post('/like_comment', [LikeCommentController::class, 'store'])->name('like_comment.store');
});

// --------------- Rota não encontrada --------------------------- //
Route::fallback(function () {
    return response()->json(['message' => 'Página não encontrada'], Response::HTTP_NOT_FOUND);
});