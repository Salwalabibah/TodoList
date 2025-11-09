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

Route::get('to-do-list', [ToDoController::class, 'index'])->name('to-do.index');
Route::post('to-do-list/store', [ToDoController::class, 'store'])->name('to-do.store');
Route::get('to-do-list/export', [ToDoController::class, 'export'])->name('to-do.export');
