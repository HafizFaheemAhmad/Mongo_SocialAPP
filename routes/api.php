<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\UserController;

// Route for registration user

Route::post('register', [RegisterController::class, 'register']);
//Route for login user
Route::post('login', [UserController::class, 'login']);
//Route for email verification
Route::get('emailConfirmation/{email}/{token}', [RegisterController::class, 'emailVarify']);
//Route for Forgetpassword
Route::get('/forgotPassword', [ForgotPasswordController::class, 'forgotPassword']);
//Route for verify email test check mail is send to mail trap for testing
Route::get('verifyemail', function () {
    $details = [
        'title' => 'Mail from hafizfaheem',
        'body' => 'This is for testing email using smtp'
    ];
    \Mail::to('hafizfaheem034@gmail.com')->send(new \App\Mail\verifyemail($details));
    dd("Email is Sent.");
});
