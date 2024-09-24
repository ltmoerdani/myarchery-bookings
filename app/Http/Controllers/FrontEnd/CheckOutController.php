<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Http\Helpers\HelperUser;
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
use App\Models\EventType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreCheckoutEventTournamentRequest;

class CheckOutController extends Controller
{
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

      $event = EventContent::join('events', 'events.id', 'event_contents.event_id')
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
                $late_price_dicount_international = ($ticket->late_price_discount_international * $ticket->international_price) / 100;
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
                $late_price_dicount_international = ($ticket->late_price_discount_international * $ticket->international_price) / 100;
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

    $event = EventContent::join('events', 'events.id', 'event_contents.event_id')
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

    if ($request->event_type == 'tournament' || $request->event_type == 'turnamen') {
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
        ->where('status', 1)
        ->where('ticket_available', '>', 0)
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

    $event = EventContent::join('events', 'events.id', 'event_contents.event_id')
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

  public function validateCodeAccess(Request $request)
  {
    $event = Event::find($request->event_id);
    $code = $request->input('code_access');

    if ($event && $code) {
      // Cek apakah kode ada di tabel event_type
      $isValid = EventType::where('event_id', $event->id)->where('code', $code)->exists();

      if ($isValid) {
        return response()->json(['status' => 'approved'], 200);
      } else {
        return response()->json(['status' => 'wrong'], 200);
      }
    }

    return response()->json(['status' => 'error'], 400);
  }

  // checkout tournament
  public function formOrderView(Request $request)
  {
    //check customer logged in or not ?
    if (Auth::guard('customer')->check() == false) {
      return redirect()->route('customer.login', ['redirectPath' => 'event_checkout']);
    }

    $event = Session::get('event_' . $request->checkoutID);

    if (empty($event)) {
      return redirect()->route('events')->with(['alert-type' => 'error', 'message' => 'Not Found Checkout ID']);
    }

    $information['customer'] = Auth::guard('customer')->user();
    $information['event'] = $event;
    $information['event_date'] = Session::get('event_date_' . $request->checkoutID);
    $information['checkoutID'] = $request->checkoutID;
    $information['organizer'] = Session::get('organizer_' . $request->checkoutID);
    $information['from_info_event'] = Session::get('from_info_event_' . $request->checkoutID);
    return view('frontend.event.event-form-order-detail', $information);
  }

  public function formOrderProcess(Request $request)
  {
    $basic = Basic::select('event_guest_checkout_status')->first();
    $event_guest_checkout_status = $basic->event_guest_checkout_status;
    if ($event_guest_checkout_status != 1) {
      if (!Auth::guard('customer')->user()) {
        return response()->json(['status' => 'error', 'message' => __('Please Login Customer')], 403);
      }
    }

    $quantityTournament = array_filter($request->quantity, function ($param) {
      if ($param > 0) {
        return $param;
      }
    });

    if (empty($quantityTournament)) {
      return response()->json(['status' => 'error', 'message' => __('Error Minimum Quantity Ticket Tournament')], 400);
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
      return response()->json(['status' => 'error', 'message' => __('Error Minimum Quantity Ticket Tournament')], 400);
    }

    $event = EventContent::join('events', 'events.id', 'event_contents.event_id')
      ->join('event_type', 'event_type.event_id', 'events.id')
      ->where('events.id', $request->event_id)
      ->select('events.*', 'event_contents.*', 'event_type.code', 'event_type.event_type', 'event_type.id')
      ->first();

    $event->is_code_access = false;

    if (!empty($event->code)) {
      unset($event->code);
      $event->is_code_access = true;
    }


    if (empty($event)) {
      return response()->json(['status' => 'error', 'message' => __('No Event Found')], 404);
    }

    //check customer logged in or not ?
    if (Auth::guard('customer')->check() == false) {
      return response()->json(['status' => 'error', 'message' => __('Please Login Customer')], 403);
    }

    $online_gateways = OnlineGateway::where('status', 1)->get();
    $offline_gateways = OfflineGateway::where('status', 1)->orderBy('serial_number', 'asc')->get();

    Session::put('online_gateways', $online_gateways);
    Session::put('offline_gateways', $offline_gateways);
    Session::put('event_' . $request->checkoutId, $event);
    Session::put('event_date_' . $request->checkoutId, $request->event_date);
    Session::put('selTickets_' . $request->checkoutId, '');

    $organizer = Organizer::join('organizer_infos', 'organizer_infos.organizer_id', 'organizers.id')
      ->where('organizers.id', '=', $event->organizer_id)
      ->select('organizers.id', 'organizers.email', 'organizers.phone', 'organizer_infos.*')
      ->first();

    // if event create from role admin and cannot organizer id
    if (!$organizer) {
      $organizer = Admin::select('id', 'first_name as name', 'email', 'phone')->first();
    }
    Session::put("organizer_" . $request->checkoutId, $organizer);
    Session::put('from_info_event_' . $request->checkoutId, $request->all());

    $get_contingent_type = ContingentType::where('event_id', $request->event_id)->first();

    Session::put('delegation_event_' . $request->checkoutId, $get_contingent_type->toArray());

    $tickets = $request->category_ticket;
    $quantity = $request->quantity;
    foreach ($tickets as $key => $value) {
      $ticket[$key] = $value;
    }

    $ticket_detail_individu_order = [];
    $ticket_detail_team_order = [];
    $ticket_detail_mix_team_order = [];
    $ticket_detail_official_order = [];
    $contingent_country_id = null;
    $contingent_country_name = null;
    $contingent_province_id = null;
    $contingent_province_name = null;

    if (strtolower($get_contingent_type->select_type) == 'city/district' || strtolower($get_contingent_type->select_type) == 'province') {
      if (!empty($get_contingent_type->country_id)) {
        $getCountry = InternationalCountries::query()
          ->select('id', 'name')
          ->where('id', $get_contingent_type->country_id)
          ->first();

        $contingent_country_id = empty($getCountry->id) ? null : $getCountry->id;
        $contingent_country_name = empty($getCountry->name) ? null : $getCountry->name;

        if (strtolower($get_contingent_type->select_type) == 'city/district') {
          if ($getCountry->id == '102') {
            $getProvince = IndonesianProvince::query()
              ->select('id', 'name')
              ->where('id', $get_contingent_type->province_id)
              ->first();

            $contingent_province_id = empty($getProvince->id) ? null : $getProvince->id;
            $contingent_province_name = empty($getProvince->name) ? null : $getProvince->name;
          } else {
            $getProvince = InternationalStates::query()
              ->select('id', 'name')
              ->where('country_id', $getCountry->id)
              ->where('id', $get_contingent_type->province_id)
              ->first();

            $contingent_province_id = empty($getProvince->id) ? null : $getProvince->id;
            $contingent_province_name = empty($getProvince->name) ? null : $getProvince->name;
          }
        }
      }
    }

    foreach ($quantity as $k => $v) {
      if (strtolower($ticket[$k]) == 'individu') {
        if ($v > 0) {
          for ($x = 0; $x < $v; $x++) {
            $ticket_detail_individu_order[] = [
              "id" => null, //id from category ticket id or ticket id

              // participant
              "user_id" => null,
              "user_full_name" => null,
              "user_gender" => 'M',
              "birthdate" => null,
              "county_id" => null,
              "country_name" => null,
              "city_id" => null,
              "city_name" => null,
              "team_name" => null,
              'country' => null,
              // end participant

              // delegation
              "contingent_type" => $get_contingent_type->contingent_type, //reference from table contingent by event
              "delegation_type" => empty($get_contingent_type->select_type) ? null : $get_contingent_type->select_type, //delegation_type = select_type in table contingent
              'country_delegation' => $contingent_country_id,
              'country_delegation_name' => $contingent_country_name,
              'province_delegation' => $contingent_province_id,
              'province_delegation_name' => $contingent_province_name,
              'city_delegation' => null,
              'city_delegation_name' => null,
              "club_id" => null,
              "club_name" => null,
              "school_id" => null,
              "school_name" => null,
              "organization_id" => null,
              "organization_name" => null,
              // end delegation

              // ticket
              "ticket_id" => null,
              "ticket_name" => null,
              "sub_category_ticket_id" => null,
              "sub_category_ticket" => null,
              "category_ticket"  => null,
              "type_data" => null,

              // ticket normal rice
              "price_scheme" => null,
              "price" => null,
              "f_price" => null,
              "international_price" => null,
              "f_international_price" => null,

              // ticket earlybird
              "early_bird_discount" => null,
              "early_bird_discount_amount" => null,
              "early_bird_discount_amount_international" => null,
              "early_bird_discount_international_type" => null,
              "early_bird_discount_international_date" => null,
              "early_bird_discount_international_time" => null,
              "early_bird_discount_international_end_date" => null,
              "early_bird_discount_international_end_time" => null,
              "early_bird_discount_type" => null,
              "early_bird_discount_date" => null,
              "early_bird_discount_time" => null,
              "early_bird_discount_end_date" => null,
              "early_bird_discount_end_time" => null,

              // ticket late price
              "late_price_discount" => null,
              "late_price_discount_amount" => null,
              "late_price_discount_amount_international" => null,
              "late_price_discount_international_type" => null,
              "late_price_discount_international_date" => null,
              "late_price_discount_international_time" => null,
              "late_price_discount_international_end_date" => null,
              "late_price_discount_international_end_time" => null,
              "late_price_discount_type" => null,
              "late_price_discount_date" => null,
              "late_price_discount_time" => null,
              "late_price_discount_end_date" => null,
              "late_price_discount_end_time" => null,
            ];
          }
        }
      }

      if (strtolower($ticket[$k]) == 'team') {
        if ($v > 0) {
          for ($x = 0; $x < $v; $x++) {
            $ticket_detail_team_order[] = [
              "id" => null, //id from category ticket id or ticket id

              // participant
              "user_id" => null,
              "user_full_name" => null,
              "user_gender" => null,
              "birthdate" => null,
              "county_id" => null,
              "country_name" => null,
              "city_id" => null,
              "city_name" => null,
              "team_name" => null,
              'country' => null,
              // end participant

              // delegation
              "contingent_type" => $get_contingent_type->contingent_type, //reference from table contingent by event
              "delegation_type" => empty($get_contingent_type->select_type) ? null : $get_contingent_type->select_type, //delegation_type = select_type in table contingent
              'country_delegation' => $contingent_country_id,
              'country_delegation_name' => $contingent_country_name,
              'province_delegation' => $contingent_province_id,
              'province_delegation_name' => $contingent_province_name,
              'city_delegation' => null,
              'city_delegation_name' => null,
              "club_id" => null,
              "club_name" => null,
              "school_id" => null,
              "school_name" => null,
              "organization_id" => null,
              "organization_name" => null,
              // end delegation

              // ticket
              "ticket_id" => null,
              "ticket_name" => null,
              "sub_category_ticket_id" => null,
              "sub_category_ticket" => null,
              "category_ticket"  => null,
              "type_data" => null,

              // ticket normal rice
              "price_scheme" => null,
              "price" => null,
              "f_price" => null,
              "international_price" => null,
              "f_international_price" => null,

              // ticket earlybird
              "early_bird_discount" => null,
              "early_bird_discount_amount" => null,
              "early_bird_discount_amount_international" => null,
              "early_bird_discount_international_type" => null,
              "early_bird_discount_international_date" => null,
              "early_bird_discount_international_time" => null,
              "early_bird_discount_international_end_date" => null,
              "early_bird_discount_international_end_time" => null,
              "early_bird_discount_type" => null,
              "early_bird_discount_date" => null,
              "early_bird_discount_time" => null,
              "early_bird_discount_end_date" => null,
              "early_bird_discount_end_time" => null,

              // ticket late price
              "late_price_discount" => null,
              "late_price_discount_amount" => null,
              "late_price_discount_amount_international" => null,
              "late_price_discount_international_type" => null,
              "late_price_discount_international_date" => null,
              "late_price_discount_international_time" => null,
              "late_price_discount_international_end_date" => null,
              "late_price_discount_international_end_time" => null,
              "late_price_discount_type" => null,
              "late_price_discount_date" => null,
              "late_price_discount_time" => null,
              "late_price_discount_end_date" => null,
              "late_price_discount_end_time" => null,
            ];
          }
        }
      }

      if (strtolower($ticket[$k]) == 'mix team') {
        if ($v > 0) {
          for ($x = 0; $x < $v; $x++) {
            $ticket_detail_mix_team_order[] = [
              "id" => null, //id from category ticket id or ticket id

              // participant
              "user_id" => null,
              "user_full_name" => null,
              "user_gender" => null,
              "birthdate" => null,
              "county_id" => null,
              "country_name" => null,
              "city_id" => null,
              "city_name" => null,
              "team_name" => null,
              'country' => null,
              // end participant

              // delegation
              "contingent_type" => $get_contingent_type->contingent_type, //reference from table contingent by event
              "delegation_type" => empty($get_contingent_type->select_type) ? null : $get_contingent_type->select_type, //delegation_type = select_type in table contingent
              'country_delegation' => $contingent_country_id,
              'country_delegation_name' => $contingent_country_name,
              'province_delegation' => $contingent_province_id,
              'province_delegation_name' => $contingent_province_name,
              'city_delegation' => null,
              'city_delegation_name' => null,
              "club_id" => null,
              "club_name" => null,
              "school_id" => null,
              "school_name" => null,
              "organization_id" => null,
              "organization_name" => null,
              // end delegation

              // ticket
              "ticket_id" => null,
              "ticket_name" => null,
              "sub_category_ticket_id" => null,
              "sub_category_ticket" => null,
              "category_ticket"  => null,
              "type_data" => null,

              // ticket normal rice
              "price_scheme" => null,
              "price" => null,
              "f_price" => null,
              "international_price" => null,
              "f_international_price" => null,

              // ticket earlybird
              "early_bird_discount" => null,
              "early_bird_discount_amount" => null,
              "early_bird_discount_amount_international" => null,
              "early_bird_discount_international_type" => null,
              "early_bird_discount_international_date" => null,
              "early_bird_discount_international_time" => null,
              "early_bird_discount_international_end_date" => null,
              "early_bird_discount_international_end_time" => null,
              "early_bird_discount_type" => null,
              "early_bird_discount_date" => null,
              "early_bird_discount_time" => null,
              "early_bird_discount_end_date" => null,
              "early_bird_discount_end_time" => null,

              // ticket late price
              "late_price_discount" => null,
              "late_price_discount_amount" => null,
              "late_price_discount_amount_international" => null,
              "late_price_discount_international_type" => null,
              "late_price_discount_international_date" => null,
              "late_price_discount_international_time" => null,
              "late_price_discount_international_end_date" => null,
              "late_price_discount_international_end_time" => null,
              "late_price_discount_type" => null,
              "late_price_discount_date" => null,
              "late_price_discount_time" => null,
              "late_price_discount_end_date" => null,
              "late_price_discount_end_time" => null,
            ];
          }
        }
      }

      if (strtolower($ticket[$k]) == 'official') {
        if ($v > 0) {
          for ($x = 0; $x < $v; $x++) {
            $info_ticket = Ticket::where('id', $k)->first();
            $ticket_detail_official_order[] = [
              "id" => null, //id from category ticket id or ticket id

              // participant
              "user_id" => null,
              "user_full_name" => null,
              "user_gender" => 'M',
              "birthdate" => null,
              "county_id" => null,
              "country_name" => null,
              "city_id" => null,
              "city_name" => null,
              "team_name" => null,
              'country' => null,
              // end participant

              // delegation
              "contingent_type" => $get_contingent_type->contingent_type, //reference from table contingent by event
              "delegation_type" => empty($get_contingent_type->select_type) ? null : $get_contingent_type->select_type, //delegation_type = select_type in table contingent
              'country_delegation' => $contingent_country_id,
              'country_delegation_name' => $contingent_country_name,
              'province_delegation' => $contingent_province_id,
              'province_delegation_name' => $contingent_province_name,
              'city_delegation' => null,
              'city_delegation_name' => null,
              "club_id" => null,
              "club_name" => null,
              "school_id" => null,
              "school_name" => null,
              "organization_id" => null,
              "organization_name" => null,
              // end delegation

              // ticket
              "ticket_id" => $k,
              "ticket_name" => $info_ticket->title,
              "sub_category_ticket_id" => null,
              "sub_category_ticket" => null,
              "category_ticket"  => $k,
              "type_data" => 'official',

              // ticket normal rice
              "price_scheme" => $info_ticket->pricing_scheme,
              "price" => $info_ticket->price,
              "f_price" => $info_ticket->f_price,
              "international_price" => $info_ticket->international_price,
              "f_international_price" => $info_ticket->f_international_price,

              // ticket earlybird
              "early_bird_discount" => $info_ticket->early_bird_discount,
              "early_bird_discount_amount" => $info_ticket->early_bird_discount_amount,
              "early_bird_discount_amount_international" => $info_ticket->early_bird_discount_amount_international,
              "early_bird_discount_international_type" => $info_ticket->early_bird_discount_international_type,
              "early_bird_discount_international_date" => $info_ticket->early_bird_discount_international_date,
              "early_bird_discount_international_time" => $info_ticket->early_bird_discount_international_time,
              "early_bird_discount_international_end_date" => $info_ticket->early_bird_discount_international_end_date,
              "early_bird_discount_international_end_time" => $info_ticket->early_bird_discount_international_end_time,
              "early_bird_discount_type" => $info_ticket->early_bird_discount_type,
              "early_bird_discount_date" => $info_ticket->early_bird_discount_date,
              "early_bird_discount_time" => $info_ticket->early_bird_discount_time,
              "early_bird_discount_end_date" => $info_ticket->early_bird_discount_end_date,
              "early_bird_discount_end_time" => $info_ticket->early_bird_discount_end_time,

              // ticket late price
              "late_price_discount" => $info_ticket->late_price_discount,
              "late_price_discount_amount" => $info_ticket->late_price_discount_amount,
              "late_price_discount_amount_international" => $info_ticket->late_price_discount_amount_international,
              "late_price_discount_international_type" => $info_ticket->late_price_discount_international_type,
              "late_price_discount_international_date" => $info_ticket->late_price_discount_international_date,
              "late_price_discount_international_time" => $info_ticket->late_price_discount_international_time,
              "late_price_discount_international_end_date" => $info_ticket->late_price_discount_international_end_date,
              "late_price_discount_international_end_time" => $info_ticket->late_price_discount_international_end_time,
              "late_price_discount_type" => $info_ticket->late_price_discount_type,
              "late_price_discount_date" => $info_ticket->late_price_discount_date,
              "late_price_discount_time" => $info_ticket->late_price_discount_time,
              "late_price_discount_end_date" => $info_ticket->late_price_discount_end_date,
              "late_price_discount_end_time" => $info_ticket->late_price_discount_end_time,
            ];
          }
        }
      }
    }

    // return response()->json(['status' => 'success', 'data' => $ticket_detail_individu_order]);
    Session::put('ticket_detail_individu_order_' . $request->checkoutId, $ticket_detail_individu_order);
    Session::put('ticket_detail_official_order_' . $request->checkoutId, $ticket_detail_official_order);
    Session::put('ticket_detail_team_order' . $request->checkoutId, $ticket_detail_team_order);
    Session::put('ticket_detail_mix_team_order' . $request->checkoutId, $ticket_detail_mix_team_order);

    return response()->json(['status' => 'success', 'message' => 'oke'], 200);
  }

  public function storeCheckoutEventTournament(Request $request)
  {
    $eventInfo = json_decode($request->event_info);
    $individu_newest = empty($request->individu) ? [] : json_decode($request->individu);
    $team_newest = empty($request->team) ? [] : json_decode($request->team);
    $mix_team_newest = empty($request->mix_team) ? [] : json_decode($request->mix_team);
    $official_newest = empty($request->official) ? [] : json_decode($request->official);
    $ticketCount = [];
    $language = $this->getLanguage();
    $language_id = $language->id;
    $checkoutID = $request->checkoutID;

    $errResponseMessage = [
      'errors' => [
        'message' => []
      ]
    ];

    if (!Auth::guard('customer')->user()) {
      return Response(
        [
          'errors' => [
            'message' => [
              'Checkout Error, because not have sessions login'
            ]
          ]
        ],
        401
      );
    }

    if ($eventInfo->is_code_access) {
      $getEventType = EventType::where('event_id', $eventInfo->event_id)->first();

      if (!empty($getEventType['code'])) {
        if ($request->code_access != $getEventType['code']) {
          $errResponseMessage['errors']['message'][] = 'Code Access Not Valid!';
        }
      }
    }

    // return response()->json(['individu' =>$individu_newest,'official'=>$official_newest]);
    if (count($individu_newest) > 0) {
      foreach ($individu_newest as $keyIDT => $individuDT) {
        if (empty($individuDT->user_full_name)) {
          $errResponseMessage['errors']['message'][] = 'This full name in detail individu ' . $keyIDT + 1 . ' is required!';
        }

        if (empty($individuDT->user_gender)) {
          $errResponseMessage['errors']['message'][] = 'This gender in detail individu ' . $keyIDT + 1 . ' is required!';
        }

        if (empty($individuDT->birthdate)) {
          $errResponseMessage['errors']['message'][] = 'This birth date in detail individu ' . $keyIDT + 1 . ' is required!';
        }

        if (empty($individuDT->county_id)) {
          $errResponseMessage['errors']['message'][] = 'This country in detail individu ' . $keyIDT + 1 . ' is required!';
        }

        if (empty($individuDT->city_id)) {
          $errResponseMessage['errors']['message'][] = 'This city in detail individu ' . $keyIDT + 1 . ' is required!';
        }

        if ($individuDT->contingent_type == 'open') {
          if (strtolower($individuDT->delegation_type) == 'country') {
            if (empty($individuDT->country_delegation)) {
              $errResponseMessage['errors']['message'][] = 'This country name in detail individu ' . $keyIDT + 1 . ' is required!';
            }
          }

          if (strtolower($individuDT->delegation_type) == 'country') {
            if (empty($individuDT->country_delegation)) {
              $errResponseMessage['errors']['message'][] = 'This country name in detail individu ' . $keyIDT + 1 . ' is required!';
            }
          }

          if (strtolower($individuDT->delegation_type) == 'province' || strtolower($individuDT->delegation_type) == 'state') {
            if (empty($individuDT->country_delegation)) {
              $errResponseMessage['errors']['message'][] = 'This country name in detail individu ' . $keyIDT + 1 . ' is required!';
            }


            if (empty($individuDT->province_delegation)) {
              $errResponseMessage['errors']['message'][] = 'This province name in detail individu ' . $keyIDT + 1 . ' is required!';
            }
          }

          if (strtolower($individuDT->delegation_type) == 'city/district') {
            if (empty($individuDT->country_delegation)) {
              $errResponseMessage['errors']['message'][] = 'This country name in detail individu ' . $keyIDT + 1 . ' is required!';
            }


            if (empty($individuDT->province_delegation)) {
              $errResponseMessage['errors']['message'][] = 'This province name in detail individu ' . $keyIDT + 1 . ' is required!';
            }

            if (empty($individuDT->city_delegation)) {
              $errResponseMessage['errors']['message'][] = 'This City or District name in detail individu ' . $keyIDT + 1 . ' is required!';
            }
          }

          if (strtolower($individuDT->delegation_type) == 'organization') {
            if (empty($individuDT->organization_name)) {
              $errResponseMessage['errors']['message'][] = 'This Organization name in detail individu ' . $keyIDT + 1 . ' is required!';
            }
          }

          if (strtolower($individuDT->delegation_type) == 'school/universities') {
            if (empty($individuDT->school_name)) {
              $errResponseMessage['errors']['message'][] = 'This School/Universities name in detail individu ' . $keyIDT + 1 . ' is required!';
            }
          }

          if (strtolower($individuDT->delegation_type) == 'club') {
            if (empty($individuDT->club_name)) {
              $errResponseMessage['errors']['message'][] = 'This Club name in detail individu ' . $keyIDT + 1 . ' is required!';
            }
          }
        }

        // if ($individuDT->contingent_type == 'open') {
        //   if (empty($individuDT->delegation_type)) {
        //     $errResponseMessage['errors']['message'][] = 'Delegation Type in detail individu ' . $keyIDT + 1 . ' is required!';
        //   } else {
        //     if (strtolower($individuDT->delegation_type) == 'country') {
        //       if (empty($individuDT->country_delegation)) {
        //         $errResponseMessage['errors']['message'][] = 'This country name in detail individu ' . $keyIDT + 1 . ' is required!';
        //       }
        //     }

        //     if (strtolower($individuDT->delegation_type) == 'province' || strtolower($individuDT->delegation_type) == 'state') {
        //       if (empty($individuDT->country_delegation)) {
        //         $errResponseMessage['errors']['message'][] = 'This country name in detail individu ' . $keyIDT + 1 . ' is required!';
        //       }


        //       if (empty($individuDT->province_delegation)) {
        //         $errResponseMessage['errors']['message'][] = 'This province name in detail individu ' . $keyIDT + 1 . ' is required!';
        //       }
        //     }

        //     if (strtolower($individuDT->delegation_type) == 'city/district') {
        //       if (empty($individuDT->country_delegation)) {
        //         $errResponseMessage['errors']['message'][] = 'This country name in detail individu ' . $keyIDT + 1 . ' is required!';
        //       }


        //       if (empty($individuDT->province_delegation)) {
        //         $errResponseMessage['errors']['message'][] = 'This province name in detail individu ' . $keyIDT + 1 . ' is required!';
        //       }

        //       if (empty($individuDT->city_delegation)) {
        //         $errResponseMessage['errors']['message'][] = 'This City or District name in detail individu ' . $keyIDT + 1 . ' is required!';
        //       }
        //     }

        //     if (strtolower($individuDT->delegation_type) == 'organization') {
        //       if (empty($individuDT->organization_name)) {
        //         $errResponseMessage['errors']['message'][] = 'This Organization name in detail individu ' . $keyIDT + 1 . ' is required!';
        //       }
        //     }

        //     if (strtolower($individuDT->delegation_type) == 'school/universities') {
        //       if (empty($individuDT->school_name)) {
        //         $errResponseMessage['errors']['message'][] = 'This School/Universities name in detail individu ' . $keyIDT + 1 . ' is required!';
        //       }
        //     }

        //     if (strtolower($individuDT->delegation_type) == 'club') {
        //       if (empty($individuDT->club_name)) {
        //         $errResponseMessage['errors']['message'][] = 'This Club name in detail individu ' . $keyIDT + 1 . ' is required!';
        //       }
        //     }
        //   }
        // }

        if ($individuDT->contingent_type != 'open') {
          if (strtolower($individuDT->delegation_type) == 'country') {
            if (empty($individuDT->country_delegation)) {
              $errResponseMessage['errors']['message'][] = 'This country name in detail individu ' . $keyIDT + 1 . ' is required!';
            }
          }

          if (strtolower($individuDT->delegation_type) == 'province' || strtolower($individuDT->delegation_type) == 'state') {
            if (empty($individuDT->province_delegation)) {
              $errResponseMessage['errors']['message'][] = 'This province name in detail individu ' . $keyIDT + 1 . ' is required!';
            }
          }

          if (strtolower($individuDT->delegation_type) == 'city/district') {
            if (empty($individuDT->city_delegation)) {
              $errResponseMessage['errors']['message'][] = 'This City or District name in detail individu ' . $keyIDT + 1 . ' is required!';
            }
          }

          if (strtolower($individuDT->delegation_type) == 'organization') {
            if (empty($individuDT->organization_name)) {
              $errResponseMessage['errors']['message'][] = 'This Organization name in detail individu ' . $keyIDT + 1 . ' is required!';
            }
          }

          if (strtolower($individuDT->delegation_type) == 'school/universities') {
            if (empty($individuDT->school_name)) {
              $errResponseMessage['errors']['message'][] = 'This School/Universities name in detail individu ' . $keyIDT + 1 . ' is required!';
            }
          }

          if (strtolower($individuDT->delegation_type) == 'club') {
            if (empty($individuDT->club_name)) {
              $errResponseMessage['errors']['message'][] = 'This Club name in detail individu ' . $keyIDT + 1 . ' is required!';
            }
          }
        }

        if (empty($individuDT->ticket_id)) {
          $errResponseMessage['errors']['message'][] = 'This Category in detail individu ' . $keyIDT + 1 . ' is required!';
        }

        if (!empty($individuDT->ticket_id)) {
          for ($i = 0; $i < count($individu_newest); $i++) {
            if ($i !== $keyIDT && $individu_newest[$i]->user_full_name == $individuDT->user_full_name && $individu_newest[$i]->ticket_id == $individuDT->ticket_id) {
              $errResponseMessage['errors']['message'][] = 'Detail Individu ' . ($keyIDT + 1) . ' has a duplicate full name and category with Detail Individu ' . ($i + 1) . '.';
              break;
            }
          }

          $ticket_id = $individuDT->ticket_id;

          // Jika ticket_id sudah ada di array $ticketCount, tambahkan count-nya
          if (isset($ticketCount[$ticket_id])) {
            $ticketCount[$ticket_id]['count']++;
          } else {
            // Jika belum ada, tambahkan ticket_id ke array dengan count = 1
            $ticketCount[$ticket_id] = [
              'ticket_id' => $ticket_id,
              'count' => 1
            ];
          }
        }

        if (!empty($individuDT->ticket_id) && !empty($individuDT->user_id) && !empty($individuDT->user_gender) && !empty($individuDT->birthdate)) {
          $checkSameParticipant = Participant::where('fname', $individuDT->user_full_name)
            ->where('gender', $individuDT->user_gender)
            ->where('birthdate', $individuDT->birthdate)
            ->first();
          // return response()->json(['check' => $checkSameParticipant->id]);
          if (!empty($checkSameParticipant)) {
            $individuDT->user_id = $checkSameParticipant->id;
          }

          $checkRegisteredParticipant = ParticipantCompetitions::leftJoin('bookings', 'participant_competitions.booking_id', '=', 'bookings.id')
            ->where('participant_competitions.event_id', $eventInfo->event_id)
            ->where('participant_competitions.ticket_id', $individuDT->ticket_id)
            ->where('participant_competitions.participant_id', $individuDT->user_id)
            ->whereIn('bookings.paymentStatus', ['completed', 'pending'])
            ->first();

          if (!empty($checkRegisteredParticipant)) {
            $errResponseMessage['errors']['message'][] = 'This name ' . $individuDT->user_full_name . ' already made an order in category ticket ' . $individuDT->sub_category_ticket . ', in detail individu ' . $keyIDT + 1;
          } else {
            $availPendingTicket = ParticipantCompetitions::leftJoin('bookings', 'participant_competitions.booking_id', '=', 'bookings.id')
              ->where('participant_competitions.event_id', $eventInfo->event_id)
              ->where('participant_competitions.participant_id', $individuDT->user_id)
              ->first();

            if (!empty($availPendingTicket)) {
              if (strtolower($availPendingTicket->paymentStatus) == 'pending') {
                $errResponseMessage['errors']['message'][] = 'already exist an order pending in individu tickets';
              }
            }
          }
        }
      }
    }

    if (count($official_newest) > 0) {
      foreach ($official_newest as $keyOfficial => $officialDT) {
        if (empty($officialDT->user_full_name)) {
          $errResponseMessage['errors']['message'][] = 'This full name in detail official ' . $keyOfficial + 1 . ' is required!';
        }

        if (empty($officialDT->user_gender)) {
          $errResponseMessage['errors']['message'][] = 'This gender in detail official ' . $keyOfficial + 1 . ' is required!';
        }

        if (empty($officialDT->birthdate)) {
          $errResponseMessage['errors']['message'][] = 'This birth date in detail official ' . $keyOfficial + 1 . ' is required!';
        }

        if (empty($officialDT->county_id)) {
          $errResponseMessage['errors']['message'][] = 'This country in detail official ' . $keyOfficial + 1 . ' is required!';
        }

        if (empty($officialDT->city_id)) {
          $errResponseMessage['errors']['message'][] = 'This city in detail official ' . $keyOfficial + 1 . ' is required!';
        }

        if ($officialDT->contingent_type == 'open') {
          if (empty($officialDT->delegation_type)) {
            $errResponseMessage['errors']['message'][] = 'This delegation typpe in detail official ' . $keyOfficial + 1 . ' is required!';
          } else {
            if (strtolower($officialDT->delegation_type) == 'country') {
              if (empty($officialDT->country_delegation)) {
                $errResponseMessage['errors']['message'][] = 'This country name in detail official ' . $keyOfficial + 1 . ' is required!';
              }
            }

            if (strtolower($officialDT->delegation_type) == 'country') {
              if (empty($officialDT->country_delegation)) {
                $errResponseMessage['errors']['message'][] = 'This country name in detail official ' . $keyOfficial + 1 . ' is required!';
              }
            }

            if (strtolower($officialDT->delegation_type) == 'province' || strtolower($officialDT->delegation_type) == 'state') {
              if (empty($officialDT->country_delegation)) {
                $errResponseMessage['errors']['message'][] = 'This country name in detail official ' . $keyOfficial + 1 . ' is required!';
              }


              if (empty($officialDT->province_delegation)) {
                $errResponseMessage['errors']['message'][] = 'This province name in detail official ' . $keyOfficial + 1 . ' is required!';
              }
            }

            if (strtolower($officialDT->delegation_type) == 'city/district') {
              if (empty($officialDT->country_delegation)) {
                $errResponseMessage['errors']['message'][] = 'This country name in detail official ' . $keyOfficial + 1 . ' is required!';
              }


              if (empty($officialDT->province_delegation)) {
                $errResponseMessage['errors']['message'][] = 'This province name in detail official ' . $keyOfficial + 1 . ' is required!';
              }

              if (empty($officialDT->city_delegation)) {
                $errResponseMessage['errors']['message'][] = 'This City or District name in detail official ' . $keyOfficial + 1 . ' is required!';
              }
            }

            if (strtolower($officialDT->delegation_type) == 'organization') {
              if (empty($officialDT->organization_name)) {
                $errResponseMessage['errors']['message'][] = 'This Organization name in detail official ' . $keyOfficial + 1 . ' is required!';
              }
            }

            if (strtolower($officialDT->delegation_type) == 'school/universities') {
              if (empty($officialDT->school_name)) {
                $errResponseMessage['errors']['message'][] = 'This School/Universities name in detail official ' . $keyOfficial + 1 . ' is required!';
              }
            }

            if (strtolower($officialDT->delegation_type) == 'club') {
              if (empty($officialDT->club_name)) {
                $errResponseMessage['errors']['message'][] = 'This Club name in detail official ' . $keyOfficial + 1 . ' is required!';
              }
            }
          }
        }

        if ($officialDT->contingent_type != 'open') {
          if (strtolower($officialDT->delegation_type) == 'country') {
            if (empty($officialDT->country_delegation)) {
              $errResponseMessage['errors']['message'][] = 'This country name in detail official ' . $keyOfficial + 1 . ' is required!';
            }
          }

          if (strtolower($officialDT->delegation_type) == 'province' || strtolower($officialDT->delegation_type) == 'state') {
            if (empty($officialDT->province_delegation)) {
              $errResponseMessage['errors']['message'][] = 'This province name in detail official ' . $keyOfficial + 1 . ' is required!';
            }
          }

          if (strtolower($officialDT->delegation_type) == 'city/district') {
            if (empty($officialDT->city_delegation)) {
              $errResponseMessage['errors']['message'][] = 'This City or District name in detail official ' . $keyOfficial + 1 . ' is required!';
            }
          }

          if (strtolower($officialDT->delegation_type) == 'organization') {
            if (empty($officialDT->organization_name)) {
              $errResponseMessage['errors']['message'][] = 'This Organization name in detail official ' . $keyOfficial + 1 . ' is required!';
            }
          }

          if (strtolower($officialDT->delegation_type) == 'school/universities') {
            if (empty($officialDT->school_name)) {
              $errResponseMessage['errors']['message'][] = 'This School/Universities name in detail official ' . $keyOfficial + 1 . ' is required!';
            }
          }

          if (strtolower($officialDT->delegation_type) == 'club') {
            if (empty($officialDT->club_name)) {
              $errResponseMessage['errors']['message'][] = 'This Club name in detail official ' . $keyOfficial + 1 . ' is required!';
            }
          }
        }

        if (empty($officialDT->user_full_name)) {
          $errResponseMessage['errors']['message'][] = 'This full name in detail official ' . $keyOfficial + 1 . ' is required!';
        }

        // Validasi duplikasi nama dan kontingen
        foreach ($official_newest as $i => $otherOfficialDT) {
          if ($i !== $keyOfficial) {
            $isDuplicate = $officialDT->user_full_name === $otherOfficialDT->user_full_name &&
              $officialDT->delegation_type === $otherOfficialDT->delegation_type;

            // Pengecekan sesuai dengan delegation type
            switch (strtolower($officialDT->delegation_type)) {
              case 'country':
                $isDuplicate = $isDuplicate && ($officialDT->country_delegation === $otherOfficialDT->country_delegation);
                break;
              case 'province':
                $isDuplicate = $isDuplicate && (
                  $officialDT->country_delegation === $otherOfficialDT->country_delegation &&
                  $officialDT->province_delegation === $otherOfficialDT->province_delegation
                );
                break;
              case 'city':
                $isDuplicate = $isDuplicate && (
                  $officialDT->country_delegation === $otherOfficialDT->country_delegation &&
                  $officialDT->province_delegation === $otherOfficialDT->province_delegation &&
                  $officialDT->city_delegation === $otherOfficialDT->city_delegation
                );
                break;
              case 'organization':
                $isDuplicate = $isDuplicate && ($officialDT->organization_name === $otherOfficialDT->organization_name);
                break;
              case 'school/universities':
                $isDuplicate = $isDuplicate && ($officialDT->school_name === $otherOfficialDT->school_name);
                break;
              case 'club':
                $isDuplicate = $isDuplicate && ($officialDT->club_name === $otherOfficialDT->club_name);
                break;
            }

            if ($isDuplicate) {
              $errResponseMessage['errors']['message'][] = 'Official ' . ($keyOfficial + 1) . ' has a duplicate name and contingent with official ' . ($i + 1) . '.';
              break;
            }
          }
        }

        $delegation_id_official = null;
        $country_id_delegation_official = null;
        $province_id_delegation_official = null;

        switch (strtolower($officialDT->delegation_type)) {
          case 'country':
            $delegation_id_official = !$officialDT->country_delegation ? null : $officialDT->country_delegation;
            break;
          case 'province':
            $delegation_id_official = !$officialDT->province_delegation ? null : $officialDT->province_delegation;
            $country_id_delegation_official = !$officialDT->country_delegation ? null : $officialDT->country_delegation;
            break;
          case 'city':
            $country_id_delegation_official = !$officialDT->country_delegation ? null : $officialDT->country_delegation;
            $province_id_delegation_official = !$officialDT->province_delegation ? null : $officialDT->province_delegation;
            $delegation_id_official = !$officialDT->city_delegation ? null : $officialDT->city_delegation;
            break;
          case 'organization':
            if (!empty($officialDT->organization_name)) {
              $checkDelegationOfficial = Organization::where('name', $officialDT->organization_name)->first();

              if (empty($checkDelegationOfficial)) {
                $newOrganization = Organization::create([
                  'name'     => $officialDT->organization_name,
                ]);

                $official_newest[$keyOfficial]->organization_id = $newOrganization->id;
                $delegation_id_official = $newOrganization->id;
              } else {
                $delegation_id_official = $checkDelegationOfficial->id;
              }
            } else {
              $delegation_id_official = null;
            }

            break;
          case 'school/universities':
            if (!empty($officialDT->school_name)) {
              $checkDelegationOfficial = School::where('name', $officialDT->school_name)->first();

              if (empty($checkDelegationOfficial)) {
                $newSchool = School::create([
                  'name'     => $officialDT->school_name,
                ]);

                $official_newest[$keyOfficial]->school_id = $newSchool->id;
                $delegation_id_official = $newSchool->id;
              } else {
                $delegation_id_official = $checkDelegationOfficial->id;
              }

              break;
            }
          case 'club':
            if (!empty($officialDT->club_name)) {
              $checkDelegationOfficial = Clubs::where('name', $officialDT->club_name)->first();

              if (empty($checkDelegationOfficial)) {
                $newClub = Clubs::create([
                  'name'     => $officialDT->club_name,
                ]);

                $official_newest[$keyOfficial]->club_id = $newClub->id;
                $delegation_id_official = $newClub->id;
              } else {
                $delegation_id_official = $checkDelegationOfficial->id;
              }
            }
            break;
        }

        if (!empty($officialDT->user_id) && !empty($officialDT->user_gender) && !empty($officialDT->birthdate)) {
          $checkSameParticipant = Participant::where('fname', $officialDT->user_full_name)
            ->where('gender', $officialDT->user_gender)
            ->where('birthdate', $officialDT->birthdate)
            ->first();

          if (!empty($checkSameParticipant)) {
            $officialDT->user_id = $checkSameParticipant->id;
          }

          if (!empty($delegation_id_official)) {
            $checkRegisteredParticipant = ParticipantCompetitions::leftJoin('bookings', 'participant_competitions.booking_id', '=', 'bookings.id')
              ->where('participant_competitions.event_id', $eventInfo->event_id)
              ->where('participant_competitions.participant_id', $individuDT->user_id)
              ->where('participant_competitions.delegation_id', $delegation_id_official);

            if (strtolower($officialDT->delegation_type) == 'province' || strtolower($officialDT->delegation_type) == 'state') {
              $checkRegisteredParticipant = $checkRegisteredParticipant
                ->where('participant_competitions.country_id', $country_id_delegation_official);
            }

            if (strtolower($officialDT->delegation_type) == 'city/district') {
              $checkRegisteredParticipant = $checkRegisteredParticipant
                ->where('participant_competitions.country_id', $country_id_delegation_official)
                ->where('participant_competitions.province_id', $province_id_delegation_official);
            }

            $checkRegisteredParticipant = $checkRegisteredParticipant
              ->whereIn('bookings.paymentStatus', ['completed', 'pending'])
              ->first();

            if (!empty($checkRegisteredParticipant)) {
              $errResponseMessage['errors']['message'][] = 'This name ' . $individuDT->user_full_name . ' in detail official ' . $keyIDT + 1 . ' this delegation must be order';
            } else {
              $availPendingTicket = ParticipantCompetitions::leftJoin('bookings', 'participant_competitions.booking_id', '=', 'bookings.id')
                ->where('participant_competitions.event_id', $eventInfo->event_id)
                ->where('participant_competitions.participant_id', $individuDT->user_id)
                ->first();

              if (!empty($availPendingTicket)) {
                if (strtolower($availPendingTicket->paymentStatus) == 'pending') {
                  $errResponseMessage['errors']['message'][] = 'already exist an order pending in official tickets';
                }
              }
            }
          }
        }
      }

      $ticketCount[$official_newest[0]->ticket_id] = [
        'ticket_id' => $official_newest[0]->ticket_id,
        'count' => count($official_newest)
      ];
    }

    if (count($ticketCount) > 0) {
      foreach ($ticketCount as $ticketCountVal) {
        $checkTicket = Ticket::where('id', $ticketCountVal['ticket_id'])->first();
        $getTicketContent = TicketContent::where('ticket_id', $ticketCountVal['ticket_id'])
          ->where('language_id', $language_id)
          ->first();

        if ($ticketCountVal['count'] >= $checkTicket['ticket_available']) {
          $errResponseMessage['errors']['message'][] = $getTicketContent['title'] . ' qouta is not match with your request';
        }
      }
    }

    if (count($errResponseMessage['errors']['message']) > 0) {
      return Response($errResponseMessage, 400);
    }

    Session::put('ticket_detail_individu_order_' . $checkoutID, $individu_newest);
    Session::put('ticket_detail_official_order_' . $checkoutID, $official_newest);
    Session::put('ticket_detail_team_order_' . $checkoutID, $team_newest);
    Session::put('ticket_detail_mix_team_order_' . $checkoutID, $mix_team_newest);

    return response()->json(['status' => 'success'], 200);
    // return response()->json(['checkoutID' => $checkoutID, 'event_info' => $eventInfo, 'ticketCount' => $ticketCount, 'individu' => $individu_newest, 'official' => $official_newest]);
  }

  public function getDataFormOrderTournament(Request $request)
  {
    $event = Session::get('event_' . $request->checkoutID);
    if (empty($event)) {
      return response()->json(['status' => 'error', 'message' => 'checkout id not found'], 404);
    }

    // return response()->json(['status' => 'success', 'data' => $request->all()]);
    $delegation_event = Session::get('delegation_event_' . $request->checkoutID);
    $category_tickets = Session::get('category_tickets_' . $request->checkoutID);
    $ticket_detail_individu_order = Session::get('ticket_detail_individu_order_' . $request->checkoutID);
    $ticket_detail_official_order = Session::get('ticket_detail_official_order_' . $request->checkoutID);
    $ticket_detail_team_order = Session::get('ticket_detail_team_order' . $request->checkoutID);
    $ticket_detail_mix_team_order = Session::get('ticket_detail_mix_team_order' . $request->checkoutID);
    $from_info_event = Session::get('from_info_event_' . $request->checkoutID);
    $organizer = Session::get('organizer_' . $request->checkoutID);

    return response()->json(
      [
        'data' => [
          'event' => $event,
          'delegation_event' => $delegation_event,
          'category_tickets' => $category_tickets,
          'ticket_detail_individu_order' => $ticket_detail_individu_order,
          'ticket_detail_official_order' => $ticket_detail_official_order,
          'ticket_detail_team_order' => $ticket_detail_team_order,
          'ticket_detail_mix_team_order' => $ticket_detail_mix_team_order,
          'from_info_event' => $from_info_event,
          'organizer' => $organizer,
        ],
        'status' => 'success'
      ],
      200
    );
  }

  public function DetailOrderEventTournament(Request $request)
  {
    //check customer logged in or not ?
    if (Auth::guard('customer')->check() == false) {
      return redirect()->route('customer.login', ['redirectPath' => 'event_checkout']);
    }

    $event = Session::get('event_' . $request->checkoutID);

    $online_gateways = OnlineGateway::where('status', 1)->get();
    $offline_gateways = OfflineGateway::where('status', 1)->orderBy('serial_number', 'asc')->get();
    $language = $this->getLanguage();
    $language_id = $language->id;

    if (empty($event)) {
      return redirect()->route('events')->with(['alert-type' => 'error', 'message' => 'Not Found Checkout ID']);
    }

    $basic = Basic::select('event_guest_checkout_status', 'percent_handling_fee')->first();
    $event_guest_checkout_status = $basic->event_guest_checkout_status;
    $percent_handling_fee = $basic->percent_handling_fee;

    $delegation_event = Session::get('delegation_event_' . $request->checkoutID);
    $category_tickets = Session::get('category_tickets_' . $request->checkoutID);
    $ticket_detail_individu_order = Session::get('ticket_detail_individu_order_' . $request->checkoutID);
    $ticket_detail_official_order = Session::get('ticket_detail_official_order_' . $request->checkoutID);
    $ticket_detail_team_order = Session::get('ticket_detail_team_order' . $request->checkoutID);
    $ticket_detail_mix_team_order = Session::get('ticket_detail_mix_team_order' . $request->checkoutID);
    $from_info_event = Session::get('from_info_event_' . $request->checkoutID);
    $organizer = Session::get('organizer_' . $request->checkoutID);

    $information['online_gateways'] = Session::get('online_gateways');
    $information['offline_gateways'] = Session::get('offline_gateways');

    $orders[] = [
      // "title" => $v,
      "title" => 'order_ticket' . time(),
      "category" => 'individu',
      "ticket_detail_individu_order" => $ticket_detail_individu_order,
      "ticket_detail_official_order" => $ticket_detail_official_order,
      "ticket_detail_team_order" => $ticket_detail_team_order,
      "ticket_detail_mix_team_order" => $ticket_detail_mix_team_order,
    ];

    $information['event'] = Event::leftJoin('event_contents', 'event_contents.event_id', '=', 'events.id')
      ->where('events.id', $from_info_event['event_id'])
      ->where('event_contents.language_id', $language_id)
      ->first();
    $information['customer'] = Auth::guard('customer')->user();
    $information['organizer'] = $organizer;
    $information['online_gateways'] = Session::get('online_gateways');
    $information['offline_gateways'] = Session::get('offline_gateways');
    // $information['ticket_infos'] = $category_ticket;
    $information['orders'] = $orders;
    $information['ppn_value'] = $percent_handling_fee;
    $information['language_id'] = $language_id;

    // $information['request_ticket_infos'] = json_encode($category_ticket);
    $information['request_orders'] = json_encode($orders);
    // dd($information);
    return view('frontend.event.event-tournament-checkout-detail', $information);
    // dd($delegation_event, $from_info_event, $organizer, $category_tickets, $ticket_detail_individu_order, $ticket_detail_official_order, $ticket_detail_team_order, $ticket_detail_mix_team_order);
  }
  // end checkout tournament
}
