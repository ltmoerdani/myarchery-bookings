<?php

namespace App\Http\Controllers\BackEnd\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Ticket\TicketTournamentRequest;
use App\Models\Language;
use App\Models\Event;
use App\Models\Event\EventContent;
use App\Models\Event\Ticket;
use App\Http\Requests\Event\TicketRequest;
use App\Models\Event\TicketContent;
use App\Models\Event\VariationContent;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Event\Booking;

class TicketController extends Controller
{
  public function index(Request $request)
  {
    $languages = Language::all();

    $language = Language::where('code', $request->language)->firstOrFail();
    $information['language'] = $language;
    $event = EventContent::where('event_id', $request->event_id)->where('language_id', $language->id)->first();
    if (empty($event)) {
      $event = EventContent::where('event_id', $request->event_id)->first();
    }

    if (empty($event)) {
      return abort(404);
    }

    $information['event'] = $event;

    if ($request->event_type == 'tournament' || $request->event_type == 'turnamen') {
      $tickets = Ticket::where('event_id', $request->event_id)->inRandomOrder()->groupBy('title')->get();
      foreach ($tickets as $key => $ticket) {
        if (strtolower($ticket->title) == 'official') {
          $detailTicket = Ticket::where('event_id', $request->event_id)
            ->where('title', $ticket->title)
            ->whereNull('competition_id')
            ->get();
        } else {
          $detailTicket = Ticket::where('event_id', $request->event_id)->where('title', $ticket->title)->get();
        }
        $ticket_available = [];
        $international_price = [];
        $local_price = [];

        foreach ($detailTicket as $valDetailTicket) {
          array_push($ticket_available, intval($valDetailTicket->ticket_available));

          if (!in_array($valDetailTicket->international_price, $international_price)) {
            array_push($international_price, intval($valDetailTicket->international_price));
          }

          if (!in_array($valDetailTicket->price, $local_price)) {
            array_push($local_price, intval($valDetailTicket->price));
          }
        }
        $tickets[$key]->ticket_available = empty($ticket_available) ? 0 : implode(", ", $ticket_available);
        $tickets[$key]->international_price = empty($international_price) ? 0 : implode(", ", $international_price);
        $tickets[$key]->local_price = empty($local_price) ? 0 : implode(", ", $local_price);
      };
      $information['tickets'] = $tickets;
      return view('backend.event.ticket.tournament', compact('information', 'languages'));
    } else {
      $tickets = Ticket::where('event_id', $request->event_id)->orderBy('id', 'desc')->get();
      $information['tickets'] = $tickets;
      return view('backend.event.ticket.index', compact('information', 'languages'));
    }
  }

  //create
  public function create(Request $request)
  {
    $information = [];
    $languages = Language::get();
    $language = Language::where('code', $request->language)->firstOrFail();
    $event = EventContent::where('event_id', $request->event_id)->where('language_id', $language->id)->first();
    if (empty($event)) {
      $event = EventContent::where('event_id', $request->event_id)->first();
    }

    $information['languages'] = $languages;
    $eventType = Event::where('id', $request->event_id)->select('event_type')->first();
    $information['eventType'] = $eventType;
    $information['event'] = $event;
    $information['getCurrencyInfo']  = $this->getCurrencyInfo();

    if ($request->event_type == 'tournament') {
      return view('backend.event.ticket.create_tournament', $information);
    } else {
      return view('backend.event.ticket.create', $information);
    }
  }

  //store
  public function store(TicketRequest $request)
  {
    $in = $request->all();
    $in['early_bird_discount'] = $request->early_bird_discount_type;
    $in['early_bird_discount_type'] = $request->discount_type;
    if ($request->pricing_type_2 == 'free') {
      $in['pricing_type'] = 'free';
      $in['price'] = 0;
      $ticket =  Ticket::create($in);
    } elseif ($request->pricing_type_2 == 'normal') {
      $in['pricing_type'] = 'normal';
      $in['price'] = $request->price;
      $in['f_price'] = $request->price;

      if ($request->event_type == "tournament") {
        $in['international_price'] = $request->international_price;
      }

      $ticket =  Ticket::create($in);
    } elseif ($request->pricing_type_2 == 'variation') {
      $in['pricing_type'] = 'variation';
      $f_price = max($request->variation_price);
      $in['f_price'] = $f_price;
      $variations = [];
      $languages = Language::get();
      foreach ($languages as $language) {
        if ($language->is_default == 1) {
          $variation_datas = $request[$language->code . '_variation_name'];
          if (!empty($variation_datas)) {
            foreach ($variation_datas as $key => $varName) {
              $variations[] = [
                'name' => $varName,
                'price' => $request->variation_price[$key],
                'ticket_available_type' => $request->v_ticket_available_type[$key],
                'ticket_available' => $request->v_ticket_available[$key],
                'original_ticket_available' => $request->v_ticket_available[$key],
                'max_ticket_buy_type' => $request->v_max_ticket_buy_type[$key],
                'v_max_ticket_buy' => $request->v_max_ticket_buy[$key]
              ];
            }
          }
        }
      }

      $variations = json_encode($variations);
      $in['variations'] = $variations;
      $ticket = Ticket::create($in);

      if ($request->pricing_type_2 == 'variation') {
        $languages = Language::get();
        foreach ($languages as $language) {
          $variation_datas = $request[$language->code . '_variation_name'];
          foreach ($variation_datas as $key => $data) {
            $variations_data['name'] = $data;
            $variations_data['key'] = $key;
            $variations_data['language_id'] = $language->id;
            $variations_data['ticket_id'] = $ticket->id;
            VariationContent::create($variations_data);
          }
        }
      }
    }

    $languages = Language::all();
    foreach ($languages as $language) {
      $data = [];
      $data['language_id'] = $language->id;
      $data['ticket_id'] = $ticket->id;
      $data['title'] = $request[$language->code . '_title'];
      $data['description'] = $request[$language->code . '_description'];
      TicketContent::create($data);
    }


    Session::flash('success', 'Added Successfully');

    return response()->json(['status' => 'success'], 200);
  }

  //edit
  public function edit(Request $request)
  {
    $languages = Language::get();
    $language = Language::where('code', $request->language)->firstOrFail();
    $information['languages'] = $languages;
    $event = EventContent::where('event_id', $request->event_id)->where('language_id', $language->id)->first();
    if (empty($event)) {
      $event = EventContent::where('event_id', $request->event_id)->first();
    }

    if (empty($event)) {
      return abort(404);
    }

    $information['event'] = $event;
    $ticket = Ticket::where('id', $request->id)->firstOrFail();
    $information['ticket'] = $ticket;
    $information['variations'] = json_decode($ticket->variations, true);
    $information['getCurrencyInfo']  = $this->getCurrencyInfo();
    return view('backend.event.ticket.edit', $information);
  }

  //update
  public function update(TicketRequest $request)
  {
    $in = $request->all();
    $in['early_bird_discount'] = $request->early_bird_discount_type;
    $in['early_bird_discount_type'] = $request->discount_type;
    $in['ticket_available'] = $request->ticket_available_type == 'limited' ? $request->ticket_available : null;

    $in['max_buy_ticket'] = $request->max_ticket_buy_type == 'limited' ? $request->max_buy_ticket : null;

    if ($request->pricing_type_2 == 'free') {
      $in['pricing_type'] = 'free';
      $in['price'] = 0;
      $ticket =  Ticket::where('id', $request->ticket_id)->first();
      $ticket->update($in);
    } elseif ($request->pricing_type_2 == 'normal') {
      $in['pricing_type'] = 'normal';
      $in['price'] = $request->price;
      $in['f_price'] = $request->price;

      if ($request->event_type == "tournament") {
        $in['international_price'] = $request->international_price;
      }

      $ticket =  Ticket::where('id', $request->ticket_id)->first();
      $ticket->update($in);
    } elseif ($request->pricing_type_2 == 'variation') {
      $in['pricing_type'] = 'variation';
      $ticket =  Ticket::where('id', $request->ticket_id)->first();

      $languages = Language::get();
      $variations = [];
      foreach ($languages as $language) {
        if ($language->is_default == 1) {
          $variation_datas = $request[$language->code . '_variation_name'];
          if (!empty($variation_datas)) {
            foreach ($variation_datas as $key => $varName) {
              $variations[] = [
                'name' => $varName,
                'price' => $request->variation_price[$key],
                'ticket_available_type' => $request->v_ticket_available_type[$key],
                'ticket_available' => $request->v_ticket_available[$key],
                'max_ticket_buy_type' => $request->v_max_ticket_buy_type[$key],
                'v_max_ticket_buy' => $request->v_max_ticket_buy[$key]
              ];
            }
          }
        }
      }

      $variations = json_encode($variations);
      $in['variations'] = $variations;
      $ticket->update($in);
      $languages = Language::get();
      foreach ($languages as $language) {
        $variation_datas = $request[$language->code . '_variation_name'];
        $variation_contents = VariationContent::where([['language_id', $language->id], ['ticket_id', $ticket->id]])->get();
        foreach ($variation_contents as $key => $variation_content) {
          $variation_content->delete();
        }

        foreach ($variation_datas as $key => $data) {
          $variations_data['name'] = $data;
          $variations_data['key'] = $key;
          $variations_data['language_id'] = $language->id;
          $variations_data['ticket_id'] = $ticket->id;
          VariationContent::create($variations_data);
        }
      }
    }

    $languages = Language::all();
    foreach ($languages as $language) {
      $ticket_content = TicketContent::where([['language_id', $language->id], ['ticket_id', $ticket->id]])->first();
      if (empty($ticket_content)) {
        $ticket_content = new TicketContent();
        $ticket_content->language_id = $language->id;
        $ticket_content->ticket_id = $ticket->id;
      }
      $ticket_content->title = $request[$language->code . '_title'];
      $ticket_content->description = $request[$language->code . '_description'];
      $ticket_content->save();
    }

    Session::flash('success', 'Updated Successfully');

    return response()->json(['status' => 'success'], 200);
  }

  // edit tournament
  public function editTournament(Request $request)
  {
    $languages = Language::get();
    $information['languages'] = $languages;
    $event = EventContent::where('event_id', $request->event_id)->first();

    if (empty($event)) {
      return abort(404);
    }

    $information['event'] = $event;
    $tickets = Ticket::where('title', $request->title)->where('event_id', $request->event_id)->get();

    $ticket_info = [];
    foreach ($tickets as $ticket) {
      $ticket_contents = TicketContent::where('ticket_id', $ticket->id)->get();
      $detail_ticket_content = [];
      if (!empty($ticket_contents)) {
        foreach ($ticket_contents as $ticket_content) {
          $language = Language::find($ticket_content->language_id);
          $ticket_content->language_code = $language->code;
          array_push($detail_ticket_content, $ticket_content);
        }
      }

      if ($ticket->pricing_scheme == 'dual_price') {
        $ticket->use_default_price = $ticket->price != $ticket->f_price || $ticket->international_price != $ticket->f_international_price ? false : true;
      } else {
        $ticket->use_default_price = $ticket->price != $ticket->f_price ? false : true;
      }

      $ticket->ticket_content = $detail_ticket_content;
      array_push($ticket_info, $ticket);
    }

    $information['list_ticket'] = $ticket_info;
    $information['ticket'] = Ticket::where('title', $request->title)
      ->where('event_id', $request->event_id)
      ->first();

    $information['getCurrencyInfo']  = $this->getCurrencyInfo();
    dd($information);
    return view('backend.event.ticket.edit_tournament', $information);
  }

  // update tournament
  public function updateTournament(TicketTournamentRequest $request)
  {
    try {
      if (empty(Auth::guard('admin'))) {
        return Response(
          [
            'errors' => [
              'message' => [
                'Update Error, because not have sessions login'
              ]
            ]
          ],
          401
        );
      }

      $checkEvent = Event::where('id', $request->event_id)->first();
      if (empty($checkEvent)) {
        return Response([
          'errors' => [
            'message' => [
              'Update Error, Because event not found!'
            ]
          ]
        ], 404);
      }

      // $check_have_a_bookings = Booking::where('event_id', $request->event_id)
      //   ->whereIn('paymentStatus', ['completed', 'pending'])
      //   ->get()
      //   ->count();

      // if ($check_have_a_bookings > 0) {
      //   return Response([
      //     'errors' => [
      //       'message' => [
      //         'Update Error, Because the event already has participants who have booked'
      //       ]
      //     ]
      //   ], 403);
      // }

      $checkTicket = Ticket::where('event_id', $request->event_id)
        ->whereIn('id', $request->ticket_id)
        ->doesntExist();

      if ($checkTicket) {
        return Response([
          'errors' => [
            'message' => [
              'Update Error, Because list category ticket not found!'
            ]
          ]
        ], 404);
      }

      DB::transaction(function () use ($request) {
        foreach ($request->ticket_id as $ticket_id) {
          $price = 0;
          $international_price = 0;

          if (!empty($request->use_default_price)) {
            if (!empty($request->use_default_price[$ticket_id])) {
              $price = empty($request->f_price) ? 0 : $request->f_price;
              $international_price = empty($request->f_international_price) ? 0 : $request->f_international_price;
            } else {
              $price = empty($request->ticket_price_local[$ticket_id]) ? 0 : $request->ticket_price_local[$ticket_id];
              $international_price = empty($request->ticket_price_international[$ticket_id]) ? 0 : $request->ticket_price_international[$ticket_id];
            }
          } else {
            $price = empty($request->ticket_price_local[$ticket_id]) ? 0 : $request->ticket_price_local[$ticket_id];
            $international_price = empty($request->ticket_price_international[$ticket_id]) ? 0 : $request->ticket_price_international[$ticket_id];
          }

          $ticket = Ticket::find($ticket_id);
          $ticket->f_price = empty($request->f_price) ? 0 : $request->f_price;
          $ticket->f_international_price = empty($request->f_international_price) ? 0 : $request->f_international_price;
          $ticket->ticket_available_type = 'limited';
          $ticket->ticket_available = empty($request->ticket_available[$ticket_id]) ? 0 : $request->ticket_available[$ticket_id];
          $ticket->original_ticket_available = empty($request->ticket_available[$ticket_id]) ? 0 : $request->ticket_available[$ticket_id];
          $ticket->price = $price;
          $ticket->international_price = $international_price;
          $ticket->max_ticket_buy_type = $request->max_ticket_buy_type;
          $ticket->max_buy_ticket = strtolower($request->max_ticket_buy_type) == 'unlimited' ? null : $request->max_buy_ticket;

          // early bird local
          $ticket->early_bird_discount = $request->early_bird_discount;
          $ticket->early_bird_discount_amount = empty($request->early_bird_discount_amount) ? null : $request->early_bird_discount_amount;
          $ticket->early_bird_discount_type = $request->early_bird_discount_local_type;
          $ticket->early_bird_discount_date = $request->early_bird_discount_date;
          $ticket->early_bird_discount_time = $request->early_bird_discount_time;
          $ticket->early_bird_discount_end_date = $request->early_bird_discount_end_date;
          $ticket->early_bird_discount_end_time = $request->early_bird_discount_end_time;

          // late price local
          $ticket->late_price_discount = $request->late_price_discount;
          $ticket->late_price_discount_type = $request->late_price_discount_type;
          $ticket->late_price_discount_amount = $request->late_price_discount_amount;
          $ticket->late_price_discount_date = $request->late_price_discount_date;
          $ticket->late_price_discount_time = $request->late_price_discount_time;
          $ticket->late_price_discount_end_date = $request->late_price_discount_end_date;
          $ticket->late_price_discount_end_time = $request->late_price_discount_end_time;

          // for early bird and late price international
          if ($request->pricing_scheme == 'dual_price') {
            $ticket->early_bird_discount_amount_international = empty($request->early_bird_discount_amount_international) ? null : $request->early_bird_discount_amount_international;
            $ticket->early_bird_discount_international_type = $request->early_bird_discount_international_type;
            $ticket->early_bird_discount_international_date = $request->early_bird_discount_international_date;
            $ticket->early_bird_discount_international_time = $request->early_bird_discount_international_time;
            $ticket->early_bird_discount_international_end_date = $request->early_bird_discount_international_end_date;
            $ticket->early_bird_discount_international_end_time = $request->early_bird_discount_international_end_time;

            $ticket->late_price_discount_international_type = $request->late_price_discount_international_type;
            $ticket->late_price_discount_amount_international = empty($request->late_price_discount_amount_international) ? 0 : $request->late_price_discount_amount_international;
            $ticket->late_price_discount_international_date = $request->late_price_discount_international_date;
            $ticket->late_price_discount_international_time = $request->late_price_discount_international_time;
            $ticket->late_price_discount_international_end_date = $request->late_price_discount_international_end_date;
            $ticket->late_price_discount_international_end_time = $request->late_price_discount_international_end_time;
          } else {
            $ticket->early_bird_discount_amount_international = null;
            $ticket->early_bird_discount_international_type = null;
            $ticket->early_bird_discount_international_date = null;
            $ticket->early_bird_discount_international_time = null;
            $ticket->early_bird_discount_international_end_date = null;
            $ticket->early_bird_discount_international_end_time = null;

            $ticket->late_price_discount_international_type = null;
            $ticket->late_price_discount_amount_international = null;
            $ticket->late_price_discount_international_date = null;
            $ticket->late_price_discount_international_time = null;
            $ticket->late_price_discount_international_end_date = null;
            $ticket->late_price_discount_international_end_time = null;
          }

          $ticket->save();
        }
      });
      Session::flash('success', 'Updated Successfully');
      return response()->json(['status' => 'success'], 200);
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  //destroy
  public function destroy(Request $request)
  {
    $ticket = Ticket::where('id', $request->id)->first();
    $ticket_contents = TicketContent::where('ticket_id', $ticket->id)->get();
    $variation_contents = VariationContent::where('ticket_id', $ticket->id)->get();
    if (count($ticket_contents) > 0) {
      foreach ($ticket_contents as $ticket_content) {
        $ticket_content->delete();
      }
    }
    if (count($variation_contents) > 0) {
      foreach ($variation_contents as $variation_content) {
        $variation_content->delete();
      }
    }
    $ticket->delete();
    return redirect()->back()->with('success', 'Ticket deleted successfully!');
  }

  //delete_variation
  public function delete_variation($id)
  {
    // $variation = TicketVariation::where('id', $id)->first();
    // $variation->delete();
    return 'success';
  }

  //bulk_delete
  public function bulk_delete(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $ticket = Ticket::find($id);

      $ticket->delete();
    }
    Session::flash('success', 'Deleted Successfully');
    return response()->json(['status' => 'success'], 200);
  }

  // update status ticket tournament
  public function edit_status_ticket_tournament(Request $request)
  {
    $ticket = Ticket::find($request->id);
    Ticket::query()->where('title', $ticket->title)->update(['status' => $request->status]);

    return redirect()->back()->with('success', 'update status ticket: ' . $ticket->title . ' successfully!');
  }
}
