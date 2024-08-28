<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\Clubs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClubController extends Controller
{
  public function s2Clubs(Request $request)
  {
    if (!Auth::guard('customer')->check()) {
      return [];
    }

    $term = $request->q;
    $query = Clubs::query()
      ->selectRaw('name as text, id')
      ->where(function ($q) use ($term) {
        $q->where('name', 'like', '%' . $term . '%');
      });

    return $query->get();
  }
}
