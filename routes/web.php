<?php

use App\Http\Controllers\ToDoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('to-do-list', [ToDoController::class, 'index'])->name('to-do.index');
Route::post('to-do-list/store', [ToDoController::class, 'store'])->name('to-do.store');
