<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\EventType;

class StoreCheckoutEventTournamentRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, mixed>
   */
  public function rules()
  {
    $request = $this->request->all();
    $eventInfo = json_decode($request['event_info']);

    $ruleArray = [];

    $getEventType = EventType::where('event_id', $eventInfo->event_id)->first();
    if (!empty($getEventType->code)) {
      $ruleArray['code_access'] = 'required';
    }

    return $ruleArray;
  }
}
