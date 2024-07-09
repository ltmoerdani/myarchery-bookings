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
use App\Models\IndonesianCities;
use App\Models\IndonesianProvince;
use App\Models\InternationalStates;
use App\Models\School;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckOutController extends Controller
{

  //checkout
  public function detailCheckout2Tournament(Request $request)
  {
    try {
      $basic = Basic::select('event_guest_checkout_status', 'percent_handling_fee')->first();
      $event_guest_checkout_status = $basic->event_guest_checkout_status;
      $percent_handling_fee = $basic->percent_handling_fee;

      $language = $this->getLanguage();
      $language_id = $language->id;

      if ($event_guest_checkout_status != 1) {
        if (!Auth::guard('customer')->user()) {
          return redirect()->route('customer.login', ['redirectPath' => 'event_checkout']);
        }
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
      $information['online_gateways'] = Session::get('online_gateways');
      $information['offline_gateways'] = Session::get('offline_gateways');

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
      if ($name_individu) {
        foreach ($name_individu as $key => $value) {
          $name[$key] = $value;
        }
      }

      $gender_individu = $request->gender_individu;
      if ($gender_individu) {
        foreach ($gender_individu as $key => $value) {
          $gender[$key] = $value;
        }
      }

      $birth_date_individu = $request->birth_date_individu;
      if ($birth_date_individu) {
        foreach ($birth_date_individu as $key => $value) {
          $birthdate[$key] = $value;
        }
      }

      $profile_country_individu = $request->profile_country_individu;
      if ($profile_country_individu) {
        foreach ($profile_country_individu as $key => $value) {
          $country[$key] = $value;
        }
      }

      $profile_city_individu = $request->profile_city_individu;
      if ($profile_city_individu) {
        foreach ($profile_city_individu as $key => $value) {
          $city[$key] = $value;
        }
      }

      $delegation_individu = $request->delegation_individu;
      if ($delegation_individu) {
        foreach ($delegation_individu as $key => $value) {
          $delegation[$key] = $value;
        }
      }

      $country_delegation_individu = $request->country_delegation_individu;
      if ($country_delegation_individu) {
        foreach ($country_delegation_individu as $key => $value) {
          $country_delegation[$key] = $value;
        }
      }

      $province_delegation_individu = $request->province_delegation_individu;
      if ($province_delegation_individu) {
        foreach ($province_delegation_individu as $key => $value) {
          $province_delegation[$key] = $value;
        }
      }

      $city_delegation_individu = $request->city_delegation_individu;
      if ($city_delegation_individu) {
        foreach ($city_delegation_individu as $key => $value) {
          $city_delegation[$key] = $value;
        }
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

      $ticket_detail_individu_order = [];
      $category_individu = $request->category_individu;
      $code_access = $request->code_access;

      $category_ticket = array();
      $categorytickets = array('ticket_id' => null, 'title' => null, 'quantity' => null, 'price_first' => null, 'price_early' => null, 'price' => null);
      if ($category_individu) {
        foreach ($category_individu as $k => $v) {
          //Get country
          $country_name = InternationalCountries::where('id', $country[$k])->first();

          //Get city
          if ($country[$k] == "102") { //Indonesia
            $city_name = IndonesianSubdistrict::find($city[$k]);
          } else {
            $city_name = InternationalCities::find($city[$k]);
          }

          $ticket = Ticket::where('id', $v)->first();
          $ticketContent = TicketContent::where('ticket_id', $v)->where('language_id', $language_id)->first();

          // ============================ early_bird_discount ====================================
          if ($ticket->early_bird_discount == 'enable') {

            $early_bird_start = Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
            $early_bird_end = Carbon::parse($ticket->early_bird_discount_end_date . $ticket->early_bird_discount_end_time);
            $today = Carbon::now();
            if (($today >= $early_bird_start) && ($today <= $early_bird_end)) {
              if ($ticket->early_bird_discount_type == 'fixed') {
                $early_bird_dicount = $ticket->early_bird_discount_amount;
              } else {
                $early_bird_dicount = ($ticket->early_bird_discount_amount * $ticket->price) / 100;
              }
            } else {
              $early_bird_dicount = 0;
            }

            $early_bird_int_start = Carbon::parse($ticket->early_bird_discount_international_date . $ticket->early_bird_discount_international_time);
            $early_bird_int_end = Carbon::parse($ticket->early_bird_discount_international_end_date . $ticket->early_bird_discount_international_end_time);
            if (($today >= $early_bird_int_start) && ($today <= $early_bird_int_end)) {
              if ($ticket->early_bird_discount_international_type == 'fixed') {
                $early_bird_dicount_international = $ticket->early_bird_discount_amount_international;
              } else {
                $early_bird_dicount_international = ($ticket->early_bird_discount_amount_international * $ticket->international_price) / 100;
              }
            } else {
              $early_bird_dicount_international = 0;
            }
          } else {
            $early_bird_dicount = 0;
            $early_bird_dicount_international = 0;
          }


          // ============================ late_price_discount ====================================
          if ($ticket->late_price_discount == 'enable') {
            $late_start = Carbon::parse($ticket->late_price_discount_date . $ticket->late_price_discount_time);
            $late_end = Carbon::parse($ticket->late_price_discount_end_date . $ticket->late_price_discount_end_time);
            $today = Carbon::now();
            if (($today >= $late_start) && ($today <= $late_end)) {
              if ($ticket->late_price_discount_type == 'fixed') {
                $late_price_dicount = $ticket->late_price_discount_amount;
              } else {
                $late_price_dicount = ($ticket->late_price_discount_amount * $ticket->price) / 100;
              }
            } else {
              $late_price_dicount = 0;
            }

            $late_int_start = Carbon::parse($ticket->late_price_discount_international_date . $ticket->late_price_discount_international_time);
            $late_int_end = Carbon::parse($ticket->late_price_discount_international_end_date . $ticket->late_price_discount_international_end_time);
            if (($today >= $late_int_start) && ($today <= $late_int_end)) {
              if ($ticket->late_price_discount_international_type == 'fixed') {
                $late_price_dicount_international = $ticket->late_price_discount_amount_international;
              } else {
                $late_price_dicount_international = ($ticket->late_price_discount_amount_international * $ticket->international_price) / 100;
              }
            } else {
              $late_price_dicount_international = 0;
            }
          } else {
            $late_price_dicount = 0;
            $late_price_dicount_international = 0;
          }


          if ($country[$k] == "102") { //Indonesia
            $ticketprice = $ticket->price;
            $tickettitle = $ticketContent->title;
            $ticketprice_first = $ticketprice;

            if ($early_bird_dicount > 0) {
              $ticketprice = $ticketprice - $early_bird_dicount;
            }

            if ($late_price_dicount > 0) {
              $ticketprice = $ticketprice + $late_price_dicount;
            }
          } else {
            $ticketprice = empty($ticket->international_price) ? $ticket->price : $ticket->international_price;
            $tickettitle = $ticketContent->title . ' (Internasional)';
            $ticketprice_first = $ticketprice;

            if ($early_bird_dicount_international > 0) {
              $ticketprice = $ticketprice - $early_bird_dicount_international;
            }

            if ($late_price_dicount_international > 0) {
              $ticketprice = $ticketprice + $late_price_dicount_international;
            }
          }

          if ($categorytickets['title'] != $tickettitle) {
            unset($categorytickets);
            $categorytickets = array('ticket_id' => $v, 'title' => $tickettitle, 'quantity' => 0, 'price_first' => 0, 'price_early' => 0, 'price' => 0);
            $category_ticket[] = &$categorytickets;
          }

          $categorytickets['price_first'] = $categorytickets['price'] + $ticketprice_first;
          $categorytickets['price_early'] = $early_bird_dicount;
          $categorytickets['price'] = $categorytickets['price'] + $ticketprice;
          $categorytickets['quantity']++;

          $country_delegation_name = '';
          if ($country_delegation_individu) {
            $country_delegation_name = InternationalCountries::find($country_delegation_individu[$k]);
          }

          $province_delegation_name = '';
          if ($province_delegation_individu) {
            if ($country_delegation_individu[$k] == "102") {
              // indonesia
              $province_delegation_name = IndonesianProvince::find($province_delegation_individu[$k]);
            } else {
              $province_delegation_name = InternationalStates::find($province_delegation_individu[$k]);
            }
          }

          $city_delegation_name = '';
          if ($city_delegation_individu) {
            if ($country_delegation_individu[$k] == "102") {
              // indonesia
              $city_delegation_name = IndonesianCities::find($city_delegation_individu[$k]);
            } else {
              $city_delegation_name = InternationalCities::find($city_delegation_individu[$k]);
            }
          }

          if ($club_delegation_individu) {
            $club_name = Clubs::where('id', $club[$k])->first();
            if (!$club_name) {
              $cek_club_name = Clubs::where('name', 'like', '%' . $club_delegation_individu[$k] . '%')->first();
              if (!$cek_club_name) {
                $club_new['name'] = $club_delegation_individu[$k];
                $new_club = Clubs::create($club_new);
                $club_name = Clubs::where('id', $new_club->id)->first();
              } else {
                $club_name = Clubs::where('name', 'like', '%' . $club_delegation_individu[$k] . '%')->first();
                $club[$k] = $club_name->id;
              }
            }
          }

          if ($school_delegation_individu) {
            $cek_school_name = School::where('name', 'like', '%' . $school_delegation_individu[$k] . '%')->first();
            if (!$cek_school_name) {
              $school_new['name'] = $school_delegation_individu[$k];
              $new_school = School::create($school_new);
              $school_id = $new_school->id;
            } else {
              $school_id = $cek_school_name->id;
            }
          }

          if ($organization_delegation_individu) {
            $cek_organization_name = Organization::where('name', 'like', '%' . $organization_delegation_individu[$k] . '%')->first();
            if (!$cek_organization_name) {
              $organization_new['name'] = $organization_delegation_individu[$k];
              $new_organization = Organization::create($organization_new);
              $organization_id = $new_organization->id;
            } else {
              $organization_id = $cek_organization_name->id;
            }
          }

          $ticket_detail_individu_order[] = [
            "id" => $v,
            "user_full_name" => $name[$k],
            "user_gender" => $gender[$k],
            "birthdate" => $birthdate[$k],
            "delegation_type" => $delegation[$k],
            "country_id" => empty($country[$k]) ? null : $country[$k],
            "country_name" => empty($country_name->name) ? null : $country_name->name,
            "city_id" => empty($city[$k]) ? null : $city[$k],
            "city_name" => empty($city_name->name) ? null : $city_name->name,
            'country' => empty($country) ? null : $country,
            'country_delegation_individu' => empty($country_delegation_individu[$k]) ? null : $country_delegation_individu[$k],
            'country_delegation_individu_name' => empty($country_delegation_name->name) ? null : $country_delegation_name->name,
            'province_delegation_individu' => empty($province_delegation_individu[$k]) ? null : $province_delegation_individu[$k],
            'province_delegation_individu_name' => empty($province_delegation_name->name) ? null : $province_delegation_name->name,
            'city_delegation_individu' => empty($city_delegation_individu[$k]) ? null : $city_delegation_individu[$k],
            'city_delegation_individu_name' => empty($city_delegation_name->name) ? null : $city_delegation_name->name,
            "club_id" => empty($club[$k]) ? null : $club[$k],
            "club_name" => empty($club_name->name) ? null : $club_name->name,
            "school_id" => empty($school_id) ? null : $school_id,
            "school_name" => empty($school_delegation_individu[$k]) ? null : $school_delegation_individu[$k],
            "organization_id" => empty($organization_id) ? null : $organization_id,
            "organization_name" => empty($organization_delegation_individu[$k]) ? null : $organization_delegation_individu[$k],
            "sub_category_ticket_id" => $v,
            "sub_category_ticket" => $tickettitle
          ];
        }
      }

      $name_official = $request->name_official;
      $gender_official = $request->gender_official;
      $birth_date_official = $request->birth_date_official;
      $profile_country_official = $request->profile_country_official;
      $profile_city_official = $request->profile_city_official;
      $delegation_official = $request->delegation_official;
      $country_delegation_official = $request->country_delegation_official;
      $province_delegation_official = $request->province_delegation_official;
      $city_delegation_official = $request->city_delegation_official;
      $club_delegation_official = $request->club_delegation_official;
      $school_delegation_official = $request->school_delegation_official;
      $organization_delegation_official = $request->organization_delegation_official;
      $category_official = $request->category_official;
      $ticket_detail_official_order = [];


      if ($category_official) {
        foreach ($category_official as $key => $value) {
          $country_name = InternationalCountries::where('id', $profile_country_official[$key])->first();

          if ($profile_country_official[$key] == "102") {
            //Indonesia
            $city_name = IndonesianSubdistrict::find($profile_city_official[$key]);
          } else {
            $city_name = InternationalCities::find($profile_city_official[$key]);
          }

          $ticket = Ticket::where('id', $value)->first();
          $ticketContent = TicketContent::where('ticket_id', $value)->where('language_id', $language_id)->first();

          // ============================ early_bird_discount ====================================
          if ($ticket->early_bird_discount == 'enable') {

            $early_bird_start = Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
            $early_bird_end = Carbon::parse($ticket->early_bird_discount_end_date . $ticket->early_bird_discount_end_time);
            $today = Carbon::now();
            if (($today >= $early_bird_start) && ($today <= $early_bird_end)) {
              if ($ticket->early_bird_discount_type == 'fixed') {
                $early_bird_dicount = $ticket->early_bird_discount_amount;
              } else {
                $early_bird_dicount = ($ticket->early_bird_discount_amount * $ticket->price) / 100;
              }
            } else {
              $early_bird_dicount = 0;
            }

            $early_bird_int_start = Carbon::parse($ticket->early_bird_discount_international_date . $ticket->early_bird_discount_international_time);
            $early_bird_int_end = Carbon::parse($ticket->early_bird_discount_international_end_date . $ticket->early_bird_discount_international_end_time);
            if (($today >= $early_bird_int_start) && ($today <= $early_bird_int_end)) {
              if ($ticket->early_bird_discount_international_type == 'fixed') {
                $early_bird_dicount_international = $ticket->early_bird_discount_amount_international;
              } else {
                $early_bird_dicount_international = ($ticket->early_bird_discount_amount_international * $ticket->international_price) / 100;
              }
            } else {
              $early_bird_dicount_international = 0;
            }
          } else {
            $early_bird_dicount = 0;
            $early_bird_dicount_international = 0;
          }

          // ============================ late_price_discount ====================================
          if ($ticket->late_price_discount == 'enable') {
            $late_start = Carbon::parse($ticket->late_price_discount_date . $ticket->late_price_discount_time);
            $late_end = Carbon::parse($ticket->late_price_discount_end_date . $ticket->late_price_discount_end_time);
            $today = Carbon::now();
            if (($today >= $late_start) && ($today <= $late_end)) {
              if ($ticket->late_price_discount_type == 'fixed') {
                $late_price_dicount = $ticket->late_price_discount_amount;
              } else {
                $late_price_dicount = ($ticket->late_price_discount_amount * $ticket->price) / 100;
              }
            } else {
              $late_price_dicount = 0;
            }

            $late_int_start = Carbon::parse($ticket->late_price_discount_international_date . $ticket->late_price_discount_international_time);
            $late_int_end = Carbon::parse($ticket->late_price_discount_international_end_date . $ticket->late_price_discount_international_end_time);
            if (($today >= $late_int_start) && ($today <= $late_int_end)) {
              if ($ticket->late_price_discount_international_type == 'fixed') {
                $late_price_dicount_international = $ticket->late_price_discount_amount_international;
              } else {
                $late_price_dicount_international = ($ticket->late_price_discount_amount_international * $ticket->international_price) / 100;
              }
            } else {
              $late_price_dicount_international = 0;
            }
          } else {
            $late_price_dicount = 0;
            $late_price_dicount_international = 0;
          }

          if ($profile_country_official[$key] == "102") {
            //Indonesia
            $ticketprice = $ticket->price;
            $tickettitle = $ticketContent->title;
            $ticketprice_first = $ticketprice;

            if ($early_bird_dicount > 0) {
              $ticketprice = $ticketprice - $early_bird_dicount;
            }

            if ($late_price_dicount > 0) {
              $ticketprice = $ticketprice + $late_price_dicount;
            }
          } else {
            $ticketprice = empty($ticket->international_price) ? $ticket->price : $ticket->international_price;
            $tickettitle = $ticketContent->title . ' (Internasional)';
            $ticketprice_first = $ticketprice;

            if ($early_bird_dicount_international > 0) {
              $ticketprice = $ticketprice - $early_bird_dicount_international;
            }

            if ($late_price_dicount_international > 0) {
              $ticketprice = $ticketprice + $late_price_dicount_international;
            }
          }

          if ($categorytickets['title'] != $tickettitle) {
            unset($categorytickets);
            $categorytickets = array('ticket_id' => $value, 'title' => $tickettitle, 'quantity' => 0, 'price_first' => 0, 'price_early' => 0, 'price' => 0);
            $category_ticket[] = &$categorytickets;
          }
          $categorytickets['price_first'] = $categorytickets['price'] + $ticketprice_first;
          $categorytickets['price_early'] = $early_bird_dicount;
          $categorytickets['price'] = $categorytickets['price'] + $ticketprice;
          $categorytickets['quantity']++;

          $country_delegation_name = '';
          if ($country_delegation_individu) {
            $country_delegation_name = InternationalCountries::find($country_delegation_official[$key]);
          }

          $province_delegation_name = '';
          if ($province_delegation_official) {
            if ($country_delegation_official[$key] == "102") {
              // indonesia
              $province_delegation_name = IndonesianProvince::find($province_delegation_official[$key]);
            } else {
              $province_delegation_name = InternationalStates::find($province_delegation_official[$key]);
            }
          }

          $city_delegation_name = '';
          if ($city_delegation_official) {
            if ($country_delegation_official[$key] == "102") {
              // indonesia
              $city_delegation_name = IndonesianCities::find($city_delegation_official[$key]);
            } else {
              $city_delegation_name = InternationalCities::find($city_delegation_official[$key]);
            }
          }

          if ($club_delegation_official) {
            $club_name = Clubs::where('id', $club_delegation_official[$key])->first();
            if (!$club_name) {
              $cek_club_name = Clubs::where('name', 'like', '%' . $club_delegation_official[$key] . '%')->first();
              if (!$cek_club_name) {
                $club_new['name'] = $club_delegation_official[$key];
                $new_club = Clubs::create($club_new);
                $club_name = Clubs::where('id', $new_club->id)->first();
              } else {
                $club_name = Clubs::where('name', 'like', '%' . $club_delegation_official[$key] . '%')->first();
                $club[$key] = $club_name->id;
              }
            }
          }

          if ($school_delegation_official) {
            $cek_school_name = School::where('name', 'like', '%' . $school_delegation_official[$key] . '%')->first();
            if (!$cek_school_name) {
              $school_new['name'] = $school_delegation_official[$key];
              $new_school = School::create($school_new);
              $school_id = $new_school->id;
            } else {
              $school_id = $cek_school_name->id;
            }
          }

          if ($organization_delegation_official) {
            $cek_organization_name = Organization::where('name', 'like', '%' . $organization_delegation_official[$key] . '%')->first();
            if (!$cek_organization_name) {
              $organization_new['name'] = $organization_delegation_official[$key];
              $new_organization = Organization::create($organization_new);
              $organization_id = $new_organization->id;
            } else {
              $organization_id = $cek_organization_name->id;
            }
          }

          $ticket_detail_official_order[] = [
            "id" => $value,
            "user_full_name" => $name_official[$key],
            "user_gender" => $gender_official[$key],
            "birthdate" => $birth_date_official[$key],
            "delegation_type" => $delegation_official[$key],
            "country_id" => empty($profile_country_official[$key]) ? null : $profile_country_official[$key],
            "country_name" => empty($country_name->name) ? null : $country_name->name,
            "city_id" => empty($profile_city_official[$key]) ? null : $profile_city_official[$key],
            "city_name" => empty($city_name->name) ? null : $city_name->name,
            'country_delegation_official' => empty($country_delegation_official[$key]) ? null : $country_delegation_official[$key],
            'country_delegation_official_name' => empty($country_delegation_name->name) ? null : $country_delegation_name->name,
            'province_delegation_official' => empty($province_delegation_official[$key]) ? null : $province_delegation_official[$key],
            'province_delegation_official_name' => empty($province_delegation_name->name) ? null : $province_delegation_name->name,
            'city_delegation_official' => empty($city_delegation_official[$key]) ? null : $city_delegation_official[$key],
            'city_delegation_official_name' => empty($city_delegation_name->name) ? null : $city_delegation_name->name,
            "club_id" => empty($club_delegation_official[$key]) ? null : $club_delegation_official[$key],
            "club_name" => empty($club_name->name) ? null : $club_name->name,
            "school_id" => empty($school_id) ? null : $school_id,
            "school_name" => empty($school_delegation_official[$key]) ? null : $school_delegation_official[$key],
            "organization_id" => empty($organization_id) ? null : $organization_id,
            "organization_name" => empty($organization_delegation_official[$key]) ? null : $organization_delegation_official[$key],
            "sub_category_ticket_id" => $value,
            "sub_category_ticket" => $tickettitle
          ];
        }
      }

      $orders[] = [
        // "title" => $v,
        "title" => 'order_ticket' . time(),
        "category" => 'individu',
        "ticket_detail_individu_order" => $ticket_detail_individu_order,
        "ticket_detail_official_order" => $ticket_detail_official_order,
        "ticket_detail_team_order" => [],
        "ticket_detail_mix_team_order" => [],
      ];
      // dd($orders);

      $information['ticket_infos'] = $category_ticket;
      $information['orders'] = $orders;
      $information['ppn_value'] = $percent_handling_fee;
      $information['language_id'] = $language_id;

      $information['request_ticket_infos'] = json_encode($category_ticket);
      $information['request_orders'] = json_encode($orders);

      // dd($information);

      return view('frontend.event.event-tournament-checkout-detail', $information);
    } catch (\Exception $e) {
      Log::build([
        'driver' => 'single',
        'path' => storage_path('logs/checkout-event-tournament-' . time() . '.log'),
      ])->error($e->getMessage());
      return abort(404);
    }
  }

  public function checkout2Tournament(Request $request)
  {
    $language = $this->getLanguage();
    $language_id = $language->id;

    $quantityTournament = array_filter($request->quantity, function ($param) {
      if ($param > 0) {
        return $param;
      }
    });

    if (empty($quantityTournament)) {
      return back()->with(['alert-type' => 'error', 'message' => __('Error Minimum Quantity Ticket Tournament')]);
    }

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
      ->join('event_type', 'event_type.event_id', 'events.id')
      ->where('events.id', $request->event_id)
      ->select('events.*', 'event_contents.*', 'event_type.code', 'event_type.event_type', 'event_type.id')
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

      foreach ($quantity as $k => $v) {
        $category_ticket[] = [
          "id" => $k,
          'name' => $ticket[$k],
          "quantity" => $v
        ];
      }
      $information['category_tickets'] = $category_ticket;

      $tickets_list = Ticket::leftjoin('ticket_contents', 'ticket_contents.ticket_id', 'tickets.id')
        ->select('tickets.*', 'ticket_contents.title as contents_title')
        ->where('ticket_contents.language_id', $language_id)
        ->where('tickets.event_id', $request->event_id)
        // ->where('tickets.title', 'Individu')
        ->get();

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
    $information['basicData'] = Basic::select('tax')->first();
    $stripe = OnlineGateway::where('keyword', 'stripe')->first();
    $stripe_info = json_decode($stripe->information, true);
    $information['stripe_key'] = $stripe_info['key'];
    $online_gateways = OnlineGateway::where('status', 1)->get();
    $offline_gateways = OfflineGateway::where('status', 1)->orderBy('serial_number', 'asc')->get();
    Session::put('online_gateways', $online_gateways);
    Session::put('offline_gateways', $offline_gateways);
    $information['online_gateways'] = Session::get('online_gateways');
    $information['offline_gateways'] = Session::get('offline_gateways');
    // dd($information);
    return view('frontend.check-out', $information);
  }
}
