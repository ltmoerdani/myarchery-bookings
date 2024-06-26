@extends('organizer.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Edit Ticket') }}</h4>
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
                    {{ strlen($event->title) > 35 ? mb_substr($event->title, 0, 35, 'UTF-8') . '...' : $event->title }}
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a
                    href="{{ route('organizer.event.ticket', ['language' => $defaultLang->code, 'event_id' => request()->input('event_id'), 'event_type' => request()->input('event_type')]) }}">
                    {{ __('Tickets') }}
                </a>
            </li>

            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Edit Ticket') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card-title d-inline-block">{{ __('Edit Ticket') }}</div>
                        </div>
                        <div class="col-lg-4">
                            <a class="mr-2 btn btn-success btn-sm float-right d-inline-block"
                                href="{{ route('event.details', ['slug' => eventSlug($defaultLang->id, request()->input('event_id')), 'id' => request()->input('event_id')]) }}"
                                target="_blank">
                                <span class="btn-label">
                                    <i class="fas fa-eye"></i>
                                </span>
                                {{ __('Preview') }}
                            </a>
                            <a class="btn btn-info btn-sm float-right d-inline-block mr-2"
                                href="{{ route('organizer.event.ticket', ['language' => $defaultLang->code, 'event_id' => request()->input('event_id'), 'event_type' => request()->input('event_type')]) }}">
                                <span class="btn-label">
                                    <i class="fas fa-backward"></i>
                                </span>
                                {{ __('Back') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-10 mx-auto">
                            <div class="alert alert-danger pb-1 dis-none" id="eventErrors">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <ul></ul>
                            </div>
                            <form id="eventForm"
                                action="{{ route('organizer.ticket_management.update_ticket_tournament') }}" method="POST"
                                enctype="multipart/form-data">
                                <input type="hidden" name="event_type" value="{{ request()->input('event_type') }}">
                                <input type="hidden" name="event_id" value="{{ request()->input('event_id') }}">
                                <input type="hidden" name="pricing_scheme" value="{{ $ticket->pricing_scheme }}">
                                <div class="row">
                                    @if ($ticket->pricing_scheme == 'single_price')
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="">
                                                    {{ __('Price') }}
                                                    ({{ $getCurrencyInfo->base_currency_text }}) *
                                                </label>
                                                <input type="number" name="f_price"
                                                    value="{{ empty($ticket->f_price) || $ticket->f_price < 1 ? 0 : $ticket->f_price }}"
                                                    class="form-control" placeholder="Enter Price" min="0">
                                                <div class="mt-1">
                                                    *{{ __('For Default price ticket') }}
                                                </div>
                                                <div class="mt-1">
                                                    *{{ __('Leave empty or set to 0 for free ticket') }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group">
                                                <label for="">
                                                    {{ __('Local Price') }}
                                                    ({{ $getCurrencyInfo->base_currency_text }}) *
                                                </label>
                                                <input type="number" name="f_price"
                                                    value="{{ empty($ticket->f_price) || $ticket->f_price < 1 ? 0 : $ticket->f_price }}"
                                                    class="form-control" placeholder="Enter Price" min="0">
                                                <div class="mt-1">
                                                    *{{ __('For Default price ticket') }}
                                                </div>
                                                <div class="mt-1">
                                                    *{{ __('Leave empty or set to 0 for free ticket') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group">
                                                <label for="">
                                                    {{ __('International Price') }}
                                                    ({{ $getCurrencyInfo->base_currency_text }}) *
                                                </label>
                                                <input type="number" name="f_international_price"
                                                    value="{{ empty($ticket->f_international_price) || $ticket->f_international_price < 1 ? 0 : $ticket->f_international_price }}"
                                                    class="form-control" placeholder="Enter Price" min="0">
                                                <div class="mt-1">
                                                    *{{ __('For Default price ticket') }}
                                                </div>
                                                <div class="mt-1">
                                                    *{{ __('Leave empty or set to 0 for free ticket') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- early bird settings --}}
                                    <div class="col-12 " id="early_bird_discount_free">
                                        <div class="form-group mt-1">
                                            <label for="">{{ __('Early Bird Discount') . '*' }}</label>
                                            <div class="selectgroup w-100">
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="early_bird_discount" value="disable"
                                                        class="selectgroup-input"
                                                        {{ $ticket->early_bird_discount == 'disable' ? 'checked' : '' }}>
                                                    <span class="selectgroup-button">{{ __('Disable') }}</span>
                                                </label>

                                                <label class="selectgroup-item">
                                                    <input type="radio" name="early_bird_discount" value="enable"
                                                        class="selectgroup-input"
                                                        {{ $ticket->early_bird_discount == 'enable' ? 'checked' : '' }}>
                                                    <span class="selectgroup-button">{{ __('Enable') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 {{ $ticket->early_bird_discount == 'enable' ? '' : 'd-none' }}"
                                        id="early_bird_dicount">
                                        @if ($ticket->pricing_scheme == 'single_price')
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Discount') }}</label>
                                                        <select name="early_bird_discount_local_type"
                                                            class="form-control">
                                                            <option disabled>{{ __('Select Discount Type') }}</option>
                                                            <option value="fixed"
                                                                {{ $ticket->early_bird_discount_type == 'fixed' ? 'selected' : '' }}>
                                                                {{ __('Fixed') }}
                                                            </option>
                                                            <option value="percentage"
                                                                {{ $ticket->early_bird_discount_type == 'percentage' ? 'selected' : '' }}>
                                                                {{ __('Percentage') }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Amount') }}</label>
                                                        <input type="number" name="early_bird_discount_amount"
                                                            value="{{ $ticket->early_bird_discount_amount }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Discount Start Date') }}</label>
                                                        <input type="date" name="early_bird_discount_date"
                                                            value="{{ $ticket->early_bird_discount_date }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Discount Start Time') }}</label>
                                                        <input type="time" name="early_bird_discount_time"
                                                            value="{{ $ticket->early_bird_discount_time }}"class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Discount End Date') }}</label>
                                                        <input type="date" name="early_bird_discount_end_date"
                                                            value="{{ $ticket->early_bird_discount_end_date }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Discount End Time') }}</label>
                                                        <input type="time" name="early_bird_discount_end_time"
                                                            value="{{ $ticket->early_bird_discount_end_time }}"class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <p class="font-weight-bold my-0">
                                                            *{{ __('For Early Bird Price Local') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Discount') }}</label>
                                                        <select name="early_bird_discount_local_type"
                                                            class="form-control">
                                                            <option disabled>{{ __('Select Discount Type') }}</option>
                                                            <option value="fixed"
                                                                {{ $ticket->early_bird_discount_type == 'fixed' ? 'selected' : '' }}>
                                                                {{ __('Fixed') }}
                                                            </option>
                                                            <option value="percentage"
                                                                {{ $ticket->early_bird_discount_type == 'percentage' ? 'selected' : '' }}>
                                                                {{ __('Percentage') }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Amount') }}</label>
                                                        <input type="number" name="early_bird_discount_amount"
                                                            value="{{ $ticket->early_bird_discount_amount }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Discount Start Date') }}</label>
                                                        <input type="date" name="early_bird_discount_date"
                                                            value="{{ $ticket->early_bird_discount_date }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Discount Start Time') }}</label>
                                                        <input type="time" name="early_bird_discount_time"
                                                            value="{{ $ticket->early_bird_discount_time }}"class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Discount End Date') }}</label>
                                                        <input type="date" name="early_bird_discount_end_date"
                                                            value="{{ $ticket->early_bird_discount_end_date }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Discount End Time') }}</label>
                                                        <input type="time" name="early_bird_discount_end_time"
                                                            value="{{ $ticket->early_bird_discount_end_time }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <p class="font-weight-bold my-0">
                                                            *{{ __('For Early Bird Price International') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Discount') }}</label>
                                                        <select name="early_bird_discount_international_type"
                                                            class="form-control">
                                                            <option disabled>{{ __('Select Discount Type') }}</option>
                                                            <option value="fixed"
                                                                {{ $ticket->early_bird_discount_type == 'fixed' ? 'selected' : '' }}>
                                                                {{ __('Fixed') }}
                                                            </option>
                                                            <option value="percentage"
                                                                {{ $ticket->early_bird_discount_type == 'percentage' ? 'selected' : '' }}>
                                                                {{ __('Percentage') }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Amount') }}</label>
                                                        <input type="number"
                                                            name="early_bird_discount_amount_international"
                                                            value="{{ $ticket->early_bird_discount_amount_international }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Discount Start Date') }}</label>
                                                        <input type="date"
                                                            name="early_bird_discount_international_date"
                                                            value="{{ $ticket->early_bird_discount_international_date }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Discount Start Time') }}</label>
                                                        <input type="time"
                                                            name="early_bird_discount_international_time"
                                                            value="{{ $ticket->early_bird_discount_international_time }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Discount End Date') }}</label>
                                                        <input type="date"
                                                            name="early_bird_discount_international_end_date"
                                                            value="{{ $ticket->early_bird_discount_international_end_date }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Discount End Time') }}</label>
                                                        <input type="time"
                                                            name="early_bird_discount_international_end_time"
                                                            value="{{ $ticket->early_bird_discount_international_end_time }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    {{-- end early bird settings --}}

                                    {{-- late price settings --}}
                                    <div class="col-12 " id="late_price_selector">
                                        <div class="form-group mt-1">
                                            <label for="">{{ __('Late Price') . '*' }}</label>
                                            <div class="selectgroup w-100">
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="late_price_discount" value="disable"
                                                        class="selectgroup-input"
                                                        {{ $ticket->late_price_discount == 'disable' ? 'checked' : '' }}>
                                                    <span class="selectgroup-button">{{ __('Disable') }}</span>
                                                </label>

                                                <label class="selectgroup-item">
                                                    <input type="radio" name="late_price_discount" value="enable"
                                                        class="selectgroup-input"
                                                        {{ $ticket->late_price_discount == 'enable' ? 'checked' : '' }}>
                                                    <span class="selectgroup-button">{{ __('Enable') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 {{ $ticket->late_price_discount == 'enable' ? '' : 'd-none' }}"
                                        id="late_price_dicount">
                                        @if ($ticket->pricing_scheme == 'single_price')
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Markup') }}</label>
                                                        <select name="late_price_discount_type" class="form-control">
                                                            <option disabled>{{ __('Select Discount Type') }}</option>
                                                            <option value="fixed"
                                                                {{ $ticket->late_price_discount_type == 'fixed' ? 'selected' : '' }}>
                                                                {{ __('Fixed') }}
                                                            </option>
                                                            <option value="percentage"
                                                                {{ $ticket->late_price_discount_type == 'percentage' ? 'selected' : '' }}>
                                                                {{ __('Percentage') }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Amount') }}</label>
                                                        <input type="number" name="late_price_discount_amount"
                                                            value="{{ $ticket->late_price_discount_amount }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Markup Start Date') }}</label>
                                                        <input type="date" name="late_price_discount_date"
                                                            value="{{ $ticket->late_price_discount_date }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Markup Start Time') }}</label>
                                                        <input type="time" name="late_price_discount_time"
                                                            value="{{ $ticket->late_price_discount_time }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Markup End Date') }}</label>
                                                        <input type="date" name="late_price_discount_end_date"
                                                            value="{{ $ticket->late_price_discount_end_date }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Markup End Time') }}</label>
                                                        <input type="time" name="late_price_discount_end_time"
                                                            value="{{ $ticket->late_price_discount_end_time }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <p class="font-weight-bold my-0">
                                                            *{{ __('For Late Price Local') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Markup') }}</label>
                                                        <select name="late_price_discount_type" class="form-control">
                                                            <option disabled>{{ __('Select Discount Type') }}</option>
                                                            <option value="fixed"
                                                                {{ $ticket->late_price_discount_type == 'fixed' ? 'selected' : '' }}>
                                                                {{ __('Fixed') }}
                                                            </option>
                                                            <option value="percentage"
                                                                {{ $ticket->late_price_discount_type == 'percentage' ? 'selected' : '' }}>
                                                                {{ __('Percentage') }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Amount') }}</label>
                                                        <input type="number" name="late_price_discount_amount"
                                                            value="{{ $ticket->late_price_discount_amount }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Markup Start Date') }}</label>
                                                        <input type="date" name="late_price_discount_date"
                                                            value="{{ $ticket->late_price_discount_date }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Markup Start Time') }}</label>
                                                        <input type="time" name="late_price_discount_time"
                                                            value="{{ $ticket->late_price_discount_time }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Markup End Date') }}</label>
                                                        <input type="date" name="late_price_discount_end_date"
                                                            value="{{ $ticket->late_price_discount_end_date }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Markup End Time') }}</label>
                                                        <input type="time" name="late_price_discount_end_time"
                                                            value="{{ $ticket->late_price_discount_end_time }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <p class="font-weight-bold my-0">
                                                            *{{ __('For Late Price International') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Markup') }}</label>
                                                        <select name="late_price_discount_international_type"
                                                            class="form-control">
                                                            <option disabled>{{ __('Select Discount Type') }}</option>
                                                            <option value="fixed"
                                                                {{ $ticket->late_price_discount_international_type == 'fixed' ? 'selected' : '' }}>
                                                                {{ __('Fixed') }}
                                                            </option>
                                                            <option value="percentage"
                                                                {{ $ticket->late_price_discount_international_type == 'percentage' ? 'selected' : '' }}>
                                                                {{ __('Percentage') }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Amount') }}</label>
                                                        <input type="number"
                                                            name="late_price_discount_amount_international"
                                                            value="{{ $ticket->late_price_discount_amount_international }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Markup Start Date') }}</label>
                                                        <input type="date"
                                                            name="late_price_discount_international_date"
                                                            value="{{ $ticket->late_price_discount_international_date }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Markup Start Time') }}</label>
                                                        <input type="time"
                                                            name="late_price_discount_international_time"
                                                            value="{{ $ticket->late_price_discount_international_time }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Markup End Date') }}</label>
                                                        <input type="date"
                                                            name="late_price_discount_international_end_date"
                                                            value="{{ $ticket->late_price_discount_international_end_date }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Markup End Time') }}</label>
                                                        <input type="time"
                                                            name="late_price_discount_international_end_time"
                                                            value="{{ $ticket->late_price_discount_international_end_time }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    {{-- end late price settings --}}

                                    {{-- set quota --}}
                                    <div class="col-12 mb-0">
                                        <div class="form-group mt-1 mb-0">
                                            <label class="mb-0">{{ __('Set Quota') . '*' }}</label>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-0">
                                        <div class="form-group mt-0">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">{{ __('Category') }}</th>
                                                            <th class="text-center">{{ __('Price') }}</th>
                                                            <th class="text-center">{{ __('Quota') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($list_ticket as $valTicket)
                                                            <tr>
                                                                <td>
                                                                    <input type="hidden" value="{{ $valTicket->id }}"
                                                                        name="ticket_id[]">
                                                                    <div class="d-flex flex-column gap-1">
                                                                        @if (!empty($valTicket->ticket_content))
                                                                            @foreach ($valTicket->ticket_content as $key_ticket_content => $ticket_content)
                                                                                <span
                                                                                    class="font-weight-bold {{ $key_ticket_content > 0 ? 'mt-1' : '' }}">
                                                                                    {{ $ticket_content->title }}
                                                                                    <span class="text-info">
                                                                                        ({{ $ticket_content->language_code }})
                                                                                    </span>
                                                                                </span>
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="form-check mt-1">
                                                                        <label class="form-check-label">
                                                                            <input class="form-check-input"
                                                                                id="default_price_status_{{ $valTicket->id }}"
                                                                                name="use_default_price[{{ $valTicket->id }}]"
                                                                                type="checkbox"
                                                                                {{ $valTicket->use_default_price ? 'Checked' : '' }}
                                                                                onchange="changeStatusPrice({{ $valTicket->id }})">
                                                                            <span class="form-check-sign">
                                                                                {{ __('Default') }}
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                    <div class="{{ $valTicket->use_default_price ? 'd-none' : '' }}"
                                                                        id="form_variant_price_{{ $valTicket->id }}">
                                                                        @if ($ticket->pricing_scheme == 'single_price')
                                                                            <div class="form-group mt-1">
                                                                                <label>{{ __('Ticket Price') . '(' . $getCurrencyInfo->base_currency_text . ')' }}</label>
                                                                                <input type="number"
                                                                                    name="ticket_price_local[{{ $valTicket->id }}]"
                                                                                    id="ticket_price_local_{{ $valTicket->id }}"
                                                                                    class="form-control" min="0"
                                                                                    value="{{ $valTicket->price }}">
                                                                                <span class="mt-1">
                                                                                    *{{ __('Leave empty or set to 0 for free ticket') }}
                                                                                </span>
                                                                            </div>
                                                                        @else
                                                                            <div class="form-group mt-1">
                                                                                <label>{{ __('Local Price') . '(' . $getCurrencyInfo->base_currency_text . ')' }}</label>
                                                                                <input type="number"
                                                                                    name="ticket_price_local[{{ $valTicket->id }}]"
                                                                                    id="ticket_price_local_{{ $valTicket->id }}"
                                                                                    class="form-control" min="0"
                                                                                    value="{{ $valTicket->price }}">
                                                                                <span class="mt-1">
                                                                                    *{{ __('Leave empty or set to 0 for free ticket') }}
                                                                                </span>
                                                                            </div>
                                                                            <div class="form-group mt-1">
                                                                                <label>{{ __('International Price') . '(' . $getCurrencyInfo->base_currency_text . ')' }}</label>
                                                                                <input type="number"
                                                                                    name="ticket_price_international[{{ $valTicket->id }}]"
                                                                                    id="ticket_price_international_{{ $valTicket->id }}"
                                                                                    class="form-control" min="0"
                                                                                    value="{{ $valTicket->international_price }}">
                                                                                <span class="mt-1">
                                                                                    *{{ __('Leave empty or set to 0 for free ticket') }}
                                                                                </span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input type="number" min="1"
                                                                            value="{{ $valTicket->ticket_available }}"
                                                                            class="form-control"
                                                                            name="ticket_available[{{ $valTicket->id }}]">
                                                                        <span class="my-1 py-0">
                                                                            *{{ __('If you want a category that cannot be ordered, please enter 0') }}
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- end set quota --}}

                                    {{-- set limit each customer order --}}
                                    <div class="col-12 " id="status_limit_each_customer">
                                        <div class="form-group mt-1">
                                            <label
                                                for="">{{ __('Maximum Number Of Tickets For Each Customer') . '*' }}</label>
                                            <div class="selectgroup w-100">
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="max_ticket_buy_type" value="unlimited"
                                                        class="selectgroup-input"
                                                        {{ $ticket->max_ticket_buy_type == 'unlimited' ? 'checked' : '' }}>
                                                    <span class="selectgroup-button">{{ __('Unlimited') }}</span>
                                                </label>
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="max_ticket_buy_type" value="limited"
                                                        class="selectgroup-input"
                                                        {{ $ticket->max_ticket_buy_type == 'limited' ? 'checked' : '' }}>
                                                    <span class="selectgroup-button">{{ __('Limited') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 {{ $ticket->max_ticket_buy_type == 'limited' ? '' : 'd-none' }}"
                                        id="form_input_each_customer">
                                        <div class="form-group">
                                            <input type="number" min="1" class="form-control"
                                                name="max_buy_ticket" value="{{ $ticket->max_buy_ticket }}">
                                            <span class="mt-1">
                                                *{{ __('Minimum Number Of Tickets For Each Customer At Least 1') }}
                                            </span>
                                        </div>
                                    </div>
                                    {{-- end set limit each customer order --}}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" id="EventSubmit" class="btn btn-success">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    @php
        $languages = App\Models\Language::get();
        $names = '';
        foreach ($languages as $language) {
            $varitaion_name = $language->code . '_variation_name[]';
            $names .= "<div class='form-group'><label for=''>Variation Name *($language->name)</label><input type='text' name='$varitaion_name' class='form-control'></div>";
        }
    @endphp
    <script>
        let names = "{!! $names !!}";
        let BaseCTxt = "{{ $getCurrencyInfo->base_currency_text }}";
        var guest_checkout_status = "{{ $websiteInfo->event_guest_checkout_status }}";
    </script>
    <script type="text/javascript" src="{{ asset('assets/admin/js/admin-partial.js?' . time()) }}"></script>
@endsection

@section('variables')
    <script>
        "use strict";
        var removeUrl = "{{ route('admin.event.imagermv') }}";
    </script>
@endsection
