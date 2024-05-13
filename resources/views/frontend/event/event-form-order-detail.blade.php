@extends('frontend.layout')
@php
    $og_title = $from_step_one['event_title'];
    if ($event->date_type == 'multiple') {
        $event_date = eventLatestDates($event->id);
        $date = strtotime(@$event_date->start_date);
    } else {
        $date = strtotime($event->start_date);
    }
@endphp

@section('pageHeading')
    {{ $from_step_one['event_title'] }}
@endsection

@section('meta-keywords', "order $og_title")
@section('meta-description', "processing order $og_title")
@section('og-title', "$og_title")

@section('custom-style')
    <style>
        .select2-container .select2-selection--single {
            box-sizing: border-box !important;
            cursor: pointer !important;
            display: block !important;
            height: 56px !important;
            user-select: none !important;
            -webkit-user-select: none !important;
            padding: 15px 25px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 26px !important;
            position: absolute !important;
            top: 1px !important;
            right: 1px !important;
            width: 20px !important;
            padding: 15px 25px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #888 transparent transparent transparent !important;
            border-style: solid !important;
            border-width: 5px 4px 0 4px !important;
            height: 0 !important;
            left: 50% !important;
            margin-left: 15px !important;
            margin-top: 15px !important;
            position: absolute !important;
            top: 50% !important;
            width: 0 !important;
        }
    </style>
@endsection

@section('content')
    <form id="eventForm" action="" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="event_id" name="event_id" value="{{ $from_step_one['event_id'] }}">
        <input type="hidden" id="base_url" value="{{ url('/') }}">

        <section class="event-details-section pt-110 rpt-90 pb-90 rpb-70">
            <div class="container">
                <div class="event-details-content">
                    <div class="row">
                        <div class="col-12 col-lg-8 order-1 order-lg-0 my-1">
                            <div class="row">
                                <div class="col-12">
                                    <h4 style="font-weight: bold">
                                        Order Details
                                    </h4>
                                    <small>
                                        *These contact details will be used for sending e-tickets and refund purposes.
                                    </small>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td rows="2">Name</td>
                                                    <td rows="1">:</td>
                                                    <td rows="1">{{ $organizer->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td rows="2">Phone Number</td>
                                                    <td rows="1">:</td>
                                                    <td rows="1">{{ $organizer->phone }}</td>
                                                </tr>
                                                <tr>
                                                    <td rows="2">Email</td>
                                                    <td rows="1">:</td>
                                                    <td rows="1">{{ $organizer->email }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @foreach ($category_tickets as $val_category_tickets)
                                    @if (strtolower($val_category_tickets['name']) == 'individu' && $val_category_tickets['quantity'] > 0)
                                        <section id="individu_section">
                                            <div class="col-12 mt-3">
                                                <h4 style="font-weight: bold">
                                                    Individu Category
                                                </h4>
                                                <small>
                                                    *Make sure that the Participant's name is exactly as written in the
                                                    government
                                                    issued
                                                    ID/Passport/Driving License. Avoid any mistake, because some Organizer
                                                    don't
                                                    allow
                                                    name
                                                    corrections after booking.
                                                </small>
                                            </div>
                                            <div class="col-12 mt-3">
                                                <div class="accordion" id="accordionParticipant">
                                                    @for ($i = 0; $i < $val_category_tickets['quantity']; $i++)
                                                        <div class="card">
                                                            <div class="card-header bg-primary-1 text-white text-left collapsed cursor-pointer"
                                                                id="participant_individu{{ $i }}"
                                                                data-toggle="collapse"
                                                                data-target="#collapse_participant_{{ $i }}"
                                                                aria-expanded="false"
                                                                aria-controls="collapse_participant_{{ $i }}">
                                                                Participant Details {{ $i + 1 }}
                                                            </div>
                                                            <div id="collapse_participant_{{ $i }}"
                                                                class="collapse {{ $i == 0 ? 'show' : '' }}"
                                                                aria-labelledby="participant_individu{{ $i }}"
                                                                data-parent="#accordionParticipant">
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-12 form-group">
                                                                            <label for="name_individu{{ $i }}">
                                                                                {{ __('Full Name') }}*
                                                                            </label>
                                                                            <input type="text" class="form-control"
                                                                                id="name_individu{{ $i }}"
                                                                                name="name_individu[]"
                                                                                placeholder="{{ __('Enter Your Full Name') }}"
                                                                                required>
                                                                        </div>
                                                                        <div class="col-12 col-lg-6 form-group">
                                                                            <label
                                                                                for="gender_individu{{ $i }}">
                                                                                {{ __('Gender') }}*
                                                                            </label>
                                                                            <select class="form-select"
                                                                                id="gender_individu{{ $i }}"
                                                                                name="gender_individu[]">
                                                                                <option value="male" selected>
                                                                                    {{ __('Male') }}</option>
                                                                                <option value="female">{{ __('Female') }}
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-12 col-lg-6 form-group">
                                                                            <label
                                                                                for="birth_date_individu{{ $i }}">
                                                                                {{ __('Birth Date') }}*
                                                                            </label>
                                                                            <input type="date" class="form-control"
                                                                                id="birth_date_individu{{ $i }}"
                                                                                name="birth_date_individu[]"
                                                                                placeholder="{{ __('Select Date') }}"
                                                                                required>
                                                                        </div>
                                                                        <div
                                                                            class="col-12 col-lg-6 form-group content-profile-country-individu-{{ $i }}">
                                                                            <label
                                                                                for="profile_country_individu{{ $i }}">
                                                                                {{ __('Country') }}*
                                                                            </label>
                                                                            <select
                                                                                class="form-select js-example-basic-single"
                                                                                id="profile_country_individu{{ $i }}"
                                                                                name="profile_country_individu[]"
                                                                                onchange="handlerProfileCountry({{ $i }})"
                                                                                required>
                                                                                <option value="" selected disabled>
                                                                                    {{ __('Select Country') }}
                                                                                </option>
                                                                                @foreach ($countries as $val_countries)
                                                                                    <option
                                                                                        value="{{ $val_countries['id'] }}">
                                                                                        {{ $val_countries['name'] }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div
                                                                            class="col-12 col-lg-6 form-group content-profile-city-individu-{{ $i }}">
                                                                            <label
                                                                                for="profile_city_individu{{ $i }}">
                                                                                {{ __('City/District') }}*
                                                                            </label>
                                                                            <select
                                                                                class="form-select js-example-basic-single"
                                                                                id="profile_city_individu{{ $i }}"
                                                                                name="profile_city_individu[]" required>
                                                                                <option value="" selected disabled>
                                                                                    {{ __('Select City/District') }}
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                        @if (strtolower($delegation_event['contingent_type']) == 'open')
                                                                            <div
                                                                                class="col-12 form-group content-delegation-individu-{{ $i }}">
                                                                                <label
                                                                                    for="delegation_individu{{ $i }}">
                                                                                    {{ __('Delegation Type') }}*
                                                                                </label>
                                                                                <select
                                                                                    class="form-select js-example-basic-single"
                                                                                    id="delegation_individu{{ $i }}"
                                                                                    name="delegation_individu[]"
                                                                                    onchange="handlerDelegationIndividu({{ $i }})"
                                                                                    required>
                                                                                    <option value="" selected
                                                                                        disabled>
                                                                                        {{ __('Select Delegation Type') }}
                                                                                    </option>
                                                                                    @foreach ($delegation_type as $val_delegation_type)
                                                                                        <option
                                                                                            value="{{ $val_delegation_type['name'] }}">
                                                                                            {{ $val_delegation_type['name'] }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        @else
                                                                            <input type="hidden"
                                                                                value="{{ $delegation_event['select_type'] }}"
                                                                                name="delegation_individu[]">
                                                                        @endif
                                                                        <div
                                                                            class="col-12 form-group content-delegation-country-individu-{{ $i }} d-none">
                                                                        </div>
                                                                        <div
                                                                            class="col-12 form-group content-delegation-province-individu-{{ $i }} d-none">
                                                                        </div>
                                                                        <div
                                                                            class="col-12 form-group content-delegation-city-individu-{{ $i }} d-none">
                                                                        </div>
                                                                        <div
                                                                            class="col-12 form-group content-delegation-school-individu-{{ $i }} d-none">
                                                                        </div>
                                                                        <div
                                                                            class="col-12 form-group content-delegation-club-individu-{{ $i }} d-none">
                                                                        </div>
                                                                        <div
                                                                            class="col-12 form-group content-delegation-organization-individu-{{ $i }} d-none">
                                                                        </div>
                                                                        <div class="col-12 form-group">
                                                                            <label
                                                                                for="category_individu{{ $i }}">
                                                                                {{ __('Category') }}*
                                                                            </label>
                                                                            <select class="form-select"
                                                                                id="category_individu{{ $i }}"
                                                                                name="category_individu[]">
                                                                                <option value="" selected>
                                                                                    {{ __('Select Category') }}</option>
                                                                                @foreach ($sub_category_tickets as $val_sub_cat_ticket)
                                                                                    @if ($val_sub_cat_ticket['category_id'] == $val_category_tickets['id'])
                                                                                        <option
                                                                                            value="{{ $val_sub_cat_ticket['id'] }}">
                                                                                            {{ $val_sub_cat_ticket['title'] }}
                                                                                        </option>
                                                                                    @endif
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        </section>
                                    @endif

                                    @if (strtolower($val_category_tickets['name']) == 'team' && $val_category_tickets['quantity'] > 0)
                                        <section id="team_section">
                                            <div class="col-12 mt-3">
                                                <h4 style="font-weight: bold">
                                                    Team Category
                                                </h4>
                                                <small>
                                                    *Make sure that the Team's name is exactly. Avoid any mistake, because
                                                    some
                                                    Organizer don't allow name, category, delegation corrections after
                                                    booking.
                                                </small>
                                            </div>
                                            <div class="col-12 mt-3">
                                                <div class="accordion" id="accordionExampleTeam">
                                                    @for ($i = 0; $i < $val_category_tickets['quantity']; $i++)
                                                        <div class="card">
                                                            <div class="card-header bg-primary-1 text-white text-left collapsed cursor-pointer"
                                                                id="team{{ $i }}" data-toggle="collapse"
                                                                data-target="#collapse_team{{ $i }}"
                                                                aria-expanded="false"
                                                                aria-controls="collapse_team{{ $i }}">
                                                                Team Details {{ $i + 1 }}
                                                            </div>
                                                            <div id="collapse_team{{ $i }}"
                                                                class="collapse {{ $i == 0 ? 'show' : '' }}"
                                                                aria-labelledby="team{{ $i }}"
                                                                data-parent="#accordionExampleTeam">
                                                                <div class="card-body">
                                                                    Some placeholder content for the second accordion panel.
                                                                    This
                                                                    panel is
                                                                    hidden by default.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        </section>
                                    @endif

                                    @if (strtolower($val_category_tickets['name']) == 'mix team' && $val_category_tickets['quantity'] > 0)
                                        <section id="mix_team_section">
                                            <div class="col-12 mt-3">
                                                <h4 style="font-weight: bold">
                                                    Mix Team Category
                                                </h4>
                                                <small>
                                                    *Make sure that the Team's name is exactly. Avoid any mistake, because
                                                    some
                                                    Organizer don't allow name, category, delegation corrections after
                                                    booking.
                                                </small>
                                            </div>
                                            <div class="col-12 mt-3">
                                                <div class="accordion" id="accordionExampleMixTeam">
                                                    @for ($i = 0; $i < $val_category_tickets['quantity']; $i++)
                                                        <div class="card">
                                                            <div class="card-header bg-primary-1 text-white text-left collapsed cursor-pointer"
                                                                id="mix_team{{ $i }}" data-toggle="collapse"
                                                                data-target="#collapse_mix_team{{ $i }}"
                                                                aria-expanded="false"
                                                                aria-controls="collapse_mix_team{{ $i }}">
                                                                Mix Team Details {{ $i + 1 }}
                                                            </div>
                                                            <div id="collapse_mix_team{{ $i }}"
                                                                class="collapse {{ $i == 0 ? 'show' : '' }}"
                                                                aria-labelledby="mix_team{{ $i }}"
                                                                data-parent="#accordionExampleMixTeam">
                                                                <div class="card-body">
                                                                    Some placeholder content for the second accordion panel.
                                                                    This
                                                                    panel is
                                                                    hidden by default.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        </section>
                                    @endif

                                    @if (strtolower($val_category_tickets['name']) == 'official' && $val_category_tickets['quantity'] > 0)
                                        <section id="official_section">
                                            <div class="col-12 mt-3">
                                                <h4 style="font-weight: bold">
                                                    Official Category
                                                </h4>
                                                <small>
                                                    *Make sure that the Official's name is exactly as written in the
                                                    government
                                                    issued
                                                    ID/Passport/Driving License. Avoid any mistake, because some Organizer
                                                    don't
                                                    allow
                                                    name corrections after booking.
                                                </small>
                                            </div>
                                            <div class="col-12 mt-3">
                                                <div class="accordion" id="accordionExampleOfficial">
                                                    @for ($i = 0; $i < $val_category_tickets['quantity']; $i++)
                                                        <div class="card">
                                                            <div class="card-header bg-primary-1 text-white text-left collapsed cursor-pointer"
                                                                id="official{{ $i }}" data-toggle="collapse"
                                                                data-target="#collapse_official{{ $i }}"
                                                                aria-expanded="false"
                                                                aria-controls="collapse_official{{ $i }}">
                                                                Official Details {{ $i + 1 }}
                                                            </div>
                                                            <div id="collapse_official{{ $i }}"
                                                                class="collapse {{ $i == 0 ? 'show' : '' }}"
                                                                aria-labelledby="official{{ $i }}"
                                                                data-parent="#accordionExampleOfficial">
                                                                <div class="card-body">
                                                                    Some placeholder content for the second accordion panel.
                                                                    This
                                                                    panel is
                                                                    hidden by default.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        </section>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 order-0 order-lg-1 my-1">
                            <div class="row">
                                <div class="col-12 d-flex flex-row flex-wrap gap-10px">
                                    <img class="lazy img-fluid img-thumbnail" style="border-radius:1rem;"
                                        data-src="{{ asset('assets/admin/img/event/thumbnail/' . $event['thumbnail']) }}"
                                        alt="Event" width="100" height="71.88">
                                    <div class="d-flex flex-column">
                                        <h5 class="font-weight-normal mb-0 text-primary-1">
                                            {{ $event->title }}
                                        </h5>
                                        <div class="d-flex justify-content-between flex-row flex-wrap gap-10px">
                                            <small class="font-weight-normal text-primary-1">
                                                <i class="far fa-calendar-alt"></i>
                                                <span class="mr-1">
                                                    {{ \Carbon\Carbon::parse($date)->timezone($websiteInfo->timezone)->translatedFormat('D, dS M Y') }}
                                                </span>
                                            </small>
                                            <small class="font-weight-normal text-primary-1">
                                                <i class="far fa-clock"></i>
                                                <span class="mr-1">
                                                    {{ $event->date_type == 'multiple' ? @$event_date->duration : $event->duration }}
                                                </span>
                                            </small>
                                        </div>
                                        <small class="font-weight-normal text-primary-1">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span>
                                                @if ($event->city != null)
                                                    {{ $event->city }}
                                                @endif
                                                @if ($event->country)
                                                    , {{ $event->country }}
                                                @endif
                                            </span>
                                        </small>
                                    </div>
                                </div>
                                <div class="col-12 mt-3 mb-2">
                                    <h4 class="font-weight-bold">{{ __('Ticket Summary') }}</h4>
                                </div>
                                <div class="col-12">
                                    <p class="font-weight-bold">Tickets Info</p>
                                </div>
                                @php
                                    $total_tickets_quantity = 0;
                                @endphp
                                @foreach ($category_tickets as $val_category_tickets)
                                    @if ($val_category_tickets['quantity'] > 0)
                                        @php
                                            $total_tickets_quantity + $val_category_tickets['quantity'];
                                        @endphp
                                        <div class="col-12 d-flex justify-content-between">
                                            <p class="font-weight-medium mb-0">
                                                {{ ucfirst($val_category_tickets['name']) }}
                                            </p>
                                            <p class="font-weight-medium mb-0">
                                                {{ $val_category_tickets['quantity'] }}
                                            </p>
                                        </div>
                                        <div class="col-12 mt-0">
                                            <hr style="width:100%;text-align:left;margin-left:0">
                                        </div>
                                    @endif
                                @endforeach
                                <div class="col-12 d-flex justify-content-between">
                                    <p class="font-weight-medium mb-0">
                                        Total Tickets
                                    </p>
                                    <p class="font-weight-medium mb-0">
                                        {{ $total_tickets_quantity }}
                                    </p>
                                </div>
                                <div class="col-12 mt-3 mb-2">
                                    <h4 class="font-weight-bold">{{ __('Code Access') }}</h4>
                                </div>
                                <div class="col-12">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="type your code"
                                            aria-label="Example text with button addon" aria-describedby="button-addon1">
                                        <div class="input-group-prepend">
                                            <button class="theme-btn w-100" type="button" id="button-addon1">
                                                {{ __('Apply') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="theme-btn w-100" type="submit">
                                        {{ __('Continue') }}
                                    </button>
                                </div>
                                <div class="col-12 mt-3">
                                    <a href="{{ route('event.details', [$event['slug'], $from_step_one['event_id']]) }}"
                                        class="theme-btn-outline-primary-1 w-100" type="button">
                                        {{ __('Back') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </form>
@endsection
@section('custom-script')
    <script src="{{ asset('assets/front/js/order-detail.js') }}"></script>
@endsection

{{-- <section id="individu_section">
                                <div class="col-12 mt-3">
                                    <h4 style="font-weight: bold">
                                        Individu Category
                                    </h4>
                                    <small>
                                        *Make sure that the Participant's name is exactly as written in the government
                                        issued
                                        ID/Passport/Driving License. Avoid any mistake, because some Organizer don't allow
                                        name
                                        corrections after booking.
                                    </small>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="accordion" id="accordionParticipant">
                                        <div class="card">
                                            <div class="card-header bg-primary-1 text-white text-left collapsed cursor-pointer"
                                                id="participant_individu1" data-toggle="collapse"
                                                data-target="#collapse_participant_1" aria-expanded="false"
                                                aria-controls="collapse_participant_1">
                                                Participant Details 1
                                            </div>
                                            <div id="collapse_participant_1" class="collapse show"
                                                aria-labelledby="participant_individu1" data-parent="#accordionParticipant">
                                                <div class="card-body">
                                                    Some placeholder content for the second accordion panel. This panel is
                                                    hidden by default.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header bg-primary-1 text-white text-left collapsed cursor-pointer"
                                                id="participant_individu2" data-toggle="collapse"
                                                data-target="#collapse_participant2" aria-expanded="false"
                                                aria-controls="collapse_participant2">
                                                Participant Details 2
                                            </div>
                                            <div id="collapse_participant2" class="collapse"
                                                aria-labelledby="participant_individu2" data-parent="#accordionParticipant">
                                                <div class="card-body">
                                                    Some placeholder content for the second accordion panel. This panel is
                                                    hidden by default.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <section id="team_section">
                                <div class="col-12 mt-3">
                                    <h4 style="font-weight: bold">
                                        Team Category
                                    </h4>
                                    <small>
                                        *Make sure that the Team's name is exactly. Avoid any mistake, because some
                                        Organizer don't allow name, category, delegation corrections after booking.
                                    </small>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="accordion" id="accordionExampleTeam">
                                        <div class="card">
                                            <div class="card-header bg-primary-1 text-white text-left collapsed cursor-pointer"
                                                id="team1" data-toggle="collapse" data-target="#collapse_team1"
                                                aria-expanded="false" aria-controls="collapse_team1">
                                                Team Details 1
                                            </div>
                                            <div id="collapse_team1" class="collapse show" aria-labelledby="team1"
                                                data-parent="#accordionExampleTeam">
                                                <div class="card-body">
                                                    Some placeholder content for the second accordion panel. This panel is
                                                    hidden by default.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header bg-primary-1 text-white text-left collapsed cursor-pointer"
                                                id="team2" data-toggle="collapse" data-target="#collapse_team2"
                                                aria-expanded="false" aria-controls="collapse_team2">
                                                Team Details 2
                                            </div>
                                            <div id="collapse_team2" class="collapse" aria-labelledby="team2"
                                                data-parent="#accordionExampleTeam">
                                                <div class="card-body">
                                                    Some placeholder content for the second accordion panel. This panel is
                                                    hidden by default.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <section id="mix_team_section">
                                <div class="col-12 mt-3">
                                    <h4 style="font-weight: bold">
                                        Mix Team Category
                                    </h4>
                                    <small>
                                        *Make sure that the Team's name is exactly. Avoid any mistake, because some
                                        Organizer don't allow name, category, delegation corrections after booking.
                                    </small>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="accordion" id="accordionExampleMixTeam">
                                        <div class="card">
                                            <div class="card-header bg-primary-1 text-white text-left collapsed cursor-pointer"
                                                id="mix_team1" data-toggle="collapse" data-target="#collapse_mix_team1"
                                                aria-expanded="false" aria-controls="collapse_mix_team1">
                                                Mix Team Details 1
                                            </div>
                                            <div id="collapse_mix_team1" class="collapse show" aria-labelledby="mix_team1"
                                                data-parent="#accordionExampleMixTeam">
                                                <div class="card-body">
                                                    Some placeholder content for the second accordion panel. This panel is
                                                    hidden by default.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header bg-primary-1 text-white text-left collapsed cursor-pointer"
                                                id="mix_team2" data-toggle="collapse" data-target="#collapse_mix_team2"
                                                aria-expanded="false" aria-controls="collapse_mix_team2">
                                                Mix Team Details 2
                                            </div>
                                            <div id="collapse_mix_team2" class="collapse" aria-labelledby="mix_team2"
                                                data-parent="#accordionExampleMixTeam">
                                                <div class="card-body">
                                                    Some placeholder content for the second accordion panel. This panel is
                                                    hidden by default.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <section id="official_section">
                                <div class="col-12 mt-3">
                                    <h4 style="font-weight: bold">
                                        Official Category
                                    </h4>
                                    <small>
                                        *Make sure that the Official's name is exactly as written in the government issued
                                        ID/Passport/Driving License. Avoid any mistake, because some Organizer don't allow
                                        name corrections after booking.
                                    </small>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="accordion" id="accordionExampleOfficial">
                                        <div class="card">
                                            <div class="card-header bg-primary-1 text-white text-left collapsed cursor-pointer"
                                                id="official1" data-toggle="collapse" data-target="#collapse_official1"
                                                aria-expanded="false" aria-controls="collapse_official1">
                                                Official Details 1
                                            </div>
                                            <div id="collapse_official1" class="collapse show"
                                                aria-labelledby="official1" data-parent="#accordionExampleOfficial">
                                                <div class="card-body">
                                                    Some placeholder content for the second accordion panel. This panel is
                                                    hidden by default.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section> --}}
