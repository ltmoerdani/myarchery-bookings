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

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">

                            <div class="float-left">
                                <div class="form-group">
                                    <form action="{{ route('admin.participant.export') }}" class="form-inline justify-content-lg-end justify-content-start">
                                        <div class="form-group">
                                        <input type="hidden" name="event" value="{{ $event_name }}" name="event" placeholder="Enter Event Name" class="form-control">
                                        <button type="submit" class="btn btn-success btn-sm ml-1" title="CSV Format">{{ __('Export List Participant') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="float-right">
                                <div class="form-group">
                                    <form action="" method="get">
                                        <input type="hidden" name="language" value="{{ request()->input('language') }}"
                                            class="hidden">
                                        <input type="text" name="title" value="{{ request()->input('title') }}"
                                            name="name" placeholder="Search" class="form-control">
                                    </form>
                                </div>
                            </div>

                            @if (count($participant) == 0)
                                <h3 class="text-center mt-2">{{ __('NO PARTICIPANT FOUND ') }}</h3>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped mt-3">
                                        <thead>
                                            <tr>
                                                <th scope="col">{{ __('No') }}</th>
                                                <th scope="col">{{ __('Event Title') }}</th>
                                                <th scope="col">{{ __('Participant Name') }}</th>
                                                <th scope="col">{{ __('Type') }}</th>
                                                <th scope="col">{{ __('Category') }}</th>
                                                <th scope="col">{{ __('Delegation') }}</th>
                                                <th scope="col">{{ __('Delegation Name') }}</th>
                                                <th scope="col">{{ __('Date Registered') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = ($participant->currentpage()-1)* $participant->perpage() + 1;
                                            @endphp
                                            @foreach ($participant as $p)
                                                <tr>
                                                    <td>{{ $no++ }}</td>
                                                    <td>
                                                        {{ $p->event_name }}
                                                    </td>
                                                    <td>
                                                        {{ $p->fname }}
                                                    </td>
                                                    <td>
                                                        {{ $p->competition_name }}
                                                    </td>
                                                    <td>
                                                        {{ $p->ticket_title }}
                                                    </td>
                                                    <td>
                                                        {{ $p->category }} 
                                                    </td>
                                                    <td>
                                                        {{ $p->delegation_name }} 
                                                    </td>
                                                    <td>
                                                        {{ $p->created_at }} 
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
                        {{ $participant->appends([
                            'language' => request()->input('language'),
                            'title' => request()->input('title'),
                        ])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
