<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\BasicSettings\Basic;
use App\Models\Country;
use App\Models\PaymentGateway\OfflineGateway;
use App\Models\PaymentGateway\OnlineGateway;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Event\EventContent;
use App\Models\Event\Ticket;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventPublisher;
use App\Models\Event\TicketContent;
use App\Models\Language;
use App\Models\Admin;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\ParticipantCompetitions;
use App\Models\InternationalCountries;
use App\Models\IndonesianSubdistrict;
use App\Models\InternationalCities;
use App\Models\DelegationType;
use App\Models\ContingentType;
use App\Models\Clubs;
use Carbon\Carbon;

class CheckOutController extends Controller
{
  //checkout
  public function detailCheckout2Tournament(Request $request)
  {
    try {
      $basic = Basic::select('event_guest_checkout_status')->first();
      $event_guest_checkout_status = $basic->event_guest_checkout_status;
      
      if ($event_guest_checkout_status != 1) {
        if (!Auth::guard('customer')->user()) {
          return redirect()->route('customer.login', ['redirectPath' => 'event_checkout']);
        }
      }
      $select = false;
      $event_type = Event::where('id', $request->event_id)->select('event_type')->first();

      $information = [];
      $information['selTickets'] = '';
      $event = Event::where('id', $request->event_id)->select('event_type')->first();

      $event =  EventContent::join('events', 'events.id', 'event_contents.event_id')
        ->where('events.id', $request->event_id)
        ->select('events.*', 'event_contents.*')
        ->first();

      Session::put('event', $event);
      $online_gateways = OnlineGateway::where('status', 1)->get();
      $offline_gateways = OfflineGateway::where('status', 1)->orderBy('serial_number', 'asc')->get();
      Session::put('online_gateways', $online_gateways);
      Session::put('offline_gateways', $offline_gateways);
      Session::put('event_date', $request->event_date);

      //check customer logged in or not ?
      if (Auth::guard('customer')->check() == false) {
        return redirect()->route('customer.login', ['redirectPath' => 'event_checkout']);
      }

      $information['customer'] = Auth::guard('customer')->user();
      $information['event'] = $event;
      $information['re_event_id'] = $request->event_id;
      
      $information['organizer'] = Organizer::join('organizer_infos', 'organizer_infos.organizer_id', 'organizers.id')
        ->where('organizers.id', '=', $event->organizer_id)
        ->select('organizers.id', 'organizers.email', 'organizers.phone', 'organizer_infos.*')
        ->first();
      // Jika diinput bukan oleh organizer
      if (!$information['organizer']) {
        $information['organizer'] = Admin::select('id', 'first_name as name', 'email', 'phone')->first();
      }

      $information['from_step_one'] = $request->all();

      $name_individu = $request->name_individu;
      foreach ($name_individu as $key => $value) {
        $name[$key] = $value;
      }

      $gender_individu = $request->gender_individu;
      foreach ($gender_individu as $key => $value) {
        $gender[$key] = $value;
      }

      $birth_date_individu = $request->birth_date_individu;
      foreach ($birth_date_individu as $key => $value) {
        $birthdate[$key] = $value;
      }

      $profile_country_individu = $request->profile_country_individu;
      foreach ($profile_country_individu as $key => $value) {
        $country[$key] = $value;
      }

      $profile_city_individu = $request->profile_city_individu;
      foreach ($profile_city_individu as $key => $value) {
        $city[$key] = $value;
      }

      $delegation_individu = $request->delegation_individu;
      foreach ($delegation_individu as $key => $value) {
        $delegation[$key] = $value;
      }

      $club_delegation_individu = $request->club_delegation_individu;
      if ($club_delegation_individu) {
        foreach ($club_delegation_individu as $key => $value) {
          $club[$key] = $value;
        }
      }

      $school_delegation_individu = $request->school_delegation_individu;
      if ($school_delegation_individu) {
        foreach ($school_delegation_individu as $key => $value) {
          $school[$key] = $value;
        }
      }

      $organization_delegation_individu = $request->organization_delegation_individu;
      if ($organization_delegation_individu) {
        foreach ($organization_delegation_individu as $key => $value) {
          $organization[$key] = $value;
        }
      }

      $category_individu = $request->category_individu;
      $code_access = $request->code_access;

      $category_ticket = array();
      $categorytickets = array('title' => null, 'quantity' => null, 'price' => null);
      foreach ($category_individu as $k => $v) {
        //Get country
        $country_name = InternationalCountries::where('id', $country[$k])->first();

        //Get city
        if ($country[$k] == "102") { //Indonesia
          $city_name = IndonesianSubdistrict::select('id', 'name')->where('province_id', $city[$k])->first();
        } else {
          $city_name = InternationalCities::select('id', 'name')->where('country_id', $country[$k])->where('state_id', $city[$k])->first();
        }

        $ticket = Ticket::where('id', $v)->first();
        $tickets = TicketContent::where('ticket_id', $v)->where('language_id', 8)->first();

        if ($categorytickets['title'] != $tickets->title) {
          unset($categorytickets);
          $categorytickets = array('title' => $tickets->title, 'quantity' => 0, 'price' => 0);
          $category_ticket[] = &$categorytickets;
        }
        $categorytickets['price'] = $categorytickets['price'] + $ticket->price;
        $categorytickets['quantity']++;

        if($club_delegation_individu){
          $club_name = Clubs::where('id', $club[$k])->first();
        }
        
        $ticket_detail_order[] = [
          "id" => $v,
          "user_full_name" => $name[$k],
          "user_gender" => $gender[$k],
          "delegation_type" => $delegation[$k],
          "country_id" => empty($country[$k]) ? null : $country[$k],
          "country_name" => empty($country_name->name) ? null : $country_name->name,
          "province_id" => empty($state[$k]) ? null : $state[$k],
          "province_name" => empty($state_name->name) ? null : $state_name->name,
          "city_id" => empty($city[$k]) ? null : $city[$k],
          "city_name" => empty($city_name->name) ? null : $city_name->name,
          "club_id" => empty($club[$k]) ? null : $club[$k],
          "club_name" => empty($club_name->name) ? null : $club_name->name,
          "school_name" => empty($school[$k]) ? null : $school[$k],
          "organization_name" => empty($organization[$k]) ? null : $organization[$k],
          "sub_category_ticket_id" => $v,
          "sub_category_ticket" => $tickets->title
        ];
      }

      $orders[] = [
        "title" => $v,
        "category" => 'individu',
        "ticket_detail_order" => $ticket_detail_order
      ];

      $information['ticket_infos'] = $category_ticket;
      $information['orders'] = $orders;

      return view('frontend.event.event-tournament-checkout-detail', $information);
    } catch (\Exception $e) {
      dd($e);
    }
  }

  public function checkout2Tournament(Request $request)
  {
    $basic = Basic::select('event_guest_checkout_status')->first();
    $event_guest_checkout_status = $basic->event_guest_checkout_status;
    if ($event_guest_checkout_status != 1) {
      if (!Auth::guard('customer')->user()) {
        return redirect()->route('customer.login', ['redirectPath' => 'event_checkout']);
      }
    }
    $select = false;
    $event_type = Event::where('id', $request->event_id)->select('event_type')->first();
    if ($event_type->event_type == 'venue') {
      foreach ($request->quantity as $qty) {
        if ($qty > 0) {
          $select = true;
          break;
        }
        continue;
      }
    } else {
      if ($request->pricing_type == 'free') {
        $select = true;
      } elseif ($request->pricing_type == 'normal') {
        if ($request->quantity == 0) {
          $select = false;
        } else {
          $select = true;
        }
      } else {
        foreach ($request->quantity as $qty) {
          if ($qty > 0) {
            $select = true;
            break;
          }
          continue;
        }
      }
    }


    if ($select == false) {
      return back()->with(['alert-type' => 'error', 'message' => 'Please Select at least one ticket']);
    }

    $information = [];
    $information['selTickets'] = '';
    $event = Event::where('id', $request->event_id)->select('event_type')->first();

    $event =  EventContent::join('events', 'events.id', 'event_contents.event_id')
      ->where('events.id', $request->event_id)
      ->select('events.*', 'event_contents.*')
      ->first();

    Session::put('event', $event);
    $online_gateways = OnlineGateway::where('status', 1)->get();
    $offline_gateways = OfflineGateway::where('status', 1)->orderBy('serial_number', 'asc')->get();
    Session::put('online_gateways', $online_gateways);
    Session::put('offline_gateways', $offline_gateways);
    Session::put('event_date', $request->event_date);

    //check customer logged in or not ?
    if (Auth::guard('customer')->check() == false) {
      return redirect()->route('customer.login', ['redirectPath' => 'event_checkout']);
    }

    if ($request->event_type == 'tournament') {
      $information['customer'] = Auth::guard('customer')->user();
      $information['event'] = $event;
      $information['organizer'] = Organizer::join('organizer_infos', 'organizer_infos.organizer_id', 'organizers.id')
        ->where('organizers.id', '=', $event->organizer_id)
        ->select('organizers.id', 'organizers.email', 'organizers.phone', 'organizer_infos.*')
        ->first();

      // Jika diinput bukan oleh organizer
      if (!$information['organizer']) {
        $information['organizer'] = Admin::select('id', 'first_name as name', 'email', 'phone')->first();
      }

      $information['from_step_one'] = $request->all();

      $contingent_type = ContingentType::where('event_id', $request->event_id)->first();
      $information['delegation_event'] = $contingent_type->toArray();

      $tickets = $request->category_ticket;
      $quantity = $request->quantity;
      foreach ($tickets as $key => $value) {
        $ticket[$key] = $value;
      }

      foreach($quantity as $k => $v){
        $category_ticket[] = [
          "id" => $k,
          'name' => $ticket[$k],
          "quantity" => $v
        ];
      }
      $information['category_tickets'] = $category_ticket;

      $tickets_list = Ticket::leftjoin('ticket_contents', 'ticket_contents.ticket_id', 'tickets.id')
        ->select('tickets.*', 'ticket_contents.title as contents_title')
        ->where('ticket_contents.language_id', 8)
        ->where('tickets.event_id', $request->event_id)->where('tickets.title', 'Individu')->get();

      foreach ($tickets_list as $list) {
        $sub_category_tickets[] = [
          "id" => $list->id,
          "title" => $list->contents_title,
          "category_id" => 1,
          "category_name" => $list->title,
          "price" => $list->price,
          "available_qouta" => $list->ticket_available
        ];
      }
      
      $information['sub_category_tickets'] = $sub_category_tickets;

      $information['delegation_type'] = DelegationType::get()->toArray();
      $information['countries'] = InternationalCountries::get()->toArray();
      return view('frontend.event.event-form-order-detail', $information);
    }
    return redirect()->route('check-out');
  }

  public function checkout2(Request $request)
  {
    $basic = Basic::select('event_guest_checkout_status')->first();
    $event_guest_checkout_status = $basic->event_guest_checkout_status;
    if ($event_guest_checkout_status != 1) {
      if (!Auth::guard('customer')->user()) {
        return redirect()->route('customer.login', ['redirectPath' => 'event_checkout']);
      }
    }
    $select = false;
    $event_type = Event::where('id', $request->event_id)->select('event_type')->first();
    if ($event_type->event_type == 'venue') {
      foreach ($request->quantity as $qty) {
        if ($qty > 0) {
          $select = true;
          break;
        }
        continue;
      }
    } else {
      if ($request->pricing_type == 'free') {
        $select = true;
      } elseif ($request->pricing_type == 'normal') {
        if ($request->quantity == 0) {
          $select = false;
        } else {
          $select = true;
        }
      } else {
        foreach ($request->quantity as $qty) {
          if ($qty > 0) {
            $select = true;
            break;
          }
          continue;
        }
      }
    }


    if ($select == false) {
      return back()->with(['alert-type' => 'error', 'message' => 'Please Select at least one ticket']);
    }

    $information = [];
    $information['selTickets'] = '';
    $event = Event::where('id', $request->event_id)->select('event_type')->first();

    $check = false;

    if ($event->event_type == 'online') {
      //**************** stock check start *************** */
      $stock = StockCheck($request->event_id, $request->quantity);
      if ($stock == 'error') {
        $check = true;
      }

      //*************** stock check end **************** */

      if ($request->pricing_type == 'normal') {

        $price = Ticket::where('event_id', $request->event_id)->select('price', 'early_bird_discount', 'early_bird_discount_amount', 'early_bird_discount_type', 'early_bird_discount_date', 'early_bird_discount_time', 'ticket_available', 'ticket_available_type', 'max_ticket_buy_type', 'max_buy_ticket')->first();
        $information['quantity'] = $request->quantity;
        $total = $request->quantity * $price->price;

        //check guest checkout status enable or not
        if ($event_guest_checkout_status != 1) {
          //check max buy by customer
          $max_buy = isTicketPurchaseOnline($request->event_id, $price->max_buy_ticket);
          if ($max_buy['status'] == 'true') {
            $check = true;
          } else {
            $check = false;
          }
        } else {
          $check = false;
        }



        if ($price->early_bird_discount == 'enable') {

          $start = Carbon::parse($price->early_bird_discount_date . $price->early_bird_discount_time);
          $end = Carbon::parse($price->early_bird_discount_date . $price->early_bird_discount_time);
          $today = Carbon::now();
          if ($today <= ($end)) {
            if ($price->early_bird_discount_type == 'fixed') {
              $early_bird_dicount = $price->early_bird_discount_amount;
            } else {
              $early_bird_dicount = ($price->early_bird_discount_amount * $total) / 100;
            }
          } else {
            $early_bird_dicount = 0;
          }
        } else {
          $early_bird_dicount = 0;
        }

        Session::put('total_early_bird_dicount', $early_bird_dicount * $request->quantity);
        $information['total'] = $total;
        Session::put('total', $total);
        Session::put('sub_total', $total);
        Session::put('quantity', $request->quantity);
      } elseif ($request->pricing_type == 'free') {
        $price = Ticket::where('event_id', $request->event_id)->select('max_buy_ticket')->first();
        //check guest checkout status enable or not
        if ($event_guest_checkout_status != 1) {
          //check max buy by customer
          $max_buy = isTicketPurchaseOnline($request->event_id, $price->max_buy_ticket);
          if ($max_buy['status'] == 'true') {
            $check = true;
          }
        }

        $information['quantity'] = $request->quantity;
        $information['total'] = 0;
        Session::put('total', 0);
        Session::put('sub_total', 0);
        Session::put('quantity', $request->quantity);
      }
    } else {
      $tickets = Ticket::where('event_id', $request->event_id)->select('id', 'title', 'pricing_type', 'price', 'variations', 'early_bird_discount', 'early_bird_discount_amount', 'early_bird_discount_type', 'early_bird_discount_date', 'early_bird_discount_time')->get();
      $ticketArr = [];

      foreach ($tickets as $key => $ticket) {

        if ($ticket->pricing_type == 'variation') {
          $varArr1 = json_decode($ticket->variations, true);
          foreach ($varArr1 as $key => $var1) {

            $stock[] = [
              'name' => $var1['name'],
              'price' => $var1['price'],
              'ticket_available' => $var1['ticket_available'] - $request->quantity[$key],
            ];

            //check early_bird discount
            if ($ticket->early_bird_discount == 'enable') {

              $start = Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
              $end = Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
              $today = Carbon::now();
              if ($today <= ($end)) {
                if ($ticket->early_bird_discount_type == 'fixed') {
                  $early_bird_dicount = $ticket->early_bird_discount_amount;
                } else {
                  $early_bird_dicount = ($var1['price'] * $ticket->early_bird_discount_amount) / 100;
                }
              } else {
                $early_bird_dicount = 0;
              }
            } else {
              $early_bird_dicount = 0;
            }

            $var1['type'] = $ticket->pricing_type;
            $var1['early_bird_dicount'] = $early_bird_dicount;
            $var1['ticket_id'] = $ticket->id;

            $ticketArr[] = $var1;
          }
          Session::put('stock', $stock);
        } elseif ($ticket->pricing_type == 'normal') {

          //check early_bird discount
          if ($ticket->early_bird_discount == 'enable') {

            $start = Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
            $end = Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
            $today = Carbon::now();
            if ($today <= ($end)) {
              if ($ticket->early_bird_discount_type == 'fixed') {
                $early_bird_dicount = $ticket->early_bird_discount_amount;
              } else {
                $early_bird_dicount = ($ticket->price * $ticket->early_bird_discount_amount) / 100;
              }
            } else {
              $early_bird_dicount = 0;
            }
          } else {
            $early_bird_dicount = 0;
          }

          $language = Language::where('is_default', 1)->first();

          $ticketContent = TicketContent::where([['ticket_id', $ticket->id], ['language_id', $language->id]])->first();
          if (empty($ticketContent)) {
            $ticketContent = TicketContent::where('ticket_id', $ticket->id)->first();
          }

          $ticketArr[] = [
            'ticket_id' => $ticket->id,
            'early_bird_dicount' => $early_bird_dicount,
            'name' => $ticketContent->title,
            'price' => $ticket->price,
            'type' => $ticket->pricing_type
          ];
        } elseif ($ticket->pricing_type == 'free') {
          $language = Language::where('is_default', 1)->first();
          $ticketContent = TicketContent::where([['ticket_id', $ticket->id], ['language_id', $language->id]])->first();
          if (empty($ticketContent)) {
            $ticketContent = TicketContent::where('ticket_id', $ticket->id)->first();
          }

          $ticketArr[] = [
            'ticket_id' => $ticket->id,
            'early_bird_dicount' => 0,
            'name' => $ticketContent->title,
            'price' => 0,
            'type' => $ticket->pricing_type
          ];
        }
      }

      $selTickets = [];
      foreach ($request->quantity as $key => $qty) {
        if ($qty > 0) {
          $selTickets[] = [
            'ticket_id' => $ticketArr[$key]['ticket_id'],
            'early_bird_dicount' => $qty * $ticketArr[$key]['early_bird_dicount'],
            'name' => $ticketArr[$key]['name'],
            'qty' => $qty,
            'price' => $ticketArr[$key]['price'],
          ];
        }
      }

      $sub_total = 0;
      $total_ticket = 0;
      $total_early_bird_dicount = 0;
      foreach ($selTickets as $key => $var) {
        $sub_total += $var['price'] * $var['qty'];
        $total_ticket += $var['qty'];
        $total_early_bird_dicount += $var['early_bird_dicount'];
      }

      $total = $sub_total - $total_early_bird_dicount;

      Session::put('total', round($total, 2));
      Session::put('sub_total', round($sub_total, 2));
      Session::put('quantity', $total_ticket);
      Session::put('selTickets', $selTickets);
      Session::put('discount', NULL);
      Session::put('total_early_bird_dicount', NULL);
      Session::put('total_early_bird_dicount', round($total_early_bird_dicount, 2));

      //stock check
      foreach ($selTickets as $selTicket) {
        $stock = TicketStockCheck($selTicket['ticket_id'], $selTicket['qty'], $selTicket['name']);

        if ($stock == 'error') {
          $check = true;
          break;
        }
        //check guest checkout status enable or not
        if ($event_guest_checkout_status != 1) {
          $check_v = isTicketPurchaseVenueBackend($request->event_id, $selTicket['ticket_id'], $selTicket['name']);
          if ($check_v['status'] == 'true') {
            $check = true;
            break;
          }
        }
      }
    }

    if ($check == true) {
      $notification = array('message' => 'Something went wrong..!', 'alert-type' => 'error');
      return back()->with($notification);
    }

    $event =  EventContent::join('events', 'events.id', 'event_contents.event_id')
      ->where('events.id', $request->event_id)
      ->select('events.*', 'event_contents.title', 'event_contents.slug', 'event_contents.city', 'event_contents.country')
      ->first();
    Session::put('event', $event);
    $online_gateways = OnlineGateway::where('status', 1)->get();
    $offline_gateways = OfflineGateway::where('status', 1)->orderBy('serial_number', 'asc')->get();
    Session::put('online_gateways', $online_gateways);
    Session::put('offline_gateways', $offline_gateways);
    Session::put('event_date', $request->event_date);
    //check customer logged in or not ?
    if (Auth::guard('customer')->check() == false) {
      return redirect()->route('customer.login', ['redirectPath' => 'event_checkout']);
    }

    return redirect()->route('check-out');
  }

  public function checkout()
  {
    $information['selTickets'] = Session::get('selTickets');
    $information['total'] = Session::get('total');
    $information['quantity'] = Session::get('quantity');
    $information['total_early_bird_dicount'] = Session::get('total_early_bird_dicount');
    $information['event'] = Session::get('event');
    $information['online_gateways'] = Session::get('online_gateways');
    $information['offline_gateways'] = Session::get('offline_gateways');
    $information['basicData'] = Basic::select('tax')->first();
    $stripe = OnlineGateway::where('keyword', 'stripe')->first();
    $stripe_info = json_decode($stripe->information, true);
    $information['stripe_key'] = $stripe_info['key'];

    return view('frontend.check-out', $information);
  }
}
