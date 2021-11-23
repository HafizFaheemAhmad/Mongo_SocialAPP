<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\FriendController;

//Routes for Comment

Route::middleware([JwtAuth::class])->group(function () {
    //Route for show comment
    Route::get('/comments', [CommentController::class, 'show']);
    //Route for save comment
    Route::post('/comment', [CommentController::class, 'store']);
    //Route for delete comment
    Route::delete('DeleteComment/{id}', [CommentController::class, 'DeleteComment']);
    //Route for update comment
    Route::put('updateComment', [CommentController::class, 'updateComment']);
    //Route for Add Friend
    Route::post('/addfriend', [FriendController::class, 'addFriend']);
    //Route for Delete Friend
    Route::delete('/unfriend', [FriendController::class, 'removeFriend']);
});
