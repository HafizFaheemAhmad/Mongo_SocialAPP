<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPassword;
use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;

class MailController extends Controller
{
    public function sendMail()
    {
        $details = [
            'title' => "Email Sending",
            'body' => "Body"
        ];
        Mail::to("hafizfaheem034@gmail.com")->send(new ForgotPassword($details));
        return "Email sent successfully!";
    }
}
