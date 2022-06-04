<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\TransactionsController;
use Illuminate\Support\Facades\Route;

Route::get('version', [SystemController::class, 'version']);

Route::middleware(['auth-token'])->group(function () {
    Route::get('token', [SystemController::class, 'test']);

    Route::get('accounts/restore/{account}', [AccountController::class, 'restore']);
    Route::get('accounts/snapshot/{account}', [AccountController::class, 'snapshot']);
    Route::resource('accounts', AccountController::class)->except(['edit', 'create']);

    Route::get('lessons/{lesson}/restore/{aggregateVersion}', [LessonController::class,'restoreModel']);
    Route::put('lessons/{lesson}/tags', [LessonController::class,'processTags']);
    Route::resource('lessons', LessonController::class)->except(['edit', 'create']);

    Route::get('courses/{course}/restore/{aggregateVersion}', [CourseController::class,'restoreModel']);
    Route::put('courses/{course}/tags', [CourseController::class,'processTags']);
    Route::get('courses/search/{searchQuery}', [CourseController::class,'search']);
    Route::resource('courses', CourseController::class)->except(['edit', 'create']);

    Route::get('comments/{comment}/restore/{aggregateVersion}', [CommentController::class,'restoreModel']);
    Route::resource('comments', CommentController::class)->except(['edit', 'create']);

    Route::get('tasks/{task}/restore/{aggregateVersion}', [TaskController::class,'restoreModel']);
    Route::put('tasks/{task}/tags', [TaskController::class,'processTags']);
    Route::resource('tasks', TaskController::class)->except(['edit', 'create']);

    Route::get('videos/{video}/restore/{aggregateVersion}', [VideoController::class,'restoreModel']);
    Route::put('videos/{video}/tags', [VideoController::class,'processTags']);
    Route::resource('videos', VideoController::class)->except(['edit', 'create']);

    Route::get('sections/{section}/restore/{aggregateVersion}', [SectionController::class,'restoreModel']);
    Route::resource('sections', SectionController::class)->except(['edit', 'create']);

    Route::get('tags/{tag}/restore/{aggregateVersion}', [TagController::class,'restoreModel']);
    Route::resource('tags', TagController::class)->except(['edit', 'create']);

    Route::get('transactions', [TransactionsController::class, 'index']);

    Route::get('auth/data/{userUuid}', [SystemController::class,'getAuthUserData'])->name('auth-user-data');
});
