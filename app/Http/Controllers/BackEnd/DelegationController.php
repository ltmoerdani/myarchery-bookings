<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DelegationType;
use App\Http\Helpers\HelperResponse;

class DelegationController extends Controller
{
    
    public function getDelegationType(Request $request){
        $data = DelegationType::get();
        return HelperResponse::Success($data, "Get Data Success");
    }

}
