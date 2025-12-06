<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ApiRouteCheck;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => '/v3'], function () {
    Route::get('/home/{company}', function () {
        return view('welcome');
    })->middleware(ApiRouteCheck::class);
});
