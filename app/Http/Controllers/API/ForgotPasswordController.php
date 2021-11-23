<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;
use App\Http\Requests\ForgotRequest;
use MongoDB\Client as DB;

class ForgotPasswordController extends Controller
{

    public function forgotPassword(ForgotRequest $request)
    {
        try {
            $input = $request->validated();
            $user = (new DB)->new->users;
            $forgetpass = $user->findone(['email' => $input['email']]);
            //$user_data = User::where('email', $input['email'])->first();
            $string = "ABC";
            $password = substr(str_shuffle(str_repeat($string, 12)), 0, 12);
            $forgetpass->password = ($password);
            //$forgetpass->save();
            //for generate link in URL
            $details['link'] = url('/' . $forgetpass->password . '/' . $forgetpass->email . '/');
            Mail::to($input['email'])->send(new ForgotPassword($details));
            if ($details) {
                $success['message'] =  "New Password Send to Your Mail";
                return response()->json([$success, 200]);
            } else {
                $success['message'] =  "Something went wrong";
                return response()->json($success, 404);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}
