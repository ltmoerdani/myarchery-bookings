<?php

namespace App\Http\Controllers\BackEnd\Region;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InternationalStates;
use App\Models\IndonesianProvince;

class RegionController extends Controller{
    
    public function getState(Request $request){
        $id = $request->id;
        if($id == "102"){ //Indonesia
            $state = IndonesianProvince::select('id','name')->get();
        }else{
            $state = InternationalStates::select('id','name')->where('country_id', $id)->get();
        }
        return response()->json($state);
    }

}
