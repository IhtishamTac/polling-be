<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PollController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
    'middleware' => 'auth:api'
], function(){
Route::post('auth/me', [AuthController::class, 'me']);
Route::post('auth/logout', [AuthController::class, 'logout']);
Route::post('auth/reset-password', [AuthController::class, 'rpw']);

Route::post('/poll', [PollController::class, 'addPoll']);
Route::get('/poll', [PollController::class, 'getAllPoll']);
Route::get('/poll/{poll_id}', [PollController::class, 'getPoll']);
Route::delete('/poll/{poll_id}', [PollController::class, 'deletePoll']);
Route::post('/poll/{poll_id}/vote/{choice_id}', [PollController::class, 'vote']);
});
Route::post('auth/login', [AuthController::class, 'login']);
