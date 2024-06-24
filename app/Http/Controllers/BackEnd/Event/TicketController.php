<?php

namespace App\Http\Controllers\BackEnd\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Event;
use App\Models\Event\EventContent;
use App\Models\Event\Ticket;
use App\Models\Event\TicketVariation;
use App\Http\Requests\Event\TicketRequest;
use App\Models\Event\TicketContent;
use App\Models\Event\VariationContent;
use Illuminate\Support\Facades\Session;

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
      $tickets = Ticket::where('event_id', $request->event_id)->orderBy('id', 'asc')->groupBy('title')->get();
      foreach ($tickets as $key => $ticket) {
        $detailTicket = Ticket::where('event_id', $request->event_id)->where('title', $ticket->title)->get();
        // $price_list = [];
        $ticket_available = [];
        $international_price = [];

        foreach ($detailTicket as $valDetailTicket) {
          // array_push($price_list,  intval($valDetailTicket->price));
          array_push($ticket_available, intval($valDetailTicket->ticket_available));
          array_push($international_price, intval($valDetailTicket->international_price));
        }
        // $tickets[$key]->price = array_sum($price_list);
        $tickets[$key]->ticket_available = array_sum($ticket_available);
        $tickets[$key]->international_price = array_sum($international_price);
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
    return view('backend.event.ticket.edit_tournament', $information);
  }
  // update tournament

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
    $variation = TicketVariation::where('id', $id)->first();
    $variation->delete();
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
}
