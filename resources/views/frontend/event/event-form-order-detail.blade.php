@extends('frontend.layout')

@section('script')
    <script src="{{ asset('assets/admin/js/helpers/select2-helpers.js?' . time()) }}"></script>
    <script src="{{ asset('assets/admin/js/helpers/global-helpers.js?' . time()) }}"></script>
@endsection

@php
    $og_title = $from_info_event['event_title'];
    if ($event->date_type == 'multiple') {
        $event_date = eventLatestDates($event->id);
        $date = strtotime(@$event_date->start_date);
    } else {
        $date = strtotime($event->start_date);
    }
@endphp

@section('pageHeading')
    {{ $from_info_event['event_title'] }}
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
            min-width: 100% !important;
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
    <form id="bookingForm" action="{{ route('process_to_detail_coheckout_tournament') }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="event_id" name="event_id" value="{{ $from_info_event['event_id'] }}">

        <section class="event-details-section pt-110 rpt-90 pb-90 rpb-70">
            <div class="container">
                <div class="event-details-content">
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-danger pb-1 dis-none" id="eventErrors">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <ul></ul>
                            </div>
                        </div>
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
                                <section id="individu_section" class="d-none">
                                    <div class="col-12 mt-3">
                                        <h4 style="font-weight: bold">
                                            {{ __('Category Order Individu') }}
                                        </h4>
                                        <small>
                                            {{ __('Description Category Order Individu') }}
                                        </small>
                                    </div>
                                    <div class="col-12 mt-3" id="form_individu"></div>
                                </section>
                                <section id="team_section" class="d-none">
                                    <div class="col-12 mt-3">
                                        <h4 style="font-weight: bold">
                                            {{ __('Category Order Team') }}
                                        </h4>
                                        <small>
                                            {{ __('Description Category Order Team') }}
                                        </small>
                                    </div>
                                    <div class="col-12 mt-3" id="form_team"></div>
                                </section>
                                <section id="mix_team_section" class="d-none">
                                    <div class="col-12 mt-3">
                                        <h4 style="font-weight: bold">
                                            {{ __('Category Order Mix Team') }}
                                        </h4>
                                        <small>
                                            {{ __('Description Category Order Mix Team') }}
                                        </small>
                                    </div>
                                    <div class="col-12 mt-3" id="form_mix_team"></div>
                                </section>
                                <section id="official_section" class="d-none">
                                    <div class="col-12 mt-3">
                                        <h4 style="font-weight: bold">
                                            {{ __('Category Order Official') }}
                                        </h4>
                                        <small>
                                            {{ __('Description Category Order Official') }}
                                        </small>
                                    </div>
                                    <div class="col-12 mt-3" id="form_official"></div>
                                </section>
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
                                <section class="list-category-tickets col-12"></section>
                                <div class="col-12 d-flex justify-content-between">
                                    <p class="font-weight-medium mb-0">
                                        Total Tickets
                                    </p>
                                    <p class="font-weight-medium mb-0 total-tickets">
                                        0
                                    </p>
                                </div>
                                @if ($event->is_code_access)
                                    <div class="col-12 mt-3 mb-2">
                                        <h4 class="font-weight-bold">{{ __('Code Access') }}</h4>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group mb-3">
                                            <div class="status_code"></div>
                                            <input type="text" name="code_access" id="code_access" class="form-control"
                                                placeholder="{{ __('Type your code access') }}"
                                                aria-label="Example text with button addon"
                                                aria-describedby="button-addon1">
                                        </div>
                                    </div>
                                @endif
                                <div class="col-12 mt-3">
                                    <button class="theme-btn w-100" type="button" id="ToDetailCheckout">
                                        {{ __('Continue') }}
                                    </button>
                                </div>
                                <div class="col-12 mt-3">
                                    <a onClick="handlerBack('{{ $event['slug'] }}','{{ $from_info_event['event_id'] }}')"
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
    <script>
        const base_url = "{{ url('/') }}";
        const url_detail_order = "{{ route('detail_order_event_tournament') }}";
        const checkoutID = "{{ $checkoutID }}";
        const titleIndividuAccordion = "{{ __('Collapse Name Form Individu Category') }}";
        const titleTeamAccordion = "{{ __('Collapse Name Form Team Category') }}";
        const titleMixTeamAccordion = "{{ __('Collapse Name Form Mix Team Category') }}";
        const titleOfficialAccordion = "{{ __('Collapse Name Form Official Category') }}";
        const fullNameLabel = "{{ __('Full Name') }}";
        const fullNamePlaceholder = "{{ __('Enter Your Full Name') }}";
        const genderLabel = "{{ __('Gender') }}";
        const genderMaleLang = "{{ __('Male') }}";
        const genderFemaleLang = "{{ __('Female') }}";
        const birthDateLabel = "{{ __('Birth Date') }}";
        const birthDatePlaceholder = "{{ __('Select Date') }}";
        const countryProfileLabel = "{{ __('Country') }}";
        const countryProfileDefaultOption = "{{ __('Select Country') }}";
        const cityDistrictProfileLabel = "{{ __('City/District') }}";
        const cityDistrictProfileDefaultOption = " {{ __('Select City/District') }}";
        const labelDelegationType = "{{ __('Delegation Type') }}";
        const labelTeamName = "{{ __('Label Input Team Name') }}";
        const placeholderProvince = "{{ __('Placeholder Option Select Province') }}";
        const placeholderTeamName = "{{ __('Placeholder Team Name') }}";
        const placeholderClub = "{{ __('Placeholder Option Select Club') }}";
        const placeholderOrganization = "{{ __('Placeholder Option Select Organization') }}";
        const placeholderSchool = "{{ __('Enter Your School/University') }}";
        const labelDelegationClub = "{{ __('Label Delegation Club') }}";
        const labelDelegationSchool = "{{ __('Label Delegation School') }}";
        const labelDelegationOrganization = "{{ __('Label Delegation Organization') }}";
        const labelDelegationCountry = "{{ __('Label Delegation Country') }}";
        const labelDelegationProvince = "{{ __('Label Delegation Province') }}";
        const labelDelegationCity = "{{ __('Label Delegation City') }}";
        const labelCategory = "{{ __('Category') }}";
        const placeholderCategory = "{{ __('Select Category') }}";
        const alertOverOrderQuotaTicket = "{{ __('ALERT OVER BID TICKET') }}";
    </script>
    <script src="{{ asset('assets/front/js/order-detail.js?' . rand()) }}"></script>
    {{-- <script src="{{ asset('assets/front/js/order-detail.min.js') }}"></script> --}}
@endsection
