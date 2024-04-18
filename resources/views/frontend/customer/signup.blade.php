@extends('frontend.layout')
@section('pageHeading')
    @if (!empty($pageHeading))
        {{ $pageHeading->customer_signup_page_title ?? __('Customer Signup') }}
    @else
        {{ __('Customer Signup') }}
    @endif
@endsection
@php
    $metaKeywords = !empty($seo->meta_keyword_customer_signup) ? $seo->meta_keyword_customer_signup : '';
    $metaDescription = !empty($seo->meta_description_customer_signup) ? $seo->meta_description_customer_signup : '';
@endphp
@section('meta-keywords', "{{ $metaKeywords }}")
@section('meta-description', "$metaDescription")
@section('custom-style')
    <style>
        .select2-container .select2-selection--single {
            box-sizing: border-box !important;
            cursor: pointer !important;
            display: block !important;
            height: 56px !important;
            user-select: none !important;
            -webkit-user-select: none !important;
            padding: 15px 25px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 26px !important;
            position: absolute !important;
            top: 1px !important;
            right: 1px !important;
            width: 20px !important;
            padding: 15px 25px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #888 transparent transparent transparent !important;
            border-style: solid !important;
            border-width: 5px 4px 0 4px !important;
            height: 0 !important;
            left: 50% !important;
            margin-left: 15px !important;
            margin-top: 15px !important;
            position: absolute !important;
            top: 50% !important;
            width: 0 !important;
        }
    </style>
@endsection
{{-- @section('hero-section')
  <!-- Page Banner Start -->
  <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
    data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}">
    <div class="container">
      <div class="banner-inner">
        <h2 class="page-title">
          @if (!empty($pageHeading))
            {{ $pageHeading->customer_signup_page_title ?? __('Customer Signup') }}
          @else
            {{ __('Customer Signup') }}
          @endif
        </h2>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item active">
              @if (!empty($pageHeading))
                {{ $pageHeading->customer_signup_page_title ?? __('Customer Signup') }}
              @else
                {{ __('Customer Signup') }}
              @endif
            </li>
          </ol>
        </nav>
      </div>
    </div>
  </section>
  <!-- Page Banner End -->
@endsection --}}
@section('content')
    <!-- SignUp Area Start -->
    <div class="signup-area pt-115 rpt-95 pb-120 rpb-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <form id="login-form" name="login_form" class="login-form" action="{{ route('customer.create') }}"
                        method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="fname"> {{ __('Full Name') }} *</label>
                                    <input type="text" name="fname" id="fname" value="{{ old('fname') }}"
                                        class="form-control" placeholder="{{ __('Enter Your Full Name') }}">
                                    @error('fname')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="email">{{ __('Email') }} *</label>
                                    <input type="email" name="email" value="{{ old('email') }}" id="email"
                                        class="form-control" placeholder="{{ __('Enter Your Email Address') }}">
                                    @error('email')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            {{-- <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="username">{{ __('Username') }} *</label>
                                    <input type="text" name="username" value="{{ old('username') }}" id="username"
                                        class="form-control" placeholder="{{ __('Enter Username') }}">
                                    @error('username')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div> --}}
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="gender"> {{ __('Gender') }} *</label>
                                    <!-- <input type="text" name="gender" id="gender" value="{{ old('gender') }}" class="form-control"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            placeholder="{{ __('Enter Your Last Name') }}"> -->
                                    <select class="form-select" aria-label="gender" name="gender" id="gender"
                                        class="form-control">
                                        <option selected>Choose</option>
                                        <option value="M">{{ __('Male') }}</option>
                                        <option value="F">{{ __('Female') }}</option>
                                    </select>
                                    @error('gender')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="phone"> {{ __('Phone Number') }} *</label>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                        class="form-control" placeholder="{{ __('Enter Your Phone Number') }}">
                                    @error('phone')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="birthdate">{{ __('Birth Date') }} *</label>
                                    <input type="date" name="birthdate" value="{{ old('birthdate') }}" id="birthdate"
                                        class="form-control" placeholder="{{ __('DD/MM/YY') }}">
                                    @error('birthdate')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="country">{{ __('Country') }} *</label>
                                    <select class="form-select js-example-basic-single" aria-label="country" name="country"
                                        id="countries" data-placeholder="Choose country" class="form-control">
                                        @foreach ($country as $d)
                                            <option value="">Choose</option>
                                            <option value="{{ json_encode($d) }}">
                                                {{ $d->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('country')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="state">{{ __('State') }} *</label>
                                    <select class="form-select js-example-basic-single" data-placeholder="Choose state"
                                        aria-label="states" name="states" id="states" class="form-control">
                                        <option value="" selected disabled>Choose State</option>
                                    </select>
                                    @error('state')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <input type="hidden" class="form-control state" name="state" id="state" />
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="password">{{ __('Password') }} *</label>
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="{{ __('Enter Password') }}">
                                    @error('password')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="re-password">{{ __('Re-enter Password') }} *</label>
                                    <input type="password" name="password_confirmation" id="re-password"
                                        class="form-control" placeholder="{{ __('Re-enter Password') }}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                @if ($basicInfo->google_recaptcha_status == 1)
                                    <div class="form-group">
                                        {!! NoCaptcha::renderJs() !!}
                                        {!! NoCaptcha::display() !!}
                                        @error('g-recaptcha-response')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <button class="theme-btn br-30 showLoader" type="submit">{{ __('Signup') }}</button>
                        </div>
                        <div class="form-group mt-3 mb-0">
                            <p>{{ __('Already have an account') }} ?<a class="text-info"
                                    href="{{ route('customer.login') }}">{{ __('Login Now') }}</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- SignUp Area End -->
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2({
                selectionCssClass: 'form-select',
            });
        });

        $("#countries").on("change", function() {
            const chooseCountry = $(this).val();
            $('#states').html('');
            $('#state').val('');
            if (chooseCountry) {
                const id = JSON.parse(chooseCountry).id;
                $.ajax({
                    type: 'GET',
                    url: `${baseUrl}/customer/get-state/${id}`,
                    success: function(response) {
                        if (response.data.length > 0) {
                            $('#states').append('<option selected disabled>Choose state</option>');
                            $.each(response.data, function(index, values) {
                                $('#states').append(
                                    `<option value="${values.id}">${values.name}</option>`
                                );
                            })
                        } else {
                            $('#states').html('');
                            $('#state').val('');
                        }
                    }
                });
            }
        })
        $("#states").on("change", function() {
            const id = $(this).val();
            if (id) {
                const nameSelected = $("#states option:selected").text();
                const mapValue = {
                    id,
                    name: nameSelected
                };
                $(".state").val(JSON.stringify(mapValue));
            }
        })
    </script>
@endsection
