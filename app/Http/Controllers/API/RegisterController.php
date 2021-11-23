<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use MongoDB\Client as DB;

class RegisterController extends Controller
{

//For user Registration

    public function register(RegisterUserRequest $request)
    {
        try {
            //generate email verification token
            $input = $request->validated();
            $file_name = null;
            //converting base64 decoded image to simple image if exist
            if (!empty($input['attachment'])) {
                // upload Attachment
                $destinationPath = storage_path('\api\users\\');
                $input_type_aux = explode("/", $input['attachment']['mime']);
                $attachment_extention = $input_type_aux[1];
                $image_base64 = base64_decode($input['attachment']['data']);
                $file_name = $input['name'] . uniqid() . '.' . $attachment_extention;
                $file = $destinationPath . $file_name;
                // saving in local storage
                file_put_contents($file, $image_base64);
            }
            $email_varified_token = base64_encode($input['name']);
            $input['varified_token'] = $email_varified_token;
            $input['password'] = bcrypt($input['password']);
            $input['profile_image'] = $file_name;
            $user = (new DB)->new->users;
            $email_exist = $user->findone(['email' => $input['email']]);
            if (empty($email_exist->email)) {
                $user = (new DB)->new->users->insertone([
                    "name" => $input["name"],
                    "email" => $input["email"],
                    "password" => $input["password"],
                    "varified_token" => $email_varified_token
                ]);
                $object_id = $user->GetInsertedId()->__toString();
                // $user = User::create($input);
                //generate URL link
                $details['link'] = url('api/emailConfirmation/' . $input["email"] . '/' . $email_varified_token);
                //send link to mailtrap
                \Mail::to($request['email'])->send(new \App\Mail\verifyemail($details));

                $success['name'] =  $input["name"];
                if ($success) {
                    $success['message'] =  " User Successful Register.";
                    $success['UserId'] =  $object_id;
                    return response()->json($success, 200);
                } else {
                    $success['message'] =  "Something went Worng!";
                    return response()->json($success, 404);
                }
            } else {
                $success['message'] =  "Email already exit";
                return response()->json($success, 404);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

//For email verification on registration

    public function emailVarify($email, $token)

    {
        try {
            $user = (new DB)->new->users;
            $email_exist = $user->findOne(["email" => $email, "varified_token" => $token]);
            // $user = User::where('email', $email)->where('varified_token', $token)->first();
            //check user is not empty
            if (!empty($email_exist->email)) {
                //get time of confirm verification
                $email_verified_at = date('Y-m-d h:i:s');
                $varified_token = '';
                //save confirmation time in database filed name "email_verified_at"
                $email_verify = $user->updateOne(
                    ['email' => $email],
                    ['$set' => ["email_verified_at" => $email_verified_at, "varified_token" => null]]
                );
                //$email_verify->save();
                //send responsed if Email link is confirm
                if ($email_verify->getModifiedCount() > 0) {
                    $success['message'] =  " Your Email is confirm.Now you are successfully Register";
                    return response()->json($success, 200);
                } else {
                    $success['message'] =  " Something went worng !! ";
                    return response()->json($success, 500);
                }
            } else {
                //send responsed if Email link  is already use
                $success['message'] =  "'Unauthorised.', ['error' => 'Link already used', 'detail' => 'this link already in use please create anotherone'";
                return response()->json($success, 404);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}
