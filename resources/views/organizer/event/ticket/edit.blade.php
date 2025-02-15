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
                <a href="#">{{ __('Event Management') }}</a>
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
                    href="{{ route('organizer.event.ticket', ['language' => $defaultLang->code, 'event_id' => request()->input('event_id'), 'event_type' => request()->input('event_type')]) }}">{{ __('Tickets') }}</a>
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
                            <a href="{{ route('organizer.event.ticket', ['language' => $defaultLang->code, 'event_id' => request()->input('event_id'), 'event_type' => request()->input('event_type')]) }}"
                                class="btn btn-info btn-sm float-right"><i class="fas fa-backward"></i>
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
                        <div class="col-lg-8 offset-lg-2">
                            <div class="alert alert-danger pb-1 dis-none" id="eventErrors">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <ul></ul>
                            </div>
                            <form id="eventForm" action="{{ route('organizer.ticket_management.update_ticket') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="event_type" value="{{ request()->input('event_type') }}">
                                <input type="hidden" name="event_id" value="{{ request()->input('event_id') }}">
                                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                                @if (request()->input('event_type') == 'venue')
                                    <div class="row ">

                                        {{-- /*****--variationwise ticket & early bird discount--****** --}}
                                        <div class="col-lg-12">
                                            <div class="form-group mt-1">
                                                <label for="">{{ __('Pricing') . '*' }}</label>
                                                <div class="selectgroup w-100">
                                                    <label class="selectgroup-item">
                                                        <input type="radio" name="pricing_type_2"
                                                            {{ $ticket->pricing_type == 'free' ? 'checked' : '' }}
                                                            value="free" class="selectgroup-input" checked>
                                                        <span class="selectgroup-button">{{ __('Free Tickets') }}</span>
                                                    </label>

                                                    <label class="selectgroup-item">
                                                        <input type="radio" name="pricing_type_2" value="variation"
                                                            {{ $ticket->pricing_type == 'variation' ? 'checked' : '' }}
                                                            class="selectgroup-input">
                                                        <span class="selectgroup-button">{{ __('Variation Wise') }}</span>
                                                    </label>

                                                    <label class="selectgroup-item">
                                                        <input type="radio" name="pricing_type_2" value="normal"
                                                            {{ $ticket->pricing_type == 'normal' ? 'checked' : '' }}
                                                            class="selectgroup-input">
                                                        <span
                                                            class="selectgroup-button">{{ __('Without Variation') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 {{ $ticket->pricing_type == 'variation' ? '' : 'd-none' }}"
                                            id="variation_pricing">
                                            <div class="form-group">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>{{ __('Variation Name') }}</th>
                                                                <th>{{ __('Price') }}</th>
                                                                <th>{{ __('Available Tickets') }}</th>
                                                                @if ($websiteInfo->event_guest_checkout_status != 1)
                                                                    <th>{{ __('Max ticket for each customer') }}</th>
                                                                @endif
                                                                <th><a href="javascrit:void(0)"
                                                                        class="btn btn-success btn-sm addRow"><i
                                                                            class="fas fa-plus-circle"></i></a></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if ($variations != null)
                                                                @foreach ($variations as $key => $item)
                                                                    <tr>
                                                                        <td>
                                                                            @php
                                                                                $variation_contents = App\Models\Event\VariationContent::where([['ticket_id', $ticket->id], ['key', $key]])->get();

                                                                            @endphp
                                                                            @foreach ($variation_contents as $variation_content)
                                                                                @php
                                                                                    $language = App\Models\Language::where('id', $variation_content->language_id)->first();
                                                                                @endphp
                                                                                <div class="form-group">
                                                                                    <label
                                                                                        for="">{{ __('Variation Name') . '*' }}
                                                                                        ({{ $language->name }})
                                                                                    </label>
                                                                                    <input type="text"
                                                                                        name="{{ $language->code }}_variation_name[]"
                                                                                        class="form-control"
                                                                                        value="{{ $variation_content['name'] }}">
                                                                                </div>
                                                                            @endforeach
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group">
                                                                                <label
                                                                                    for="">{{ __('Price') }}({{ $getCurrencyInfo->base_currency_text }})
                                                                                    *</label>
                                                                                <input type="text"
                                                                                    name="variation_price[]"
                                                                                    value="{{ $item['price'] }}"
                                                                                    class="form-control">
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="from-group mt-1">
                                                                                <input type="checkbox"
                                                                                    @checked($item['ticket_available_type'] == 'limited')
                                                                                    name="v_ticket_available_type[]"
                                                                                    value="limited"
                                                                                    class="ticket_available_type {{ $item['ticket_available_type'] == 'unlimited' ? 'd-none' : '' }}"
                                                                                    id="limited_{{ $loop->iteration }}"
                                                                                    data-id="{{ $loop->iteration }}">
                                                                                <label
                                                                                    for="limited_{{ $loop->iteration }}"
                                                                                    class="limited_{{ $loop->iteration }} {{ $item['ticket_available_type'] == 'unlimited' ? 'd-none' : '' }}">{{ __('Limited') }}</label>

                                                                                <input type="checkbox"
                                                                                    @checked($item['ticket_available_type'] == 'unlimited')
                                                                                    name="v_ticket_available_type[]"
                                                                                    value="unlimited"
                                                                                    class="ticket_available_type {{ $item['ticket_available_type'] == 'limited' ? 'd-none' : '' }}"
                                                                                    id="unlimited_{{ $loop->iteration }}"
                                                                                    data-id="{{ $loop->iteration }}">
                                                                                <label
                                                                                    for="unlimited_{{ $loop->iteration }}"
                                                                                    class="unlimited_{{ $loop->iteration }} {{ $item['ticket_available_type'] == 'limited' ? 'd-none' : '' }}">{{ __('Unlimited') }}</label>

                                                                            </div>

                                                                            <div class="form-group {{ $item['ticket_available_type'] == 'unlimited' ? 'd-none' : '' }}"
                                                                                id="input_{{ $loop->iteration }}">
                                                                                <label
                                                                                    for="">{{ __('Ticket Available') . '*' }}
                                                                                </label>
                                                                                <input type="text"
                                                                                    name="v_ticket_available[]"
                                                                                    value="{{ $item['ticket_available'] }}"
                                                                                    class="form-control">
                                                                            </div>
                                                                        </td>
                                                                        @if ($websiteInfo->event_guest_checkout_status != 1)
                                                                            <td>
                                                                                <div class="from-group mt-1">
                                                                                    <input type="checkbox"
                                                                                        @checked($item['max_ticket_buy_type'] == 'limited')
                                                                                        name="v_max_ticket_buy_type[]"
                                                                                        value="limited"
                                                                                        class="max_ticket_buy_type {{ $item['max_ticket_buy_type'] == 'unlimited' ? 'd-none' : '' }}"
                                                                                        id="buy_limited_{{ $loop->iteration }}"
                                                                                        data-id="{{ $loop->iteration }}">
                                                                                    <label
                                                                                        for="buy_limited_{{ $loop->iteration }}"
                                                                                        class="buy_limited_{{ $loop->iteration }} {{ $item['max_ticket_buy_type'] == 'unlimited' ? 'd-none' : '' }}">{{ __('Limited') }}</label>

                                                                                    <input type="checkbox"
                                                                                        @checked($item['max_ticket_buy_type'] == 'unlimited')
                                                                                        name="v_max_ticket_buy_type[]"
                                                                                        value="unlimited"
                                                                                        class="max_ticket_buy_type {{ $item['max_ticket_buy_type'] == 'limited' ? 'd-none' : '' }}"
                                                                                        id="buy_unlimited_{{ $loop->iteration }}"
                                                                                        data-id="{{ $loop->iteration }}">
                                                                                    <label
                                                                                        for="buy_unlimited_{{ $loop->iteration }}"
                                                                                        class="buy_unlimited_{{ $loop->iteration }} {{ $item['max_ticket_buy_type'] == 'limited' ? 'd-none' : '' }}">{{ __('Unlimited') }}</label>
                                                                                </div>

                                                                                <div class="form-group {{ $item['max_ticket_buy_type'] == 'unlimited' ? 'd-none' : '' }}"
                                                                                    id="input2_{{ $loop->iteration }}">
                                                                                    <label
                                                                                        for="">{{ __('Max ticket for each customer') . '*' }}
                                                                                    </label>
                                                                                    <input type="text"
                                                                                        name="v_max_ticket_buy[]"
                                                                                        class="form-control"
                                                                                        value="{{ $item['v_max_ticket_buy'] }}">
                                                                                </div>
                                                                            </td>
                                                                        @else
                                                                            <input type="hidden"
                                                                                name="v_max_ticket_buy_type[]"
                                                                                value="unlimited">
                                                                            <input type="hidden"
                                                                                name="v_max_ticket_buy[]"
                                                                                class="form-control">
                                                                        @endif
                                                                        <td>
                                                                            <a href="javascript:void(0)"
                                                                                class="btn btn-danger btn-sm deleteRow"> <i
                                                                                    class="fas fa-minus"></i></a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td>
                                                                        @foreach ($languages as $language)
                                                                            <div class="form-group">
                                                                                <label
                                                                                    for="">{{ __('Variation Name') . '*' }}
                                                                                    ({{ $language['name'] }})
                                                                                </label>
                                                                                <input type="text"
                                                                                    name="{{ $language['code'] }}_variation_name[]"
                                                                                    class="form-control">
                                                                            </div>
                                                                        @endforeach
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <label for="">{{ __('Price') . '*' }}
                                                                                ({{ $getCurrencyInfo->base_currency_text }})
                                                                            </label>
                                                                            <input type="text" name="variation_price[]"
                                                                                class="form-control">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="from-group mt-1">
                                                                            <input type="checkbox" checked
                                                                                name="v_ticket_available_type[]"
                                                                                value="limited"
                                                                                class="ticket_available_type"
                                                                                id="limited_1" data-id="1">
                                                                            <label for="limited_1"
                                                                                class="limited_1 ">{{ __('Limited') }}</label>

                                                                            <input type="checkbox"
                                                                                name="v_ticket_available_type[]"
                                                                                value="unlimited"
                                                                                class="ticket_available_type d-none"
                                                                                id="unlimited_1" data-id="1">
                                                                            <label for="unlimited_1"
                                                                                class="unlimited_1 d-none">{{ __('Unlimited') }}</label>
                                                                        </div>

                                                                        <div class="form-group" id="input_1">
                                                                            <label
                                                                                for="">{{ __('Ticket Available') }}
                                                                                * </label>
                                                                            <input type="text"
                                                                                name="v_ticket_available[]" value=""
                                                                                class="form-control">
                                                                        </div>
                                                                    </td>
                                                                    @if ($websiteInfo->event_guest_checkout_status != 1)
                                                                        <td>
                                                                            <div class="from-group mt-1">
                                                                                <input type="checkbox" checked
                                                                                    name="v_max_ticket_buy_type[]"
                                                                                    value="limited"
                                                                                    class="max_ticket_buy_type"
                                                                                    id="buy_limited_1" data-id="1">
                                                                                <label for="buy_limited_1"
                                                                                    class="buy_limited_1 ">{{ __('Limited') }}</label>

                                                                                <input type="checkbox"
                                                                                    name="v_max_ticket_buy_type[]"
                                                                                    value="unlimited"
                                                                                    class="max_ticket_buy_type d-none"
                                                                                    id="buy_unlimited_1" data-id="1">
                                                                                <label for="buy_unlimited_1"
                                                                                    class="buy_unlimited_1 d-none">{{ __('Unlimited') }}</label>
                                                                            </div>

                                                                            <div class="form-group" id="input2_1">
                                                                                <label
                                                                                    for="">{{ __('Max ticket for each customer') . '*' }}
                                                                                </label>
                                                                                <input type="text"
                                                                                    name="v_max_ticket_buy[]"
                                                                                    class="form-control">
                                                                            </div>
                                                                        </td>
                                                                    @else
                                                                        <input type="hidden"
                                                                            name="v_max_ticket_buy_type[]"
                                                                            value="unlimited">
                                                                        <input type="hidden" name="v_max_ticket_buy[]"
                                                                            class="form-control">
                                                                    @endif
                                                                    <td>
                                                                        <a href="javascript:void(0)"
                                                                            class="btn btn-danger btn-sm deleteRow">
                                                                            <i class="fas fa-minus"></i></a>
                                                                    </td>
                                                                </tr>
                                                            @endif

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 {{ $ticket->pricing_type == 'normal' ? '' : 'd-none' }}"
                                            id="normal_pricing">
                                            <div class="form-group">
                                                <label for="">{{ __('Price') }}
                                                    ({{ $getCurrencyInfo->base_currency_text }}) *</label>
                                                <input type="number" name="price" value="{{ $ticket->price }}"
                                                    class="form-control" placeholder="Enter Price">
                                            </div>
                                        </div>

                                        <div class="col-lg-12  {{ $ticket->pricing_type == 'free' ? 'd-none' : '' }}"
                                            id="early_bird_discount_free">
                                            <div class="form-group mt-1">
                                                <label for="">{{ __('Early Bird Discount') . '*' }}</label>
                                                <div class="selectgroup w-100">
                                                    <label class="selectgroup-item">
                                                        <input type="radio" name="early_bird_discount_type"
                                                            {{ $ticket->early_bird_discount == 'disable' ? 'checked' : '' }}
                                                            value="disable" class="selectgroup-input" checked>
                                                        <span class="selectgroup-button">{{ __('Disable') }}</span>
                                                    </label>

                                                    <label class="selectgroup-item">
                                                        <input type="radio" name="early_bird_discount_type"
                                                            {{ $ticket->early_bird_discount == 'enable' ? 'checked' : '' }}
                                                            value="enable" class="selectgroup-input">
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
                                                            <option
                                                                {{ $ticket->early_bird_discount_type == 'fixed' ? 'selected' : '' }}
                                                                value="fixed">{{ __('Fixed') }}</option>
                                                            <option
                                                                {{ $ticket->early_bird_discount_type == 'percentage' ? 'selected' : '' }}
                                                                value="percentage">{{ __('Percentage') }}</option>
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
                                                        <label for="">{{ __('Discount End Date') }}</label>
                                                        <input type="date" name="early_bird_discount_date"
                                                            value="{{ $ticket->early_bird_discount_date }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Discount End Time') }}</label>
                                                        <input type="time" name="early_bird_discount_time"
                                                            value="{{ $ticket->early_bird_discount_time }}"class="form-control">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <!--=====--variationwise ticket & early bird discount--====== --->

                                        <!---=======Ticekt limtit & ticket for each customer start--=====---->
                                        <div
                                            class="hideInvariatinwiseTicket col-lg-12 {{ $ticket->pricing_type == 'variation' ? 'd-none' : '' }}">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group mt-1">
                                                        <label
                                                            for="">{{ __('Total Number of Available Tickets') . '*' }}</label>
                                                        <div class="selectgroup w-100">
                                                            <label class="selectgroup-item">
                                                                <input type="radio" name="ticket_available_type"
                                                                    {{ $ticket->ticket_available_type == 'unlimited' ? 'checked' : '' }}
                                                                    value="unlimited" class="selectgroup-input">
                                                                <span
                                                                    class="selectgroup-button">{{ __('Unlimited') }}</span>
                                                            </label>

                                                            <label class="selectgroup-item">
                                                                <input type="radio" name="ticket_available_type"
                                                                    {{ $ticket->ticket_available_type == 'limited' ? 'checked' : '' }}
                                                                    value="limited" class="selectgroup-input">
                                                                <span
                                                                    class="selectgroup-button">{{ __('Limited') }}</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 {{ $ticket->ticket_available_type == 'limited' ? '' : 'd-none' }}"
                                                    id="ticket_available">
                                                    <div class="form-group">
                                                        <label>{{ __('Enter total number of available tickets') . '*' }}</label>
                                                        <input type="number" name="ticket_available"
                                                            value="{{ $ticket->ticket_available }}"
                                                            placeholder="Enter total number of available tickets"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                @if ($websiteInfo->event_guest_checkout_status != 1)
                                                    <div class="col-lg-6">
                                                        <div class="form-group mt-1">
                                                            <label
                                                                for="">{{ __('Maximum number of tickets for each customer') . '*' }}</label>
                                                            <div class="selectgroup w-100">
                                                                <label class="selectgroup-item">
                                                                    <input type="radio" name="max_ticket_buy_type"
                                                                        value="unlimited" class="selectgroup-input"
                                                                        {{ $ticket->max_ticket_buy_type == 'unlimited' ? 'checked' : '' }}>
                                                                    <span
                                                                        class="selectgroup-button">{{ __('Unlimited') }}</span>
                                                                </label>

                                                                <label class="selectgroup-item">
                                                                    <input type="radio" name="max_ticket_buy_type"
                                                                        value="limited" class="selectgroup-input"
                                                                        {{ $ticket->max_ticket_buy_type == 'limited' ? 'checked' : '' }}>
                                                                    <span
                                                                        class="selectgroup-button">{{ __('Limited') }}</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 {{ $ticket->max_ticket_buy_type == 'unlimited' ? 'd-none' : '' }}"
                                                        id="max_buy_ticket">
                                                        <div class="form-group">
                                                            <label>{{ __('Enter Maximum number of tickets for each customer') . '*' }}</label>
                                                            <input type="number" name="max_buy_ticket"
                                                                value="{{ $ticket->max_buy_ticket }}"
                                                                placeholder="Enter Maximum number of tickets for each customer"
                                                                class="form-control">
                                                        </div>
                                                    </div>
                                                @else
                                                    <input type="hidden" name="max_ticket_buy_type" value="unlimited">
                                                @endif
                                            </div>
                                        </div>
                                        <!---======-Ticekt limtit & ticket for each customer end--======= --->

                                @endif
                                <div class="col-lg-12">
                                    <div id="accordion" class="mt-3">
                                        @foreach ($languages as $language)
                                            <div class="version">
                                                <div class="version-header" id="heading{{ $language->id }}">
                                                    <h5 class="mb-0">
                                                        <button type="button" class="btn btn-link"
                                                            data-toggle="collapse"
                                                            data-target="#collapse{{ $language->id }}"
                                                            aria-expanded="{{ $language->is_default == 1 ? 'true' : 'false' }}"
                                                            aria-controls="collapse{{ $language->id }}">
                                                            {{ $language->name . __(' Language') }}
                                                            {{ $language->is_default == 1 ? '(Default)' : '' }}
                                                        </button>
                                                    </h5>
                                                </div>
                                                @php
                                                    $ticket_content = App\Models\Event\TicketContent::where([['ticket_id', $ticket->id], ['language_id', $language->id]])->first();
                                                @endphp

                                                <div id="collapse{{ $language->id }}"
                                                    class="collapse {{ $language->is_default == 1 ? 'show' : '' }}"
                                                    aria-labelledby="heading{{ $language->id }}"
                                                    data-parent="#accordion">
                                                    <div class="version-body">
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div
                                                                    class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                    <label>{{ __('Ticket Name') . '*' }}</label>
                                                                    <input type="text"
                                                                        name="{{ $language->code }}_title"
                                                                        placeholder="Enter Ticket Name"
                                                                        value="{{ @$ticket_content->title }}"
                                                                        class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col">
                                                                <div
                                                                    class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                    <label>{{ __('Description') }}</label>
                                                                    <textarea class="form-control" name="{{ $language->code }}_description" placeholder="Enter Description">{{ @$ticket_content->description }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col">
                                                                @php $currLang = $language; @endphp

                                                                @foreach ($languages as $language)
                                                                    @continue($language->id == $currLang->id)

                                                                    <div class="form-check py-0">
                                                                        <label class="form-check-label">
                                                                            <input class="form-check-input"
                                                                                type="checkbox"
                                                                                onchange="cloneInput('collapse{{ $currLang->id }}', 'collapse{{ $language->id }}', event)">
                                                                            <span
                                                                                class="form-check-sign">{{ __('Clone for') }}
                                                                                <strong
                                                                                    class="text-capitalize text-secondary">{{ $language->name }}</strong>
                                                                                {{ __('language') }}</span>
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
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
        var removeUrl = "{{ route('organizer.event.imagermv') }}";
    </script>
@endsection
