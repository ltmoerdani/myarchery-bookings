@extends('frontend.layout')
@php
    print_r($organizer->email);
    $og_title = $from_step_one['event_title'];
@endphp

@section('pageHeading')
    {{ $from_step_one['event_title'] }}
@endsection

@section('meta-keywords', "order $og_title")
@section('meta-description', "processing order $og_title")
@section('og-title', "$og_title")


@section('content')
    {{-- @php
        print_r($from_step_one);
    @endphp --}}
    <section class="event-details-section pt-110 rpt-90 pb-90 rpb-70">
        <div class="container">
            <div class="event-details-content">
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <div class="row">
                            <div class="col-12">
                                <h4 style="font-weight: bold">
                                    Order Details
                                </h4>
                                <small>
                                    *These contact details will be used for sending e-tickets and refund purposes.
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">syuyu</div>
                </div>
            </div>
        </div>
    </section>
@endsection
