<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Clubs;
use App\Http\Helpers\HelperResponse;

class ClubController extends Controller
{
    
    public function getClubs(Request $request){
        $data = Clubs::get();
        return HelperResponse::Success($data, "Get Data Success");
    }

}
