<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\Event\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
  public function s2Tickets(Request $request)
  {
    if (!Auth::guard('customer')->check()) {
      return [];
    }

    if (empty($request->event_id) || empty($request->title_titcket)) {
      return [];
    }

    $gender = '';
    $language = $this->getLanguage();
    $language_id = $language->id;
    $language_code = $language->code;

    if (strtolower($request->title_titcket) == 'individu') {
      if (empty($request->gender)) {
        return [];
      }

      switch (strtolower($request->gender)) {
        case 'm':
          $gender = $language_code == 'en' ? 'Men' : 'putra';
          break;
        case 'f':
          $gender = $language_code == 'en' ? 'Women' : 'putri';
          break;
        case 'female':
          $gender = $language_code == 'en' ? 'Women' : 'putri';
          break;
        default:
          $gender = $language_code == 'en' ? 'Men' : 'putra';
          break;
      }
    }


    $term = $request->q;
    $tickets_list = Ticket::leftJoin('ticket_contents', 'ticket_contents.ticket_id', 'tickets.id')
      ->select('tickets.*', 'ticket_contents.id as content_id', 'ticket_contents.title as contents_title')
      ->where('tickets.event_id', $request->event_id)
      ->where('ticket_contents.language_id', $language_id)
      ->where('status', 1)
      ->where('ticket_available', '>', 0)
      ->where('tickets.title', strtolower($request->title_titcket) == 'mix' ? 'mix team' : $request->title_titcket);

    if ($gender == 'Men') {
      $tickets_list = $tickets_list->where('ticket_contents.title', 'not like', '%' . 'women' . '%');
    } else {
      $tickets_list = $tickets_list->where('ticket_contents.title', 'like', '%' . $gender . '%');
    }

    $tickets_list = $tickets_list
      ->where('ticket_contents.title', 'like', '%' . $term . '%')
      ->get();

    if (empty($tickets_list)) {
      return [];
    }


    // return $tickets_list;

    $sub_category_tickets = [];
    foreach ($tickets_list as $list) {
      $sub_category_tickets[] = [
        "id" => $list->id,
        "title" => $list->contents_title,
        "sub_category_id" => $list->content_id,
        "category_id" => $list->id,
        "category_name" => $list->title,
        'ticket_available' => $list->ticket_available,
        'original_ticket_available' => $list->original_ticket_available,

        // ticket normal rice
        "price_scheme" => $list->pricing_scheme,
        "price" => $list->price,
        "f_price" => $list->f_price,
        "international_price" => $list->international_price,
        "f_international_price" => $list->f_international_price,

        // ticket earlybird
        "early_bird_discount" => $list->early_bird_discount,
        "early_bird_discount_amount" => $list->early_bird_discount_amount,
        "early_bird_discount_amount_international" => $list->early_bird_discount_amount_international,
        "early_bird_discount_international_type" => $list->early_bird_discount_international_type,
        "early_bird_discount_international_date" => $list->early_bird_discount_international_date,
        "early_bird_discount_international_time" => $list->early_bird_discount_international_time,
        "early_bird_discount_international_end_date" => $list->early_bird_discount_international_end_date,
        "early_bird_discount_international_end_time" => $list->early_bird_discount_international_end_time,
        "early_bird_discount_type" => $list->early_bird_discount_type,
        "early_bird_discount_date" => $list->early_bird_discount_date,
        "early_bird_discount_time" => $list->early_bird_discount_time,
        "early_bird_discount_end_date" => $list->early_bird_discount_end_date,
        "early_bird_discount_end_time" => $list->early_bird_discount_end_time,

        // ticket late price
        "late_price_discount" => $list->early_bird_discount_end_time,
        "late_price_discount_amount" => $list->early_bird_discount_end_time,
        "late_price_discount_amount_international" => $list->early_bird_discount_end_time,
        "late_price_discount_international_type" => $list->early_bird_discount_end_time,
        "late_price_discount_international_date" => $list->early_bird_discount_end_time,
        "late_price_discount_international_time" => $list->early_bird_discount_end_time,
        "late_price_discount_international_end_date" => $list->early_bird_discount_end_time,
        "late_price_discount_international_end_time" => $list->early_bird_discount_end_time,
        "late_price_discount_type" => $list->early_bird_discount_end_time,
        "late_price_discount_date" => $list->early_bird_discount_end_time,
        "late_price_discount_time" => $list->early_bird_discount_end_time,
        "late_price_discount_end_date" => $list->early_bird_discount_end_time,
        "late_price_discount_end_time" => $list->early_bird_discount_end_time,
      ];
    }

    return $sub_category_tickets;
  }
}
