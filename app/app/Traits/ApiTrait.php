<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

trait ApiTrait
{
    public function sendResponse($message, $data = null, $code = 200, $showDataWhenNull = false)
    {
        $response = [
            'code' => $code,
            'message' => $message
        ];

        // Check if the status code is in the 2xx range
        if ($code >= 200 && $code < 300) {
            $response['success'] = true;
        }

        if ($data || $showDataWhenNull) {
            $response['data'] = $data;
        }

        return response()->json($response, 200);
    }

    protected function setCookies($key, $value)
    {
        return Cookie::make(
            $key, // Name
            $value, // Value
            120, // time to expire
            '/', // Path
            config('session.domain'), // Domain
            false, // Secure
            true, // httpOnly
            false, // Raw
            'strict' //same-site   <-----
        );
    }
}
