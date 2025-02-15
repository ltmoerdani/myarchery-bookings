@extends('backend.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Detail Participant Event') }}</h4>
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
          <a href="{{ route('admin.event_management.event', ['language' => $defaultLang->code]) }}">{{ __('All Events') }}</a>
      </li>
      <li class="separator">
            <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
            <a href="#">{{ __('Detail Participant Event') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-4">
              <div class="card-title d-inline-block">{{ __('Detail Participant Event') }}</div>
              <div class="card-title d-inline-block">{{ $event_title }}</div>
            </div>

            <div class="col-lg-4">
              <form action="" method="get">
                <input type="text" value="{{ request()->input('event_name') }}" name="event_name"
                  placeholder="Search" class="form-control">
              </form>
            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($participant) == 0)
                <h3 class="text-center mt-3">{{ __('NO PARTICIPANT FOUND') . '!' }}</h3>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-3">
                    <thead>
                      <tr>
                        <th scope="col">{{ __('No') }}</th>
                        <th scope="col">{{ __('Participant Name') }}</th>
                        <th scope="col">{{ __('Type') }}</th>
                        <th scope="col">{{ __('Category') }}</th>
                        <!-- <th scope="col">{{ __('Delegation') }}</th> -->
                        <th scope="col">{{ __('Delegation Name') }}</th>
                        <th scope="col">{{ __('Date Registered') }}</th>
                        <th scope="col">{{ __('Status') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php
                        $no = ($participant->currentpage()-1)* $participant->perpage() + 1;
                      @endphp
                      @foreach ($participant as $p)
                        <tr>
                          <td>{{ $no++ }}</td>
                          <td>{{ $p->fname }}</td>
                          <td>{{ $p->competition_type }}</td>
                          <td>{{ $p->ticket_title }}</td>
                          <!-- <td>{{ $p->category }}</td> -->
                          <td>{{ $p->delegation_name }}</td>
                          <td>{{ $p->created_at }}</td>
                          <td>
                            <form id="statusForm-{{ $p->id }}" class="d-inline-block" action="{{ route('admin.update_participant', ['id' => $p->id, 'language' => request()->input('language')]) }}" method="post">
                                @csrf
                                <select
                                    @if ($p->status == 3)
                                        class="form-control form-control-sm bg-danger text-dark"
                                    @elseif ($p->status == 2)
                                        class="form-control form-control-sm bg-warning text-dark"
                                    @else
                                        class="form-control form-control-sm bg-primary"
                                    @endif
                                    name="status"
                                    onchange="document.getElementById('statusForm-{{ $p->id }}').submit()">
                                    <option value="1"
                                        {{ $p->status == 1 ? 'selected' : '' }}>
                                        {{ __('Active') }}
                                    </option>
                                    <option value="2"
                                        {{ $p->status == 2 ? 'selected' : '' }}>
                                        {{ __('Cancel') }}
                                    </option>
                                    <option value="3"
                                        {{ $p->status == 3 ? 'selected' : '' }}>
                                        {{ __('Refund') }}
                                    </option>
                                </select>
                            </form>
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
                    'event_name' => request()->input('event_name'),
                ])->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
