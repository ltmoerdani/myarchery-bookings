@extends('organizer.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Tickets') }}</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('organizer.dashboard') }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Events Management') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a
                    href="{{ route('organizer.event_management.event', ['language' => $defaultLang->code]) }}">{{ __('All Events') }}</a>
            </li>

            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">
                    {{ strlen($information['event']['title']) > 35 ? mb_substr($information['event']['title'], 0, 35, 'UTF-8') . '...' : $information['event']['title'] }}
                </a>
            </li>

            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a
                    href="{{ route('organizer.event.ticket', ['language' => $defaultLang->code, 'event_id' => request()->input('event_id'), 'event_type' => request()->input('event_type')]) }}">{{ __('Tickets') }}</a>
            </li>

        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="card-title d-inline-block">
                                {{ __('Tickets') }}
                            </div>
                        </div>

                        <div class="col-lg-4 offset-lg-1 mt-2 mt-lg-0">

                            <a href="{{ route('organizer.event_management.event', ['language' => $defaultLang->code, 'event_type' => request()->input('event_type')]) }}"
                                class="btn btn-info  btn-sm float-right"><i class="fas fa-backward"></i>
                                {{ __('Back') }}</a>

                            <a class="mr-2 btn btn-success btn-sm float-right d-inline-block"
                                href="{{ route('event.details', ['slug' => eventSlug($defaultLang->id, request()->input('event_id')), 'id' => request()->input('event_id')]) }}"
                                target="_blank">
                                <span class="btn-label">
                                    <i class="fas fa-eye"></i>
                                </span>
                                {{ __('Preview') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">

                            @if (session()->has('course_status_warning'))
                                <div class="alert alert-warning">
                                    <p class="text-dark mb-0">{{ session()->get('course_status_warning') }}</p>
                                </div>
                            @endif

                            @if (count($information['tickets']) == 0)
                                <h3 class="text-center mt-2">{{ __('NO TICKET FOUND  ') . '!' }}</h3>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped mt-3" id="basic-datatables">
                                        <thead>
                                            <tr>
                                                <th scope="col">{{ __('Title') }}</th>
                                                <th scope="col">{{ __('Tickets Available') }}</th>
                                                <th scope="col">{{ __('Local Price*') }}</th>
                                                <th scope="col">{{ __('International Price*') }}</th>
                                                <th scope="col">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($information['tickets'] as $ticket)
                                                <tr>
                                                    <td width="30%">
                                                        {{ $ticket->title }}
                                                    </td>
                                                    <td width="20%">
                                                        @if ($ticket->pricing_type == 'variation')
                                                            @php
                                                                $variation = json_decode($ticket->variations, true);
                                                            @endphp
                                                            @foreach ($variation as $v)
                                                                @if ($v['ticket_available_type'] == 'unlimited')
                                                                    {{ __('Unlimited') }}
                                                                @else
                                                                    {{ $v['ticket_available'] }}
                                                                @endif
                                                                @if (!$loop->last)
                                                                    ,
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            @if ($ticket->ticket_available_type == 'unlimited')
                                                                <span
                                                                    class="badge badge-info">{{ $ticket->ticket_available_type }}</span>
                                                            @else
                                                                {{ $ticket->ticket_available }}
                                                            @endif
                                                        @endif

                                                    </td>
                                                    <td>
                                                        {{ $ticket->local_price }}
                                                    </td>
                                                    <td>
                                                        {{ $ticket->international_price }}
                                                    </td>
                                                    {{-- <td>
                                                        @if ($ticket->pricing_type == 'normal')
                                                            @if ($ticket->early_bird_discount == 'enable')
                                                                @php
                                                                    $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                                                                @endphp

                                                                @if ($ticket->early_bird_discount_type == 'fixed' && !$discount_date->isPast())
                                                                    @php
                                                                        $calculate_price = $ticket->price - $ticket->early_bird_discount_amount;
                                                                    @endphp
                                                                    {{ symbolPrice($calculate_price) }}
                                                                    <del>
                                                                        {{ symbolPrice($ticket->price) }}
                                                                    </del>
                                                                @elseif ($ticket->early_bird_discount_type == 'percentage' && !$discount_date->isPast())
                                                                    @php
                                                                        $c_price = ($ticket->price * $ticket->early_bird_discount_amount) / 100;
                                                                        $calculate_price = $ticket->price - $c_price;
                                                                    @endphp
                                                                    {{ symbolPrice($calculate_price) }}
                                                                    <del>
                                                                        {{ symbolPrice($ticket->price) }}
                                                                    </del>
                                                                @else
                                                                    @php
                                                                        $calculate_price = $ticket->price;
                                                                    @endphp
                                                                    {{ symbolPrice($calculate_price) }}
                                                                @endif
                                                            @else
                                                                {{ symbolPrice($ticket->price) }}
                                                            @endif
                                                        @elseif ($ticket->pricing_type == 'variation')
                                                            @php
                                                                $variation = json_decode($ticket->variations, true);
                                                            @endphp
                                                            @foreach ($variation as $v)
                                                                @if ($ticket->early_bird_discount == 'enable')
                                                                    @php
                                                                        $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                                                                    @endphp

                                                                    @if ($ticket->early_bird_discount_type == 'fixed' && !$discount_date->isPast())
                                                                        @php
                                                                            $calculate_price = $v['price'] - $ticket->early_bird_discount_amount;
                                                                        @endphp
                                                                        {{ symbolPrice($calculate_price) }}
                                                                        <del>

                                                                            {{ symbolPrice($v['price']) }}
                                                                        </del>
                                                                    @elseif ($ticket->early_bird_discount_type == 'percentage' && !$discount_date->isPast())
                                                                        @php
                                                                            $c_price = ($v['price'] * $ticket->early_bird_discount_amount) / 100;
                                                                            $calculate_price = $v['price'] - $c_price;
                                                                        @endphp
                                                                        {{ symbolPrice($calculate_price) }}

                                                                        <del>
                                                                            {{ symbolPrice($v['price']) }}
                                                                        </del>
                                                                    @else
                                                                        @php
                                                                            $calculate_price = $v['price'];
                                                                        @endphp
                                                                        {{ symbolPrice($calculate_price) }}
                                                                    @endif
                                                                    @if (!$loop->last)
                                                                        ,
                                                                    @endif
                                                                @else
                                                                    {{ symbolPrice($v['price']) }}
                                                                    @if (!$loop->last)
                                                                        ,
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                        @elseif ($ticket->pricing_type == 'free')
                                                            <span class="badge badge-info">{{ __('Free') }}</span>
                                                        @endif

                                                    </td>
                                                    <td>
                                                        @if ($ticket->pricing_type == 'normal')
                                                            @if ($ticket->early_bird_discount == 'enable')
                                                                @php
                                                                    $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                                                                @endphp

                                                                @if ($ticket->early_bird_discount_type == 'fixed' && !$discount_date->isPast())
                                                                    @php
                                                                        $calculate_price = $ticket->international_price - $ticket->early_bird_discount_amount;
                                                                    @endphp
                                                                    {{ symbolPrice($calculate_price) }}
                                                                    <del>
                                                                        {{ symbolPrice($ticket->international_price) }}
                                                                    </del>
                                                                @elseif ($ticket->early_bird_discount_type == 'percentage' && !$discount_date->isPast())
                                                                    @php
                                                                        $c_price = ($ticket->international_price * $ticket->early_bird_discount_amount) / 100;
                                                                        $calculate_price = $ticket->international_price - $c_price;
                                                                    @endphp
                                                                    {{ symbolPrice($calculate_price) }}
                                                                    <del>
                                                                        {{ symbolPrice($ticket->international_price) }}
                                                                    </del>
                                                                @else
                                                                    @php
                                                                        $calculate_price = $ticket->international_price;
                                                                    @endphp
                                                                    {{ symbolPrice($calculate_price) }}
                                                                @endif
                                                            @else
                                                                {{ symbolPrice($ticket->international_price) }}
                                                            @endif
                                                        @endif
                                                    </td> --}}
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-secondary dropdown-toggle btn-sm"
                                                                type="button" id="dropdownMenuButton"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                {{ __('Select') }}
                                                            </button>

                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                <a href="{{ route('organizer.event.edit.ticket-tournament', ['language' => $defaultLang->code, 'event_id' => request()->input('event_id'), 'event_type' => request()->input('event_type'), 'title' => $ticket->title]) }}"
                                                                    class="dropdown-item">
                                                                    {{ __('Edit') }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                    *) Price per ticket
                </div>

                <div class="card-footer"></div>
            </div>
        </div>
    </div>
@endsection
