<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\School;

class SchoolUnivesityController extends Controller
{
  public function s2SchoolUniversity(Request $request)
  {
    if (!Auth::guard('customer')->check()) {
      return [];
    }

    $term = $request->q;
    $query = School::query()
      ->selectRaw('name as text, id')
      ->where('is_active', 1)
      ->where(function ($q) use ($term) {
        $q->where('name', 'like', '%' . $term . '%');
      });

    return $query->get();
  }
}
