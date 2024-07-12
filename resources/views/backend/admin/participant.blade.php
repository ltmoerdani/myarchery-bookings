@extends('backend.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Events Participant') }}</h4>
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
                <a href="#">{{ __('Events Participant') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card-title d-inline-block">
                                {{ __('Events Participant') . ' (' . $language->name . ' ' . __('Language') . ')' }}
                            </div>
                        </div>

                        <div class="col-lg-3">
                            @if (!empty($langs))
                                <select name="language" class="form-control"
                                    onchange="window.location='{{ url()->current() . '?language=' }}' + this.value+'&event_type='+'{{ request()->input('event_type') }}'">
                                    <option selected disabled>{{ __('Select a Language') }}</option>
                                    @foreach ($langs as $lang)
                                        <option value="{{ $lang->code }}"
                                            {{ $lang->code == request()->input('language') ? 'selected' : '' }}>
                                            {{ $lang->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        <!-- <div class="col-lg-4 offset-lg-1 mt-2 mt-lg-0">

                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle btn-sm float-right" type="button"
                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    {{ __('Add Event') }}
                                </button>

                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a href="{{ route('add.event.event', ['type' => 'online']) }}" class="dropdown-item">
                                        {{ __('Online Event') }}
                                    </a>

                                    <a href="{{ route('add.event.event', ['type' => 'venue']) }}" class="dropdown-item">
                                        {{ __('Venue Event') }}
                                    </a>

                                    <a href="{{ route('add.event.event', ['type' => 'tournament']) }}"
                                        class="dropdown-item">
                                        {{ __('Tournament Event') }}
                                    </a>
                                </div>
                            </div>
                        </div> -->

                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">

                            <div class="float-right">
                                <div class="form-group">
                                    <form action="" method="get">
                                        <input type="hidden" name="language" value="{{ request()->input('language') }}"
                                            class="hidden">
                                        <input type="text" name="title" value="{{ request()->input('title') }}"
                                            name="name" placeholder="Enter Event Name" class="form-control">
                                    </form>
                                </div>
                            </div>

                            @if (count($events) == 0)
                                <h3 class="text-center mt-2">
                                    {{ __('NO EVENT CONTENT FOUND FOR ') . $language->name . '!' }}</h3>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped mt-3">
                                        <thead>
                                            <tr>
                                                <th scope="col" width="30%">{{ __('Title') }}</th>
                                                <th scope="col">{{ __('Organizer') }}</th>
                                                <th scope="col">{{ __('Type') }}</th>
                                                <th scope="col">{{ __('Category') }}</th>
                                                <th scope="col">{{ __('Start Date') }}</th>
                                                <th scope="col">{{ __('End Date') }}</th>
                                                <th scope="col">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($events as $event)
                                                <tr>
                                                    <td width="20%">
                                                        <a target="_blank"
                                                            href="{{ route('event.details', ['slug' => $event->slug, 'id' => $event->id]) }}">{{ strlen($event->title) > 30 ? mb_substr($event->title, 0, 30, 'UTF-8') . '....' : $event->title }}</a>
                                                    </td>
                                                    <td>
                                                        @if ($event->organizer)
                                                            <a target="_blank"
                                                                href="{{ route('admin.organizer_management.organizer_details', ['id' => $event->organizer_id, 'language' => $defaultLang->code]) }}">
                                                                {{ strlen($event->organizer->username) > 20 ? mb_substr($event->organizer->username, 0, 20, 'UTF-8') . '....' : $event->organizer->username }}</a>
                                                        @else
                                                            <span class="badge badge-success">{{ __('Admin') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ ucfirst($event->event_type) }}
                                                    </td>
                                                    <td>
                                                        {{ $event->category }}
                                                    </td>
                                                    <td>
                                                        {{ $event->start_date }} {{ $event->start_time }}
                                                    </td>
                                                    <td>
                                                        {{ $event->end_date }} {{ $event->end_time }}
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-secondary dropdown-toggle btn-sm"
                                                                type="button" id="dropdownMenuButton"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                {{ __('Select') }}
                                                            </button>

                                                            <div class="dropdown-menu"
                                                                aria-labelledby="dropdownMenuButton">
                                                                <a href="{{ route('admin.detail_event_participant', ['id' => $event->id]) }}"
                                                                    class="dropdown-item">
                                                                    {{ __('Detail Participant') }}
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
                </div>

                <div class="card-footer text-center">
                    <div class="d-inline-block mt-3">
                        {{ $events->appends([
                            'language' => request()->input('language'),
                            'title' => request()->input('title'),
                        ])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
