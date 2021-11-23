<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PostController;

//Route For Middleware
Route::middleware([JwtAuth::class])->group(function () {

    //Routes for Post

    //Route for show posts
    Route::get('/posts', [PostController::class, 'index']);
    //Route for show post top-five
    Route::get('/posts/top-five', [PostController::class, 'indexTopFive']);
    //Route for show post
    Route::get('/posts/{post}', [PostController::class, 'show']);
    //Route for show ALL post
    Route::get('postall', [PostController::class, 'showAll']);
    //Route for save post
    Route::post('/post', [PostController::class, 'store']);
    //Route for update post
    Route::put('update', [PostController::class, 'UpdatePost']);
    //Route for delele post
    Route::delete('delete/{id}', [PostController::class, 'DeletePost']);
});
