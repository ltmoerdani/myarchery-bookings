<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\DelegationType;
use Illuminate\Http\Request;

class DelegationTypeController extends Controller
{
  public function s2GetDelagationType(Request $request)
  {
    $term = $request->q;
    $query = DelegationType::query()
      ->select('id', 'name')
      ->where(function ($q) use ($term) {
        $q->where('name', 'like', '%' . $term . '%');
      });

    if (empty($query->get())) {
      return [];
    }

    return $query->get()->map(function ($record) {
      return ['id' => $record->name, 'name' => $record->name];
    });
  }
}
