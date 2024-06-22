@extends('backend.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Edit Ticket') }}</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('admin.dashboard') }}">
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
                    href="{{ route('admin.event_management.event', ['language' => $defaultLang->code]) }}">{{ __('All Events') }}</a>
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
                    href="{{ route('admin.event.ticket', ['language' => $defaultLang->code, 'event_id' => request()->input('event_id'), 'event_type' => request()->input('event_type')]) }}">{{ __('Tickets') }}</a>
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
                                href="{{ route('admin.event.ticket', ['language' => $defaultLang->code, 'event_id' => request()->input('event_id'), 'event_type' => request()->input('event_type')]) }}">
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
                            <form id="eventForm" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="event_type" value="{{ request()->input('event_type') }}">
                                <input type="hidden" name="event_id" value="{{ request()->input('event_id') }}">
                                <div class="row">
                                    @if ($ticket->pricing_scheme == 'single_price')
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="">
                                                    {{ __('Price') }}
                                                    ({{ $getCurrencyInfo->base_currency_text }}) *
                                                </label>
                                                <input type="number" name="price"
                                                    value="{{ empty($ticket->f_price) || $ticket->f_price < 1 ? 0 : $ticket->f_price }}"
                                                    class="form-control" placeholder="Enter Price" min="0">
                                                <div class="mt-1">
                                                    *For Default price ticket
                                                </div>
                                                <div class="mt-1">
                                                    *Leave empty for free ticket
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="">
                                                    {{ __('Local Price') }}
                                                    ({{ $getCurrencyInfo->base_currency_text }}) *
                                                </label>
                                                <input type="number" name="price"
                                                    value="{{ empty($ticket->f_price) || $ticket->f_price < 1 ? 0 : $ticket->f_price }}"
                                                    class="form-control" placeholder="Enter Price" min="0">
                                                <div class="mt-1">
                                                    *For Default price ticket
                                                </div>
                                                <div class="mt-1">
                                                    *Leave empty for free ticket
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="">
                                                    {{ __('International Price') }}
                                                    ({{ $getCurrencyInfo->base_currency_text }}) *
                                                </label>
                                                <input type="number" name="price"
                                                    value="{{ empty($ticket->f_international_price) || $ticket->f_international_price < 1 ? 0 : $ticket->f_international_price }}"
                                                    class="form-control" placeholder="Enter Price" min="0">
                                                <div class="mt-1">
                                                    *For Default price ticket
                                                </div>
                                                <div class="mt-1">
                                                    *Leave empty for free ticket
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- early bird settings --}}
                                    <div class="col-lg-12 " id="early_bird_discount_free">
                                        <div class="form-group mt-1">
                                            <label for="">{{ __('Early Bird Discount') . '*' }}</label>
                                            <div class="selectgroup w-100">
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="early_bird_discount_type" value="disable"
                                                        class="selectgroup-input"
                                                        {{ $ticket->early_bird_discount == 'disable' ? 'checked' : '' }}>
                                                    <span class="selectgroup-button">{{ __('Disable') }}</span>
                                                </label>

                                                <label class="selectgroup-item">
                                                    <input type="radio" name="early_bird_discount_type" value="enable"
                                                        class="selectgroup-input"
                                                        {{ $ticket->early_bird_discount == 'enable' ? 'checked' : '' }}>
                                                    <span class="selectgroup-button">{{ __('Enable') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 {{ $ticket->early_bird_discount == 'enable' ? '' : 'd-none' }}"
                                        id="early_bird_dicount">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="">{{ __('Discount') }}</label>
                                                    <select name="discount_type" class="form-control">
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
                                                        value="" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="">{{ __('Discount End Date') }}</label>
                                                    <input type="date" name="early_bird_discount_date" value=""
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="">{{ __('Discount End Time') }}</label>
                                                    <input type="time" name="early_bird_discount_time"
                                                        value=""class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- end early bird settings --}}

                                    {{-- early bird settings --}}
                                    <div class="col-lg-12 " id="late_price_selector">
                                        <div class="form-group mt-1">
                                            <label for="">{{ __('Late Price') . '*' }}</label>
                                            <div class="selectgroup w-100">
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="late_price_discount_type" value="disable"
                                                        class="selectgroup-input"
                                                        {{ $ticket->late_price_discount == 'disable' ? 'checked' : '' }}>
                                                    <span class="selectgroup-button">{{ __('Disable') }}</span>
                                                </label>

                                                <label class="selectgroup-item">
                                                    <input type="radio" name="late_price_discount_type" value="enable"
                                                        class="selectgroup-input"
                                                        {{ $ticket->late_price_discount == 'enable' ? 'checked' : '' }}>
                                                    <span class="selectgroup-button">{{ __('Enable') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 {{ $ticket->late_price_discount == 'enable' ? '' : 'd-none' }}"
                                        id="late_price_dicount">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="">{{ __('Discount') }}</label>
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
                                                        value="" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="">{{ __('Discount End Date') }}</label>
                                                    <input type="date" name="late_price_discount_date" value=""
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="">{{ __('Discount End Time') }}</label>
                                                    <input type="time" name="late_price_discount_time"
                                                        value=""class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- end early bird settings --}}
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
