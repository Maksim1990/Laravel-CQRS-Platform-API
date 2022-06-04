<?php
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Welcome to Webmastery School Platform API';
})->name('main');


Route::get('/redis', [SystemController::class,'redis'])->name('redis');
Route::get('/redis/{key}', [SystemController::class,'redisKey'])->name('redis-key');
