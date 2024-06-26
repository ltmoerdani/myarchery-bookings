<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class TicketTournamentRequest extends FormRequest
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
    $ruleArray = [
      'ticket_id' => 'required|array',
      'event_id' => 'required',
      'max_ticket_buy_type' => 'required|in:unlimited,limited',
      'early_bird_discount' => 'required|in:disable,enable',
      'early_bird_discount_local_type' => 'required_if:early_bird_discount,enable|in:fixed,percentage',
      'late_price_discount' => 'required|in:disable,enable',
      'late_price_discount_type' => 'required_if:late_price_discount,enable|in:fixed,percentage',
    ];

    if ($this->early_bird_discount == 'enable') {
      if ($this->early_bird_discount_local_type == 'percentage') {
        $ruleArray['early_bird_discount_amount'] = "required|integer|max:100";
      } else {
        $ruleArray['early_bird_discount_amount'] = "required|integer|min:1";
      }

      $ruleArray['early_bird_discount_date'] = "required|date";
      $ruleArray['early_bird_discount_time'] = "required";
      $ruleArray['early_bird_discount_end_date'] = "required|date|after:early_bird_discount_date";
      $ruleArray['early_bird_discount_end_time'] = "required";

      if ($this->pricing_scheme == 'dual_price') {
        $ruleArray['early_bird_discount_international_type'] = 'required|in:fixed,percentage';

        if ($this->early_bird_discount_international_type == 'percentage') {
          $ruleArray['early_bird_discount_amount_international'] = "required|integer|max:100";
        } else {
          $ruleArray['early_bird_discount_amount_international'] = "required|integer|min:1";
        }

        $ruleArray['early_bird_discount_international_date'] = "required|date";
        $ruleArray['early_bird_discount_international_time'] = "required";
        $ruleArray['early_bird_discount_international_end_date'] = "required|date|after:early_bird_discount_international_date";
        $ruleArray['early_bird_discount_international_end_time'] = "required";
      }
    }

    if ($this->late_price_discount == 'enable') {
      if ($this->late_price_discount_type == 'percentage') {
        $ruleArray['late_price_discount_amount'] = "required|integer|max:100";
      } else {
        $ruleArray['late_price_discount_amount'] = "required|integer|min:1";
      }

      $ruleArray['late_price_discount_date'] = "required|date";
      $ruleArray['late_price_discount_time'] = "required";
      $ruleArray['late_price_discount_end_date'] = "required|date|after:late_price_discount_date";
      $ruleArray['late_price_discount_end_time'] = "required";

      if ($this->pricing_scheme == 'dual_price') {
        $ruleArray['late_price_discount_international_type'] = 'required|in:fixed,percentage';

        if ($this->late_price_discount_international_type == 'percentage') {
          $ruleArray['late_price_discount_amount_international'] = "required|integer|max:100";
        } else {
          $ruleArray['late_price_discount_amount_international'] = "required|numeric|min:1";
        }

        $ruleArray['late_price_discount_international_date'] = "required|date";
        $ruleArray['late_price_discount_international_time'] = "required";
        $ruleArray['late_price_discount_international_end_date'] = "required|date|after:late_price_discount_international_date";
        $ruleArray['late_price_discount_international_end_time'] = "required";
      }
    }

    if ($this->max_ticket_buy_type == 'limited') {
      $ruleArray['max_buy_ticket'] = 'required|integer|min:1';
    }

    return $ruleArray;
  }

  public function messages()
  {
    $messageArray = [];

    if ($this->early_bird_discount == 'enable') {
      if ($this->early_bird_discount_local_type == 'percentage') {
        $messageArray['early_bird_discount_amount.max'] = 'The early bird discount amount must not be greater than 100, because you choose Discount Percentage';
      } else {
        $messageArray['early_bird_discount_amount.min'] = 'The early bird discount amount must be at least 1, because you choose Discount Fixed';
      }

      $messageArray['early_bird_discount_date.required'] = 'The early bird start date field is required.';
      $messageArray['early_bird_discount_time.required'] = 'The early bird start time field is required.';


      if ($this->pricing_scheme == 'dual_price') {
        if ($this->early_bird_discount_international_type == 'percentage') {
          $messageArray['early_bird_discount_amount_international.max'] = 'The early bird discount amount must not be greater than 100, because you choose International Discount Percentage';
        } else {
          $messageArray['early_bird_discount_amount_international.min'] = "'The early bird discount amount must be at least 1, because you choose International Discount Fixed'";
        }

        $messageArray['early_bird_discount_international_date.required'] = 'The early bird international start date field is required.';
        $messageArray['early_bird_discount_international_time.required'] = 'The early bird international start time field is required.';
      }
    }

    if ($this->late_price_discount == 'enable') {
      $messageArray['late_price_discount_amount.required'] = 'The late price markup amount field is required.';

      if ($this->early_bird_discount_local_type == 'percentage') {
        $messageArray['late_price_discount_amount.max'] = 'The late price markup amount must not be greater than 100, because you choose Markup Percentage';
      } else {
        $messageArray['late_price_discount_amount.min'] = 'The late price markup amount must be at least 1, because you choose Markup Fixed';
      }

      $messageArray['late_price_discount_date.required'] = 'The late price start date field is required.';
      $messageArray['late_price_discount_time.required'] = 'The late price start time field is required.';
      $messageArray['late_price_discount_end_date.required'] = 'The late price end date field is required.';
      $messageArray['late_price_discount_end_time.required'] = 'The late price end time field is required.';
      $messageArray['late_price_discount_end_date.after'] = 'The late price end date must be a date after late price start date.';



      if ($this->pricing_scheme == 'dual_price') {
        $messageArray['late_price_discount_amount_international.required'] = 'The late price international markup amount field is required.';

        if ($this->late_price_discount_international_type == 'percentage') {
          $messageArray['late_price_discount_amount_international.max'] = 'The late price markup amount must not be greater than 100, because you choose International Markup Percentage';
        } else {
          $messageArray['late_price_discount_amount_international.min'] = "'The late price markup amount must be at least 1, because you choose International Markup Fixed'";
        }

        $messageArray['late_price_discount_international_date.required'] = 'The late price international start date field is required.';
        $messageArray['late_price_discount_international_time.required'] = 'The late price international start time field is required.';
        $messageArray['late_price_discount_international_end_date.required'] = 'The late price international end date field is required.';
        $messageArray['late_price_discount_international_end_time.required'] = 'The late price international end time field is required.';
        $messageArray['late_price_discount_international_end_date.after'] = 'The late price international end date must be a date after late price international start date.';
      }
    }

    if ($this->max_ticket_buy_type == 'limited') {
      $messageArray['max_buy_ticket.required'] = 'Minimum Number Of Tickets For Each Customer is required';
      $messageArray['max_buy_ticket.min'] = 'Minimum Number Of Tickets For Each Customer At Least 1';
    }

    return $messageArray;
  }
}
