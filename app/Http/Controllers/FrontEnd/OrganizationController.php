<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizationController extends Controller
{
  public function s2Organizations(Request $request)
  {
    if (!Auth::guard('customer')->check()) {
      return [];
    }

    $term = $request->q;
    $query = Organization::query()
      ->selectRaw('name as text, id')
      ->where(function ($q) use ($term) {
        $q->where('name', 'like', '%' . $term . '%');
      });

    return $query->get();
  }
}
