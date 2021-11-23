<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use \Firebase\JWT\key;
use App\Models\User;
use MongoDB\Client as DB;


class JwtAuth
{
    public function handle(Request $request, Closure $next)
    {
        //Get token
        $token = request()->bearerToken();

        try {
            JWT::decode($token, new Key('example', 'HS256'));

            $userr = (new DB)->new->users;
            $user_data = $userr->findone(['jwt_token' => $token]);
            //check token is not empty
            if (!$user_data['jwt_token']) {
                $data['message'] = "Your account is logout.Kindly Login first to get access";
                $data['error'] = "Somethng went worng";
                return response()->json($data, 404);
            } else {
                return $next($request);
            }
        } catch (\Exception $ex) {
            $data['error'] = $ex->getMessage();
            $data['message'] = 'Something wrong with Token';
            return response()->json($data, 404);
        }
    }
}
