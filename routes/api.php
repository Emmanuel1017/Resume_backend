<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RefreshController;
use App\Http\Controllers\Api\TodoController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\ResumeData\AboutController;
use App\Http\Controllers\Api\ResumeData\ExperiencesController;
use App\Http\Controllers\Api\ResumeData\PostsController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::controller(RegisterController::class)
    ->middleware(['api', 'return-json']) // Use our JSON Middleware
    ->group(function () {
        Route::post('register', 'register');
    });
Route::controller(LoginController::class)
    ->middleware(['api', 'return-json']) // Use our JSON Middleware
    ->group(function () {
        Route::post('login', 'login')->name('login');
    });


Route::controller(LogoutController::class)
    ->middleware(['api', 'return-json']) // Use our JSON Middleware
    ->group(function () {
        Route::post('logout', 'logout');
    });

Route::controller(RefreshController::class)
    ->middleware(['api', 'return-json']) // Use our JSON Middleware
    ->group(function () {
        Route::post('refresh', 'refresh');
    });


Route::controller(AboutController::class)
    ->middleware(['api', 'return-json']) // Use our JSON Middleware
    ->group(function () {
        Route::get('about', 'index');
        Route::post('add-about', 'create_item');
    });

Route::controller(ExperiencesController::class)
    ->middleware(['api', 'return-json']) // Use our JSON Middleware
    ->group(function () {
        Route::get('experiences', 'index');
        Route::post('add-experience', 'create_item');
    });

Route::controller(PostsController7::class)
    ->middleware(['api', 'return-json']) // Use our JSON Middleware
    ->group(function () {
        Route::get('posts', 'index');
        Route::post('add-post', 'create_item');
    });
