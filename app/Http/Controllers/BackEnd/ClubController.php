<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Clubs;
use App\Models\Language;
use App\Http\Helpers\HelperResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class ClubController extends Controller
{

  public function index(Request $request)
  {
    $language = Language::where('code', $request->language)->firstOrFail();
    $information['language'] = $language;

    $information['clubs'] = Clubs::select('id', 'name', 'logo', 'place_name', 'description', 'address')
      ->orderBy('name', 'asc')
      ->get();

    return view('backend.club.index', $information);
  }

  public function store(Request $request)
  {
    $rules = [
      'name' => 'required|unique:clubs',
      'place_name' => 'required',
      'address' => 'required',
    ];

    $message = [
      'name.required' => 'Name Club is required.',
      'name.unique' => 'Name Club is available.',
      'place_name.required' => 'Place Name Club is required.',
      'address.required' => 'Address Club is required.',
    ];

    $validator = Validator::make($request->all(), $rules, $message);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $in = $request->all();
    $in['logo'] = null;
    $img = $request->file('logo');
    if ($request->hasFile('logo')) {
      $filename = time() . '-club-logo.' . $img->getClientOriginalExtension();
      $directory = public_path('assets/admin/img/club/logo/');
      @mkdir($directory, 0775, true);
      $request->file('logo')->move($directory, $filename);
      $in['logo'] = url('/') . '/assets/admin/img/club/logo/' . $filename;
    }

    Clubs::create($in);
    Session::flash('success', 'Create Successfully');

    return Response::json(['status' => 'success', 'data' => $in], 200);
  }

  public function update(Request $request)
  {
    $rules = [
      'name' => [
        'required',
        Rule::unique('clubs', 'name')->ignore($request->id)
      ],
      'place_name' => 'required',
      'address' => 'required',
    ];

    $message = [
      'name.required' => 'Name Club is required.',
      'name.unique' => 'Name Club is available.',
      'place_name.required' => 'Place Name Club is required.',
      'address.required' => 'Address Club is required.',
    ];

    $validator = Validator::make($request->all(), $rules, $message);
    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $in = $request->all();
    $in['logo'] = null;
    $img = $request->file('logo');
    if ($request->hasFile('logo')) {
      $filename = time() . '-club-logo.' . $img->getClientOriginalExtension();
      $directory = public_path('assets/admin/img/club/logo/');
      @mkdir($directory, 0775, true);
      $request->file('logo')->move($directory, $filename);
      $in['logo'] = url('/') . '/assets/admin/img/club/logo/' . $filename;
    }

    Clubs::find($request->id)->update($in);
    Session::flash('success', 'Updated Successfully');

    return Response::json(['status' => 'success',], 200);
  }

  public function destroy($id)
  {
    Clubs::find($id)->delete();
    return redirect()->back()->with('success', 'Deleted Successfully');
  }

  public function bulkDestroy(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      Clubs::find($id)->delete();
    }

    Session::flash('success', 'Deleted Successfully');

    return Response::json(['status' => 'success'], 200);
  }

  public function getClubs(Request $request)
  {
    $data = Clubs::get();
    return HelperResponse::Success($data, "Get Data Success");
  }
}
