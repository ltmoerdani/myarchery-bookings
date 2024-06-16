<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Participant;

class HelperUser
{

  public static function AutoGenerateUsernameParticipant($data)
  {
    try {
      $checkUser = Participant::where('fname', $data['fname'])->where('lname', $data['lname'])
        ->where('gender', $data['gender'])->where('birthdate', $data['birthdate'])->first();
      if ($checkUser) {
        if (!$checkUser->username) {
          $parts = explode(" ", $data['fname']);
          $lastname = array_pop($parts);
          $random_str = rand(100, 999);
          $username = strtolower($lastname . $random_str . '.' . $parts[0]);

          $checkUsername = Participant::where('username', $username)->first();
          if ($checkUsername) {
            $parts = explode(" ", $data['fname']);
            $lastname = array_pop($parts);
            $random_str = rand(100, 999);
            $username = strtolower($lastname . $random_str . '.' . $parts[0]);
            return $username;
          } else {
            return $username;
          }
        } else {
          return $checkUser->username;
        }
      } else {
        $username = '';
        $random_str = rand(100, 999);

        $parts = explode(" ", $data['fname']);
        if (count($parts) > 1) {
          $lastname = array_pop($parts);
          $username = strtolower($lastname . $random_str . '.' . $parts[0]);
        } else {
          $username = strtolower($data['fname'] . $random_str);
        }

        return $username;
      }
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }
}
