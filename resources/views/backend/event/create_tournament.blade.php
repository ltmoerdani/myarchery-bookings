@extends('backend.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Add Event Tournament') }}</h4>
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
                <a href="#">{{ __('Event Management') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a
                    href="{{ route('choose-event-type', ['language' => $defaultLang->code]) }}">{{ __('Choose Event Type') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Add Event Tournament') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title d-inline-block">{{ __('Add Event Tournament') }}</div>
                    <a class="btn btn-info btn-sm float-right d-inline-block"
                        href="{{ route('admin.event_management.event', ['language' => $defaultLang->code]) }}">
                        <span class="btn-label">
                            <i class="fas fa-backward"></i>
                        </span>
                        {{ __('Back') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="alert alert-danger pb-1 dis-none" id="eventErrors">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <ul></ul>
                            </div>
                            <div class="col-lg-12">
                                <label for="" class="mb-2"><strong>{{ __('Gallery Images') }} **</strong></label>
                                <form action="{{ route('admin.event.imagermvtournament') }}" id="my-dropzone"
                                    enctype="multipart/formdata" class="dropzone create">
                                    @csrf
                                    <div class="fallback">
                                        <input name="file" type="file" multiple />
                                    </div>
                                </form>
                                <div class=" mb-0" id="errpreimg">

                                </div>
                            </div>
                            <form id="eventForm" action="{{ route('admin.event_management.store_event_tournament') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="event_type" value="{{ request()->input('type') }}">
                                <input type="hidden" id="base_url" value="{{ url('/') }}">
                                <div class="form-group">
                                    <label for="">{{ __('Thumbnail Image') . '*' }}</label>
                                    <br>
                                    <div class="thumb-preview">
                                        <img src="{{ asset('assets/admin/img/noimage.jpg') }}" alt="..."
                                            class="uploaded-img">
                                    </div>

                                    <div class="mt-3">
                                        <div role="button" class="btn btn-primary btn-sm upload-btn">
                                            {{ __('Choose Image') }}
                                            <input type="file" class="img-input" name="thumbnail">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
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
                                                                        checked>
                                                                    <span
                                                                        class="selectgroup-button">{{ __('Active') }}</span>
                                                                </label>

                                                                <label class="selectgroup-item">
                                                                    <input type="radio" name="countdown_status"
                                                                        value="0"
                                                                        class="selectgroup-input countDownStatusType">
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
                                    <!-- <div class="col-12">
                                            <div class="card border border-1">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-12 mt-2">
                                                            <div class="form-group">
                                                                <label>
                                                                    {{ __('Currency Type') . '*' }}
                                                                </label>
                                                                <div class="selectgroup w-100">
                                                                    <label class="selectgroup-item">
                                                                        <input type="radio" name="currency_type"
                                                                            value="idr"
                                                                            class="selectgroup-input eventDateType" checked>
                                                                        <span
                                                                            class="selectgroup-button">{{ __('Single Currency') }}</span>
                                                                    </label>

                                                                    <label class="selectgroup-item">
                                                                        <input type="radio" name="currency_type"
                                                                            value="idr,usd"
                                                                            class="selectgroup-input eventDateType">
                                                                        <span
                                                                            class="selectgroup-button">{{ __('Dual Currency') }}</span>
                                                                    </label>
                                                                </div>
                                                                <p class="mb-0 p-0">*Select 'Dual Currency' to display prices
                                                                    in
                                                                    both IDR
                                                                    (primary) and USD.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
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
                                                                        checked>
                                                                    <span
                                                                        class="selectgroup-button">{{ __('Public') }}</span>
                                                                </label>

                                                                <label class="selectgroup-item">
                                                                    <input type="radio" name="event_publisher"
                                                                        value="private"
                                                                        class="selectgroup-input eventType">
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
                                                                    id="code" placeholder="type your code" />
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
                                                            required />
                                                    </div>
                                                    <div class="col-12 col-md-6 col-xl-3 mt-2">
                                                        <label class="mb-1">
                                                            {{ __('Start Time') . '*' }}
                                                        </label>
                                                        <input class="form-control" type="time"
                                                            placeholder="Choose Date" name="start_time" id="start_time"
                                                            required />
                                                    </div>
                                                    <div class="col-12 col-md-6 col-xl-3 mt-2">
                                                        <label class="mb-1">
                                                            {{ __('End Date') . '*' }}
                                                        </label>
                                                        <input class="form-control" type="date"
                                                            placeholder="Choose Date" name="end_date" id="end_date"
                                                            required />
                                                    </div>
                                                    <div class="col-12 col-md-6 col-xl-3 mt-2">
                                                        <label class="mb-1">
                                                            {{ __('End Time') . '*' }}
                                                        </label>
                                                        <input class="form-control" type="time"
                                                            placeholder="Choose Date" name="end_time" id="end_time"
                                                            required />
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
                                                                    <tr>
                                                                        <td>
                                                                            <div class="form-group">
                                                                                <select name="competition_categories[]"
                                                                                    id="competition_categories[]"
                                                                                    class="form-control">
                                                                                    @foreach ($competition_categories as $cat)
                                                                                        <option
                                                                                            value="{{ $cat->id }}">
                                                                                            {{ $cat->name }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group">
                                                                                <select name="competition_class_type[]"
                                                                                    id="competition_class_type[]"
                                                                                    class="form-control">
                                                                                    @foreach ($competition_class_type as $type)
                                                                                        <option
                                                                                            value="{{ $type->id }}">
                                                                                            {{ $type->name }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group">
                                                                                <input type="text"
                                                                                    name="competition_class_name[]"
                                                                                    id="competition_class_name[]"
                                                                                    value="" class="form-control">
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group">
                                                                                <select name="competition_distance[]"
                                                                                    id="competition_distance[]"
                                                                                    class="form-control">
                                                                                    @foreach ($competition_distance as $dis)
                                                                                        <option
                                                                                            value="{{ $dis->id }}">
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
                                                                        class="selectgroup-input delegationType" checked>
                                                                    <span
                                                                        class="selectgroup-button">{{ __('Open') }}</span>
                                                                </label>

                                                                <label class="selectgroup-item">
                                                                    <input type="radio" name="delegation_type"
                                                                        value="selected" id="delegation_type"
                                                                        class="selectgroup-input delegationType">
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
                                                    <div class="col-12 mt-2 select-type-field d-none">
                                                        <div class="form-group">
                                                            <label class="mb-1">
                                                                {{ __('Select Type') . '*' }}
                                                            </label>
                                                            <select class="custom-select" id="select_type"
                                                                name="select_type" required>
                                                                <option selected value="province">Province</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mt-2 select-country-field d-none">
                                                        <div class="form-group">
                                                            <label class="mb-1">
                                                                {{ __('Select Country') . '*' }}
                                                            </label>
                                                            <select class="custom-select" id="select_country"
                                                                name="select_country" required>
                                                                <option selected value="country">Country</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mt-2 select-state-field d-none">
                                                        <div class="form-group">
                                                            <label class="mb-1">
                                                                {{ __('Select State') . '*' }}
                                                            </label>
                                                            <select class="custom-select" id="select_state"
                                                                name="select_state" required>
                                                                <option selected value="state">state</option>
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
                                                        <select class="custom-select" id="team" name="team"
                                                            required>
                                                            <option value="active">Active</option>
                                                            <option selected value="disable">Disable</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-12 col-md-6 col-xl-3 mt-2">
                                                        <label class="mb-1">
                                                            {{ __('Mixed Team') }}
                                                        </label>
                                                        <select class="custom-select" id="mixed_team" name="mixed_team"
                                                            required>
                                                            <option value="active">Active</option>
                                                            <option selected value="disable">Disable</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-12 col-md-6 col-xl-3 mt-2">
                                                        <label class="mb-1">
                                                            {{ __('Official') }}
                                                        </label>
                                                        <select class="custom-select" id="official" name="official"
                                                            required>
                                                            <option value="active">Active</option>
                                                            <option selected value="disable">Disable</option>
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
                                                            {{ __('Upload File') }}
                                                        </label>
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input"
                                                                id="upload_file" name="upload_file"
                                                                aria-describedby="upload_file" accept=".doc,.docx,.pdf"
                                                                style="background:#fff">
                                                            <label class="custom-file-label" for="upload_file"
                                                                style="background:#fff">Choose
                                                                file</label>
                                                        </div>
                                                        <small>
                                                            *Doc and PDF only. Max 2mb
                                                        </small>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label class="mb-1">
                                                            {{ __('Status') }}
                                                        </label>
                                                        <select class="custom-select" id="status" name="status"
                                                            required>
                                                            <option selected value="1">Active</option>
                                                            <option value="0">Disable</option>
                                                        </select>
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

                                                    @if (request()->input('type') == 'venue')
                                                        <div class="row">
                                                            <div class="col-lg-8">
                                                                <div class="form-group">
                                                                    <label
                                                                        for="">{{ __('Address') . '*' }}</label>
                                                                    <input type="text"
                                                                        name="{{ $language->code }}_address"
                                                                        class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                                                                        placeholder="{{ __('Enter Address') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <div class="form-group">
                                                                    <label for="">{{ __('County') . '*' }}</label>
                                                                    <input type="text"
                                                                        name="{{ $language->code }}_country"
                                                                        placeholder="{{ __('Enter Country') }}"
                                                                        class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <div class="form-group">
                                                                    <label for="">{{ __('State') }}</label>
                                                                    <input type="text"
                                                                        name="{{ $language->code }}_state"
                                                                        class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                                                                        placeholder="{{ __('Enter State') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <div class="form-group">
                                                                    <label for="">{{ __('City') . '*' }}</label>
                                                                    <input type="text"
                                                                        name="{{ $language->code }}_city"
                                                                        class="form-control {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                                                                        placeholder="Enter City">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4">
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

                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" id="EventSubmit" class="btn btn-success">
                                {{ __('Save') }}
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
    @endphp
    <script>
        let languages = "{{ $languages }}";
    </script>
    <script type="text/javascript" src="{{ asset('assets/admin/js/admin-partial.js') }}"></script>
    <script src="{{ asset('assets/admin/js/admin_dropzone.js') }}"></script>
@endsection

@section('variables')
    <script>
        "use strict";
        var storeUrl = "{{ route('admin.event.imagesstoretournament') }}";
        var removeUrl = "{{ route('admin.event.imagermvtournament') }}";
        var loadImgs = 0;

        // let i = 0;
    </script>
@endsection
