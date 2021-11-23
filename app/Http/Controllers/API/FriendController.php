<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Friend;
use \Firebase\JWT\JWT;
use \Firebase\JWT\key;
use MongoDB\Client as DB;

class FriendController extends Controller
{

// Add Friend

    public function addFriend(Request $req)
    {
        $fid = $req->fid;
        $token = request()->bearerToken();
        try {
            $decoded_data = JWT::decode($token, new Key('example', 'HS256'));
            $user = (new DB)->new->users;
            $friend = $user->findone(['jwt_token' => $token]);
            //User::where("jwt_token", $token)->first();
            if ($decoded_data->data->id != $fid) {
                $friend = (new DB)->new->Friend;
                $friend_request_exist = $friend->findOne(
                    ['$or' => [
                        [
                            '$and' =>
                            [
                                ['userid_1' => $decoded_data->data->id], ['userid_2' => $fid]
                            ]
                        ], [
                            '$and' =>
                            [
                                ['userid_2' => $decoded_data->data->id], ['userid_1' => $fid]
                            ]
                        ]
                    ]]
                );

                if (!$friend_request_exist) {
                    if ($friend->insertOne(['userid_1' => $decoded_data->data->id, 'userid_2' => $fid]))
                    // if (Friend::where(['userid_1' => $decoded_data->data->id, 'userid_2' => $fid])
                    //     ->orwhere(['userid_1' => $fid, 'userid_2' => $decoded_data->data->id])
                    // ->doesntExist()
                    {
                        $friend = new Friend;
                        $friend->userid_1 = $decoded_data->data->id;
                        $friend->userid_2 = $fid;
                        //$friend->save();
                        return response()->json(["messsage" => "you are friend now of" . $fid]);
                    } else {
                        return response(["message" => "User with id = " . $fid . " is already your friend"]);
                    }
                } else {
                    return response(["message" => "already your frined"]);
                }
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

//Remove Friend

    public function removeFriend(Request $req)
    {
        try {
            $fid = $req->fid;
            $token = request()->bearerToken();
            $decoded_data = JWT::decode($token, new Key('example', 'HS256'));
            $user = (new DB)->new->Friend;
            $unfriend = $user->deleteOne(
                ['$or' => [
                    [
                        '$and' =>
                        [
                            ['userid_1' => $decoded_data->data->id], ['userid_2' => $fid]
                        ]
                    ], [
                        '$and' =>
                        [
                            ['userid_2' => $decoded_data->data->id], ['userid_1' => $fid]
                        ]
                    ]
                ]]
            );
            if ($unfriend->getDeletedCount()) {
                //User::where("jwt_token", $token)->first();
                // if (Friend::where(['userid_1' => $decoded_data->data->id, 'userid_2' => $fid])->delete()) {
                return response(['Message' => 'Unfriend Successfuly']);
            } else {
                return response(['Message' => 'You are not the friend of ' . $fid]);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}
