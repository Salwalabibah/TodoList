<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ToDoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

Route::apiResource('to-do-list', ToDoController::class)->only(['index', 'store']);
Route::get('to-do-list/export', [ToDoController::class, 'export'])->name('to-do.export');
Route::get('to-do-list/chart', [ToDoController::class, 'chart'])->name('to-do.chart');
 