<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Exceptions\HttpResponseException;

class HelperUser
{

    public static function AutoGenerateUsername($data){
        try {
            $data['fname'] = 'gina dwitasari;';
            $data['gender'] = 2;
            $data['birthdate'] = '2000/12/12';

            

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


}

