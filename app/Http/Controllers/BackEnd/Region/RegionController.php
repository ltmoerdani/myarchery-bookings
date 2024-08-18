<?php

namespace App\Http\Controllers\BackEnd\Region;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InternationalCountries;
use App\Models\InternationalStates;
use App\Models\InternationalCities;
use App\Models\IndonesianProvince;
use App\Models\IndonesianSubdistrict;
use App\Models\IndonesianCities;
use App\Http\Helpers\HelperResponse;
use App\Http\Helpers\HelperUser;

class RegionController extends Controller
{

  public function getCountry(Request $request)
  {
    $data = InternationalCountries::get();
    return HelperResponse::Success($data, "Get Data Success");
  }

  public function getState(Request $request)
  {
    $id = $request->id;
    if ($id == "102") { //Indonesia
      $data = IndonesianProvince::select('id', 'name')->get();
    } else {
      $data = InternationalStates::select('id', 'name')->where('country_id', $id)->get();
    }
    return HelperResponse::Success($data, "Get Data Success");
  }

  public function getCity(Request $request)
  {
    $id_country = $request->id_country;
    $id_state = $request->id_state;
    if ($id_country == "102") { //Indonesia
      // $data = IndonesianSubdistrict::select('id','name')->where('province_id', $id_state)->get();
      // $data = IndonesianSubdistrict::select('id', 'name');
      $data = IndonesianCities::select('id', 'name');
      if (!empty($id_state)) {
        $data = $data->where('province_id', $id_state);
      }
    } else {
      // $data = InternationalCities::select('id','name')->where('state_id', $id_state)->get();
      $data = InternationalCities::select('id', 'name')->where('country_id', $id_country);
      if (!empty($id_state)) {
        $data =   $data->where('state_id', $id_state);
      }
    }
    return HelperResponse::Success($data->get(), "Get Data Success");
  }

  public function s2GetCountry(Request $request)
  {
    $term = $request->q;
    $query = InternationalCountries::query()
      ->select('id', 'name')
      ->where(function ($q) use ($term) {
        $q->where('name', 'like', '%' . $term . '%');
      });

    return $query->get();
  }

  public function s2GetCity(Request $request)
  {
    $id_country = $request->id_country;
    $id_state = $request->id_state;
    $term = $request->q;

    if (empty($id_country)) {
      return [];
    }

    if ($id_country == "102") {
      $data = IndonesianCities::query()
        ->select('id', 'name');
      if (!empty($id_state)) {
        $data = $data->where('province_id', $id_state);
      }
    } else {
      // $data = InternationalCities::select('id','name')->where('state_id', $id_state)->get();
      $data = InternationalCities::query()
        ->select('id', 'name')
        ->where('country_id', $id_country);
      if (!empty($id_state)) {
        $data =   $data->where('state_id', $id_state);
      }
    }

    $data = $data->where(function ($q) use ($term) {
      $q->where('name', 'like', '%' . $term . '%');
    });

    return $data->get();
  }
}
