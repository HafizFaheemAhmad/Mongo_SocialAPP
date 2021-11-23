<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;

//Route For Middleware

Route::middleware([JwtAuth::class])->group(function () {

    //Routes for Users

    //Route for logout user
    Route::post('logout', [UserController::class, 'logout']);
    //Route for Update user
    Route::put('updateUser/{id}', [UserController::class, 'UpdateUser']);
    //Route for delete user
    Route::delete('deleteUser/{id}', [UserController::class, 'DeleteUser']);
    //Route for search user
    Route::get('search/{name}', [UserController::class, 'SearchUser']);
});
