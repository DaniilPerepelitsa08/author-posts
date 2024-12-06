<?php


use App\Http\Controllers\AuthorController;
use Illuminate\Support\Facades\Route;

Route::get('/get-authors-posts', [AuthorController::class, 'getAuthorsPosts']);
Route::get('/author', [AuthorController::class, 'getAuthor']);
