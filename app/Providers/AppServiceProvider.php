<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    public function sendResponse($result, $status_code)
    {
        $response = [
            'success' => true,
            'data'    => $result,
        ];
        return response()->json($response, $status_code);
    }

    public function sendError($error, $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];
        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }

    public function boot()
    {
        //
    }
}
