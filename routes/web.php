<?php

use Illuminate\Support\Facades\Route;
use jeremykenedy\LaravelEmailDatabaseLog\Http\Controllers\EmailLogController;
use jeremykenedy\LaravelEmailDatabaseLog\Http\Middleware\AuthorizeEmailLog;

Route::prefix(config('laravel-email-database-log.path', 'email-log'))
    ->middleware(array_merge(config('laravel-email-database-log.middleware', ['web', 'auth']), [AuthorizeEmailLog::class]))
    ->name('email-log.')
    ->group(function () {
        Route::get('/', [EmailLogController::class, 'index'])->name('index');
        Route::get('/{id}', [EmailLogController::class, 'show'])->where('id', '[0-9]+')->name('show');
    });
