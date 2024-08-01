@extends('organizer.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Withdraws') }}</h4>
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
        <a href="#">{{ __('Account Bank') }}</a>
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
                {{ __('My Withdraws') }}
              </div>
            </div>
            <div class="col-lg-4">
              <div class="card-title">{{ __('Your Balance') }} :
                {{ $settings->base_currency_symbol_position == 'left' ? $settings->base_currency_symbol : '' }}
                {{ Auth::guard('organizer')->user()->amount }}
                {{ $settings->base_currency_symbol_position == 'right' ? $settings->base_currency_symbol : '' }}</div>
            </div>
            <div class="col-lg-4 mt-2 mt-lg-0">

              <a href="{{ route('organizer.withdraw.addaccount', ['language' => $defaultLang->code]) }}"
                class="btn btn-secondary btn-sm float-lg-right float-left" style="margin-left: 10px;">
                <i class="fas fa-plus"></i> {{ __('Add Bank Account')."!" }}
              </a> 
 
              <a href="{{ route('organizer.withdraw.create', ['language' => $defaultLang->code]) }}"
                class="btn btn-secondary btn-sm float-lg-right float-left" style="margin-left: 10px;">
                <i class="fas fa-plus"></i> {{ __('Withdraw Now')."!" }}
              </a>

              <button class="btn btn-danger btn-sm float-lg-right float-left mr-2 d-none bulk-delete"
                data-href="{{ route('organizer.witdraw.bulk_delete_withdraw') }}">
                <i class="flaticon-interface-5"></i> {{ __('Delete') }}
              </button>
            </div>
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



            <div class="table-responsive">
              <table class="table table-striped mt-3" id="basic-datatables">
                <thead>
                  <tr>
                    <th scope="col">{{ __('No') }}</th>
                    <th scope="col">{{ __('Bank Name') }}</th>
                    <th scope="col">{{ __('Account Number') }}</th>
                    <th scope="col">{{ __('Account Name') }}</th>
                    <th scope="col">{{ __('Status') }}</th>
                    <th scope="col">{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                      $no = ($account->currentpage()-1)* $account->perpage() + 1;
                  @endphp
                  @foreach ($account as $item)
                    <tr>
                      <td>
                        {{ $no++ }}
                      </td>
                      <td>
                        {{ $item->bank->bank_name }}
                      </td>
                      <td>
                        {{ $item->account_no }}
                      </td>
                      <td>
                        {{ $item->account_name }}
                      </td>
                      <td>
                        @if ($item->is_active == 1)
                          Active
                        @else
                          Deactive
                        @endif
                      </td>
                      <td>
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#withdrawModal{{ $item->id }}"
                          class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>
                        <form class="deleteForm d-inline-block"
                          action="{{ route('organizer.witdraw.delete_withdraw', ['id' => $item->id]) }}" method="post">

                          @csrf
                          <button type="submit" class="btn btn-danger btn-sm deleteBtn"><i class="fas fa-trash"></i></button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="card-footer"></div>
    </div>
  </div>
  </div>
  @foreach ($account as $item)
    <div class="modal fade" id="withdrawModal{{ $item->id }}" tabindex="-1" role="dialog"
      aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="exampleModalLongTitle">{{ __('Withdraw Information') }}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
              {{ __('Close') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  @endforeach
@endsection
