@extends('backend.layout')
@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Edit Event Tournament') }}</h4>
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

            @php
                $event_title = DB::table('event_contents')
                    ->where('language_id', $defaultLang->id)
                    ->where('event_id', $event->id)
                    ->select('title')
                    ->first();
                if (empty($event_title)) {
                    $event_title = DB::table('event_contents')
                        ->where('event_id', $event->id)
                        ->select('title')
                        ->first();
                }

            @endphp
            <li class="nav-item">
                <a href="#">
                    {{ strlen($event_title->title) > 35 ? mb_substr($event_title->title, 0, 35, 'UTF-8') . '...' : $event_title->title }}
                </a>

            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>

            <li class="nav-item">
                <a href="#">{{ __('Edit Event Tournament') }}</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title d-inline-block">{{ __('Edit Event Tournament') }}</div>
                    <a class="btn btn-info btn-sm float-right d-inline-block" href="{{ url()->previous() }}">
                        <span class="btn-label">
                            <i class="fas fa-backward"></i>
                        </span>
                        {{ __('Back') }}
                    </a>
                    <a class="mr-2 btn btn-success btn-sm float-right d-inline-block"
                        href="{{ route('event.details', ['slug' => eventSlug($defaultLang->id, $event->id), 'id' => $event->id]) }}"
                        target="_blank">
                        <span class="btn-label">
                            <i class="fas fa-eye"></i>
                        </span>
                        {{ __('Preview') }}
                    </a>
                    <a class="mr-2 btn btn-secondary btn-sm float-right d-inline-block"
                        href="{{ route('admin.event.ticket', ['language' => $defaultLang->code, 'event_id' => $event->id, 'event_type' => $event->event_type]) }}"
                        target="_blank">
                        <span class="btn-label">
                            <i class="far fa-ticket"></i>
                        </span>
                        {{ __('Tickets') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="alert alert-danger pb-1 dis-none" id="eventErrors">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <ul></ul>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label for="" class="mb-2">
                                <strong>{{ __('Gallery Images') }} **</strong>
                            </label>
                            <div id="reload-slider-div">
                                <div class="row mt-2">
                                    <div class="col">
                                        <table class="table" id="img-table">

                                        </table>
                                    </div>
                                </div>
                            </div>
                            <form action="{{ route('admin.event.imagesstoretournament') }}" id="my-dropzone"
                                enctype="multipart/formdata" class="dropzone create">
                                @csrf
                                <div class="fallback">
                                    <input name="file" type="file" multiple />
                                </div>
                                <input type="hidden" value="{{ $event->id }}" name="event_id">
                            </form>
                            <div class=" mb-0" id="errpreimg">

                            </div>
                        </div>
                        <div class="col-12">
                            <form id="eventForm" action="{{ route('admin.event_management.store_event_tournament') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="event_id" value="{{ $event->id }}">
                                <input type="hidden" name="event_type" value="{{ $event->event_type }}">
                                <input type="hidden" name="gallery_images" value="0">
                                <input type="hidden" id="base_url" value="{{ url('/') }}">

                                <div class="form-group">
                                    <label for="">{{ __('Thumbnail Image') . '*' }}</label>
                                    <br>
                                    <div class="thumb-preview">
                                        <img src="{{ $event->thumbnail ? asset('assets/admin/img/event/thumbnail/' . $event->thumbnail) : asset('assets/admin/img/noimage.jpg') }}"
                                            alt="..." class="uploaded-img">
                                    </div>
                                    <div class="mt-3">
                                        <div role="button" class="btn btn-primary btn-sm upload-btn">
                                            {{ __('Choose Image') }}
                                            <input type="file" class="img-input" name="thumbnail">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card border border-1">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12 mt-2">
                                                        <label class="mb-1 px-2">
                                                            {{ __('Organizer') }}
                                                        </label>
                                                        <div class="form-group">
                                                            <select class="custom-select select2" id="organizer_id"
                                                                name="organizer_id" required>
                                                                <option disabled value="">
                                                                    Choose Organizer
                                                                </option>
                                                                @foreach ($organizers as $val_organizer)
                                                                    <option
                                                                        {{ $val_organizer->id == $event->organizer_id ? 'selected' : '' }}
                                                                        value="{{ $val_organizer->id }}">
                                                                        {{ $val_organizer->email }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card border border-1">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12 mt-2">
                                                        <div class="form-group">
                                                            <label>
                                                                {{ __('Countdown Status') . '*' }}
                                                            </label>
                                                            <div class="selectgroup w-100">
                                                                <label class="selectgroup-item">
                                                                    <input type="radio" name="countdown_status"
                                                                        value="1"
                                                                        class="selectgroup-input countDownStatusType"
                                                                        {{ $event->countdown_status == 1 ? 'checked' : '' }}>
                                                                    <span
                                                                        class="selectgroup-button">{{ __('Active') }}</span>
                                                                </label>

                                                                <label class="selectgroup-item">
                                                                    <input type="radio" name="countdown_status"
                                                                        value="0"
                                                                        class="selectgroup-input countDownStatusType"
                                                                        {{ $event->countdown_status == 0 ? 'checked' : '' }}>
                                                                    <span
                                                                        class="selectgroup-button">{{ __('Disable') }}</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card border border-1">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12 mt-2">
                                                        <div class="form-group">
                                                            <label>
                                                                {{ __('Pricing Scheme') . '*' }}
                                                            </label>
                                                            <div class="selectgroup w-100">
                                                                <label class="selectgroup-item">
                                                                    <input type="radio" name="pricing_scheme"
                                                                        value="single_price"
                                                                        class="selectgroup-input countDownStatusType"
                                                                        {{ $ticket_info->pricing_scheme == 'single_price' ? 'checked' : '' }}>
                                                                    <span
                                                                        class="selectgroup-button">{{ __('Single Price') }}</span>
                                                                </label>

                                                                <label class="selectgroup-item">
                                                                    <input type="radio" name="pricing_scheme"
                                                                        value="dual_price"
                                                                        class="selectgroup-input countDownStatusType"
                                                                        {{ $ticket_info->pricing_scheme == 'dual_price' ? 'checked' : '' }}>
                                                                    <span
                                                                        class="selectgroup-button">{{ __('Dual Price') }}</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 px-4">
                                                        <p class="font-weight-bold mt-0">
                                                            *Choose 'Dual Price' to set different pricing for local and
                                                            international customers.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card border border-1">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12 mt-2">
                                                        <div class="form-group">
                                                            <label>
                                                                {{ __('Event Publisher') . '*' }}
                                                            </label>
                                                            <div class="selectgroup w-100">
                                                                <label class="selectgroup-item">
                                                                    <input type="radio" name="event_publisher"
                                                                        value="public" class="selectgroup-input eventType"
                                                                        {{ $event_publisher->event_type == 'public' ? 'checked' : '' }}>
                                                                    <span
                                                                        class="selectgroup-button">{{ __('Public') }}</span>
                                                                </label>

                                                                <label class="selectgroup-item">
                                                                    <input type="radio" name="event_publisher"
                                                                        value="private"
                                                                        class="selectgroup-input eventType"
                                                                        {{ $event_publisher->event_type == 'private' ? 'checked' : '' }}>
                                                                    <span
                                                                        class="selectgroup-button">{{ __('Private') }}</span>
                                                                </label>
                                                            </div>
                                                            <small>
                                                                *Publish your event publicly or keep it private. For private
                                                                events, share the link exclusively with selected
                                                                individuals.
                                                            </small>
                                                        </div>
                                                        <div class="row mt-3 px-3">
                                                            <div class="col-12 mb-2">
                                                                <label class="fw-bold">
                                                                    {{ __('Code') }}
                                                                </label>
                                                            </div>
                                                            <div class="col-12 col-md-6 my-1">
                                                                <input class="form-control" type="text" name="code"
                                                                    id="code" value="{{ $event_publisher->code }}"
                                                                    placeholder="type your code" />
                                                            </div>
                                                            <div class="col-12 col-md-6 my-1">
                                                                <Button type="button"
                                                                    class="btn btn-block btn-secondary btn-generate-code">
                                                                    <i class="fa fa-ticket-alt mr-1"></i> Generate
                                                                </Button>
                                                            </div>
                                                            <div class="col-12 mt-2">
                                                                <small>
                                                                    *Only fill in this field if you want registrants with a
                                                                    code to proceed. You can enter your own code or choose
                                                                    to generate one.
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card border border-1">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12 col-md-6 col-xl-3 mt-2">
                                                        <label class="mb-1">
                                                            {{ __('Start Date') . '*' }}
                                                        </label>
                                                        <input class="form-control" type="date"
                                                            placeholder="Choose Date" name="start_date" id="start_date"
                                                            required value="{{ $event->start_date }}" />
                                                    </div>
                                                    <div class="col-12 col-md-6 col-xl-3 mt-2">
                                                        <label class="mb-1">
                                                            {{ __('Start Time') . '*' }}
                                                        </label>
                                                        <input class="form-control" type="time"
                                                            placeholder="Choose Date" name="start_time" id="start_time"
                                                            required value="{{ $event->start_time }}" />
                                                    </div>
                                                    <div class="col-12 col-md-6 col-xl-3 mt-2">
                                                        <label class="mb-1">
                                                            {{ __('End Date') . '*' }}
                                                        </label>
                                                        <input class="form-control" type="date"
                                                            placeholder="Choose Date" name="end_date" id="end_date"
                                                            required value="{{ $event->end_date }}" />
                                                    </div>
                                                    <div class="col-12 col-md-6 col-xl-3 mt-2">
                                                        <label class="mb-1">
                                                            {{ __('End Time') . '*' }}
                                                        </label>
                                                        <input class="form-control" type="time"
                                                            placeholder="Choose Date" name="end_time" id="end_time"
                                                            required value="{{ $event->end_time }}" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card border border-1">
                                            <input type="hidden" disabled readonly id="competition_categories_value"
                                                value="{{ $competition_categories }}" />
                                            <input type="hidden" disabled readonly id="competition_class_type_value"
                                                value="{{ $competition_class_type }}" />
                                            <input type="hidden" disabled readonly id="competition_distance_value"
                                                value="{{ $competition_distance }}" />

                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <label class="mb-1">
                                                            {{ __('Set Category') . '*' }}
                                                        </label>
                                                    </div>
                                                    <div class="col-12 mt-3">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-center">{{ __('Category') }}
                                                                        </th>
                                                                        <th class="text-center">
                                                                            {{ __('Type Class') }}
                                                                        </th>
                                                                        <th class="text-center">
                                                                            {{ __('Class Name') }}
                                                                        </th>
                                                                        <th class="text-center">{{ __('Distance') }}
                                                                        </th>
                                                                        <th class="text-center">
                                                                            <a href="javascrit:void(0)"
                                                                                class="btn btn-sm btn-success addSetCategory">
                                                                                <i class="fas fa-plus-circle"></i>
                                                                            </a>
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="dynamic_content_set_category">
                                                                    @foreach ($competitions as $competition)
                                                                        <tr>
                                                                            <td>
                                                                                <div class="form-group">
                                                                                    <select name="competition_categories[]"
                                                                                        id="competition_categories[]"
                                                                                        class="form-control"
                                                                                        value="{{ $competition->competition_category_id }}">
                                                                                        @foreach ($competition_categories as $cat)
                                                                                            <option
                                                                                                value="{{ $cat->id }}"
                                                                                                {{ $cat->id == $competition->competition_category_id ? 'selected' : '' }}>
                                                                                                {{ $cat->name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="form-group">
                                                                                    <select name="competition_class_type[]"
                                                                                        id="competition_class_type[]"
                                                                                        class="form-control"
                                                                                        value="{{ $competition->class_type }}">
                                                                                        @foreach ($competition_class_type as $type)
                                                                                            <option
                                                                                                value="{{ $type->id }}"
                                                                                                {{ $type->id == $competition->class_type ? 'selected' : '' }}>
                                                                                                {{ $type->name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="form-group">
                                                                                    <input type="text"
                                                                                        name="competition_class_name[]"
                                                                                        id="competition_class_name[]"
                                                                                        value="{{ $competition->class_name }}"
                                                                                        class="form-control">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="form-group">
                                                                                    <select name="competition_distance[]"
                                                                                        id="competition_distance[]"
                                                                                        class="form-control"
                                                                                        value="{{ $competition->distance }}">
                                                                                        @foreach ($competition_distance as $dis)
                                                                                            <option
                                                                                                value="{{ $dis->id }}"
                                                                                                {{ $dis->id == $competition->distance ? 'selected' : '' }}>
                                                                                                {{ $dis->name }} Meter
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <a href="javascript:void(0)"
                                                                                    id="buttonDelete[]"
                                                                                    class="btn btn-sm btn-danger deleteSetCategory">
                                                                                    <i class="fas fa-minus"></i></a>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card border border-1">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12 mt-2">
                                                        <div class="form-group">
                                                            <label>
                                                                {{ __('Delegation Type') . '*' }}
                                                            </label>
                                                            <div class="selectgroup w-100">
                                                                <label class="selectgroup-item">
                                                                    <input type="radio" name="delegation_type"
                                                                        value="open" id="delegation_type"
                                                                        class="selectgroup-input delegationType"
                                                                        {{ $contingent_type->contingent_type == 'open' ? 'checked' : '' }}>
                                                                    <span
                                                                        class="selectgroup-button">{{ __('Open') }}</span>
                                                                </label>

                                                                <label class="selectgroup-item">
                                                                    <input type="radio" name="delegation_type"
                                                                        value="selected" id="delegation_type"
                                                                        class="selectgroup-input delegationType"
                                                                        {{ $contingent_type->contingent_type == 'selected' ? 'checked' : '' }}>
                                                                    <span
                                                                        class="selectgroup-button">{{ __('Selected') }}</span>
                                                                </label>
                                                            </div>
                                                            <p class="mb-0 p-0">
                                                                *You can set the content type to 'Open', allowing
                                                                partisipants to freely enter names like country, club, or
                                                                school, or 'Selected', where partisipants choose from
                                                                predefined options such as a specific club or schools.
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="col-12 mt-2 select-type-field {{ $contingent_type->contingent_type != 'selected' ? 'd-none' : '' }}">
                                                        <div class="form-group">
                                                            <label class="mb-1">
                                                                {{ __('Select Type') . '*' }}
                                                            </label>
                                                            <select class="custom-select selectTypeDelegation"
                                                                id="select_type" name="select_type" required
                                                                value="{{ $contingent_type->select_type }}">
                                                                <option value="" disabled>Choose Delegation
                                                                    Type</option>
                                                                @foreach ($delegation_type as $val_delegation_type)
                                                                    <option value="{{ $val_delegation_type->name }}"
                                                                        {{ $contingent_type->select_type == $val_delegation_type->name ? 'selected' : '' }}>
                                                                        {{ strtolower($val_delegation_type->name) == 'club' ? 'Club/Team' : $val_delegation_type->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="col-12 mt-2 select-country-field {{ strtolower($contingent_type->select_type) == 'province' || strtolower($contingent_type->select_type) == 'city/district' ? '' : 'd-none' }}">
                                                        <div class="form-group">
                                                            <label class="mb-1">
                                                                {{ __('Select Country') . '*' }}
                                                            </label>
                                                            <select class="custom-select select2 fieldCountry"
                                                                id="select_country" name="select_country"
                                                                value="{{ $contingent_type->country_id }}">
                                                                <option selected value="">Choose Country</option>
                                                                @foreach ($international_countries as $value_international_country)
                                                                    <option value="{{ $value_international_country->id }}"
                                                                        {{ $contingent_type->country_id == $value_international_country->id ? 'selected' : '' }}>
                                                                        {{ $value_international_country->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="col-12 mt-2 select-state-field {{ strtolower($contingent_type->select_type) == 'city/district' ? '' : 'd-none' }}">
                                                        <div class="form-group">
                                                            <label class="mb-1">
                                                                {{ __('Select State') . '*' }}
                                                            </label>
                                                            <select class="custom-select select2 fieldState"
                                                                id="select_state" name="select_state"
                                                                value="{{ !$contingent_type->province_id ? '' : $contingent_type->province_id }}">
                                                                <option value=""
                                                                    {{ !$contingent_type->province_id ? 'selected' : '' }}
                                                                    disabled>Choose State</option>
                                                                @if (strtolower($contingent_type->select_type) == 'city/district')
                                                                    @foreach ($state_delegation_list as $value_state_delegation)
                                                                        <option value="{{ $value_state_delegation->id }}"
                                                                            {{ $contingent_type->province_id == $value_state_delegation->id ? 'selected' : '' }}>
                                                                            {{ $value_state_delegation->name }}
                                                                        </option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card border border-1">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12 col-md-6 col-xl-3 mt-2">
                                                        <label class="mb-1">
                                                            {{ __('Individual') . '*' }}
                                                        </label>
                                                        <select class="custom-select" id="individu" name="individu"
                                                            required disabled readonly>
                                                            <option selected disabled value="active">Active</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-12 col-md-6 col-xl-3 mt-2">
                                                        <label class="mb-1">
                                                            {{ __('Team') }}
                                                        </label>
                                                        <select class="custom-select"
                                                            value="{{ $team_allowed == true ? 'active' : 'disable' }}"
                                                            id="team" name="team" required>
                                                            <option value="active">Active</option>
                                                            <option value="disable">Disable</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-12 col-md-6 col-xl-3 mt-2">
                                                        <label class="mb-1">
                                                            {{ __('Mixed Team') }}
                                                        </label>
                                                        <select class="custom-select"
                                                            value="{{ $mix_team_allowed == true ? 'active' : 'disable' }}"
                                                            id="mixed_team" name="mixed_team" required>
                                                            <option value="active">Active</option>
                                                            <option value="disable">Disable</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-12 col-md-6 col-xl-3 mt-2">
                                                        <label class="mb-1">
                                                            {{ __('Official') }}
                                                        </label>
                                                        <select class="custom-select"
                                                            value="{{ $official_allowed == true ? 'active' : 'disable' }}"
                                                            id="official" name="official" required>
                                                            <option value="active">Active</option>
                                                            <option value="disable">Disable</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card border border-1">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12 col-md-6">
                                                        <label class="mb-1">
                                                            {{ __('Upload File THB') }}
                                                        </label>
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input"
                                                                id="thb_file" name="thb_file"
                                                                aria-describedby="thb_file" accept=".doc,.docx,.pdf"
                                                                style="background:#fff">
                                                            <label class="custom-file-label" for="thb_file"
                                                                style="background:#fff">Choose
                                                                file</label>
                                                        </div>
                                                        @cannot($event->thb_file)
                                                            <div class="my-2">
                                                                <a
                                                                    href="{{ asset('assets/admin/img/event/tournament_uploaded/' . $event->thb_file) }}">
                                                                    {{ __('Download THB File') }}
                                                                </a>
                                                            </div>
                                                        @endcannot
                                                        <small>
                                                            *Doc and PDF only. Max 2mb
                                                        </small>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label class="mb-1">
                                                            {{ __('Status') }}
                                                        </label>
                                                        <select class="custom-select" id="status" name="status"
                                                            value="{{ $event->status }}" required>
                                                            <option value="1"
                                                                {{ $event->status == 1 ? 'selected' : '' }}>
                                                                Active</option>
                                                            <option value="0"
                                                                {{ $event->status == 0 ? 'selected' : '' }}>
                                                                Disable</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card border border-1">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label for="">{{ __('Latitude') }}*</label>
                                                            <input type="text" name="latitude" placeholder="Latitude"
                                                                class="form-control" value="{{ $event->latitude }}"
                                                                required>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label for="">{{ __('Longitude') }}*</label>
                                                            <input type="text" placeholder="Longitude"
                                                                name="longitude" class="form-control"
                                                                value="{{ $event->longitude }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mt-0 px-4">
                                                        <p class="font-weight-bold">
                                                            *Enter a Latitude and Longitude from Google Maps
                                                            Platform(website or application mobile) for to display
                                                            the location on Google Maps on the event page.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="accordion" class="mt-3">
                                    @foreach ($languages as $language)
                                        <div class="version">
                                            <div class="version-header" id="heading{{ $language->id }}">
                                                <h5 class="mb-0">
                                                    <button type="button" class="btn btn-link" data-toggle="collapse"
                                                        data-target="#collapse{{ $language->id }}"
                                                        aria-expanded="{{ $language->is_default == 1 ? 'true' : 'false' }}"
                                                        aria-controls="collapse{{ $language->id }}">
                                                        {{ $language->name . ' ' . __('Language') }}
                                                        {{ $language->is_default == 1 ? '(' . __('Default') . ')' : '' }}
                                                    </button>
                                                </h5>
                                            </div>
                                            @php
                                                $event_content = DB::table('event_contents')
                                                    ->where('language_id', $language->id)
                                                    ->where('event_id', $event->id)
                                                    ->first();
                                            @endphp
                                            <div id="collapse{{ $language->id }}"
                                                class="collapse {{ $language->is_default == 1 ? 'show' : '' }}"
                                                aria-labelledby="heading{{ $language->id }}" data-parent="#accordion">
                                                <div class="version-body">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Event Title') . '*' }}</label>
                                                                <input type="text" class="form-control"
                                                                    name="{{ $language->code }}_title"
                                                                    placeholder="{{ __('Enter Event Name') }}">
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                @php
                                                                    $categories = DB::table('event_categories')
                                                                        ->where('language_id', $language->id)
                                                                        ->where('status', 1)
                                                                        ->orderBy('serial_number', 'asc')
                                                                        ->get();
                                                                @endphp

                                                                <label for="">{{ __('Category') . '*' }}</label>
                                                                <select name="{{ $language->code }}_category_id"
                                                                    class="form-control">
                                                                    <option selected disabled>{{ __('Select Category') }}
                                                                    </option>

                                                                    @foreach ($categories as $category)
                                                                        <option value="{{ $category->id }}">
                                                                            {{ $category->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if (request()->input('type') == 'venue' || request()->input('type') == 'tournament')
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="form-group">
                                                                    <label
                                                                        for="">{{ __('Address') . '*' }}</label>
                                                                    <input type="text"
                                                                        name="{{ $language->code }}_address"
                                                                        class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                                                                        placeholder="{{ __('Enter Address') }}">
                                                                    <p class="font-weight-bold">
                                                                        *Enter a full address or location name to display
                                                                        the location on Google Maps on the event page.
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="form-group">
                                                                    <label
                                                                        for="">{{ __('County') . '*' }}</label>
                                                                    {{-- <input type="text"
                                                                        name="{{ $language->code }}_country"
                                                                        placeholder="{{ __('Enter Country') }}"
                                                                        class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}"> --}}
                                                                    <select class="custom-select select2"
                                                                        name="{{ $language->code }}_country"
                                                                        onchange="handleChooseEventContentLanguageCountry('{{ $language->code }}')"
                                                                        id="{{ $language->code }}_country">
                                                                        <option selected disable value="">
                                                                            Choose Country
                                                                        </option>
                                                                        @foreach ($international_countries as $value_international_country)
                                                                            <option
                                                                                value="{{ $value_international_country->id }}">
                                                                                {{ $value_international_country->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="form-group">
                                                                    <label for="">{{ __('State') }}</label>
                                                                    <select class="custom-select select2"
                                                                        name="{{ $language->code }}_state"
                                                                        onchange="handleChooseEventContentLanguageState('{{ $language->code }}')"
                                                                        id="{{ $language->code }}_state">
                                                                        <option selected disable value="">
                                                                            Choose State
                                                                        </option>
                                                                    </select>
                                                                    {{-- <input type="text"
                                                                        name="{{ $language->code }}_state"
                                                                        class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                                                                        placeholder="{{ __('Enter State') }}"> --}}
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="form-group">
                                                                    <label for="">{{ __('City') . '*' }}</label>
                                                                    <select class="custom-select select2"
                                                                        name="{{ $language->code }}_city"
                                                                        id="{{ $language->code }}_city">
                                                                        <option selected disable value="">
                                                                            Choose City
                                                                        </option>
                                                                    </select>
                                                                    {{-- <input type="text"
                                                                        name="{{ $language->code }}_city"
                                                                        class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                                                                        placeholder="Enter City"> --}}
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="form-group">
                                                                    <label
                                                                        for="">{{ __('Zip/Post Code') }}</label>
                                                                    <input type="text"
                                                                        placeholder="{{ __('Enter Zip/Post Code') }}"
                                                                        name="{{ $language->code }}_zip_code"
                                                                        class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="row">
                                                        <div class="col">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Description') . '*' }}</label>
                                                                <textarea id="descriptionTmce{{ $language->id }}" class="form-control summernote"
                                                                    name="{{ $language->code }}_description" placeholder="{{ __('Enter Event Description') }}" data-height="300"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Refund Policy') }} *</label>
                                                                <textarea class="form-control" name="{{ $language->code }}_refund_policy" rows="5"
                                                                    placeholder="{{ __('Enter Refund Policy') }}"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Meta Keywords') }}</label>
                                                                <input class="form-control"
                                                                    name="{{ $language->code }}_meta_keywords"
                                                                    placeholder="{{ __('Enter Meta Keywords') }}"
                                                                    data-role="tagsinput">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Meta Description') }}</label>
                                                                <textarea class="form-control" name="{{ $language->code }}_meta_description" rows="5"
                                                                    placeholder="{{ __('Enter Meta Description') }}"></textarea>
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
                                                                        <input class="form-check-input" type="checkbox"
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
                                <div id="sliders"></div>
                            </form>
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
    @endphp
    <script>
        let languages = "{{ $languages }}";
    </script>
    <script type="text/javascript" src="{{ asset('assets/admin/js/admin-partial.js') }}"></script>
    <script src="{{ asset('assets/admin/js/admin_dropzone.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script>
@endsection

@section('variables')
    <script>
        "use strict";
        var storeUrl = "{{ route('admin.event.imagesstoretournament') }}";
        var removeUrl = "{{ route('admin.event.imagermvtournament') }}";

        var rmvdbUrl = "{{ route('admin.event.imgdbrmv') }}";
        var loadImgs = "{{ route('admin.event.images', $event->id) }}";
    </script>
@endsection
