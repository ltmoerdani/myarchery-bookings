@extends('frontend.layout')
@section('pageHeading')
    {{ $content->title }}
@endsection

@php
    $og_title = $content->title;
    $og_description = strip_tags($content->description);
    $og_image = asset('assets/admin/img/event/thumbnail/' . $content->thumbnail);
@endphp

@section('meta-keywords', "{{ $content->meta_keywords }}")
@section('meta-description', "$content->meta_description")
@section('og-title', "$og_title")
@section('og-description', "$og_description")
@section('og-image', "$og_image")

@section('custom-style')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/summernote-content.css') }}">
@endsection

@section('content')
    @php
        $map_address = preg_replace('/\s+/u', ' ', trim($content->address));
        $map_address = str_replace('/', ' ', $map_address);
        $map_address = str_replace('?', ' ', $map_address);
        $map_address = str_replace(',', ' ', $map_address);
    @endphp
    <!-- <section class="event-details-section pt-110 rpt-90 pb-90 rpb-70"> -->
    <section class="event-details-section rpt-90 pb-90 rpb-70">
        <div class="container">
            <div class="event-details-content">
                <div class="row">
                    <div class="col-12 col-lg-7">
                        <div class="row">
                            <div class="col-12">
                                <div class="event-details-image mb-50">
                                    <div class="event-details-images">
                                        @foreach ($images as $item)
                                            <a href="{{ asset('assets/admin/img/event-gallery/' . $item->image) }}">
                                                <img class="lazy image-overlay-tournament"
                                                    data-src="{{ asset('assets/admin/img/event-gallery/' . $item->image) }}"
                                                    alt="Event Details">
                                            </a>
                                        @endforeach
                                    </div>

                                    <div class="buttons">
                                        @if (Auth::guard('customer')->check())
                                            @php
                                                $customer_id = Auth::guard('customer')->user()->id;
                                                $event_id = $content->id;
                                                $checkWishList = checkWishList($event_id, $customer_id);
                                            @endphp
                                        @else
                                            @php
                                                $checkWishList = false;
                                            @endphp
                                        @endif
                                        @if ($content->event_type != 'online')
                                            <a href="javascript:void(0)" data-toggle="modal"
                                                data-target=".bd-example-modal-lg">
                                                <i class="fas fa-map-marker-alt m-0"></i>
                                            </a>
                                        @endif
                                        <a href="{{ $checkWishList == false ? route('addto.wishlist', $content->id) : route('remove.wishlist', $content->id) }}"
                                            class="{{ $checkWishList == true ? 'text-success' : '' }}"><i
                                                class="fas fa-bookmark"></i></a>
                                        <a href="javascript:void(0)" data-toggle="modal" data-target=".share-event">
                                            <i class="fas fa-share-alt"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="event-top d-flex flex-wrap-wrap has-gap">
                                    @php
                                        if ($content->date_type == 'multiple') {
                                            $event_date = eventLatestDates($content->id);
                                            $date = strtotime(@$event_date->start_date);
                                        } else {
                                            $date = strtotime($content->start_date);
                                        }
                                    @endphp
                                    @if ($content->date_type != 'multiple')
                                        <div class="event-top-date">
                                            <div class="event-month">
                                                {{ \Carbon\Carbon::parse($date)->timezone($websiteInfo->timezone)->translatedFormat('M') }}
                                            </div>
                                            <div class="event-date">
                                                {{ \Carbon\Carbon::parse($date)->timezone($websiteInfo->timezone)->translatedFormat('d') }}
                                            </div>
                                        </div>
                                    @endif
                                    <div class="event-bottom-content">
                                        @php
                                            if ($content->date_type == 'multiple') {
                                                $event_date = eventLatestDates($content->id);
                                                $startDateTime = @$event_date->start_date_time;
                                                $endDateTime = @$event_date->end_date_time;
                                                //for multiple get last end date
                                                $last_end_date = eventLastEndDates($content->id);
                                                $last_end_date = $last_end_date->end_date_time;

                                                $now_time = \Carbon\Carbon::now()
                                                    ->timezone($websiteInfo->timezone)
                                                    ->translatedFormat('Y-m-d H:i:s');
                                            } else {
                                                $now_time = \Carbon\Carbon::now()
                                                    ->timezone($websiteInfo->timezone)
                                                    ->translatedFormat('Y-m-d H:i:s');
                                                $startDateTime = $content->start_date . ' ' . $content->start_time;
                                                $endDateTime = $content->end_date . ' ' . $content->end_time;
                                            }
                                            $over = false;

                                        @endphp
                                        @if ($content->date_type == 'single' && $content->countdown_status == 1)
                                            <div class="event-details-top">
                                                @if ($startDateTime >= $now_time)
                                                    <h2 class="title">{{ $content->title }} <span
                                                            class="badge badge-info">{{ __('Upcoming') }}</span>
                                                    </h2>
                                                @elseif ($startDateTime <= $endDateTime && $endDateTime >= $now_time)
                                                    <h2 class="title">
                                                        {{ $content->title }}
                                                        <span class="badge badge-success">{{ __('Running') }}</span>
                                                    </h2>
                                                @else
                                                    @php
                                                        $over = true;
                                                    @endphp
                                                    <h2 class="title">
                                                        {{ $content->title }}
                                                        <span class="badge badge-danger">{{ __('Over') }}</span>
                                                    </h2>
                                                @endif
                                            </div>
                                        @elseif ($content->date_type == 'multiple')
                                            <div class="event-details-top">
                                                <h2 class="title">{{ $content->title }}
                                                    @if ($startDateTime >= $now_time)
                                                        <span class="badge badge-info">{{ __('Upcoming') }}</span>
                                                    @elseif ($startDateTime <= $last_end_date && $last_end_date >= $now_time)
                                                        <span class="badge badge-success">{{ __('Running') }}</span>
                                                    @else
                                                        @php
                                                            $over = true;
                                                        @endphp
                                                        <span class="badge badge-danger">{{ __('Over') }}</span>
                                                    @endif
                                                </h2>
                                            </div>
                                        @else
                                            <div class="event-details-top">
                                                <h2 class="title">{{ $content->title }}</h2>
                                            </div>

                                        @endif

                                        <div class="event-details-header mb-25">
                                            <ul>
                                                <li><i class="far fa-calendar-alt"></i>
                                                    {{ \Carbon\Carbon::parse($date)->timezone($websiteInfo->timezone)->translatedFormat('D, dS M Y') }}
                                                </li>

                                                <li><i class="far fa-clock"></i>
                                                    {{ $content->date_type == 'multiple' ? @$event_date->duration : $content->duration }}
                                                </li>
                                                @if ($content->event_type == 'venue' || $content->event_type == 'tournament')
                                                    <li><i class="fas fa-map-marker-alt"></i>
                                                        @if ($content->city != null)
                                                            {{ $content->city }}
                                                        @else
                                                            {{ __('Tournament') }}
                                                        @endif
                                                        @if ($content->state)
                                                            , {{ $content->state }}
                                                        @endif
                                                        @if ($content->country)
                                                            , {{ $content->country }}
                                                        @endif
                                                    </li>
                                                @else
                                                    <li><i class="fas fa-map-marker-alt"></i> {{ __('Online') }}</li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{--  qouta --}}
                            <div class="col-12 my-2">
                                <ul class="bg-light nav mb-3 d-flex justify-content-center flex-wrap" id="pills-tab"
                                    role="tablist">
                                    <input type="hidden" id="category_tickets"
                                        value="{{ json_encode($category_tickets) }}">
                                    @foreach ($category_tickets as $key_ct => $ct_value)
                                        <li class="nav-item" role="presentation">
                                            <a href="#"
                                                class="nav-link nav-event-tournament category-event-tournament-info {{ $key_ct == 0 ? 'active' : '' }}"
                                                id="pills-category-{{ $ct_value['id'] }}-tab" data-toggle="pill"
                                                data-target="#pills-category-{{ $ct_value['id'] }}" type="button"
                                                role="tab" aria-controls="pills-category-{{ $ct_value['id'] }}"
                                                aria-selected="false" data-info="{{ json_encode($ct_value) }}"
                                                data-category-id="{{ $ct_value['id'] }}">
                                                {{ $ct_value['category_name'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="tab-content" id="pills-tabContent">
                                    @foreach ($category_tickets as $key_ct => $ct_value)
                                        <div class="tab-pane fade {{ $key_ct == 0 ? 'show active' : '' }}"
                                            id="pills-category-{{ $ct_value['id'] }}" role="tabpanel"
                                            aria-labelledby="pills-category-{{ $ct_value['id'] }}-tab">
                                            <div class="d-flex justify-content-center flex-row flex-wrap gap-1">
                                                @foreach ($ct_value['sub_category'] as $key_sub_category => $val_sub_category)
                                                    <button
                                                        class="btn btn-warning button-warning-custom {{ 'button-sub-category-' . $key_sub_category . '-' . $ct_value['id'] }} info-detail-qouta-ticket {{ $key_sub_category == 0 && $key_ct == 0 ? 'first-info-detail-quota-ticket' : '' }}"
                                                        type="button" data-category-key-id="{{ $key_sub_category }}"
                                                        data-category-id="{{ $ct_value['id'] }}"
                                                        data-ticket-quota="{{ json_encode($val_sub_category['tickets']) }}">
                                                        @if (strtolower($ct_value['category_name']) == 'official')
                                                            {{ $val_sub_category['sub_category_name'] }}
                                                        @else
                                                            {{ $val_sub_category['sub_category_name'] }} -
                                                            {{ $val_sub_category['distance'] }} M
                                                        @endif
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            {{-- <div class="col-12 my-2">
                                <div class="card bg-light text-primary" style="font-weight: bold">
                                    <div class="card-body py-1 px-0 d-flex justify-content-center">
                                        {{ __('Quota Match') }}
                                    </div>
                                </div>
                            </div> --}}
                            {{-- <div class="col-12 mb-2 d-flex justify-content-center justify-content-md-start flex-row flex-wrap"
                                style="gap:10px;" id="content-qouta-ticket">
                            </div> --}}
                            {{-- end qouta --}}
                            <div class="col-12">
                                <div class="event-details-content-inner">
                                    <div class="event-info d-flex align-items-center mb-1">
                                        <span>
                                            <a
                                                href="{{ route('events', ['category' => $content->slug]) }}">{{ $content->name }}</a>
                                        </span>
                                    </div>
                                    @if (Session::has('paypal_error'))
                                        <div class="alert alert-danger">{{ Session::get('paypal_error') }}</div>
                                    @endif
                                    @php
                                        Session::put('paypal_error', null);
                                    @endphp
                                    <h3 class="inner-title mb-25">{{ __('Description') }}</h3>

                                    <div class="summernote-content">
                                        {!! $content->description !!}
                                    </div>

                                    @if ($content->event_type != 'online')
                                        <h3 class="inner-title mb-30">{{ __('Map') }}</h3>
                                        <div class="our-location mb-50">
                                            <iframe
                                                src="//maps.google.com/maps?width=100%25&amp;height=385&amp;hl=en&amp;q={{ $map_address }}&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"
                                                height="385" class="map-h" allowfullscreen="" loading="lazy"></iframe>
                                        </div>
                                    @endif

                                    @if (!empty($content->refund_policy))
                                        <h3>{{ __('Return Policy') }}</h3>
                                        <p>{{ @$content->refund_policy }}</p>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5">
                        <div class="sidebar-sticky mt-5">
                            <form id="eventForm" action="{{ route('processing_to_form_order_tournament') }}"
                                method="post">
                                @csrf
                                <input type="hidden" name="event_id" value="{{ $content->id }}" id="event_id">
                                <input type="hidden" name="event_title" value="{{ $content->title }}" id="">
                                <input type="hidden" name="pricing_type" value="{{ $content->pricing_type }}">
                                <input type="hidden" name="event_type" value="{{ $content->event_type }}"
                                    id="">
                                <div class="event-details-information event-details-information-tournament">
                                    <input type="hidden" name="date_type" value="{{ $content->date_type }}">
                                    @if ($content->date_type == 'multiple')
                                        @php
                                            $dates = eventDates($content->id);
                                            $exp_dates = eventExpDates($content->id);
                                        @endphp

                                        <div class="form-group">
                                            <label for="">{{ __('Select Date') }}</label>
                                            <select name="event_date" id="" class="form-control">
                                                @if (count($dates) > 0)
                                                    @foreach ($dates as $date)
                                                        <option value="{{ FullDateTime($date->start_date_time) }}">
                                                            {{ FullDateTime($date->start_date_time) }}
                                                            ({{ timeZoneOffset($websiteInfo->timezone) }}
                                                            {{ __('GMT') }})
                                                        </option>
                                                    @endforeach
                                                @endif
                                                @if (count($exp_dates) > 0)
                                                    @foreach ($exp_dates as $exp_date)
                                                        <option disabled value="">
                                                            {{ FullDateTime($exp_date->start_date_time) }}
                                                            ({{ timeZoneOffset($websiteInfo->timezone) }}
                                                            {{ __('GMT') }})
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('event_date')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @else
                                        <input type="hidden" name="event_date"
                                            value="{{ FullDateTime($content->start_date . $content->start_time) }}">
                                    @endif
                                    {{-- organised --}}
                                    <b>{{ __('Organised By') }}</b>
                                    <hr>
                                    @if ($organizer == '')
                                        @php
                                            $admin = App\Models\Admin::first();
                                        @endphp
                                        <div class="author">
                                            <a
                                                href="{{ route('frontend.organizer.details', [$admin->id, str_replace(' ', '-', $admin->username), 'admin' => 'true']) }}"><img
                                                    class="lazy"
                                                    data-src="{{ asset('assets/admin/img/admins/' . $admin->image) }}"
                                                    alt="Author"></a>
                                            <div class="content">
                                                <h6><a
                                                        href="{{ route('frontend.organizer.details', [$admin->id, str_replace(' ', '-', $admin->username), 'admin' => 'true']) }}">{{ $admin->username }}</a>
                                                </h6>
                                            </div>
                                        </div>
                                    @else
                                        <div class="author">
                                            <a
                                                href="{{ route('frontend.organizer.details', [$organizer->id, str_replace(' ', '-', $organizer->username)]) }}">
                                                @if ($organizer->photo != null)
                                                    <img class="lazy"
                                                        data-src="{{ asset('assets/admin/img/organizer-photo/' . $organizer->photo) }}"
                                                        alt="Author">
                                                @else
                                                    <img class="lazy"
                                                        data-src="{{ asset('assets/front/images/user.png') }}"
                                                        alt="Author">
                                                @endif

                                            </a>

                                            <div class="content">
                                                <h6><a
                                                        href="{{ route('frontend.organizer.details', [$organizer->id, str_replace(' ', '-', $organizer->username)]) }}">{{ $organizer->username }}</a>
                                                </h6>
                                                <a
                                                    href="{{ route('frontend.organizer.details', [$organizer->id, str_replace(' ', '-', $organizer->username)]) }}">{{ __('View  Profile') }}</a>
                                            </div>
                                        </div>
                                    @endif
                                    {{-- Count down start --}}
                                    @if ($content->date_type == 'single' && $content->countdown_status == 1)
                                        <div class="event-details-top">
                                            @if ($startDateTime >= $now_time)
                                                <b>{{ __('Event Starts In') }}</b>
                                                <hr>
                                                @php
                                                    $dt = Carbon\Carbon::parse($startDateTime);
                                                    $year = $dt->year;
                                                    $month = $dt->month;
                                                    $day = $dt->day;
                                                    $end_time = Carbon\Carbon::parse($startDateTime);
                                                    $hour = $end_time->hour;
                                                    $minute = $end_time->minute;
                                                    $now = str_replace('+00:00', '.000' . timeZoneOffset($websiteInfo->timezone) . '00:00', gmdate('c'));
                                                @endphp
                                                <div class="count-down mb-3" dir="ltr">
                                                    <div class="event-countdown" data-now="{{ $now }}"
                                                        data-year="{{ $year }}" data-month="{{ $month }}"
                                                        data-day="{{ $day }}" data-hour="{{ $hour }}"
                                                        data-minute="{{ $minute }}"
                                                        data-timezone="{{ timeZoneOffset($websiteInfo->timezone) }}">
                                                    </div>
                                                </div>
                                            @elseif ($startDateTime <= $endDateTime && $endDateTime >= $now_time)
                                                <p>{{ __('The Event is Running') }}</p>
                                            @else
                                                <p>{{ __('The Event is Over') }}</p>
                                            @endif
                                        </div>
                                    @endif
                                    {{-- Countdown end --}}

                                    {{-- Button Listing --}}

                                    {{-- <div class="event-details-top">
                                        <hr>
                                        <div class="row">
                                            <div class="col-12">
                                                <a href="#" class="theme-btn-outline-warning w-100 mt-20">
                                                    {{ __('Participant List') }}
                                                </a>
                                            </div>
                                            <div class="col-12">
                                                <a href="#" class="theme-btn-outline-warning w-100 mt-20">
                                                    {{ __('Competition Standings') }}
                                                </a>
                                            </div>
                                            <div class="col-12">
                                                <a href="#" class="theme-btn-outline-warning w-100 mt-20">
                                                    {{ __('Medal Standings') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div> --}}

                                    {{-- end button listing --}}

                                    {{-- location --}}
                                    @if ($content->address != null)
                                        <!-- <hr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <b><i class="fas fa-map-marker-alt"></i> {{ $content->address }}</b>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <hr> -->
                                    @endif
                                    {{-- end location --}}

                                    {{-- Add to calendar --}}

                                    {{-- @php
                                        $start_date = str_replace('-', '', $content->start_date);
                                        $start_time = str_replace(':', '', $content->start_time);
                                        $end_date = str_replace('-', '', $content->end_date);
                                        $end_time = str_replace(':', '', $content->end_time);
                                    @endphp --}}
                                    {{-- <div class="dropdown show pt-4 pb-4">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-calendar-alt"></i> {{ __('Add to Calendar') }}
                                        </a>

                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <a target="_blank" class="dropdown-item"
                                                href="//calendar.google.com/calendar/u/0/r/eventedit?text={{ $content->title }}&dates={{ $start_date }}T{{ $start_time }}/{{ $end_date }}T{{ $end_time }}&ctz={{ $websiteInfo->timezone }}&details=For+details,+click+here:+{{ route('event.details', [$content->eventSlug, $content->id]) }}&location={{ $content->event_type == 'online' ? 'Online' : $content->address }}&sf=true">{{ __('Google Calendar') }}</a>
                                            <a target="_blank" class="dropdown-item"
                                                href="//calendar.yahoo.com/?v=60&view=d&type=20&TITLE={{ $content->title }}&ST={{ $start_date }}T{{ $start_time }}&ET={{ $end_date }}T{{ $end_time }}&DUR=9959&DESC=For%20details%2C%20click%20here%3A%20{{ route('event.details', [$content->eventSlug, $content->id]) }}&in_loc={{ $content->event_type == 'online' ? 'Online' : $content->address }}">{{ __('Yahoo') }}</a>
                                        </div>
                                    </div> --}}

                                    {{-- end add to calendar --}}

                                    {{-- ticket list --}}
                                    @php
                                        $tickets = DB::table('tickets')
                                            ->select(DB::raw('tickets.*'))
                                            ->where('event_id', $content->id)
                                            ->whereNull('deleted_at')
                                            ->where('status', 1)
                                            ->groupBy('title')
                                            ->get();

                                        $competitons = DB::select(
                                            "SELECT GROUP_CONCAT(DISTINCT competition_categories.name ORDER BY competition_categories.name SEPARATOR ', ') AS name,
                                                (SELECT name FROM competition_type WHERE competition_type.id=competitions.competition_type_id)
AS competition_type  FROM `competitions`, competition_categories WHERE competitions.event_id=" .
                                                $content->id .
                                                " AND competition_categories.id=competitions.competition_category_id AND competitions.deleted_at IS NULL
                                            GROUP BY competitions.competition_type_id ORDER BY competitions.competition_category_id ASC",
                                        );
                                    @endphp

                                    @if (count($tickets) > 0)
                                        <b>{{ __('Select Tickets') }}</b>
                                        <hr>
                                        @foreach ($tickets as $ticket)
                                            @if ($ticket->pricing_type == 'normal')
                                                @php
                                                    if ($ticket->ticket_available_type == 'limited') {
                                                        $stock = $ticket->ticket_available;
                                                    } else {
                                                        $stock = 'unlimited';
                                                    }

                                                    //ticket purchase or not check
                                                    $ticket_content = App\Models\Event\TicketContent::where([['language_id', $currentLanguageInfo->id], ['ticket_id', $ticket->id]])->first();

                                                    if (Auth::guard('customer')->user() && $ticket->max_ticket_buy_type == 'limited') {
                                                        $purchase = isTicketPurchaseVenue($ticket->event_id, $ticket->max_buy_ticket, $ticket->id, @$ticket_content->title);
                                                    } else {
                                                        $purchase = ['status' => 'false', 'p_qty' => 0];
                                                    }
                                                @endphp
                                                <p class="mb-0"><strong></strong></p>
                                                <!-- <p class="mb-0"><strong>$ticket_content->title</strong></p> -->
                                                <div class="click-show">
                                                    <div class="show-content">
                                                        {!! @$ticket_content->description !!}
                                                    </div>
                                                    @if (strlen(@$ticket_content->description) > 50)
                                                        <div class="read-more-btn">
                                                            <span>{{ __('Read more') }}</span>
                                                            <span>{{ __('Read less') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="price-count">
                                                    <div class="d-flex flex-column">
                                                        <h6 dir="ltr">
                                                            {{ $ticket->title }} Category
                                                        </h6>
                                                    </div>
                                                    <div class="quantity-input">
                                                        <input type="hidden" class="ticket_id"
                                                            value="{{ $ticket->id }}">
                                                        <input type="hidden" id="ticket_title_{{ $ticket->id }}"
                                                            name="category_ticket[{{ $ticket->id }}]"
                                                            value="{{ $ticket->title }}">
                                                        <button class="quantity-down" type="button" id="quantityDown">
                                                            -
                                                        </button>
                                                        <input class="quantity" id="ticket_quantity_{{ $ticket->id }}"
                                                            readonly type="number" value="0" data-price="10000"
                                                            data-max_buy_ticket="10"
                                                            name="quantity[{{ $ticket->id }}]"
                                                            data-ticket_id="{{ $ticket->id }}" data-stock="100"
                                                            data-purchase="false" data-p_qty="0">
                                                        <button class="quantity-up" type="button" id="quantityUP">
                                                            +
                                                        </button>
                                                    </div>
                                                    <small>{{ $competitons[0]->name }}</small>
                                                </div>
                                                {{-- @elseif($ticket->pricing_type == 'variation')
                                                @php
                                                    $variations = json_decode($ticket->variations);

                                                    $varition_names = App\Models\Event\VariationContent::where([['ticket_id', $ticket->id], ['language_id', $currentLanguageInfo->id]])->get();
                                                    if (empty($varition_names)) {
                                                        $varition_names = App\Models\Event\VariationContent::where('ticket_id', $ticket->id)->get();
                                                    }

                                                    $de_lang = App\Models\Language::where('is_default', 1)->first();
                                                    $de_varition_names = App\Models\Event\VariationContent::where([['ticket_id', $ticket->id], ['language_id', $de_lang->id]])->get();
                                                    if (empty($de_varition_names)) {
                                                        $de_varition_names = App\Models\Event\VariationContent::where([['ticket_id', $ticket->id]])->get();
                                                    }
                                                @endphp
                                                @foreach ($variations as $key => $item)
                                                    @php
                                                        //ticket purchase or not check
                                                        if (Auth::guard('customer')->user()) {
                                                            if (count($de_varition_names) > 0) {
                                                                $purchase = isTicketPurchaseVenue($ticket->event_id, $item->v_max_ticket_buy, $ticket->id, $de_varition_names[$key]['name']);
                                                            }
                                                        } else {
                                                            $purchase = ['status' => 'false', 'p_qty' => 0];
                                                        }
                                                        $ticket_content = App\Models\Event\TicketContent::where([['language_id', $currentLanguageInfo->id], ['ticket_id', $ticket->id]])->first();
                                                        if (empty($ticket_content)) {
                                                            $ticket_content = App\Models\Event\TicketContent::where([['ticket_id', $ticket->id]])->first();
                                                        }
                                                    @endphp
                                                    <p class="mb-0"><strong>{{ @$ticket_content->title }} -
                                                            {{ @$varition_names[$key]['name'] }}</strong>
                                                    </p>
                                                    <div class="click-show">
                                                        <div class="show-content">
                                                            {!! @$ticket_content->description !!}
                                                        </div>
                                                        @if (strlen(@$ticket_content->description) > 50)
                                                            <div class="read-more-btn">
                                                                <span>{{ __('Read more') }}</span>
                                                                <span>{{ __('Read less') }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="price-count">
                                                        <h6 dir="ltr">
                                                            @if ($ticket->early_bird_discount == 'enable')
                                                                @php
                                                                    $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                                                                @endphp
                                                                @if ($ticket->early_bird_discount_type == 'fixed' && !$discount_date->isPast())
                                                                    @php
                                                                        $calculate_price = $item->price - $ticket->early_bird_discount_amount;
                                                                    @endphp
                                                                    {{ symbolPrice($calculate_price) }}

                                                                    <del>
                                                                        {{ symbolPrice($item->price) }}
                                                                    </del>
                                                                @elseif ($ticket->early_bird_discount_type == 'percentage' && !$discount_date->isPast())
                                                                    @php
                                                                        $c_price = ($item->price * $ticket->early_bird_discount_amount) / 100;
                                                                        $calculate_price = $item->price - $c_price;
                                                                    @endphp
                                                                    {{ symbolPrice($calculate_price) }}

                                                                    <del>
                                                                        {{ symbolPrice($item->price) }}
                                                                    </del>
                                                                @else
                                                                    @php
                                                                        $calculate_price = $item->price;
                                                                    @endphp
                                                                    {{ symbolPrice($calculate_price) }}
                                                                @endif
                                                            @else
                                                                @php
                                                                    $calculate_price = $item->price;
                                                                @endphp
                                                                {{ symbolPrice($calculate_price) }}
                                                            @endif

                                                        </h6>

                                                        <div class="quantity-input">
                                                            <button class="quantity-down_variation" type="button"
                                                                id="quantityDown">
                                                                -
                                                            </button>
                                                            <input type="hidden" name="v_name[]"
                                                                value="{{ $item->name }}">
                                                            @php
                                                                if ($item->ticket_available_type == 'limited') {
                                                                    $stock = $item->ticket_available;
                                                                } else {
                                                                    $stock = 'unlimited';
                                                                }
                                                                if ($item->max_ticket_buy_type == 'limited') {
                                                                    $max_buy = $item->v_max_ticket_buy;
                                                                } else {
                                                                    $max_buy = 'unlimited';
                                                                }
                                                            @endphp
                                                            <input type="number" value="0" class="quantity"
                                                                data-price="{{ $calculate_price }}"
                                                                data-max_buy_ticket="{{ $max_buy }}"
                                                                data-name="{{ $item->name }}" name="quantity[]"
                                                                data-ticket_id="{{ $ticket->id }}" readonly
                                                                data-stock="{{ $stock }}"
                                                                data-purchase="{{ $purchase['status'] }}"
                                                                data-p_qty="{{ $purchase['p_qty'] }}">
                                                            <button class="quantity-up" type="button" id="quantityUP">
                                                                +
                                                            </button>
                                                        </div>
                                                        @if ($ticket->early_bird_discount == 'enable')
                                                            @php
                                                                $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                                                            @endphp
                                                            @if (!$discount_date->isPast())
                                                                <p>{{ __('Discount available') . ' ' }} :
                                                                    ({{ __('till') . ' ' }} :
                                                                    <span
                                                                        dir="ltr">{{ \Carbon\Carbon::parse($discount_date)->timezone($websiteInfo->timezone)->translatedFormat('Y/m/d h:i a') }}</span>)
                                                                </p>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <p
                                                        class="text-warning max_error_{{ $ticket->id }}{{ $item->v_max_ticket_buy }} ">
                                                    </p>
                                                @endforeach
                                            @elseif($ticket->pricing_type == 'free')
                                                @php
                                                    if ($ticket->ticket_available_type == 'limited') {
                                                        $stock = $ticket->ticket_available;
                                                    } else {
                                                        $stock = 'unlimited';
                                                    }

                                                    //ticket purchase or not check
                                                    $de_lang = App\Models\Language::where('is_default', 1)->first();
                                                    $ticket_content_default = App\Models\Event\TicketContent::where([['language_id', $de_lang->id], ['ticket_id', $ticket->id]])->first();
                                                    if (Auth::guard('customer')->user() && $ticket->max_ticket_buy_type == 'limited') {
                                                        $purchase = isTicketPurchaseVenue($ticket->event_id, $ticket->max_buy_ticket, $ticket->id, @$ticket_content_default->title);
                                                    } else {
                                                        $purchase = ['status' => 'false', 'p_qty' => 1];
                                                    }
                                                    $ticket_content = App\Models\Event\TicketContent::where([['language_id', $currentLanguageInfo->id], ['ticket_id', $ticket->id]])->first();
                                                @endphp
                                                <p class="mb-0"><strong>{{ @$ticket_content->title }}</strong></p>
                                                <div class="click-show">
                                                    <div class="show-content">
                                                        {!! @$ticket_content->description !!}
                                                    </div>
                                                    @if (strlen(@$ticket_content->description) > 50)
                                                        <div class="read-more-btn">
                                                            <span>{{ __('Read more') }}</span>
                                                            <span>{{ __('Read less') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="price-count">
                                                    <h6>
                                                        <span class="">{{ __('free') }}</span>
                                                    </h6>
                                                    <div class="quantity-input">
                                                        <button class="quantity-down" type="button" id="quantityDown">
                                                            -
                                                        </button>
                                                        <input class="quantity"
                                                            data-max_buy_ticket="{{ $ticket->max_buy_ticket }}"
                                                            type="number" value="0"
                                                            data-price="{{ $ticket->price }}" name="quantity[]"
                                                            data-ticket_id="{{ $ticket->id }}" readonly
                                                            data-stock="{{ $stock }}"
                                                            data-purchase="{{ $purchase['status'] }}"
                                                            data-p_qty="{{ $purchase['p_qty'] }}">
                                                        <button class="quantity-up" type="button" id="quantityUP">
                                                            +
                                                        </button>
                                                    </div>
                                                </div>
                                                <p
                                                    class="text-warning max_error_{{ $ticket->id }}{{ $ticket->max_ticket_buy_type == 'limited' ? $ticket->max_buy_ticket : '' }} ">
                                                </p> --}}
                                            @endif
                                        @endforeach
                                    @endif
                                    {{-- end ticket list --}}

                                    {{-- @if ($tickets_count > 0)
                                        <div class="total">
                                            <b>{{ __('Total Price') . ' :' }} </b>
                                            <span class="h4" dir="ltr">
                                                <span>{{ $basicInfo->base_currency_symbol_position == 'left' ? $basicInfo->base_currency_symbol : '' }}</span>
                                                <span id="total_price">0</span>
                                                <span>{{ $basicInfo->base_currency_symbol_position == 'right' ? $basicInfo->base_currency_symbol : '' }}</span>

                                            </span>
                                            <input type="hidden" name="total" id="total">
                                        </div>
                                        <button class="theme-btn w-100 mt-20"
                                            type="submit">{{ __('Book Now') }}</button>
                                    @endif --}}
                                    <button class="theme-btn w-100 mt-20" type="button" id="NextFormOrder">
                                        {{ __('Book Now') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-12">
                        @if (!empty(showAd(3)))
                            <div class="text-center mt-4">
                                {!! showAd(3) !!}
                            </div>
                        @endif
                    </div>
                    <div class="col-12">
                        @if (count($related_events) > 0)
                            <hr>
                            <div class="releted-event-header mt-50">
                                <h3>{{ __('Related Events') }}</h3>
                                <div class="slick-next-prev mb-10">
                                    <button class="prev"><i class="fas fa-chevron-left"></i></button>
                                    <button class="next"><i class="fas fa-chevron-right"></i></button>
                                </div>
                            </div>
                            <div class="related-event-wrap">
                                @foreach ($related_events as $event)
                                    <div class="event-item">
                                        <div class="event-image">
                                            <a href="{{ route('event.details', [$event->slug, $event->id]) }}">
                                                <img class="lazy"
                                                    data-src="{{ asset('assets/admin/img/event/thumbnail/' . $event->thumbnail) }}"
                                                    alt="Event">
                                            </a>
                                        </div>
                                        <div class="event-content">
                                            <ul class="time-info">
                                                <li>
                                                    <i class="far fa-calendar-alt"></i>
                                                    <span>
                                                        @php
                                                            $date = strtotime($event->start_date);
                                                        @endphp
                                                        {{ \Carbon\Carbon::parse($date)->timezone($websiteInfo->timezone)->translatedFormat('d M') }}
                                                    </span>
                                                </li>
                                                @php
                                                    if ($event->date_type == 'multiple') {
                                                        $event_date = eventLatestDates($event->id);
                                                        $date = strtotime(@$event_date->start_date);
                                                    } else {
                                                        $date = strtotime($event->start_date);
                                                    }
                                                @endphp
                                                <li>
                                                    <i class="far fa-hourglass"></i>
                                                    <span
                                                        title="Event Duration">{{ $event->date_type == 'multiple' ? @$event_date->duration : $event->duration }}</span>
                                                </li>
                                                <li>
                                                    <i class="far fa-clock"></i>
                                                    <span>
                                                        @php
                                                            $start_time = strtotime($event->start_time);
                                                        @endphp
                                                        {{ \Carbon\Carbon::parse($start_time)->timezone($websiteInfo->timezone)->translatedFormat('h:s A') }}
                                                    </span>
                                                </li>
                                            </ul>
                                            @if ($event->organizer_id != null)
                                                @php
                                                    $organizer = App\Models\Organizer::where('id', $event->organizer_id)->first();
                                                @endphp
                                                @if ($organizer)
                                                    <a href="{{ route('frontend.organizer.details', [$organizer->id, str_replace(' ', '-', $organizer->username)]) }}"
                                                        class="organizer">{{ __('By') }}&nbsp;&nbsp;{{ $organizer->username }}</a>
                                                @endif
                                            @else
                                                @php
                                                    $admin = App\Models\Admin::first();
                                                @endphp
                                                <a href="{{ route('frontend.organizer.details', [$admin->id, str_replace(' ', '-', $admin->username), 'admin' => 'true']) }}"
                                                    class="organizer">{{ $admin->username }}</a>
                                            @endif
                                            <h5>
                                                <a href="{{ route('event.details', [$event->slug, $event->id]) }}">
                                                    @if (strlen($event->title) > 30)
                                                        {{ mb_substr($event->title, 0, 30) . '...' }}
                                                    @else
                                                        {{ $event->title }}
                                                    @endif
                                                </a>
                                            </h5>
                                            @php
                                                $desc = strip_tags($event->description);
                                            @endphp

                                            @if (strlen($desc) > 45)
                                                <p>{{ mb_substr($desc, 0, 50) . '....' }}</p>
                                            @else
                                                <p>{{ $desc }}</p>
                                            @endif
                                            @php
                                                $ticket = DB::table('tickets')
                                                    ->where('event_id', $event->id)
                                                    ->whereNull('deleted_at')
                                                    ->first();
                                                $event_count = DB::table('tickets')
                                                    ->where('event_id', $event->id)
                                                    ->whereNull('deleted_at')
                                                    ->get()
                                                    ->count();
                                            @endphp

                                            <div class="price-remain">
                                                <div class="location">
                                                    @if ($event->event_type == 'venue' || $event->event_type == 'tournament')
                                                        <i class="fas fa-map-marker-alt"></i>
                                                        <span>
                                                            @if ($event->city != null)
                                                                {{ $event->city }}
                                                            @endif
                                                            @if ($event->country)
                                                                , {{ $event->country }}
                                                            @endif
                                                        </span>
                                                    @else
                                                        <i class="fas fa-map-marker-alt"></i>
                                                        <span>{{ __('Online') }}</span>
                                                    @endif
                                                </div>
                                                <span>
                                                    @if ($ticket)
                                                        @if ($ticket->event_type == 'online')
                                                            @if ($ticket->price != null)
                                                                <span class="price" dir="ltr">
                                                                    @if ($ticket->early_bird_discount == 'enable')
                                                                        @php
                                                                            $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                                                                        @endphp
                                                                        @if ($ticket->early_bird_discount_type == 'fixed' && !$discount_date->isPast())
                                                                            @php
                                                                                $calculate_price = $ticket->price - $ticket->early_bird_discount_amount;
                                                                            @endphp
                                                                            {{ symbolPrice($calculate_price) }}
                                                                            <span>
                                                                                <del>
                                                                                    {{ symbolPrice($ticket->price) }}
                                                                                </del>
                                                                            </span>
                                                                        @elseif ($ticket->early_bird_discount_type == 'percentage' && !$discount_date->isPast())
                                                                            @php
                                                                                $p_price = ($ticket->price * $ticket->early_bird_discount_amount) / 100;
                                                                                $calculate_price = $ticket->price - $p_price;
                                                                            @endphp
                                                                            {{ symbolPrice($calculate_price) }}

                                                                            <span>
                                                                                <del>
                                                                                    {{ symbolPrice($ticket->price) }}
                                                                                </del>
                                                                            </span>
                                                                        @else
                                                                            @php
                                                                                $calculate_price = $ticket->price;
                                                                            @endphp
                                                                            {{ symbolPrice($calculate_price) }}
                                                                        @endif
                                                                    @else
                                                                        @php
                                                                            $calculate_price = $ticket->price;
                                                                        @endphp
                                                                        {{ symbolPrice($calculate_price) }}
                                                                    @endif
                                                                </span>
                                                            @else
                                                                <span class="price">{{ __('Free') }}</span>
                                                            @endif
                                                        @endif
                                                        @if ($ticket->event_type == 'venue')
                                                            @if ($ticket->pricing_type == 'variation')
                                                                <span class="price" dir="ltr">
                                                                    @php
                                                                        $variation = json_decode($ticket->variations, true);
                                                                        $price = $variation[0]['price'];
                                                                    @endphp
                                                                    <span class="price">
                                                                        @if ($ticket->early_bird_discount == 'enable')
                                                                            @php
                                                                                $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                                                                            @endphp
                                                                            @if ($ticket->early_bird_discount_type == 'fixed' && !$discount_date->isPast())
                                                                                @php
                                                                                    $calculate_price = $price - $ticket->early_bird_discount_amount;
                                                                                @endphp
                                                                                {{ symbolPrice($calculate_price) }}
                                                                                <span><del>
                                                                                        {{ symbolPrice($price) }}
                                                                                    </del></span>
                                                                            @elseif ($ticket->early_bird_discount_type == 'percentage' && !$discount_date->isPast())
                                                                                @php
                                                                                    $p_price = ($price * $ticket->early_bird_discount_amount) / 100;
                                                                                    $calculate_price = $p_price - $price;
                                                                                @endphp

                                                                                {{ symbolPrice($calculate_price) }}

                                                                                <span>
                                                                                    <del>
                                                                                        {{ symbolPrice($price) }}
                                                                                    </del>
                                                                                </span>
                                                                            @else
                                                                                @php
                                                                                    $calculate_price = $price;
                                                                                @endphp
                                                                                {{ symbolPrice($calculate_price) }}
                                                                            @endif
                                                                        @else
                                                                            @php
                                                                                $calculate_price = $price;
                                                                            @endphp
                                                                            {{ symbolPrice($calculate_price) }}
                                                                        @endif
                                                                        <strong>{{ $event_count > 1 ? '*' : '' }}</strong>
                                                                    </span>
                                                                </span>
                                                            @elseif($ticket->pricing_type == 'normal')
                                                                <span class="price" dir="ltr">

                                                                    @if ($ticket->early_bird_discount == 'enable')
                                                                        @php
                                                                            $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                                                                        @endphp
                                                                        @if ($ticket->early_bird_discount_type == 'fixed' && !$discount_date->isPast())
                                                                            @php
                                                                                $calculate_price = $ticket->price - $ticket->early_bird_discount_amount;
                                                                            @endphp

                                                                            {{ symbolPrice($calculate_price) }}
                                                                            <span>
                                                                                <del>
                                                                                    {{ symbolPrice($ticket->price) }}
                                                                                </del>
                                                                            </span>
                                                                        @elseif ($ticket->early_bird_discount_type == 'percentage' && !$discount_date->isPast())
                                                                            @php
                                                                                $p_price = ($ticket->price * $ticket->early_bird_discount_amount) / 100;
                                                                                $calculate_price = $ticket->price - $p_price;
                                                                            @endphp
                                                                            {{ symbolPrice($calculate_price) }}

                                                                            <span>
                                                                                <del>
                                                                                    {{ symbolPrice($ticket->price) }}
                                                                                </del>
                                                                            </span>
                                                                        @else
                                                                            @php
                                                                                $calculate_price = $ticket->price;
                                                                            @endphp
                                                                            {{ symbolPrice($calculate_price) }}
                                                                        @endif
                                                                    @else
                                                                        @php
                                                                            $calculate_price = $ticket->price;
                                                                        @endphp
                                                                        {{ symbolPrice($calculate_price) }}
                                                                    @endif
                                                                    <strong>{{ $event_count > 1 ? '*' : '' }}</strong>
                                                                </span>
                                                            @else
                                                                <span class="price">{{ __('Free') }}</span>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        @if (Auth::guard('customer')->check())
                                            @php
                                                $customer_id = Auth::guard('customer')->user()->id;
                                                $event_id = $event->id;
                                                $checkWishList = checkWishList($event_id, $customer_id);
                                            @endphp
                                        @else
                                            @php
                                                $checkWishList = false;
                                            @endphp
                                        @endif
                                        <a href="{{ $checkWishList == false ? route('addto.wishlist', $event->id) : route('remove.wishlist', $event->id) }}"
                                            class="wishlist-btn {{ $checkWishList == true ? 'bg-success' : '' }}">
                                            <i class="far fa-bookmark"></i>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('modals')
    @includeIf('frontend.partials.modals')
@endsection

@section('custom-script')
    <script type="text/javascript" src="{{ asset('assets/admin/js/helpers/global-helpers.js?' . time()) }}"></script>
    <script type="text/javascript" src="{{ asset('assets/front/js/event-detail.js?' . time()) }}"></script>
    <!-- <script>
        $(document).ready(function() {
            $(".first-info-detail-quota-ticket").trigger('click');
        });

        $(".category-event-tournament-info").on("click", function() {
            $("#content-qouta-ticket").empty();
            $(".button-warning-custom").removeClass("active")

            const categoryID = this.getAttribute("data-category-id");

            if ($(`.button-sub-category-0-${categoryID}`)) {
                $(`.button-sub-category-0-${categoryID}`).addClass("active")
                const dataInfo = JSON.parse(this.getAttribute("data-info"));
                if (dataInfo.sub_category) {
                    if (dataInfo.sub_category[0].tickets) {
                        const dataTickets = dataInfo.sub_category[0].tickets;
                        let content = ''
                        dataTickets.map((val) => {
                            const status = val.available_qouta > 0 ? 'Tersedia' : 'Tidak Tersedia';
                            const badgeColor = val.available_qouta > 0 ? 'badge-success' : 'badge-danger';
                            content += `
                                <div class="card">
                                  <div class="card-body p-2">
                                      <h5 class="card-title text-primary text-center" style="font-weight:bold">
                                          ${val.ticket_title}
                                      </h5>
                                      <div class="text-center">
                                          <span class="badge badge-pill ${badgeColor}" style="font-size:0.8rem">
                                              ${status}: ${val.available_qouta}/${val.original_qouta}
                                          </span>
                                      </div>
                                  </div>
                              </div>
                            `
                        })
                        $("#content-qouta-ticket").append(content);
                    }
                }
            }
        })

        $(".info-detail-qouta-ticket").on("click", function() {
            $("#content-qouta-ticket").empty();

            const dataTickets = JSON.parse(this.getAttribute("data-ticket-quota"));
            const categoryID = this.getAttribute("data-category-id");
            const categoryKeyID = this.getAttribute("data-category-key-id");

            $(".button-warning-custom").removeClass("active")
            $(`.button-sub-category-${categoryKeyID}-${categoryID}`).addClass("active")
            let content = ''
            dataTickets.map((val) => {
                const status = val.available_qouta > 0 ? 'Tersedia' : 'Tidak Tersedia';
                const badgeColor = val.available_qouta > 0 ? 'badge-success' : 'badge-danger';
                content += `
                  <div class="card">
                      <div class="card-body p-2">
                          <h5 class="card-title text-primary text-center" style="font-weight:bold">
                              ${val.ticket_title}
                          </h5>
                          <div class="text-center">
                              <span class="badge badge-pill ${badgeColor}" style="font-size:0.8rem">
                                  ${status}: ${val.available_qouta}/${val.original_qouta}
                              </span>
                          </div>
                      </div>
                  </div>
                `
            })
            $("#content-qouta-ticket").append(content);
        });
    </script> -->
@endsection
