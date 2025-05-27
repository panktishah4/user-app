<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::post('store',[UserController::class,'store']);
Route::put('update',[UserController::class,'update']);

Route::get('search/users',[UserController::class,'searchUsers']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
