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
    <form action="{{ route('detail-check-out-tournament') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="event_id" name="event_id" value="{{ $from_step_one['event_id'] }}">
        <input type="hidden" id="base_url" value="{{ url('/') }}">
        <input type="hidden" class="contingent_type" value="{{ $delegation_event['contingent_type'] }}"
            id="contingent_type">

        <section class="event-details-section pt-110 rpt-90 pb-90 rpb-70">
            <div class="container">
                <div class="event-details-content">
                    <div class="row">
                        <div class="col-12 col-lg-8 order-1 order-lg-0 my-1">
                            <div class="row">
                                <div class="col-12">
                                    <h4 style="font-weight: bold">
                                        {{ __('Order Details') }}
                                    </h4>
                                    <small>
                                        {{ __('Description Order Detail') }}
                                    </small>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td rows="2">{{ __('Info Name Customer On Order Detail') }}</td>
                                                    <td rows="1">:</td>
                                                    <td rows="1">
                                                        {{ empty($customer->fname) ? '' : $customer->fname }}
                                                        {{ empty($customer->lname) ? '' : $customer->lname }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td rows="2">
                                                        {{ __('Info Phone Number Customer On Order Detail') }}</td>
                                                    <td rows="1">:</td>
                                                    <td rows="1">
                                                        {{ empty($customer->phone) ? '' : $customer->phone }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td rows="2">{{ __('Info Email Customer On Order Detail') }}</td>
                                                    <td rows="1">:</td>
                                                    <td rows="1">
                                                        {{ empty($customer->email) ? '' : $customer->email }}
                                                    </td>
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
                                                    {{ __('Category Order Individu') }}
                                                </h4>
                                                <small>
                                                    {{ __('Description Category Order Individu') }}
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
                                                                {{ __('Collapse Name Form Individu Category') }}
                                                                {{ $i + 1 }}
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
                                                                                max="{{ date('Y-m-d') }}" required>
                                                                        </div>
                                                                        <div
                                                                            class="col-12 col-lg-6 form-group d-flex flex-column gap-2 content-profile-country-individu-{{ $i }}">
                                                                            <label
                                                                                for="profile_country_individu{{ $i }}">
                                                                                {{ __('Country') }}*
                                                                            </label>
                                                                            <select
                                                                                class="custom-select js-example-basic-single"
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
                                                                            class="col-12 col-lg-6 d-flex flex-column gap-2 form-group content-profile-city-individu-{{ $i }}">
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
                                                                                class="col-12 form-group d-flex flex-column gap-2 content-delegation-individu-{{ $i }}">
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
                                                                                name="delegation_individu[]"
                                                                                value="{{ $delegation_event['select_type'] }}" />
                                                                            @if (strtolower($delegation_event['select_type']) == 'province')
                                                                                <input type="hidden"
                                                                                    class="country_delegation_individu{{ $i }}"
                                                                                    value="{{ $delegation_event['country_id'] }}"
                                                                                    id="country_delegation_individu{{ $i }}">
                                                                            @endif

                                                                            @if (strtolower($delegation_event['select_type']) == 'city/district')
                                                                                <input type="hidden"
                                                                                    class="country_delegation_individu{{ $i }}"
                                                                                    value="{{ $delegation_event['country_id'] }}"
                                                                                    id="country_delegation_individu{{ $i }}">
                                                                                <input type="hidden"
                                                                                    class="province_delegation_individu{{ $i }}"
                                                                                    value="{{ $delegation_event['province_id'] }}"
                                                                                    id="province_delegation_individu{{ $i }}">
                                                                            @endif
                                                                            <input type="hidden"
                                                                                class="delegation_individu_choosed{{ $i }}"
                                                                                value="{{ $delegation_event['select_type'] }}"
                                                                                id="delegation_individu{{ $i }}">
                                                                        @endif
                                                                        <div
                                                                            class="col-12 content-delegation-country-individu-{{ $i }} d-none">
                                                                        </div>
                                                                        <div
                                                                            class="col-12 content-delegation-province-individu-{{ $i }} d-none">
                                                                        </div>
                                                                        <div
                                                                            class="col-12 content-delegation-city-individu-{{ $i }} d-none">
                                                                        </div>
                                                                        <div
                                                                            class="col-12 content-delegation-school-individu-{{ $i }} d-none">
                                                                        </div>
                                                                        <div
                                                                            class="col-12 content-delegation-club-individu-{{ $i }} d-none">
                                                                        </div>
                                                                        <div
                                                                            class="col-12 content-delegation-organization-individu-{{ $i }} d-none">
                                                                        </div>
                                                                        <div class="col-12">
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
                                                                                    @if (strtolower($val_sub_cat_ticket['category_name']) == 'individu')
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
                                                    {{ __('Category Order Team') }}
                                                </h4>
                                                <small>
                                                    {{ __('Description Category Order Team') }}
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
                                                                {{ __('Collapse Name Form Team Category') }}
                                                                {{ $i + 1 }}
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
                                                    {{ __('Category Order Mix Team') }}
                                                </h4>
                                                <small>
                                                    {{ __('Description Category Order Mix Team') }}
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
                                                                {{ __('Collapse Name Form Mix Team Category') }}
                                                                {{ $i + 1 }}
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
                                                    {{ __('Category Order Official') }}
                                                </h4>
                                                <small>
                                                    {{ __('Description Category Order Official') }}
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
                                                                {{ __('Collapse Name Form Official Category') }}
                                                                {{ $i + 1 }}
                                                            </div>
                                                            <div id="collapse_official{{ $i }}"
                                                                class="collapse {{ $i == 0 ? 'show' : '' }}"
                                                                aria-labelledby="official{{ $i }}"
                                                                data-parent="#accordionExampleOfficial">
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-12 form-group">
                                                                            <label
                                                                                for="name_official{{ $i }}">
                                                                                {{ __('Full Name') }}*
                                                                            </label>
                                                                            <input type="text" class="form-control"
                                                                                id="name_official{{ $i }}"
                                                                                name="name_official[]"
                                                                                placeholder="{{ __('Enter Your Full Name') }}"
                                                                                required>
                                                                        </div>
                                                                        <div class="col-12 col-lg-6 form-group">
                                                                            <label
                                                                                for="gender_official{{ $i }}">
                                                                                {{ __('Gender') }}*
                                                                            </label>
                                                                            <select class="form-select"
                                                                                id="gender_official{{ $i }}"
                                                                                name="gender_official[]">
                                                                                <option value="male" selected>
                                                                                    {{ __('Male') }}</option>
                                                                                <option value="female">
                                                                                    {{ __('Female') }}
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-12 col-lg-6 form-group">
                                                                            <label
                                                                                for="birth_date_official{{ $i }}">
                                                                                {{ __('Birth Date') }}*
                                                                            </label>
                                                                            <input type="date" class="form-control"
                                                                                id="birth_date_official{{ $i }}"
                                                                                name="birth_date_official[]"
                                                                                placeholder="{{ __('Select Date') }}"
                                                                                max="{{ date('Y-m-d') }}" required>
                                                                        </div>
                                                                        <div
                                                                            class="col-12 col-lg-6 form-group d-flex flex-column gap-2 content-profile-country-individu-{{ $i }}">
                                                                            <label
                                                                                for="profile_country_official{{ $i }}">
                                                                                {{ __('Country') }}*
                                                                            </label>
                                                                            <select
                                                                                class="custom-select js-example-basic-single"
                                                                                id="profile_country_official{{ $i }}"
                                                                                name="profile_country_official[]"
                                                                                onchange="handlerOfficialCountry({{ $i }})"
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
                                                                            class="col-12 col-lg-6 d-flex flex-column gap-2 form-group content-profile-city-individu-{{ $i }}">
                                                                            <label
                                                                                for="profile_city_official{{ $i }}">
                                                                                {{ __('City/District') }}*
                                                                            </label>
                                                                            <select
                                                                                class="form-select js-example-basic-single"
                                                                                id="profile_city_official{{ $i }}"
                                                                                name="profile_city_official[]" required>
                                                                                <option value="" selected disabled>
                                                                                    {{ __('Select City/District') }}
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                        @if (strtolower($delegation_event['contingent_type']) == 'open')
                                                                            <div
                                                                                class="col-12 form-group d-flex flex-column gap-2 content-delegation-official-{{ $i }}">
                                                                                <label
                                                                                    for="delegation_official{{ $i }}">
                                                                                    {{ __('Delegation Type') }}*
                                                                                </label>
                                                                                <select
                                                                                    class="form-select js-example-basic-single"
                                                                                    id="delegation_official{{ $i }}"
                                                                                    name="delegation_official[]"
                                                                                    onchange="handlerDelegationOfficial({{ $i }})"
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
                                                                                name="delegation_official[]"
                                                                                value="{{ $delegation_event['select_type'] }}" />
                                                                            @if (strtolower($delegation_event['select_type']) == 'province')
                                                                                <input type="hidden"
                                                                                    class="country_delegation_official{{ $i }}"
                                                                                    value="{{ $delegation_event['country_id'] }}"
                                                                                    id="country_delegation_official{{ $i }}">
                                                                            @endif

                                                                            @if (strtolower($delegation_event['select_type']) == 'city/district')
                                                                                <input type="hidden"
                                                                                    class="country_delegation_official{{ $i }}"
                                                                                    value="{{ $delegation_event['country_id'] }}"
                                                                                    id="country_delegation_official{{ $i }}">
                                                                                <input type="hidden"
                                                                                    class="province_delegation_official{{ $i }}"
                                                                                    value="{{ $delegation_event['province_id'] }}"
                                                                                    id="province_delegation_official{{ $i }}">
                                                                            @endif
                                                                            <input type="hidden"
                                                                                class="delegation_official_choosed{{ $i }}"
                                                                                value="{{ $delegation_event['select_type'] }}"
                                                                                id="delegation_official{{ $i }}">
                                                                        @endif
                                                                        <div
                                                                            class="col-12 content-delegation-country-official-{{ $i }} d-none">
                                                                        </div>
                                                                        <div
                                                                            class="col-12 content-delegation-province-official-{{ $i }} d-none">
                                                                        </div>
                                                                        <div
                                                                            class="col-12 content-delegation-city-official-{{ $i }} d-none">
                                                                        </div>
                                                                        <div
                                                                            class="col-12 content-delegation-school-official-{{ $i }} d-none">
                                                                        </div>
                                                                        <div
                                                                            class="col-12 content-delegation-club-official-{{ $i }} d-none">
                                                                        </div>
                                                                        <div
                                                                            class="col-12 content-delegation-organization-official-{{ $i }} d-none">
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <label
                                                                                for="category_official{{ $i }}">
                                                                                {{ __('Category') }}*
                                                                            </label>
                                                                            <select class="form-select"
                                                                                id="category_official{{ $i }}"
                                                                                name="category_official[]">
                                                                                <option value="" selected>
                                                                                    {{ __('Select Category') }}</option>
                                                                                @foreach ($sub_category_tickets as $val_sub_cat_ticket)
                                                                                    @if (strtolower($val_sub_cat_ticket['category_name']) == 'official')
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
                                            $total_tickets_quantity = $total_tickets_quantity + $val_category_tickets['quantity'];
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
                                @if (!empty($event->code))
                                    <div class="col-12 mt-3 mb-2">
                                        <h4 class="font-weight-bold">{{ __('Code Access') }}</h4>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group mb-3">
                                            <div class="status_code"></div>
                                            <input type="text" name="code_access" id="code_access"
                                                class="form-control" placeholder="type your code"
                                                aria-label="Example text with button addon"
                                                aria-describedby="button-addon1">
                                            <div class="input-group-prepend">
                                                <button class="theme-btn w-100"
                                                    onclick="handleCheckCodeEvent({{ $event->id }})" type="button"
                                                    id="button-addon1">
                                                    {{ __('Apply') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-12 mt-3">
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

<script>
    function handleCheckCodeEvent($id) {
        alert($id);
    }
</script>
