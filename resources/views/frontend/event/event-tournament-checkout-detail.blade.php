@extends('frontend.layout')
@php
    $og_title = $event['title'];
    if ($event['date_type'] == 'multiple') {
        $event_date = eventLatestDates($event['id']);
        $date = strtotime(@$event_date['start_date']);
    } else {
        $date = strtotime($event['start_date']);
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
    <form id="eventForm" method="POST"
        action="{{ route('ticket.booking.tournament', [$event['id'], 'type' => 'guest', 'form_type' => 'tournament']) }}">
        @csrf
        <input type="hidden" id="event_id" name="event_id" value="{{ $event['id'] }}">
        <input type="hidden" id="base_url" value="{{ url('/') }}">

        <section class="event-details-section pt-110 rpt-90 pb-90 rpb-70">
            <div class="container">
                <div class="event-details-content">
                    <div class="row">
                        <div class="col-12 col-lg-8 order-1 order-lg-0 my-1">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <h4 style="font-weight: bold">
                                        {{ __('Order Details') }}
                                    </h4>
                                    <small>
                                        {{ __('Description Order Detail') }}
                                    </small>
                                </div>
                                <div class="col-12 mb-3">
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
