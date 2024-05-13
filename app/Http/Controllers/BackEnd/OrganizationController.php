<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organization;
use App\Http\Helpers\HelperResponse;

class OrganizationController extends Controller{

    public function getListOrganization(Request $request){
        $data = Organization::get();
        return HelperResponse::Success($data, "Get Data Success");
    }

}
