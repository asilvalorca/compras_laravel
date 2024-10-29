<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Auth;

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


Route::post('/user', function(){
    return"hola";
})->middleware('jwt.verify');

Route::post('/login2',[AuthController::class, 'login2'])->name("login");
Route::post('/login',[AuthController::class, 'login'])->name("login");
Route::get('/me',[AuthController::class, 'me'])->name("me");

Route::get('/prueba', function(){
    return json_encode(array("prueba" => "hola"));
});
