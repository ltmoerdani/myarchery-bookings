<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Exceptions\HttpResponseException;

class HelperResponse
{

    public static function Success($data = [], $messages = '', $code = 200){
        try {
            $response = ['data' => $data,'message' => $messages, 'code' => $code, 'status' => true];
            return Response($response, $code);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public static function Error($errors = [], $message, $code = 400){
        try {
            $response = ['data' => $errors, 'message' => $message, 'code' => $code, 'status' => false];
            return Response($response, $code);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

}
