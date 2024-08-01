@extends('organizer.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Add Account Bank') }}</h4>
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
        <a href="#">{{ __('Add Account Bank') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-8">
              <div class="card-title">{{ __('Add Account Bank') }}</div>
            </div>
            <!-- <div class="col-lg-4">
              <div class="card-title float-left float-lg-right">{{ __('Your Balance') }} :
                {{ $settings->base_currency_symbol_position == 'left' ? $settings->base_currency_symbol : '' }}
                {{ Auth::guard('organizer')->user()->amount }}
                {{ $settings->base_currency_symbol_position == 'right' ? $settings->base_currency_symbol : '' }}</div>
            </div> -->
          </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-lg-6 offset-lg-3">
              <form id="ajaxForm" action="{{ route('organizer.withdraw.save-account') }}" method="POST" enctype="multipart/form-data">
                @csrf

                @if ($errors->any())
                  <div class="alert alert-danger">
                    <p><strong>Opps Something went wrong</strong></p>
                    <ul>
                      @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif

                <div class="form-group">
                  <label for="">{{ __('Bank Name') }} <span class="text-danger">*</span></label>
                  <select name="bank_name" id="bank_name" class="form-control" required>
                    <option selected disabled value="">{{ __('Select Bank Name') }}</option>
                    @foreach ($bank as $item)
                      <option value="{{ $item->id }}">{{ $item->bank_name }}</option>
                    @endforeach
                  </select>
                  <p id="err_bank_name" class="mt-2 mb-0 text-danger em"></p>
                </div>

                <div class="form-group">
                  <label>{{ __('Account Number') }} <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="account_number" placeholder="{{ __('Enter Account Number') }}">
                  @if ($errors->has('account_number'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('account_number') }}</p>
                  @endif
                </div>
                <div class="form-group">
                  <label>{{ __('Account Name') }} <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="account_name" placeholder="{{ __('Enter Account Name') }}">
                  @if ($errors->has('account_name'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('account_name') }}</p>
                  @endif
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="row">
            <div class="col-12 text-center">
              <button type="submit" id="submitBtn" class="btn btn-success">
                {{ __('Save Account') }}
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
@endsection
@section('script')
  <script src="{{ asset('assets/admin/js/organizer-withdraw.js') }}"></script>
@endsection
