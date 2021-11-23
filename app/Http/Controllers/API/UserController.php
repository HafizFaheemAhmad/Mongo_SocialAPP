<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginUserRequest;
use MongoDB\Client as DB;

// use Validator;
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

//For user Login

    public function login(LoginUserRequest $request)
    {
        try {
            $input = $request->validated();
            //check email and password for authentication
            $userr = (new DB)->new->users;
            $user = $userr->findone(['email' => $input['email']]);
            if (!empty($user->email)) {
                if (Hash::check($input['password'], $user->password)) {
                    $user_data = array(
                        "id" => $user->_id->__toString(),
                        "name" => $user->name,
                        "email" => $user->email
                    );
                    $iss = "localhost";
                    $iat = time();
                    $nbf = $iat + 10;
                    $exp = $iat + 1800;
                    $aud = "User";
                    $payload_info = array(
                        "iss" => $iss,
                        "iat" => $iat,
                        "nbf" => $nbf,
                        "exp" => $exp,
                        "aud" => $aud,
                        "data" => $user_data
                    );
                    //generate Token use firebase library
                    $key = 'example';
                    $jwt = JWT::encode($payload_info, $key);
                    $user->jwt_token = $jwt;
                    //update Token in database fieldname "jwt_token"
                    //  $user->update();
                    $userr = (new DB)->new->users;
                    $login = $userr->findone(['email' => $input['email']]);
                    if (!empty($login->email)) {
                        $login = $userr->updateOne(['email' => $input['email']], ['$set' => ["jwt_token" => $jwt]]);
                        //User::where("email", $user->email)->update(["jwt_token" => $jwt]);
                        $success['message'] =  " User Successful login.";
                        $success['Authentication'] = $jwt;
                        return response()->json($success, 200);
                    } else {
                        $success['message'] =  "Something went Worng!";
                        return response()->json($success, 404);
                    }
                } else {
                    $success['message'] =  "password incorrect!";
                    return response()->json($success, 404);
                }
            } else {
                $success['message'] =  "email not register!";
                return response()->json($success, 404);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

//for logout user

    public function logout(Request $request)
    {
        try {
            $token = $request->bearerToken();
            $userr = (new DB)->new->users;
            $decoded_data = JWT::decode($token, new Key('example', 'HS256'));
            if (!empty($decoded_data->data->email)) {
                $login = $userr->updateOne(['email' => $decoded_data->data->email], ['$set' => ["jwt_token" => Null]]);
                //$delete = User::where("jwt_token", $token)->update(["jwt_token" => NULL]);
                if ($login) {
                    $success['message'] =  " User Successful Logout.";
                    return response()->json($success, 200);
                } else {
                    $success['message'] =  "Something went Worng!";
                    return response()->json($success, 404);
                }
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

//For Delete User

    public function DeleteUser(Request $request, $id)
    {
        try {
            // $user = new User();
            // $user = User::find($id);
            $token = $request->bearerToken();
            $userr = (new DB)->new->users;
            $decoded_data = JWT::decode($token, new Key('example', 'HS256'));
            if (!empty($decoded_data->data->id)) {
                $userr = (new DB)->new->users;
                $login = $userr->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($decoded_data->data->id)]);
                if ($login->getDeletedCount() > 0) {
                    //$userr->delete();
                    $success['message'] =  " User Successfully Delete.";
                    return response()->json([$success, 200, "data" => $userr]);
                } else {
                    $success['message'] =  "User not exist";
                    return response()->json([$success, 404, "data" => $userr]);
                }
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

//For Search User

    public function SearchUser($name)
    {
        $userr = (new DB)->new->users;
        $request = $userr->findOne([
            "name" => new \MongoDB\BSON\Regex("a")
        ])->toArray();
        if ($request)
            return response()->json($request, $this->successStatus);
        else {
            $success['message'] =  "Something went Worng!";
        }
    }

//For Update user

    public function UpdateUser(Request $request, $id)
    {
        try {
            //$user = User::find($id);
            $collection = (new DB)->new;
            $user_exist = (array)$collection->users->findOne([
                '_id' => new \MongoDB\BSON\ObjectId("$id")
            ]);
            $user_exist['name'] = $request->input('name');
            $user_exist['email'] = $request->input('email');
            $user_exist['password'] = $request->input('password');
            $input = $request;
            $file_name = null;
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
                $user_exist['profile_image'] = $file_name;
            }
            $update = $collection->users->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId("$id")],
                [
                    '$set' => $user_exist
                ]
            );
            if ($update->getModifiedCount()) {
                $success['message'] =  "User Updated Successfully";
                return response()->json([$success, 200, "data" => $user_exist]);
            } else {
                $success['message'] =  "something went wrong!!!";
                return response()->json([$success, 404, "data" => $user_exist]);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}
