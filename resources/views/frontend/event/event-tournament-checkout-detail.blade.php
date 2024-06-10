@extends('frontend.layout')
@php
    $og_title = $event['title'];
    if ($event->date_type == 'multiple') {
        $event_date = eventLatestDates($event->id);
        $date = strtotime(@$event_date->start_date);
    } else {
        $date = strtotime($event->start_date);
    }
@endphp

@section('pageHeading')
    {{ $event['title'] }}
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
    <form id="eventForm"
        action="{{ route('ticket.booking.tournament', [$re_event_id, 'type' => 'guest', 'form_type' => 'tournament']) }}"
        method="POST">
        @csrf
        <input type="hidden" id="event_id" name="event_id" value="{{ $from_step_one['event_id'] }}">
        <input type="hidden" id="base_url" value="{{ url('/') }}">

        <section class="event-details-section pt-110 rpt-90 pb-90 rpb-70">
            <div class="container">
                <div class="event-details-content">
                    <div class="row">
                        <div class="col-12 col-lg-8 order-1 order-lg-0 my-1">
                            <div class="row">
                                {{-- <div class="col-12">
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
                                </div> --}}
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
                                @foreach ($orders as $val_order)
                                    @if (strtolower($val_order['category']) == 'individu' && count($val_order['ticket_detail_order']) > 0)
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
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead class="table-secondary">
                                                            <tr>
                                                                <th scope="col">No</th>
                                                                <th scope="col">Full Name</th>
                                                                <th scope="col">Gender</th>
                                                                <th scope="col">Delegation</th>
                                                                <th scope="col">Category</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($val_order['ticket_detail_order'] as $key => $val_order_ticket)
                                                                @php
                                                                    $delegation_from = '';
                                                                    switch (strtolower($val_order_ticket['delegation_type'])) {
                                                                        case 'country':
                                                                            $delegation_from = $val_order_ticket['country_name'];
                                                                            break;
                                                                        case 'province':
                                                                            $delegation_from = $val_order_ticket['province_name'];
                                                                            break;
                                                                        case 'state':
                                                                            $delegation_from = $val_order_ticket['province_name'];
                                                                            break;
                                                                        case 'city':
                                                                            $delegation_from = $val_order_ticket['city_name'];
                                                                            break;
                                                                        case 'school/universities':
                                                                            $delegation_from = $val_order_ticket['school_name'];
                                                                            break;
                                                                        case 'organization':
                                                                            $delegation_from = $val_order_ticket['organization_name'];
                                                                            break;
                                                                        default:
                                                                            $delegation_from = $val_order_ticket['club_name'];
                                                                            break;
                                                                    }
                                                                @endphp
                                                                <tr>
                                                                    <th scope="row">{{ $key + 1 }}</th>
                                                                    <td>{{ ucfirst($val_order_ticket['user_full_name']) }}
                                                                    </td>
                                                                    <td>{{ ucfirst($val_order_ticket['user_gender']) }}
                                                                    </td>
                                                                    <td>{{ !empty($delegation_from) ? $delegation_from : '-' }}
                                                                    </td>
                                                                    <td>{{ $val_order_ticket['sub_category_ticket'] }}</td>
                                                                </tr>
                                                            @endforeach

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </section>
                                    @endif
                                    @if (strtolower($val_order['category']) == 'team' && count($val_order['ticket_detail_order']) > 0)
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
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead class="table-secondary">
                                                            <tr>
                                                                <th scope="col">#</th>
                                                                <th scope="col">First</th>
                                                                <th scope="col">Last</th>
                                                                <th scope="col">Handle</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <th scope="row">1</th>
                                                                <td>Mark</td>
                                                                <td>Otto</td>
                                                                <td>@mdo</td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">2</th>
                                                                <td>Jacob</td>
                                                                <td>Thornton</td>
                                                                <td>@fat</td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">3</th>
                                                                <td>Larry</td>
                                                                <td>the Bird</td>
                                                                <td>@twitter</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </section>
                                    @endif
                                    @if (strtolower($val_order['category']) == 'mix team' && count($val_order['ticket_detail_order']) > 0)
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
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead class="table-secondary">
                                                            <tr>
                                                                <th scope="col">#</th>
                                                                <th scope="col">First</th>
                                                                <th scope="col">Last</th>
                                                                <th scope="col">Handle</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <th scope="row">1</th>
                                                                <td>Mark</td>
                                                                <td>Otto</td>
                                                                <td>@mdo</td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">2</th>
                                                                <td>Jacob</td>
                                                                <td>Thornton</td>
                                                                <td>@fat</td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">3</th>
                                                                <td>Larry</td>
                                                                <td>the Bird</td>
                                                                <td>@twitter</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </section>
                                    @endif
                                    @if (strtolower($val_order['category']) == 'official' && count($val_order['ticket_detail_order']) > 0)
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
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead class="table-secondary">
                                                            <tr>
                                                                <th scope="col">#</th>
                                                                <th scope="col">First</th>
                                                                <th scope="col">Last</th>
                                                                <th scope="col">Handle</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <th scope="row">1</th>
                                                                <td>Mark</td>
                                                                <td>Otto</td>
                                                                <td>@mdo</td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">2</th>
                                                                <td>Jacob</td>
                                                                <td>Thornton</td>
                                                                <td>@fat</td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">3</th>
                                                                <td>Larry</td>
                                                                <td>the Bird</td>
                                                                <td>@twitter</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </section>
                                    @endif
                                @endforeach

                            </div>
                        </div>

                        <div class="col-12 col-lg-4 order-0 order-lg-1 my-1">
                            <input type="hidden" name="event" value="{{ $event }}">
                            <input type="hidden" name="request_ticket_infos" value="{{ $request_ticket_infos }}">
                            <input type="hidden" name="request_orders" value="{{ $request_orders }}">
                            <input type="hidden" name="language_id" value="{{ $language_id }}">
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
                                    <h4 class="font-weight-bold">{{ __('Order Summary') }}</h4>
                                </div>
                                <div class="col-12">
                                    <p class="font-weight-bold">Tickets Info</p>
                                </div>
                                @php
                                    $fee_sub_total = 0;
                                    $total_tickets_quantity = 0;
                                @endphp
                                @foreach ($ticket_infos as $val_ticket_infos)
                                    @if ($val_ticket_infos['quantity'] > 0)
                                        @php
                                            $fee_sub_total = $fee_sub_total + $val_ticket_infos['price'];
                                            $total_tickets_quantity = $total_tickets_quantity + $val_ticket_infos['quantity'];
                                        @endphp
                                        <div class="col-12 d-flex justify-content-between flex-wrap">
                                            <p class="font-weight-medium mb-0 mx-1">
                                                {{ ucfirst($val_ticket_infos['title']) }}
                                            </p>
                                            <p class="font-weight-medium mb-0 mx-1">
                                                {{ $val_ticket_infos['quantity'] }}x
                                            </p>
                                            <p class="font-weight-medium mb-0 mx-1">
                                                Rp. {{ $val_ticket_infos['price'] }}
                                            </p>
                                        </div>
                                        <div class="col-12 mt-0">
                                            <hr style="width:100%;text-align:left;margin-left:0">
                                        </div>
                                    @endif
                                @endforeach
                                <div class="col-12 d-flex justify-content-between mt-2">
                                    <p class="font-weight-medium mb-0">
                                        {{ __('Total Tickets') }}
                                    </p>
                                    <p class="font-weight-medium mb-0">
                                        {{ $total_tickets_quantity }}
                                    </p>
                                </div>
                                <div class="col-12 d-flex justify-content-between mt-2">
                                    <p class="font-weight-medium mb-0">
                                        {{ __('Sub Total') }}
                                    </p>
                                    <p class="font-weight-medium mb-0">
                                        Rp. {{ $fee_sub_total }}
                                    </p>
                                </div>
                                <div class="col-12 d-flex justify-content-between mt-2">
                                    <p class="font-weight-medium mb-0">
                                        {{ __('Handling Fee') }} ({{ $ppn_value }}%)
                                    </p>
                                    <p class="font-weight-medium mb-0">
                                        Rp. {{ $ppn_total = ($fee_sub_total * $ppn_value) / 100 }}
                                    </p>
                                </div>
                                <div class="col-12 mt-0 mb-0">
                                    <hr style="width:100%;text-align:left;margin-left:0">
                                </div>
                                <div class="col-12 d-flex justify-content-between mt-1">
                                    <p class="font-weight-medium mb-0">
                                        {{ __('Total') }}
                                    </p>
                                    <p class="font-weight-medium mb-0">
                                        Rp. {{ $fee_sub_total + $ppn_total }}
                                    </p>
                                </div>
                                <div class="col-12 mt-3 mb-2">
                                    <h4 class="font-weight-bold">{{ __('Payment Methods') }}</h4>
                                </div>
                                <div class="col-12 mt-1">
                                    <select class="form-select" name="gateway" id="payment">
                                        <option value="xendit" selected>Xendit Payment Gateway</option>
                                    </select>
                                </div>
                                <input type="hidden" name="total" value="{{ $fee_sub_total }}">
                                <input type="hidden" name="quantity" value="{{ $total_tickets_quantity }}">

                                <div class="col-12 mt-3">
                                    <button class="theme-btn w-100" type="submit">
                                        {{ __('Proceed To Pay') }}
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
