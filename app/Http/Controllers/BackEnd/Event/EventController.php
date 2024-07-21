<?php

namespace App\Http\Controllers\BackEnd\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\StoreRequest;
use App\Http\Requests\Event\StoreTournamentRequest;
use App\Http\Requests\Event\UpdateRequest;
use App\Http\Requests\Event\UpdateRequestTournament;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Event;
use App\Models\Event\EventImage;
use App\Models\Event\EventContent;
use App\Models\Event\EventDates;
use App\Models\Event\Ticket;
use App\Models\Organizer;
use App\Models\State;
use App\Models\EventType;
use App\Models\InternationalCountries;
use App\Models\IndonesianProvince;
use App\Models\IndonesianCities;
use App\Models\InternationalStates;
use App\Models\InternationalCities;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Mews\Purifier\Facades\Purifier;
use Spatie\GoogleCalendar\Event as GoogleCalendarEvent;
use App\Models\EventPublisher;
use App\Models\EventKurs;
use App\Models\ContingentType;
use App\Models\Competitions;
use App\Models\CompetitionType;
use App\Models\CompetitionCategories;
use App\Models\CompetitionClassType;
use App\Models\CompetitionClassName;
use App\Models\CompetitionDistance;
use App\Models\DelegationType;
use App\Models\Event\TicketContent;
use App\Http\Helpers\HelperEvent;
use App\Http\Helpers\HelperResponse;
use App\Models\Event\Booking;
use App\Models\ParticipantCompetitions;
use App\Models\TicketPrice;
use Illuminate\Support\Facades\Auth;


class EventController extends Controller
{
  //index
  public function index(Request $request)
  {
    $information['langs'] = Language::all();

    $language = Language::where('code', $request->language)->firstOrFail();
    $information['language'] = $language;

    $event_type = null;
    if (filled($request->event_type)) {
      $event_type = $request->event_type;
    }
    $title = null;
    if (request()->filled('title')) {
      $title = request()->input('title');
    }

    $events = Event::join('event_contents', 'event_contents.event_id', '=', 'events.id')
      ->join('event_categories', 'event_categories.id', '=', 'event_contents.event_category_id')
      ->where('event_contents.language_id', '=', $language->id)
      ->when($title, function ($query) use ($title) {
        return $query->where('event_contents.title', 'like', '%' . $title . '%');
      })
      ->when($event_type, function ($query) use ($event_type) {
        return $query->where('events.event_type', $event_type);
      })
      ->select('events.*', 'event_contents.id as eventInfoId', 'event_contents.title', 'event_contents.slug', 'event_categories.name as category')
      ->orderByDesc('events.id')
      ->paginate(10);

    $information['events'] = $events;
    return view('backend.event.index', $information);
  }
  //choose_event_type
  public function choose_event_type()
  {
    return view('backend.event.event_type');
  }
  //online_event
  public function add_event(Request $request)
  {
    $information = [];
    $languages = Language::get();
    $information['languages'] = $languages;
    $countries = Country::get();
    $information['countries'] = $countries;
    $organizers = Organizer::get();
    $information['organizers'] = $organizers;
    $information['getCurrencyInfo']  = $this->getCurrencyInfo();

    if ($request->type == "tournament" || $request->type == "turnamen") {
      $information['competition_categories'] = CompetitionCategories::all();
      $information['competition_class_type'] = CompetitionClassType::all();
      $information['competition_class_name'] = CompetitionClassName::all();
      $information['competition_distance'] = CompetitionDistance::all();
      $information['delegation_type'] = DelegationType::all();
      $information['international_countries'] = InternationalCountries::all();
      return view('backend.event.create_tournament', $information);
    } else {
      return view('backend.event.create', $information);
    }
  }

  public function gallerystore(Request $request)
  {
    $img = $request->file('file');
    $allowedExts = array('jpg', 'png', 'jpeg');
    $rules = [
      'file' => [
        'dimensions:width=1170,height=570',
        function ($attribute, $value, $fail) use ($img, $allowedExts) {
          $ext = $img->getClientOriginalExtension();
          if (!in_array($ext, $allowedExts)) {
            return $fail("Only png, jpg, jpeg images are allowed");
          }
        }
      ]
    ];

    $messages = [
      'file.dimensions' => 'The file has invalid image dimensions ' . $img->getClientOriginalName()
    ];

    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
      $validator->getMessageBag()->add('error', 'true');
      return response()->json($validator->errors());
    }
    $filename = uniqid() . '.jpg';
    @mkdir(public_path('assets/admin/img/event-gallery/'), 0775, true);
    $img->move(public_path('assets/admin/img/event-gallery/'), $filename);
    $pi = new EventImage;
    if (!empty($request->event_id)) {
      $pi->event_id = $request->event_id;
    }
    $pi->image = $filename;
    $pi->save();
    return response()->json(['status' => 'success', 'file_id' => $pi->id]);
  }

  public function imagermv(Request $request)
  {
    $pi = EventImage::where('id', $request->fileid)->first();
    @unlink(public_path('assets/admin/img/event-gallery/') . $pi->image);
    $pi->delete();
    return $pi->id;
  }

  public function gallerystoretournament(Request $request)
  {
    $img = $request->file('file');
    $allowedExts = array('jpg', 'png', 'jpeg');
    $rules = [
      'file' => [
        // 'dimensions:width=1170,height=570',
        function ($attribute, $value, $fail) use ($img, $allowedExts) {
          $ext = $img->getClientOriginalExtension();
          if (!in_array($ext, $allowedExts)) {
            return $fail("Only png, jpg, jpeg images are allowed");
          }
        }
      ]
    ];

    $messages = [
      'file.dimensions' => 'The file has invalid image dimensions ' . $img->getClientOriginalName()
    ];

    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
      $validator->getMessageBag()->add('error', 'true');
      return response()->json($validator->errors());
    }
    $filename = uniqid() . '.jpg';
    @mkdir(public_path('assets/admin/img/event-gallery/'), 0775, true);
    $img->move(public_path('assets/admin/img/event-gallery/'), $filename);
    $pi = new EventImage;
    if (!empty($request->event_id)) {
      $pi->event_id = $request->event_id;
    }
    $pi->image = $filename;
    $pi->save();
    return response()->json(['status' => 'success', 'file_id' => $pi->id]);
  }

  public function imagermvtournament(Request $request)
  {
    $pi = EventImage::where('id', $request->fileid)->first();
    @unlink(public_path('assets/admin/img/event-gallery/') . $pi->image);
    $pi->delete();
    return $pi->id;
  }

  public function store(StoreRequest $request)
  {
    DB::transaction(function () use ($request) {
      //calculate duration
      if ($request->date_type == 'single') {
        $start = Carbon::parse($request->start_date . $request->start_time);
        $end =  Carbon::parse($request->end_date . $request->end_time);
        $diffent = DurationCalulate($start, $end);
      }
      //calculate duration end

      $in = $request->all();
      $in['duration'] = $request->date_type == 'single' ? $diffent : '';

      $img = $request->file('thumbnail');

      $in['organizer_id'] = $request->organizer_id;
      if ($request->hasFile('thumbnail')) {
        $filename = time() . '.' . $img->getClientOriginalExtension();
        $directory = public_path('assets/admin/img/event/thumbnail/');
        @mkdir($directory, 0775, true);
        $request->file('thumbnail')->move($directory, $filename);
        $in['thumbnail'] = $filename;
      }
      $in['f_price'] = $request->price;
      $in['end_date_time'] = Carbon::parse($request->end_date . ' ' . $request->end_time);
      $event = Event::create($in);

      if ($request->date_type == 'multiple') {
        $i = 1;
        foreach ($request->m_start_date as $key => $date) {
          $start = Carbon::parse($date . $request->m_start_time[$key]);
          $end =  Carbon::parse($request->m_end_date[$key] . $request->m_end_time[$key]);
          $diffent = DurationCalulate($start, $end);

          EventDates::create([
            'event_id' => $event->id,
            'start_date' => $date,
            'start_time' => $request->m_start_time[$key],
            'end_date' => $request->m_end_date[$key],
            'end_time' => $request->m_end_time[$key],
            'duration' => $diffent,
            'start_date_time' => $start,
            'end_date_time' => $end,
          ]);
          if ($i == 1) {
            $event->update([
              'duration' => $diffent
            ]);
          }
          $i++;
        }
        //update event date time
        $event_date = EventDates::where('event_id', $event->id)->orderBy('end_date_time', 'desc')->first();

        $event->end_date_time = $event_date->end_date_time;
        $event->save();
      }


      $in['event_id'] = $event->id;
      if ($request->event_type == 'online') {
        if (!$request->pricing_type) {
          $in['pricing_type'] = 'normal';
        }
        $in['early_bird_discount'] = $request->early_bird_discount_type;
        $in['early_bird_discount_type'] = $request->discount_type;
        Ticket::create($in);
      }

      $slders = $request->slider_images;

      foreach ($slders as $key => $id) {
        $event_image = EventImage::where('id', $id)->first();
        if ($event_image) {
          $event_image->event_id = $event->id;
          $event_image->save();
        }
      }
      $languages = Language::all();

      foreach ($languages as $language) {
        $event_content = new EventContent();
        $event_content->language_id = $language->id;
        $event_content->event_category_id = $request[$language->code . '_category_id'];
        $event_content->event_id = $event->id;
        $event_content->title = $request[$language->code . '_title'];
        if ($request->event_type == 'venue') {
          $event_content->address = $request[$language->code . '_address'];
          $event_content->country = $request[$language->code . '_country'];
          $event_content->state = $request[$language->code . '_state'];
          $event_content->city = $request[$language->code . '_city'];
          $event_content->zip_code = $request[$language->code . '_zip_code'];
        }
        $event_content->slug = createSlug($request[$language->code . '_title']);
        $event_content->description = Purifier::clean($request[$language->code . '_description'], 'youtube');
        $event_content->refund_policy = $request[$language->code . '_refund_policy'];
        $event_content->meta_keywords = $request[$language->code . '_meta_keywords'];
        $event_content->meta_description = $request[$language->code . '_meta_description'];
        $event_content->save();
      }
    });
    Session::flash('success', 'Added Successfully');
    return response()->json(['status' => 'success'], 200);
  }

  public function store_tournament_backup(StoreTournamentRequest $request)
  {
    try {
      if (empty(Auth::guard('admin'))) {
        return Response(
          [
            'errors' => [
              'message' => [
                'Create Error, because not have sessions login'
              ]
            ]
          ],
          401
        );
      }

      DB::transaction(function () use ($request) {
        $request->is_featured = "yes";
        $request->date_type = "single";

        //calculate duration
        if ($request->date_type == 'single') {
          $start = Carbon::parse($request->start_date . $request->start_time);
          $end =  Carbon::parse($request->end_date . $request->end_time);
          $diffent = DurationCalulate($start, $end);
        }
        //calculate duration end

        $in = $request->all();
        $in['duration'] = $request->date_type == 'single' ? $diffent : '';

        $img = $request->file('thumbnail');
        if ($request->hasFile('thumbnail')) {
          $filename = time() . '.' . $img->getClientOriginalExtension();
          $directory = public_path('assets/admin/img/event/thumbnail/');
          @mkdir($directory, 0775, true);
          $request->file('thumbnail')->move($directory, $filename);
          $in['thumbnail'] = $filename;
        }

        $thb_file = $request->file('thb_file');
        if ($request->hasFile('thb_file')) {
          $filename = 'thb-file-' . time() . '.' . $thb_file->getClientOriginalExtension();
          $directory = public_path('assets/admin/img/event/tournament_uploaded/');
          @mkdir($directory, 0775, true);
          $request->file('thb_file')->move($directory, $filename);
          $in['thb_file'] = $filename;
        }

        $in['f_price'] = $request->price;
        $in['end_date_time'] = Carbon::parse($request->end_date . ' ' . $request->end_time);
        $in['is_featured'] = $request->is_featured;
        $event = Event::create($in);


        $in['event_id'] = $event->id;
        if ($request->event_type == 'online') {
          if (!$request->pricing_type) {
            $in['pricing_type'] = 'normal';
          }
          $in['early_bird_discount'] = $request->early_bird_discount_type;
          $in['early_bird_discount_type'] = $request->discount_type;
          Ticket::create($in);
        }

        $slders = $request->slider_images;
        foreach ($slders as $key => $id) {
          $event_image = EventImage::where('id', $id)->first();
          if ($event_image) {
            $event_image->event_id = $event->id;
            $event_image->save();
          }
        }

        $languages = Language::all();
        foreach ($languages as $language) {
          $event_content = new EventContent();
          $event_content->language_id = $language->id;
          $event_content->event_category_id = $request[$language->code . '_category_id'];
          $event_content->event_id = $event->id;
          $event_content->title = $request[$language->code . '_title'];

          if ($request->event_type == 'venue') {
            $event_content->address = $request[$language->code . '_address'];
            $event_content->country = $request[$language->code . '_country'];
            $event_content->state = $request[$language->code . '_state'];
            $event_content->city = $request[$language->code . '_city'];
            $event_content->zip_code = $request[$language->code . '_zip_code'];
          }

          if ($request->event_type == 'tournament' || $request->event_type == 'turnamen') {
            $event_content->country_id = $request[$language->code . '_country'];
            $event_content->state_id = $request[$language->code . '_state'];
            $event_content->city_id = $request[$language->code . '_city'];
            $event_content->country = InternationalCountries::find($request[$language->code . '_country'])->name;
            $event_content->address = $request[$language->code . '_address'];
            $event_content->zip_code = $request[$language->code . '_zip_code'];

            if ($request[$language->code . '_country'] == 102 || $request[$language->code . '_country'] == '102') {
              $event_content->state = IndonesianProvince::find($request[$language->code . '_state'])->name;
              $event_content->city = IndonesianCities::find($request[$language->code . '_city'])->name;
            } else {
              $event_content->state = InternationalStates::find($request[$language->code . '_state'])->name;
              $event_content->city = InternationalCities::find($request[$language->code . '_city'])->name;
            }
          }

          $event_content->slug = createSlug($request[$language->code . '_title']);
          $event_content->description = Purifier::clean($request[$language->code . '_description'], 'youtube');
          $event_content->refund_policy = $request[$language->code . '_refund_policy'];
          $event_content->meta_keywords = $request[$language->code . '_meta_keywords'];
          $event_content->meta_description = $request[$language->code . '_meta_description'];
          $event_content->save();
        }

        // event type public or private
        if ($request->event_publisher) {
          $input['event_id'] = $event->id;
          $input['event_type'] = $request->event_publisher;
          $input['shared_type'] = 'event type ' . $request->event_publisher;
          $input['link_event'] = $request->link_event_publisher;
          $input['code'] = $request->code;
          $input['description'] = $request->description_event_publisher;
          EventPublisher::create($input);
        }

        // contingent type
        if ($request->delegation_type) {
          $input['event_id'] = $event->id;
          $input['contingent_type'] = $request->delegation_type;
          $input['select_type'] = $request->select_type;
          $input['country_id'] = $request->select_country;
          $input['country'] = $request->contingent_country;
          $input['province_id'] = $request->select_state;
          $input['province'] = $request->contingent_province;
          $input['state_id'] = $request->select_state;
          $input['state'] = $request->contingent_state;
          $input['city_id'] = $request->contingent_city_id;
          $input['city'] = $request->contingent_city;
          ContingentType::create($input);
        }

        // Add Competition Category
        $i = 1;
        foreach ($request->competition_categories as $key => $c) {
          $competition_categories = CompetitionCategories::where('id', $request->competition_categories[$key])->first();
          $name_competition = $competition_categories->name . ' ' . $request->competition_class_name[$key] . ' ' . $request->competition_distance[$key] . ' Meter';

          $competitions = Competitions::create([
            'event_id' => $event->id,
            'name' => $name_competition,
            'competition_type_id' => 0,
            'competition_category_id' => $request->competition_categories[$key],
            'gender' => null,
            'contingent' => null,
            'distance' => $request->competition_distance[$key],
            'class_type' => $request->competition_class_type[$key],
            'class_name' => $request->competition_class_name[$key],
            'description' => null,
          ]);
          $competition_id = $competitions->id;

          // Individual
          $gender = ['Putra', 'Putri'];
          foreach ($gender as $g) {
            $ticket['event_id'] = $event->id;
            $ticket['competition_id'] = $competition_id;
            $ticket['event_type'] = 'tournament';
            $ticket['title'] = 'Individu';
            $ticket['ticket_available_type'] = 'limited';
            $ticket['ticket_available'] = 100;
            $ticket['original_ticket_available'] = 100;
            $ticket['max_ticket_buy_type'] = 'limited';
            $ticket['max_buy_ticket'] = 10;
            $ticket['pricing_type'] = 'normal';
            $ticket['pricing_scheme'] = $request['pricing_scheme'];
            $ticket['price'] = 300000;
            $ticket['f_price'] = 300000;
            $ticket['international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
            $ticket['f_international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
            $ticket['early_bird_discount'] = 'disable';
            $ticket['early_bird_discount_type'] = 'fixed';
            $ticket['late_price_discount'] = 'disable';
            $ticket['late_price_discount_type'] = 'fixed';
            $t = Ticket::create($ticket);

            $languages = Language::all();
            foreach ($languages as $language) {
              if ($language->id == 8) {
                if ($g == 'Putra') {
                  $g = 'Men';
                } elseif ($g == 'Putri') {
                  $g = 'Women';
                }
              }

              if ($language->id == 23) {
                if ($g == 'Men') {
                  $g = 'Putra';
                } elseif ($g == 'Women') {
                  $g = 'Putri';
                }
              }

              $data['language_id'] = $language->id;
              $data['ticket_id'] = $t->id;
              $data['title'] = $name_competition . ' Individu ' . $g;
              $data['description'] = null;
              TicketContent::create($data);
            }
          }

          // Team
          if ($request->team == "active") {
            $gender = ['Putra', 'Putri'];
            foreach ($gender as $g) {
              $ticket['event_id'] = $event->id;
              $ticket['event_type'] = 'tournament';
              $ticket['title'] = 'Team';
              $ticket['ticket_available_type'] = 'limited';
              $ticket['ticket_available'] = 100;
              $ticket['original_ticket_available'] = 100;
              $ticket['max_ticket_buy_type'] = 'limited';
              $ticket['max_buy_ticket'] = 10;
              $ticket['pricing_type'] = 'normal';
              $ticket['pricing_scheme'] = $request['pricing_scheme'];
              $ticket['price'] = 300000;
              $ticket['f_price'] = 300000;
              $ticket['international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
              $ticket['f_international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
              $ticket['early_bird_discount'] = 'disable';
              $ticket['early_bird_discount_type'] = 'fixed';
              $ticket['late_price_discount'] = 'disable';
              $ticket['late_price_discount_type'] = 'fixed';
              $t = Ticket::create($ticket);

              $languages = Language::all();
              foreach ($languages as $language) {
                if ($language->id == 8) {
                  if ($g == 'Putra') {
                    $g = 'Men';
                  } elseif ($g == 'Putri') {
                    $g = 'Women';
                  }
                }

                if ($language->id == 23) {
                  if ($g == 'Men') {
                    $g = 'Putra';
                  } elseif ($g == 'Women') {
                    $g = 'Putri';
                  }
                }

                $data['language_id'] = $language->id;
                $data['ticket_id'] = $t->id;
                $data['title'] = $name_competition . ' Team ' . $g;
                $data['description'] = null;
                TicketContent::create($data);
              }
            }
          }

          // Mix Team
          if ($request->mixed_team == "active") {
            $ticket['event_id'] = $event->id;
            $ticket['event_type'] = 'tournament';
            $ticket['title'] = 'Mix Team';
            $ticket['ticket_available_type'] = 'limited';
            $ticket['ticket_available'] = 100;
            $ticket['original_ticket_available'] = 100;
            $ticket['max_ticket_buy_type'] = 'limited';
            $ticket['max_buy_ticket'] = 10;
            $ticket['pricing_type'] = 'normal';
            $ticket['pricing_scheme'] = $request['pricing_scheme'];
            $ticket['price'] = 300000;
            $ticket['f_price'] = 300000;
            $ticket['international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
            $ticket['f_international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
            $ticket['early_bird_discount'] = 'disable';
            $ticket['early_bird_discount_type'] = 'fixed';
            $ticket['late_price_discount'] = 'disable';
            $ticket['late_price_discount_type'] = 'fixed';
            $t = Ticket::create($ticket);

            $languages = Language::all();
            foreach ($languages as $language) {
              $data['language_id'] = $language->id;
              $data['ticket_id'] = $t->id;
              $data['title'] = $name_competition . ' Mix Team';
              $data['description'] = null;
              TicketContent::create($data);
            }
          }

          // // Official
          // if ($request->official == "active") {
          //   $ticket['event_id'] = $event->id;
          //   $ticket['event_type'] = 'tournament';
          //   $ticket['title'] = 'Official';
          //   $ticket['ticket_available_type'] = 'limited';
          //   $ticket['ticket_available'] = 100;
          //   $ticket['original_ticket_available'] = 100;
          //   $ticket['max_ticket_buy_type'] = 'limited';
          //   $ticket['max_buy_ticket'] = 10;
          //   $ticket['pricing_type'] = 'normal';
          //   $ticket['pricing_scheme'] = $request['pricing_scheme'];
          //   $ticket['price'] = 300000;
          //   $ticket['f_price'] = 300000;
          //   $ticket['international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
          //   $ticket['f_international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
          //   $ticket['early_bird_discount'] = 'disable';
          //   $ticket['early_bird_discount_type'] = 'fixed';
          //   $ticket['late_price_discount'] = 'disable';
          //   $ticket['late_price_discount_type'] = 'fixed';
          //   $t = Ticket::create($ticket);

          //   $languages = Language::all();
          //   foreach ($languages as $language) {
          //     $data['language_id'] = $language->id;
          //     $data['ticket_id'] = $t->id;
          //     $data['title'] = $name_competition . ' Official';
          //     $data['description'] = null;
          //     TicketContent::create($data);
          //   }
          // }

          $i++;
        }

        // Official
        if ($request->official == "active") {
          $ticket['event_id'] = $event->id;
          $ticket['competition_id'] = null;
          $ticket['event_type'] = 'tournament';
          $ticket['title'] = 'Official';
          $ticket['ticket_available_type'] = 'limited';
          $ticket['ticket_available'] = 100;
          $ticket['original_ticket_available'] = 100;
          $ticket['max_ticket_buy_type'] = 'limited';
          $ticket['max_buy_ticket'] = 10;
          $ticket['pricing_type'] = 'normal';
          $ticket['pricing_scheme'] = $request['pricing_scheme'];
          $ticket['price'] = 300000;
          $ticket['f_price'] = 300000;
          $ticket['international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
          $ticket['f_international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
          $ticket['early_bird_discount'] = 'disable';
          $ticket['early_bird_discount_type'] = 'fixed';
          $ticket['late_price_discount'] = 'disable';
          $ticket['late_price_discount_type'] = 'fixed';
          $t = Ticket::create($ticket);

          $languages = Language::all();
          foreach ($languages as $language) {
            $data['language_id'] = $language->id;
            $data['ticket_id'] = $t->id;
            $data['title'] = 'Official';
            $data['description'] = 'For Officials Only';
            TicketContent::create($data);
          }
        }
      });

      Session::flash('success', 'Added Successfully');
      return response()->json(['status' => 'success'], 200);
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  public function store_tournament(StoreTournamentRequest $request)
  {
    try {
      if (empty(Auth::guard('admin'))) {
        return Response(
          [
            'errors' => [
              'message' => [
                'Create Error, because not have sessions login'
              ]
            ]
          ],
          401
        );
      }

      DB::transaction(function () use ($request) {
        $request->is_featured = "yes";
        $request->date_type = "single";

        //calculate duration
        if ($request->date_type == 'single') {
          $start = Carbon::parse($request->start_date . $request->start_time);
          $end =  Carbon::parse($request->end_date . $request->end_time);
          $diffent = DurationCalulate($start, $end);
        }
        //calculate duration end

        $in = $request->all();
        $in['duration'] = $request->date_type == 'single' ? $diffent : '';

        $img = $request->file('thumbnail');
        if ($request->hasFile('thumbnail')) {
          $filename = time() . '.' . $img->getClientOriginalExtension();
          $directory = public_path('assets/admin/img/event/thumbnail/');
          @mkdir($directory, 0775, true);
          $request->file('thumbnail')->move($directory, $filename);
          $in['thumbnail'] = $filename;
        }

        $thb_file = $request->file('thb_file');
        if ($request->hasFile('thb_file')) {
          $filename = 'thb-file-' . time() . '.' . $thb_file->getClientOriginalExtension();
          $directory = public_path('assets/admin/img/event/tournament_uploaded/');
          @mkdir($directory, 0775, true);
          $request->file('thb_file')->move($directory, $filename);
          $in['thb_file'] = $filename;
        }

        $in['f_price'] = $request->price;
        $in['end_date_time'] = Carbon::parse($request->end_date . ' ' . $request->end_time);
        $in['is_featured'] = $request->is_featured;
        $event = Event::create($in);


        $in['event_id'] = $event->id;
        if ($request->event_type == 'online') {
          if (!$request->pricing_type) {
            $in['pricing_type'] = 'normal';
          }
          $in['early_bird_discount'] = $request->early_bird_discount_type;
          $in['early_bird_discount_type'] = $request->discount_type;
          Ticket::create($in);
        }

        $slders = $request->slider_images;
        foreach ($slders as $key => $id) {
          $event_image = EventImage::where('id', $id)->first();
          if ($event_image) {
            $event_image->event_id = $event->id;
            $event_image->save();
          }
        }

        $languages = Language::all();
        foreach ($languages as $language) {
          $event_content = new EventContent();
          $event_content->language_id = $language->id;
          $event_content->event_category_id = $request[$language->code . '_category_id'];
          $event_content->event_id = $event->id;
          $event_content->title = $request[$language->code . '_title'];

          if ($request->event_type == 'venue') {
            $event_content->address = $request[$language->code . '_address'];
            $event_content->country = $request[$language->code . '_country'];
            $event_content->state = $request[$language->code . '_state'];
            $event_content->city = $request[$language->code . '_city'];
            $event_content->zip_code = $request[$language->code . '_zip_code'];
          }

          if ($request->event_type == 'tournament' || $request->event_type == 'turnamen') {
            $event_content->country_id = $request[$language->code . '_country'];
            $event_content->state_id = $request[$language->code . '_state'];
            $event_content->city_id = $request[$language->code . '_city'];
            $event_content->country = InternationalCountries::find($request[$language->code . '_country'])->name;
            $event_content->address = $request[$language->code . '_address'];
            $event_content->zip_code = $request[$language->code . '_zip_code'];

            if ($request[$language->code . '_country'] == 102 || $request[$language->code . '_country'] == '102') {
              $event_content->state = IndonesianProvince::find($request[$language->code . '_state'])->name;
              $event_content->city = IndonesianCities::find($request[$language->code . '_city'])->name;
            } else {
              $event_content->state = InternationalStates::find($request[$language->code . '_state'])->name;
              $event_content->city = InternationalCities::find($request[$language->code . '_city'])->name;
            }
          }

          $event_content->slug = createSlug($request[$language->code . '_title']);
          $event_content->description = Purifier::clean($request[$language->code . '_description'], 'youtube');
          $event_content->refund_policy = $request[$language->code . '_refund_policy'];
          $event_content->meta_keywords = $request[$language->code . '_meta_keywords'];
          $event_content->meta_description = $request[$language->code . '_meta_description'];
          $event_content->save();
        }

        // event type public or private
        if ($request->event_publisher) {
          $input['event_id'] = $event->id;
          $input['event_type'] = $request->event_publisher;
          $input['shared_type'] = 'event type ' . $request->event_publisher;
          $input['link_event'] = $request->link_event_publisher;
          $input['code'] = $request->code;
          $input['description'] = $request->description_event_publisher;
          EventPublisher::create($input);
        }

        // contingent type
        if ($request->delegation_type) {
          $input['event_id'] = $event->id;
          $input['contingent_type'] = $request->delegation_type;
          $input['select_type'] = $request->select_type;
          $input['country_id'] = $request->select_country;
          $input['country'] = $request->contingent_country;
          $input['province_id'] = $request->select_state;
          $input['province'] = $request->contingent_province;
          $input['state_id'] = $request->select_state;
          $input['state'] = $request->contingent_state;
          $input['city_id'] = $request->contingent_city_id;
          $input['city'] = $request->contingent_city;
          ContingentType::create($input);
        }

        // Add Competition Category
        $i = 1;
        foreach ($request->competition_categories as $key => $c) {
          $competition_categories = CompetitionCategories::where('id', $request->competition_categories[$key])->first();
          $name_competition = $competition_categories->name . ' ' . $request->competition_class_name[$key] . ' ' . $request->competition_distance[$key] . ' Meter';

          $competitions = Competitions::create([
            'event_id' => $event->id,
            'name' => $name_competition,
            'competition_type_id' => 0,
            'competition_category_id' => $request->competition_categories[$key],
            'gender' => null,
            'contingent' => null,
            'distance' => $request->competition_distance[$key],
            'class_type' => $request->competition_class_type[$key],
            'class_name' => $request->competition_class_name[$key],
            'description' => null,
          ]);
          $competition_id = $competitions->id;

          // Individual
          $gender = ['Putra', 'Putri'];
          foreach ($gender as $g) {
            $ticket['event_id'] = $event->id;
            $ticket['competition_id'] = $competition_id;
            $ticket['event_type'] = 'tournament';
            $ticket['title'] = 'Individu';
            $ticket['ticket_available_type'] = 'limited';
            $ticket['ticket_available'] = 100;
            $ticket['original_ticket_available'] = 100;
            $ticket['max_ticket_buy_type'] = 'limited';
            $ticket['max_buy_ticket'] = 10;
            $ticket['pricing_type'] = 'normal';
            $ticket['pricing_scheme'] = $request['pricing_scheme'];
            $ticket['price'] = 300000;
            $ticket['f_price'] = 300000;
            $ticket['international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
            $ticket['f_international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
            $ticket['early_bird_discount'] = 'disable';
            $ticket['early_bird_discount_type'] = 'fixed';
            $ticket['late_price_discount'] = 'disable';
            $ticket['late_price_discount_type'] = 'fixed';
            $t = Ticket::create($ticket);

            $languages = Language::all();
            foreach ($languages as $language) {
              if ($language->id == 8) {
                if ($g == 'Putra') {
                  $g = 'Men';
                } elseif ($g == 'Putri') {
                  $g = 'Women';
                }
              }

              if ($language->id == 23) {
                if ($g == 'Men') {
                  $g = 'Putra';
                } elseif ($g == 'Women') {
                  $g = 'Putri';
                }
              }

              $data['language_id'] = $language->id;
              $data['ticket_id'] = $t->id;
              $data['title'] = $name_competition . ' Individu ' . $g;
              $data['description'] = null;
              TicketContent::create($data);
            }
          }

          // Team
          $gender = ['Putra', 'Putri'];
          foreach ($gender as $g) {
            $ticket['event_id'] = $event->id;
            $ticket['event_type'] = 'tournament';
            $ticket['title'] = 'Team';
            $ticket['ticket_available_type'] = 'limited';
            $ticket['ticket_available'] = 100;
            $ticket['original_ticket_available'] = 100;
            $ticket['max_ticket_buy_type'] = 'limited';
            $ticket['max_buy_ticket'] = 10;
            $ticket['pricing_type'] = 'normal';
            $ticket['pricing_scheme'] = $request['pricing_scheme'];
            $ticket['price'] = 300000;
            $ticket['f_price'] = 300000;
            $ticket['international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
            $ticket['f_international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
            $ticket['early_bird_discount'] = 'disable';
            $ticket['early_bird_discount_type'] = 'fixed';
            $ticket['late_price_discount'] = 'disable';
            $ticket['late_price_discount_type'] = 'fixed';
            $t = Ticket::create($ticket);

            $languages = Language::all();
            foreach ($languages as $language) {
              if ($language->id == 8) {
                if ($g == 'Putra') {
                  $g = 'Men';
                } elseif ($g == 'Putri') {
                  $g = 'Women';
                }
              }

              if ($language->id == 23) {
                if ($g == 'Men') {
                  $g = 'Putra';
                } elseif ($g == 'Women') {
                  $g = 'Putri';
                }
              }

              $data['language_id'] = $language->id;
              $data['ticket_id'] = $t->id;
              $data['title'] = $name_competition . ' Team ' . $g;
              $data['description'] = null;
              TicketContent::create($data);
            }
          }

          // Mix Team
          $ticket['event_id'] = $event->id;
          $ticket['event_type'] = 'tournament';
          $ticket['title'] = 'Mix Team';
          $ticket['ticket_available_type'] = 'limited';
          $ticket['ticket_available'] = 100;
          $ticket['original_ticket_available'] = 100;
          $ticket['max_ticket_buy_type'] = 'limited';
          $ticket['max_buy_ticket'] = 10;
          $ticket['pricing_type'] = 'normal';
          $ticket['pricing_scheme'] = $request['pricing_scheme'];
          $ticket['price'] = 300000;
          $ticket['f_price'] = 300000;
          $ticket['international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
          $ticket['f_international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
          $ticket['early_bird_discount'] = 'disable';
          $ticket['early_bird_discount_type'] = 'fixed';
          $ticket['late_price_discount'] = 'disable';
          $ticket['late_price_discount_type'] = 'fixed';
          $t = Ticket::create($ticket);

          $languages = Language::all();
          foreach ($languages as $language) {
            $data['language_id'] = $language->id;
            $data['ticket_id'] = $t->id;
            $data['title'] = $name_competition . ' Mix Team';
            $data['description'] = null;
            TicketContent::create($data);
          }
          $i++;
        }

        // Official
        $ticket['event_id'] = $event->id;
        $ticket['competition_id'] = null;
        $ticket['event_type'] = 'tournament';
        $ticket['title'] = 'Official';
        $ticket['ticket_available_type'] = 'limited';
        $ticket['ticket_available'] = 100;
        $ticket['original_ticket_available'] = 100;
        $ticket['max_ticket_buy_type'] = 'limited';
        $ticket['max_buy_ticket'] = 10;
        $ticket['pricing_type'] = 'normal';
        $ticket['pricing_scheme'] = $request['pricing_scheme'];
        $ticket['price'] = 300000;
        $ticket['f_price'] = 300000;
        $ticket['international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
        $ticket['f_international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
        $ticket['early_bird_discount'] = 'disable';
        $ticket['early_bird_discount_type'] = 'fixed';
        $ticket['late_price_discount'] = 'disable';
        $ticket['late_price_discount_type'] = 'fixed';
        $t = Ticket::create($ticket);

        $languages = Language::all();
        foreach ($languages as $language) {
          $data['language_id'] = $language->id;
          $data['ticket_id'] = $t->id;
          $data['title'] = 'Official';
          $data['description'] = 'For Officials Only';
          TicketContent::create($data);
        }
      });

      Session::flash('success', 'Added Successfully');
      return response()->json(['status' => 'success'], 200);
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  /**
   * delete events dates
   */
  public function deleteDate($id)
  {
    $date = EventDates::where('id', $id)->first();
    $date->delete();
    return 'success';
  }
  /**
   * Update status (active/DeActive) of a specified resource.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function updateStatus(Request $request, $id)
  {
    $event = Event::find($id);

    $event->update([
      'status' => $request['status']
    ]);
    Session::flash('success', 'Deleted Successfully');

    return redirect()->back();
  }
  /**
   * Update featured status of a specified resource.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function updateFeatured(Request $request, $id)
  {
    $event = Event::find($id);

    if ($request['is_featured'] == 'yes') {
      $event->is_featured = 'yes';
      $event->save();

      Session::flash('success', 'Updated Successfully');
    } else {
      $event->is_featured = 'no';
      $event->save();

      Session::flash('success', 'Updated Successfully');
    }

    return redirect()->back();
  }

  public function edit($id)
  {
    $event = Event::with('ticket')->findOrFail($id);
    if (empty($event)) {
      return back();
    }
    $information['event'] = $event;

    $information['languages'] = Language::all();
    $information['countries'] = Country::get();
    $information['cities'] = City::where('country_id',  $event->country)->orderBy('name', 'asc')->get();
    $information['states'] = State::where('country_id',  $event->country)->orderBy('name', 'asc')->get();
    $organizers = Organizer::get();
    $information['organizers'] = $organizers;

    $information['getCurrencyInfo']  = $this->getCurrencyInfo();

    if ($event->event_type == "tournament" || $event->event_type == "turnamen") {
      $information['ticket_info'] =  Ticket::withTrashed()->where('event_id', $id)->first();
      // $information['individu_allowed'] = Ticket::withTrashed()->where('event_id', $id)->where('title', 'Individu')->where('deleted_at', null)->exists();
      // $information['team_allowed'] = Ticket::where('event_id', $id)->where('title', 'Team')->where('deleted_at', null)->exists();
      // $information['mix_team_allowed'] = Ticket::where('event_id', $id)->where('title', 'Mix Team')->where('deleted_at', null)->exists();
      // $information['official_allowed'] = Ticket::where('event_id', $id)->where('title', 'Official')->where('deleted_at', null)->exists();
      $information['event_publisher'] = EventPublisher::where('event_id', $id)->first();
      $information['contingent_type'] = ContingentType::where('event_id', $id)->first();
      $information['competitions'] = Competitions::where('event_id', $id)->get();
      $information['competition_categories'] = CompetitionCategories::all();
      $information['competition_class_type'] = CompetitionClassType::all();
      $information['competition_class_name'] = CompetitionClassName::all();
      $information['competition_distance'] = CompetitionDistance::all();
      $information['delegation_type'] = DelegationType::all();
      $information['international_countries'] = InternationalCountries::all();
      if (strtolower($information['contingent_type']->select_type) == 'city/district') {
        if ($information['contingent_type']->country_id == 102) {
          $information['state_delegation_list'] = IndonesianProvince::select('id', 'name')->get();
        } else {
          $information['state_delegation_list'] = InternationalStates::select('id', 'name')
            ->where('country_id', $information['contingent_type']->country_id)
            ->get();
        }
      }
      return view('backend.event.edit_tournament', $information);
    } else {
      return view('backend.event.edit', $information);
    }
  }

  public function imagedbrmv(Request $request)
  {
    $pi = EventImage::where('id', $request->fileid)->first();
    $event_id = $pi->event_id;
    $image_count = EventImage::where('event_id', $event_id)->get()->count();
    if ($image_count > 1) {
      @unlink(public_path('assets/admin/img/event-gallery/') . $pi->image);
      $pi->delete();
      return $pi->id;
    } else {
      return 'false';
    }
  }

  public function images($portid)
  {
    $images = EventImage::where('event_id', $portid)->get();
    return $images;
  }

  public function update(UpdateRequest $request)
  {
    //calculate duration
    if ($request->date_type == 'single') {
      $start = Carbon::parse($request->start_date . $request->start_time);
      $end =  Carbon::parse($request->end_date . $request->end_time);
      $diffent = DurationCalulate($start, $end);
    }
    //calculate duration end
    $img = $request->file('thumbnail');

    $in = $request->all();

    $event = Event::where('id', $request->event_id)->first();
    if ($request->hasFile('thumbnail')) {
      @unlink(public_path('assets/admin/img/event/thumbnail/') . $event->thumbnail);
      $filename = time() . '.' . $img->getClientOriginalExtension();
      @mkdir(public_path('assets/admin/img/event/thumbnail/'), 0775, true);
      $request->file('thumbnail')->move(public_path('assets/admin/img/event/thumbnail/'), $filename);
      $in['thumbnail'] = $filename;
    }

    $languages = Language::all();

    $i = 1;
    foreach ($languages as $language) {
      $event_content = EventContent::where('event_id', $event->id)->where('language_id', $language->id)->first();
      if (!$event_content) {
        $event_content = new EventContent();
      }
      $event_content->language_id = $language->id;
      $event_content->event_category_id = $request[$language->code . '_category_id'];
      $event_content->event_id = $event->id;
      $event_content->title = $request[$language->code . '_title'];
      if ($request->event_type == 'venue') {
        $event_content->address = $request[$language->code . '_address'];
        $event_content->country = $request[$language->code . '_country'];
        $event_content->state = $request[$language->code . '_state'];
        $event_content->city = $request[$language->code . '_city'];
        $event_content->zip_code = $request[$language->code . '_zip_code'];
      }
      $event_content->slug = createSlug($request[$language->code . '_title']);
      $event_content->description = Purifier::clean($request[$language->code . '_description'], 'youtube');
      $event_content->refund_policy = $request[$language->code . '_refund_policy'];
      $event_content->meta_keywords = $request[$language->code . '_meta_keywords'];
      $event_content->meta_description = $request[$language->code . '_meta_description'];
      $event_content->save();
    }
    if ($request->event_type == 'online') {
      if (!$request->pricing_type) {
        $pricing_type = 'normal';
      } else {
        $pricing_type = $request->pricing_type;
      }
      Ticket::where('event_id', $request->event_id)->update([
        'price' => $request->price,
        'f_price' => $request->price,
        'pricing_type' => $pricing_type,
        'ticket_available_type' => $request->ticket_available_type,
        'ticket_available' => $request->ticket_available_type == 'limited' ? $request->ticket_available : null,
        'max_ticket_buy_type' => $request->max_ticket_buy_type,
        'max_buy_ticket' => $request->max_ticket_buy_type == 'limited' ? $request->max_buy_ticket : null,
        'early_bird_discount' => $request->early_bird_discount_type,
        'early_bird_discount_type' => $request->discount_type,
        'early_bird_discount_amount' => $request->early_bird_discount_amount,
        'early_bird_discount_date' => $request->early_bird_discount_date,
        'early_bird_discount_time' => $request->early_bird_discount_time,
      ]);
    }

    $event = Event::where('id', $event->id)->first();

    if ($request->date_type == 'multiple') {
      $i = 1;
      foreach ($request->m_start_date as $key => $date) {
        $start = Carbon::parse($date . $request->m_start_time[$key]);
        $end =  Carbon::parse($request->m_end_date[$key] . $request->m_end_time[$key]);
        $diffent = DurationCalulate($start, $end);

        if (!empty($request->date_ids[$key])) {
          $event_date = EventDates::where('id', $request->date_ids[$key])->first();
          $event_date->start_date = $date;
          $event_date->start_time = $request->m_start_time[$key];
          $event_date->end_date = $request->m_end_date[$key];
          $event_date->end_time = $request->m_end_time[$key];
          $event_date->duration = $diffent;
          $event_date->start_date_time = $start;
          $event_date->end_date_time = $end;
          $event_date->save();
        } else {
          EventDates::create([
            'event_id' => $event->id,
            'start_date' => $date,
            'start_time' => $request->m_start_time[$key],
            'end_date' => $request->m_end_date[$key],
            'end_time' => $request->m_end_time[$key],
            'duration' => $diffent,
            'start_date_time' => $start,
            'end_date_time' => $end,
          ]);
        }
        if ($i == 1) {
          $event->update([
            'duration' => $diffent
          ]);
        }
        $i++;
      }
    }

    if ($request->date_type == 'single') {
      $in['end_date_time'] = Carbon::parse($request->end_date . ' ' . $request->end_time);
      $in['duration'] = $diffent;
    } else {
      //update event date time
      $event_date = EventDates::where('event_id', $event->id)->orderBy('end_date_time', 'desc')->first();

      $in['end_date_time'] = $event_date->end_date_time;
    }


    $event->update($in);
    Session::flash('success', 'Updated Successfully');

    return response()->json(['status' => 'success'], 200);
  }

  public function update_tournament_backup(UpdateRequestTournament $request)
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

      $check_have_a_bookings = Booking::where('event_id', $request->event_id)
        ->whereIn('paymentStatus', ['completed', 'pending'])
        ->get()
        ->count();

      if ($check_have_a_bookings > 0) {
        return Response([
          'errors' => [
            'message' => [
              'Update Error, Because the event already has participants who have booked'
            ]
          ]
        ], 403);
      }

      // DB Transaction
      DB::transaction(function () use ($request) {
        $request->is_featured = "yes";
        $request->date_type = "single";

        //calculate duration
        if ($request->date_type == 'single') {
          $start = Carbon::parse($request->start_date . $request->start_time);
          $end = Carbon::parse($request->end_date . $request->end_time);
          $diffent = DurationCalulate($start, $end);
        }
        //calculate duration end
        $in = $request->all();
        $in['duration'] = $request->date_type == 'single' ? $diffent : '';

        $event = Event::where('id', $request->event_id)->first();

        $img = $request->file('thumbnail');
        if ($request->hasFile('thumbnail')) {
          $filename = time() . '.' . $img->getClientOriginalExtension();
          $directory = public_path('assets/admin/img/event/thumbnail/');
          @mkdir($directory, 0775, true);
          $request->file('thumbnail')->move($directory, $filename);
          $in['thumbnail'] = $filename;
        }

        $thb_file = $request->file('thb_file');
        if ($request->hasFile('thb_file')) {
          $filename = 'thb-file-' . time() . '.' . $thb_file->getClientOriginalExtension();
          $directory = public_path('assets/admin/img/event/tournament_uploaded/');
          @mkdir($directory, 0775, true);
          $request->file('thb_file')->move($directory, $filename);
          $in['thb_file'] = $filename;
        }

        $in['end_date_time'] = Carbon::parse($request->end_date . ' ' . $request->end_time);
        $in['is_featured'] = $request->is_featured;
        $in['f_price'] = $request->price;


        // event content
        $languages = Language::all();

        foreach ($languages as $language) {
          $event_content = EventContent::where('event_id', $event->id)->where('language_id', $language->id)->first();
          if (!$event_content) {
            $event_content = new EventContent();
          }
          $event_content->language_id = $language->id;
          $event_content->event_category_id = $request[$language->code . '_category_id'];
          $event_content->event_id = $event->id;
          $event_content->title = $request[$language->code . '_title'];

          if ($request->event_type == 'tournament' || $request->event_type == 'turnamen') {
            $event_content->country_id = $request[$language->code . '_country'];
            $event_content->state_id = $request[$language->code . '_state'];
            $event_content->city_id = $request[$language->code . '_city'];
            $event_content->country = InternationalCountries::find($request[$language->code . '_country'])->name;
            $event_content->address = $request[$language->code . '_address'];
            $event_content->zip_code = $request[$language->code . '_zip_code'];

            if ($request[$language->code . '_country'] == 102 || $request[$language->code . '_country'] == '102') {
              $event_content->state = IndonesianProvince::find($request[$language->code . '_state'])->name;
              $event_content->city = IndonesianCities::find($request[$language->code . '_city'])->name;
            } else {
              $event_content->state = InternationalStates::find($request[$language->code . '_state'])->name;
              $event_content->city = InternationalCities::find($request[$language->code . '_city'])->name;
            }
          }

          $event_content->slug = createSlug($request[$language->code . '_title']);
          $event_content->description = Purifier::clean($request[$language->code . '_description'], 'youtube');
          $event_content->refund_policy = $request[$language->code . '_refund_policy'];
          $event_content->meta_keywords = $request[$language->code . '_meta_keywords'];
          $event_content->meta_description = $request[$language->code . '_meta_description'];
          $event_content->save();
        }

        // event type public or private
        if ($request->event_publisher) {
          $event_publisher = EventPublisher::where('event_id', $event->id)->first();
          if (!$event_publisher) {
            $event_publisher = new EventPublisher();
          }
          $event_publisher->event_id = $event->id;
          $event_publisher->event_type = $request->event_publisher;
          $event_publisher->shared_type = 'event type ' . $request->event_publisher;
          $event_publisher->link_event = $request->link_event_publisher;
          $event_publisher->code = $request->code;
          $event_publisher->description = $request->description_event_publisher;
          $event_publisher->save();
        }

        // contingent type
        if ($request->delegation_type) {
          $contingent_type = ContingentType::where('event_id', $event->id)->first();
          if (!$contingent_type) {
            $contingent_type = new ContingentType();
          }
          $contingent_type->event_id = $event->id;
          $contingent_type->contingent_type = $request->delegation_type;
          $contingent_type->select_type = $request->select_type;
          $contingent_type->country_id = $request->select_country;
          $contingent_type->country = $request->contingent_country;
          $contingent_type->province_id = $request->select_state;
          $contingent_type->province = $request->contingent_province;
          $contingent_type->state_id = $request->select_state;
          $contingent_type->state = $request->contingent_state;
          $contingent_type->city_id = $request->contingent_city_id;
          $contingent_type->city = $request->contingent_city;
          $contingent_type->save();
        }

        Competitions::where('event_id', $event->id)->delete();
        Ticket::where('event_id', $event->id)->delete();

        $i = 1;
        foreach ($request->competition_categories as $key => $c) {
          $competition_categories = CompetitionCategories::where('id', $request->competition_categories[$key])->first();
          $name_competition = $competition_categories->name . ' ' . $request->competition_class_name[$key] . ' ' . $request->competition_distance[$key] . ' Meter';

          $competitions = Competitions::create([
            'event_id' => $event->id,
            'name' => $name_competition,
            'competition_type_id' => 0,
            'competition_category_id' => $request->competition_categories[$key],
            'gender' => null,
            'contingent' => null,
            'distance' => $request->competition_distance[$key],
            'class_type' => $request->competition_class_type[$key],
            'class_name' => $request->competition_class_name[$key],
            'description' => null,
          ]);
          $competition_id = $competitions->id;

          // Individual
          $gender = ['Putra', 'Putri'];
          foreach ($gender as $g) {
            $ticket['event_id'] = $event->id;
            $ticket['competition_id'] = $competition_id;
            $ticket['event_type'] = 'tournament';
            $ticket['title'] = 'Individu';
            $ticket['ticket_available_type'] = 'limited';
            $ticket['ticket_available'] = 100;
            $ticket['original_ticket_available'] = 100;
            $ticket['max_ticket_buy_type'] = 'limited';
            $ticket['max_buy_ticket'] = 10;
            $ticket['pricing_type'] = 'normal';
            $ticket['pricing_scheme'] = $request['pricing_scheme'];
            $ticket['price'] = 300000;
            $ticket['f_price'] = 300000;
            $ticket['international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
            $ticket['f_international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
            $ticket['early_bird_discount'] = 'disable';
            $ticket['early_bird_discount_type'] = 'fixed';
            $ticket['late_price_discount'] = 'disable';
            $ticket['late_price_discount_type'] = 'fixed';
            $t = Ticket::create($ticket);

            $languages = Language::all();
            foreach ($languages as $language) {
              if ($language->id == 8) {
                if ($g == 'Putra') {
                  $g = 'Men';
                } elseif ($g == 'Putri') {
                  $g = 'Women';
                }
              }

              if ($language->id == 23) {
                if ($g == 'Men') {
                  $g = 'Putra';
                } elseif ($g == 'Women') {
                  $g = 'Putri';
                }
              }

              $data['language_id'] = $language->id;
              $data['ticket_id'] = $t->id;
              $data['title'] = $name_competition . ' Individu ' . $g;
              $data['description'] = null;
              TicketContent::create($data);
            }
          }

          // Team
          if ($request->team == "active") {
            $gender = ['Putra', 'Putri'];
            foreach ($gender as $g) {
              $ticket['event_id'] = $event->id;
              $ticket['event_type'] = 'tournament';
              $ticket['title'] = 'Team';
              $ticket['ticket_available_type'] = 'limited';
              $ticket['ticket_available'] = 100;
              $ticket['original_ticket_available'] = 100;
              $ticket['max_ticket_buy_type'] = 'limited';
              $ticket['max_buy_ticket'] = 10;
              $ticket['pricing_type'] = 'normal';
              $ticket['pricing_scheme'] = $request['pricing_scheme'];
              $ticket['price'] = 300000;
              $ticket['f_price'] = 300000;
              $ticket['international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
              $ticket['f_international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
              $ticket['early_bird_discount'] = 'disable';
              $ticket['early_bird_discount_type'] = 'fixed';
              $ticket['late_price_discount'] = 'disable';
              $ticket['late_price_discount_type'] = 'fixed';
              $t = Ticket::create($ticket);

              $languages = Language::all();
              foreach ($languages as $language) {
                if ($language->id == 8) {
                  if ($g == 'Putra') {
                    $g = 'Men';
                  } elseif ($g == 'Putri') {
                    $g = 'Women';
                  }
                }

                if ($language->id == 23) {
                  if ($g == 'Men') {
                    $g = 'Putra';
                  } elseif ($g == 'Women') {
                    $g = 'Putri';
                  }
                }

                $data['language_id'] = $language->id;
                $data['ticket_id'] = $t->id;
                $data['title'] = $name_competition . ' Team ' . $g;
                $data['description'] = null;
                TicketContent::create($data);
              }
            }
          }

          // Mix Team
          if ($request->mixed_team == "active") {
            $ticket['event_id'] = $event->id;
            $ticket['event_type'] = 'tournament';
            $ticket['title'] = 'Mix Team';
            $ticket['ticket_available_type'] = 'limited';
            $ticket['ticket_available'] = 100;
            $ticket['original_ticket_available'] = 100;
            $ticket['max_ticket_buy_type'] = 'limited';
            $ticket['max_buy_ticket'] = 10;
            $ticket['pricing_type'] = 'normal';
            $ticket['pricing_scheme'] = $request['pricing_scheme'];
            $ticket['price'] = 300000;
            $ticket['f_price'] = 300000;
            $ticket['international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
            $ticket['f_international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
            $ticket['early_bird_discount'] = 'disable';
            $ticket['early_bird_discount_type'] = 'fixed';
            $ticket['late_price_discount'] = 'disable';
            $ticket['late_price_discount_type'] = 'fixed';
            $t = Ticket::create($ticket);

            $languages = Language::all();
            foreach ($languages as $language) {
              $data['language_id'] = $language->id;
              $data['ticket_id'] = $t->id;
              $data['title'] = $name_competition . ' Mix Team';
              $data['description'] = null;
              TicketContent::create($data);
            }
          }

          // // Official
          // if ($request->official == "active") {
          //   $ticket['event_id'] = $event->id;
          //   $ticket['event_type'] = 'tournament';
          //   $ticket['title'] = 'Official';
          //   $ticket['ticket_available_type'] = 'limited';
          //   $ticket['ticket_available'] = 100;
          //   $ticket['original_ticket_available'] = 100;
          //   $ticket['max_ticket_buy_type'] = 'limited';
          //   $ticket['max_buy_ticket'] = 10;
          //   $ticket['pricing_type'] = 'normal';
          //   $ticket['pricing_scheme'] = $request['pricing_scheme'];
          //   $ticket['price'] = 300000;
          //   $ticket['f_price'] = 300000;
          //   $ticket['international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
          //   $ticket['f_international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
          //   $ticket['early_bird_discount'] = 'disable';
          //   $ticket['early_bird_discount_type'] = 'fixed';
          //   $ticket['late_price_discount'] = 'disable';
          //   $ticket['late_price_discount_type'] = 'fixed';
          //   $t = Ticket::create($ticket);

          //   $languages = Language::all();
          //   foreach ($languages as $language) {
          //     $data['language_id'] = $language->id;
          //     $data['ticket_id'] = $t->id;
          //     $data['title'] = $name_competition . ' Official';
          //     $data['description'] = null;
          //     TicketContent::create($data);
          //   }
          // }

          $i++;
        }

        // Official
        if ($request->official == "active") {
          $ticket['event_id'] = $event->id;
          $ticket['competition_id'] = null;
          $ticket['event_type'] = 'tournament';
          $ticket['title'] = 'Official';
          $ticket['ticket_available_type'] = 'limited';
          $ticket['ticket_available'] = 100;
          $ticket['original_ticket_available'] = 100;
          $ticket['max_ticket_buy_type'] = 'limited';
          $ticket['max_buy_ticket'] = 10;
          $ticket['pricing_type'] = 'normal';
          $ticket['pricing_scheme'] = $request['pricing_scheme'];
          $ticket['price'] = 300000;
          $ticket['f_price'] = 300000;
          $ticket['international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
          $ticket['f_international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
          $ticket['early_bird_discount'] = 'disable';
          $ticket['early_bird_discount_type'] = 'fixed';
          $ticket['late_price_discount'] = 'disable';
          $ticket['late_price_discount_type'] = 'fixed';
          $t = Ticket::create($ticket);

          $languages = Language::all();
          foreach ($languages as $language) {
            $data['language_id'] = $language->id;
            $data['ticket_id'] = $t->id;
            $data['title'] = 'Official';
            $data['description'] = 'For Officials Only';
            TicketContent::create($data);
          }
        }

        $event->update($in);
      });
      // end DB Transaction

      Session::flash('success', 'Updated Successfully');
      return response()->json(['status' => 'success'], 200);
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  public function update_tournament(UpdateRequestTournament $request)
  {
    try {
      // return response()->json(['data' => $request->all()]);
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

      DB::transaction(function () use ($request) {
        $request->is_featured = "yes";
        $request->date_type = "single";

        //calculate duration
        if ($request->date_type == 'single') {
          $start = Carbon::parse($request->start_date . $request->start_time);
          $end = Carbon::parse($request->end_date . $request->end_time);
          $diffent = DurationCalulate($start, $end);
        }
        //calculate duration end
        $in = $request->all();
        $in['duration'] = $request->date_type == 'single' ? $diffent : '';

        $event = Event::where('id', $request->event_id)->first();

        $img = $request->file('thumbnail');
        if ($request->hasFile('thumbnail')) {
          $filename = time() . '.' . $img->getClientOriginalExtension();
          $directory = public_path('assets/admin/img/event/thumbnail/');
          @mkdir($directory, 0775, true);
          $request->file('thumbnail')->move($directory, $filename);
          $in['thumbnail'] = $filename;
        }

        $thb_file = $request->file('thb_file');
        if ($request->hasFile('thb_file')) {
          $filename = 'thb-file-' . time() . '.' . $thb_file->getClientOriginalExtension();
          $directory = public_path('assets/admin/img/event/tournament_uploaded/');
          @mkdir($directory, 0775, true);
          $request->file('thb_file')->move($directory, $filename);
          $in['thb_file'] = $filename;
        }

        $in['end_date_time'] = Carbon::parse($request->end_date . ' ' . $request->end_time);
        $in['is_featured'] = $request->is_featured;
        $in['f_price'] = $request->price;


        // event content
        $languages = Language::all();

        foreach ($languages as $language) {
          $event_content = EventContent::where('event_id', $event->id)->where('language_id', $language->id)->first();
          if (!$event_content) {
            $event_content = new EventContent();
          }
          $event_content->language_id = $language->id;
          $event_content->event_category_id = $request[$language->code . '_category_id'];
          $event_content->event_id = $event->id;
          $event_content->title = $request[$language->code . '_title'];

          if ($request->event_type == 'tournament' || $request->event_type == 'turnamen') {
            $event_content->country_id = $request[$language->code . '_country'];
            $event_content->state_id = $request[$language->code . '_state'];
            $event_content->city_id = $request[$language->code . '_city'];
            $event_content->country = InternationalCountries::find($request[$language->code . '_country'])->name;
            $event_content->address = $request[$language->code . '_address'];
            $event_content->zip_code = $request[$language->code . '_zip_code'];

            if ($request[$language->code . '_country'] == 102 || $request[$language->code . '_country'] == '102') {
              $event_content->state = IndonesianProvince::find($request[$language->code . '_state'])->name;
              $event_content->city = IndonesianCities::find($request[$language->code . '_city'])->name;
            } else {
              $event_content->state = InternationalStates::find($request[$language->code . '_state'])->name;
              $event_content->city = InternationalCities::find($request[$language->code . '_city'])->name;
            }
          }

          $event_content->slug = createSlug($request[$language->code . '_title']);
          $event_content->description = Purifier::clean($request[$language->code . '_description'], 'youtube');
          $event_content->refund_policy = $request[$language->code . '_refund_policy'];
          $event_content->meta_keywords = $request[$language->code . '_meta_keywords'];
          $event_content->meta_description = $request[$language->code . '_meta_description'];
          $event_content->save();
        }

        // event type public or private
        if ($request->event_publisher) {
          $event_publisher = EventPublisher::where('event_id', $event->id)->first();
          if (!$event_publisher) {
            $event_publisher = new EventPublisher();
          }
          $event_publisher->event_id = $event->id;
          $event_publisher->event_type = $request->event_publisher;
          $event_publisher->shared_type = 'event type ' . $request->event_publisher;
          $event_publisher->link_event = $request->link_event_publisher;
          $event_publisher->code = $request->code;
          $event_publisher->description = $request->description_event_publisher;
          $event_publisher->save();
        }

        // contingent type
        if ($request->delegation_type) {
          $contingent_type = ContingentType::where('event_id', $event->id)->first();
          if (!$contingent_type) {
            $contingent_type = new ContingentType();
          }
          $contingent_type->event_id = $event->id;
          $contingent_type->contingent_type = $request->delegation_type;
          $contingent_type->select_type = $request->select_type;
          $contingent_type->country_id = $request->select_country;
          $contingent_type->country = $request->contingent_country;
          $contingent_type->province_id = $request->select_state;
          $contingent_type->province = $request->contingent_province;
          $contingent_type->state_id = $request->select_state;
          $contingent_type->state = $request->contingent_state;
          $contingent_type->city_id = $request->contingent_city_id;
          $contingent_type->city = $request->contingent_city;
          $contingent_type->save();
        }

        $i = 1;
        $listCompetitionId = null;
        foreach ($request->competition_categories as $key => $c) {
          $competition_categories = CompetitionCategories::where('id', $request->competition_categories[$key])->first();
          $name_competition = $competition_categories->name . ' ' . $request->competition_class_name[$key] . ' ' . $request->competition_distance[$key] . ' Meter';
          if (!empty($request->competition_id)) {
            if (!empty($request->competition_id[$key])) {
              $listCompetitionId[] = $key;
              $competition_categories = CompetitionCategories::where('id', $request->competition_categories[$key])->first();
              $name_competition = $competition_categories->name . ' ' . $request->competition_class_name[$key] . ' ' . $request->competition_distance[$key] . ' Meter';
              Competitions::find($key)->update([
                'name' => $name_competition,
                'competition_category_id' => $request->competition_categories[$key],
                'distance' => $request->competition_distance[$key],
                'class_type' => $request->competition_class_type[$key],
                'class_name' => $request->competition_class_name[$key],
              ]);

              Ticket::withTrashed()->where('event_id', $event->id)->where('competition_id', $key)->restore();
              Ticket::withTrashed()->where('event_id', $event->id)->where('competition_id', $key)->update(['pricing_scheme' => $request['pricing_scheme']]);
            }

            if (empty($request->competition_id[$key])) {
              $checkHaveSameCategory = Competitions::withTrashed()
                ->where('event_id', $event->id)
                ->where('competition_category_id', $request->competition_categories[$key])
                ->where('distance', $request->competition_distance[$key])
                ->where('class_type', $request->competition_class_type[$key])
                ->where('class_name', $request->competition_class_name[$key])
                ->first();

              if (!empty($checkHaveSameCategory)) {
                $listCompetitionId[] = $checkHaveSameCategory->id;
                $competition_id = $checkHaveSameCategory->id;
                Ticket::withTrashed()->where('competition_id', $competition_id)->where('event_id', $event->id)->restore();
                Ticket::withTrashed()->where('event_id', $event->id)->where('competition_id', $competition_id)->update(['pricing_scheme' => $request['pricing_scheme']]);
              } else {
                $competitions = Competitions::create([
                  'event_id' => $event->id,
                  'name' => $name_competition,
                  'competition_type_id' => 0,
                  'competition_category_id' => $request->competition_categories[$key],
                  'gender' => null,
                  'contingent' => null,
                  'distance' => $request->competition_distance[$key],
                  'class_type' => $request->competition_class_type[$key],
                  'class_name' => $request->competition_class_name[$key],
                  'description' => null,
                ]);
                $competition_id = $competitions->id;
                $listCompetitionId[] = $competition_id;

                // Individual
                $individuTicketOld = Ticket::where('event_id', $event->id)->where('title', 'Individu')->first();
                $gender = ['Putra', 'Putri'];
                foreach ($gender as $g) {
                  $ticket['event_id'] = $event->id;
                  $ticket['competition_id'] = $competition_id;
                  $ticket['event_type'] = 'tournament';
                  $ticket['title'] = 'Individu';
                  $ticket['ticket_available_type'] = 'limited';
                  $ticket['ticket_available'] = 100;
                  $ticket['original_ticket_available'] = 100;
                  $ticket['max_ticket_buy_type'] = 'limited';
                  $ticket['max_buy_ticket'] = 10;
                  $ticket['pricing_type'] = 'normal';
                  $ticket['pricing_scheme'] = $request['pricing_scheme'];
                  $ticket['price'] = $individuTicketOld->f_price;
                  $ticket['f_price'] = $individuTicketOld->f_price;
                  $ticket['international_price'] = $individuTicketOld->f_international_price;
                  $ticket['f_international_price'] = $individuTicketOld->f_international_price;
                  $ticket['early_bird_discount'] =  $individuTicketOld->early_bird_discount;
                  $ticket['early_bird_discount_type'] =  $individuTicketOld->early_bird_discount_type;
                  $ticket['late_price_discount'] = $individuTicketOld->late_price_discount;
                  $ticket['late_price_discount_type'] =  $individuTicketOld->late_price_discount_type;
                  $ticket['status'] = $individuTicketOld->status;
                  $t = Ticket::create($ticket);

                  $languages = Language::all();
                  foreach ($languages as $language) {
                    if ($language->id == 8) {
                      if ($g == 'Putra') {
                        $g = 'Men';
                      } elseif ($g == 'Putri') {
                        $g = 'Women';
                      }
                    }

                    if ($language->id == 23) {
                      if ($g == 'Men') {
                        $g = 'Putra';
                      } elseif ($g == 'Women') {
                        $g = 'Putri';
                      }
                    }

                    $data['language_id'] = $language->id;
                    $data['ticket_id'] = $t->id;
                    $data['title'] = $name_competition . ' Individu ' . $g;
                    $data['description'] = null;
                    TicketContent::create($data);
                  }
                }

                // Team
                $teamTicketOld = Ticket::where('event_id', $event->id)->where('title', 'Team')->first();
                foreach ($gender as $g) {
                  $ticket['event_id'] = $event->id;
                  $ticket['event_type'] = 'tournament';
                  $ticket['competition_id'] = $competition_id;
                  $ticket['title'] = 'Team';
                  $ticket['ticket_available_type'] = 'limited';
                  $ticket['ticket_available'] = 0;
                  $ticket['original_ticket_available'] = 0;
                  $ticket['max_ticket_buy_type'] = 'limited';
                  $ticket['max_buy_ticket'] = 1;
                  $ticket['pricing_type'] = 'normal';
                  $ticket['pricing_scheme'] = $request['pricing_scheme'];
                  $ticket['price'] = $teamTicketOld->f_price;
                  $ticket['f_price'] = $teamTicketOld->f_price;
                  $ticket['international_price'] = $teamTicketOld->f_international_price;
                  $ticket['f_international_price'] = $teamTicketOld->f_international_price;
                  $ticket['early_bird_discount'] =  $teamTicketOld->early_bird_discount;
                  $ticket['early_bird_discount_type'] =  $teamTicketOld->early_bird_discount_type;
                  $ticket['late_price_discount'] = $teamTicketOld->late_price_discount;
                  $ticket['late_price_discount_type'] =  $teamTicketOld->late_price_discount_type;
                  $ticket['status'] = $teamTicketOld->status;
                  $t = Ticket::create($ticket);

                  $languages = Language::all();
                  foreach ($languages as $language) {
                    if ($language->id == 8) {
                      if ($g == 'Putra') {
                        $g = 'Men';
                      } elseif ($g == 'Putri') {
                        $g = 'Women';
                      }
                    }

                    if ($language->id == 23) {
                      if ($g == 'Men') {
                        $g = 'Putra';
                      } elseif ($g == 'Women') {
                        $g = 'Putri';
                      }
                    }

                    $data['language_id'] = $language->id;
                    $data['ticket_id'] = $t->id;
                    $data['title'] = $name_competition . ' Team ' . $g;
                    $data['description'] = null;
                    TicketContent::create($data);
                  }
                }

                // Mix Team
                $mixTeamTicketOld = Ticket::where('event_id', $event->id)->where('title', 'Mix Team')->first();
                $ticket['event_id'] = $event->id;
                $ticket['event_type'] = 'tournament';
                $ticket['competition_id'] = $competition_id;
                $ticket['title'] = 'Mix Team';
                $ticket['ticket_available_type'] = 'limited';
                $ticket['ticket_available'] = 100;
                $ticket['original_ticket_available'] = 100;
                $ticket['max_ticket_buy_type'] = 'limited';
                $ticket['max_buy_ticket'] = 10;
                $ticket['pricing_type'] = 'normal';
                $ticket['pricing_scheme'] = $request['pricing_scheme'];
                $ticket['price'] = $mixTeamTicketOld->f_price;
                $ticket['f_price'] = $mixTeamTicketOld->f_price;
                $ticket['international_price'] = $mixTeamTicketOld->f_international_price;
                $ticket['f_international_price'] = $mixTeamTicketOld->f_international_price;
                $ticket['early_bird_discount'] =  $mixTeamTicketOld->early_bird_discount;
                $ticket['early_bird_discount_type'] =  $mixTeamTicketOld->early_bird_discount_type;
                $ticket['late_price_discount'] = $mixTeamTicketOld->late_price_discount;
                $ticket['late_price_discount_type'] =  $mixTeamTicketOld->late_price_discount_type;
                $ticket['status'] = $mixTeamTicketOld->status;
                $t = Ticket::create($ticket);

                $languages = Language::all();
                foreach ($languages as $language) {
                  $data['language_id'] = $language->id;
                  $data['ticket_id'] = $t->id;
                  $data['title'] = $name_competition . ' Mix Team';
                  $data['description'] = null;
                  TicketContent::create($data);
                }
              }
            }
          }

          if (empty($request->competition_id)) {
            $checkHaveSameCategory = Competitions::withTrashed()
              ->where('event_id', $event->id)
              ->where('competition_category_id', $request->competition_categories[$key])
              ->where('distance', $request->competition_distance[$key])
              ->where('class_type', $request->competition_class_type[$key])
              ->where('class_name', $request->competition_class_name[$key])
              ->first();

            if (!empty($checkHaveSameCategory)) {
              $listCompetitionId[] = $checkHaveSameCategory->id;
              $competition_id = $checkHaveSameCategory->id;
              Ticket::withTrashed()->where('event_id', $event->id)->where('competition_id', $competition_id)->restore();
              Ticket::withTrashed()->where('event_id', $event->id)->where('competition_id', $competition_id)->update(['pricing_scheme' => $request['pricing_scheme']]);
            } else {
              $competitions = Competitions::create([
                'event_id' => $event->id,
                'name' => $name_competition,
                'competition_type_id' => 0,
                'competition_category_id' => $request->competition_categories[$key],
                'gender' => null,
                'contingent' => null,
                'distance' => $request->competition_distance[$key],
                'class_type' => $request->competition_class_type[$key],
                'class_name' => $request->competition_class_name[$key],
                'description' => null,
              ]);
              $competition_id = $competitions->id;
              $listCompetitionId[] = $competition_id;

              // Individual
              $individuTicketOld = Ticket::where('event_id', $event->id)->where('title', 'Individu')->first();
              $gender = ['Putra', 'Putri'];
              foreach ($gender as $g) {
                $ticket['event_id'] = $event->id;
                $ticket['competition_id'] = $competition_id;
                $ticket['event_type'] = 'tournament';
                $ticket['title'] = 'Individu';
                $ticket['ticket_available_type'] = 'limited';
                $ticket['ticket_available'] = 100;
                $ticket['original_ticket_available'] = 100;
                $ticket['max_ticket_buy_type'] = 'limited';
                $ticket['max_buy_ticket'] = 10;
                $ticket['pricing_type'] = 'normal';
                $ticket['pricing_scheme'] = $request['pricing_scheme'];
                $ticket['price'] = $individuTicketOld->f_price;
                $ticket['f_price'] = $individuTicketOld->f_price;
                $ticket['international_price'] = $individuTicketOld->f_international_price;
                $ticket['f_international_price'] = $individuTicketOld->f_international_price;
                $ticket['early_bird_discount'] =  $individuTicketOld->early_bird_discount;
                $ticket['early_bird_discount_type'] =  $individuTicketOld->early_bird_discount_type;
                $ticket['late_price_discount'] = $individuTicketOld->late_price_discount;
                $ticket['late_price_discount_type'] =  $individuTicketOld->late_price_discount_type;
                $ticket['status'] = $individuTicketOld->status;
                $t = Ticket::create($ticket);

                $languages = Language::all();
                foreach ($languages as $language) {
                  if ($language->id == 8) {
                    if ($g == 'Putra') {
                      $g = 'Men';
                    } elseif ($g == 'Putri') {
                      $g = 'Women';
                    }
                  }

                  if ($language->id == 23) {
                    if ($g == 'Men') {
                      $g = 'Putra';
                    } elseif ($g == 'Women') {
                      $g = 'Putri';
                    }
                  }

                  $data['language_id'] = $language->id;
                  $data['ticket_id'] = $t->id;
                  $data['title'] = $name_competition . ' Individu ' . $g;
                  $data['description'] = null;
                  TicketContent::create($data);
                }
              }

              // Team
              $teamTicketOld = Ticket::where('event_id', $event->id)->where('title', 'Team')->first();
              foreach ($gender as $g) {
                $ticket['event_id'] = $event->id;
                $ticket['competition_id'] = $competition_id;
                $ticket['event_type'] = 'tournament';
                $ticket['title'] = 'Team';
                $ticket['ticket_available_type'] = 'limited';
                $ticket['ticket_available'] = 0;
                $ticket['original_ticket_available'] = 0;
                $ticket['max_ticket_buy_type'] = 'limited';
                $ticket['max_buy_ticket'] = 1;
                $ticket['pricing_type'] = 'normal';
                $ticket['pricing_scheme'] = $request['pricing_scheme'];
                $ticket['price'] = $teamTicketOld->f_price;
                $ticket['f_price'] = $teamTicketOld->f_price;
                $ticket['international_price'] = $teamTicketOld->f_international_price;
                $ticket['f_international_price'] = $teamTicketOld->f_international_price;
                $ticket['early_bird_discount'] =  $teamTicketOld->early_bird_discount;
                $ticket['early_bird_discount_type'] =  $teamTicketOld->early_bird_discount_type;
                $ticket['late_price_discount'] = $teamTicketOld->late_price_discount;
                $ticket['late_price_discount_type'] =  $teamTicketOld->late_price_discount_type;
                $ticket['status'] = $teamTicketOld->status;
                $t = Ticket::create($ticket);

                $languages = Language::all();
                foreach ($languages as $language) {
                  if ($language->id == 8) {
                    if ($g == 'Putra') {
                      $g = 'Men';
                    } elseif ($g == 'Putri') {
                      $g = 'Women';
                    }
                  }

                  if ($language->id == 23) {
                    if ($g == 'Men') {
                      $g = 'Putra';
                    } elseif ($g == 'Women') {
                      $g = 'Putri';
                    }
                  }

                  $data['language_id'] = $language->id;
                  $data['ticket_id'] = $t->id;
                  $data['title'] = $name_competition . ' Team ' . $g;
                  $data['description'] = null;
                  TicketContent::create($data);
                }
              }

              // Mix Team
              $mixTeamTicketOld = Ticket::where('event_id', $event->id)->where('title', 'Mix Team')->first();
              $ticket['event_id'] = $event->id;
              $ticket['event_type'] = 'tournament';
              $ticket['competition_id'] = $competition_id;
              $ticket['title'] = 'Mix Team';
              $ticket['ticket_available_type'] = 'limited';
              $ticket['ticket_available'] = 100;
              $ticket['original_ticket_available'] = 100;
              $ticket['max_ticket_buy_type'] = 'limited';
              $ticket['max_buy_ticket'] = 10;
              $ticket['pricing_type'] = 'normal';
              $ticket['pricing_scheme'] = $request['pricing_scheme'];
              $ticket['price'] = $mixTeamTicketOld->f_price;
              $ticket['f_price'] = $mixTeamTicketOld->f_price;
              $ticket['international_price'] = $mixTeamTicketOld->f_international_price;
              $ticket['f_international_price'] = $mixTeamTicketOld->f_international_price;
              $ticket['early_bird_discount'] =  $mixTeamTicketOld->early_bird_discount;
              $ticket['early_bird_discount_type'] =  $mixTeamTicketOld->early_bird_discount_type;
              $ticket['late_price_discount'] = $mixTeamTicketOld->late_price_discount;
              $ticket['late_price_discount_type'] =  $mixTeamTicketOld->late_price_discount_type;
              $ticket['status'] = $mixTeamTicketOld->status;
              $t = Ticket::create($ticket);

              $languages = Language::all();
              foreach ($languages as $language) {
                $data['language_id'] = $language->id;
                $data['ticket_id'] = $t->id;
                $data['title'] = $name_competition . ' Mix Team';
                $data['description'] = null;
                TicketContent::create($data);
              }
            }
          }

          $i++;
        }

        // official
        $official = Ticket::query()->withTrashed()
          ->where('title', 'Official')
          ->where('event_id', $event->id)
          ->latest()
          ->first();
        if (!empty($official)) {
          $official->update(['competition_id' => null, 'pricing_scheme' => $request['pricing_scheme']]);
          $official->restore();
        }

        if (empty($official)) {
          $ticket['event_id'] = $event->id;
          $ticket['event_type'] = 'tournament';
          $ticket['competition_id'] = null;
          $ticket['title'] = 'Official';
          $ticket['ticket_available_type'] = 'limited';
          $ticket['ticket_available'] = 100;
          $ticket['original_ticket_available'] = 100;
          $ticket['max_ticket_buy_type'] = 'limited';
          $ticket['max_buy_ticket'] = 10;
          $ticket['pricing_type'] = 'normal';
          $ticket['pricing_scheme'] = $request['pricing_scheme'];
          $ticket['price'] = 300000;
          $ticket['f_price'] = 300000;
          $ticket['international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
          $ticket['f_international_price'] = $request['pricing_scheme'] != 'single_price' ? 500000 : null;
          $ticket['early_bird_discount'] = 'disable';
          $ticket['early_bird_discount_type'] = 'fixed';
          $ticket['late_price_discount'] = 'disable';
          $ticket['late_price_discount_type'] = 'fixed';
          $t = Ticket::create($ticket);

          $languages = Language::all();
          foreach ($languages as $language) {
            $data['language_id'] = $language->id;
            $data['ticket_id'] = $t->id;
            $data['title'] = $name_competition . ' Official';
            $data['description'] = null;
            TicketContent::create($data);
          }
        }

        $event->update($in);
        Competitions::where('event_id', $event->id)->whereNotIn('id', $listCompetitionId)->delete();
        Ticket::where('event_id', $event->id)->whereNotIn('competition_id', $listCompetitionId)->delete();
        Ticket::query()
          ->withTrashed()
          ->where('event_id', $event->id)
          ->update(['pricing_scheme' => $request['pricing_scheme']]);
        // return response()->json(['data' => $listCompetitionId]);

      });
      Session::flash('success', 'Updated Successfully');
      return response()->json(['status' => 'success'], 200);
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $event = Event::find($id);

    if (empty($event)) {
      return redirect()->back()->with('warning', 'Delete failed, because event not found!');
    }

    @unlink(public_path('assets/admin/img/event/thumbnail/') . $event->thumbnail);

    $event_contents = EventContent::where('event_id', $event->id)->get();
    foreach ($event_contents as $event_content) {
      $event_content->delete();
    }
    $event_images = EventImage::where('event_id', $event->id)->get();
    foreach ($event_images as $event_image) {
      @unlink(public_path('assets/admin/img/event-gallery/') . $event_image->image);
      $event_image->delete();
    }

    //bookings
    $bookings = $event->booking()->get();
    foreach ($bookings as $booking) {
      // first, delete the attachment
      @unlink(public_path('assets/admin/file/attachments/') . $booking->attachment);

      // second, delete the invoice
      @unlink(public_path('assets/admin/file/invoices/') . $booking->invoice);

      $booking->delete();
    }

    //tickets
    $tickets = $event->tickets()->get();
    foreach ($tickets as $ticket) {
      $ticket->delete();
    }
    //wishlists
    $wishlists = $event->wishlists()->get();
    foreach ($wishlists as $wishlist) {
      $wishlist->delete();
    }

    //dates
    $dates = $event->dates()->get();
    foreach ($dates as $date) {
      $date->delete();
    }

    // finally delete the event
    $event->delete();

    return redirect()->back()->with('success', 'Deleted Successfully');
  }

  public function destroy_tournament($id)
  {
    $event = Event::find($id);
    if (empty($event)) {
      return redirect()->back()->with('warning', 'Delete failed, because event not found!');
    }

    // $checkBookings = Booking::where('event_id', $id)->count();
    $checkBookings = Booking::where('event_id', $id)
      ->whereIn('paymentStatus', ['completed', 'pending'])
      ->get()
      ->count();
    if ($checkBookings > 0) {
      return redirect()->back()->with('warning', 'Delete failed, because have a participants order!');
    }


    @unlink(public_path('assets/admin/img/event/thumbnail/') . $event->thumbnail);

    if (!empty($event->thb_file)) {
      @unlink(public_path('assets/admin/img/event/tournament_uploaded/') . $event->thb_file);
    }


    $event_contents = EventContent::where('event_id', $event->id)->get();
    foreach ($event_contents as $event_content) {
      $event_content->delete();
    }

    // delete publication events
    EventPublisher::where('event_id', $event->id)->delete();
    ContingentType::where('event_id', $event->id)->delete();

    $event_images = EventImage::where('event_id', $event->id)->get();
    foreach ($event_images as $event_image) {
      @unlink(public_path('assets/admin/img/event-gallery/') . $event_image->image);
      $event_image->delete();
    }


    //tickets & ticket contents
    $tickets = $event->tickets()->get();
    foreach ($tickets as $ticket) {
      TicketContent::where('ticket_id', $ticket->id)->delete();
      TicketPrice::where('ticket_id', $ticket->id)->delete();
      $ticket->delete();
    }

    //wishlists
    $wishlists = $event->wishlists()->get();
    foreach ($wishlists as $wishlist) {
      $wishlist->delete();
    }

    //dates
    $dates = $event->dates()->get();
    foreach ($dates as $date) {
      $date->delete();
    }

    // finally delete the event
    $event->delete();

    return redirect()->back()->with('success', 'Deleted Successfully');
  }

  //bulk_delete
  public function bulk_delete(Request $request)
  {
    foreach ($request->ids as $id) {
      $event = Event::find($id);

      @unlink(public_path('assets/admin/img/event/thumbnail/') . $event->thumbnail);

      $event_contents = EventContent::where('event_id', $event->id)->get();
      foreach ($event_contents as $event_content) {
        $event_content->delete();
      }
      $event_images = EventImage::where('event_id', $event->id)->get();
      foreach ($event_images as $event_image) {
        @unlink(public_path('assets/admin/img/event-gallery/') . $event_image->image);
        $event_image->delete();
      }

      //bookings
      $bookings = $event->booking()->get();
      foreach ($bookings as $booking) {
        // first, delete the attachment
        @unlink(public_path('assets/admin/file/attachments/') . $booking->attachment);

        // second, delete the invoice
        @unlink(public_path('assets/admin/file/invoices/') . $booking->invoice);

        $booking->delete();
      }

      //tickets
      $tickets = $event->tickets()->get();
      foreach ($tickets as $ticket) {
        $ticket->delete();
      }

      //wishlists
      $wishlists = $event->wishlists()->get();
      foreach ($wishlists as $wishlist) {
        $wishlist->delete();
      }

      //dates
      $dates = $event->dates()->get();
      foreach ($dates as $date) {
        $date->delete();
      }

      // finally delete the event
      $event->delete();
    }
    Session::flash('success', 'Deleted Successfully');
    return response()->json(['status' => 'success'], 200);
  }

  public function codeGenerate()
  {
    return HelperEvent::AutoGenerateCode();
  }

  public function checkCodeEvent(Request $request)
  {
    $event_id = $request->get('eventId');
    $code_access = $request->get('codeAccess');

    if (empty($code_access)) {
      return HelperResponse::Error([], "Code Access is required");
    }

    $checkEvent = Event::where('id', $event_id)->first();

    if (empty($checkEvent)) {
      return HelperResponse::Error([], "Not Found Event", 404);
    }

    $getEventType = EventType::where('event_id', $event_id)->first();

    if ($getEventType->code != $code_access) {
      return HelperResponse::Error([], "Not Match Code", 401);
    }

    return HelperResponse::Success([], "Code Valid!");
  }


  public function getCompetitionType()
  {
    $data = CompetitionType::get();
    return HelperResponse::Success($data, "Get Data Success");
  }
}
