@extends('frontend.layout')
@section('pageHeading')
    {{ __('Reset Password') }}
@endsection
@section('hero-section')
    <!-- Page Banner Start -->
    <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
        data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}">
        <div class="container">
            <div class="banner-inner">
                <h2 class="page-title">{{ __('Reset Password') }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('index') }}">{{ __('Home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Reset Password') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>
    <!-- Page Banner End -->
@endsection
@section('content')
    <!-- LogIn Area Start -->
    <div class="login-area pt-115 rpt-95 pb-120 rpb-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    @if (Session::has('success'))
                        <div class="alert alert-success">{{ Session::get('success') }}</div>
                    @endif
                    @if (Session::has('alert'))
                        <div class="alert alert-danger">{{ Session::get('alert') }}</div>
                    @endif
                    <form id="login-form" name="login_form" class="login-form"
                        action="{{ route('organizer.update-forget-password') }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ request()->input('token') }}">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="password">{{ __('New Password' . ' *') }}</label>
                                {{-- <input type="password" name="password" id="password" class="form-control"
                                    placeholder="Enter Password" required> --}}
                                <div class="input-group mb-2">
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="{{ __('Enter Password') }}" required>
                                    <div class="input-group-prepend password-button-showhide" style="cursor:pointer">
                                        <div class="input-group-text" id="class-showhide-password">
                                            <i class="fa fa-eye"></i>
                                        </div>
                                    </div>
                                </div>
                                @error('password')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="re-password">{{ __('Confirm Password' . ' *') }}</label>
                                {{-- <input type="password" name="password_confirmation" id="re-password" class="form-control"
                                    placeholder="Confirm Password" required> --}}
                                <div class="input-group mb-2">
                                    <input type="password" name="password_confirmation" id="re-password"
                                        class="form-control" placeholder="{{ __('Confirm Password') }}" required>
                                    <div class="input-group-prepend repassword-button-showhide" style="cursor:pointer">
                                        <div class="input-group-text" id="class-showhide-repassword">
                                            <i class="fa fa-eye"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <button class="theme-btn br-30" type="submit"
                                data-loading-text="Please wait...">{{ __('Reset Password') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- LogIn Area End -->
@endsection

@section('custom-script')
    <script>
        const btnPassShowHide = document.querySelector(".password-button-showhide");
        const btnRePassShowHide = document.querySelector(".repassword-button-showhide");
        const inputPass = document.querySelector("#password");
        const inputRePass = document.querySelector("#re-password");
        const iconPass = document.querySelector("#class-showhide-password");
        const iconRePass = document.querySelector("#class-showhide-repassword");

        btnPassShowHide.addEventListener("click", function() {
            inputPass.type = inputPass.type === 'password' ? 'text' : 'password';
            iconPass.innerHTML = inputPass.type === 'password' ? `<i class="fa fa-eye"></i>` :
                `<i class="fa fa-eye-slash"></i>`
        });

        btnRePassShowHide.addEventListener("click", function() {
            inputRePass.type = inputRePass.type === 'password' ? 'text' : 'password';
            iconRePass.innerHTML = inputRePass.type === 'password' ? `<i class="fa fa-eye"></i>` :
                `<i class="fa fa-eye-slash"></i>`
        });
    </script>
@endsection
