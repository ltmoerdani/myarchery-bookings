@extends('backend.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@includeIf('backend.partials.rtl-style')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Hero Section') }}</h4>
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
                <a href="#">{{ __('Home Page') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Hero Section') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="card-title">{{ __('Update Hero Section') }}</div>
                        </div>

                        <div class="col-lg-2">
                            @includeIf('backend.partials.languages')
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8 offset-lg-2">
                            <label for="" class="mb-2">
                                <strong>{{ __('Background Image') }} *</strong>
                            </label>
                            <div id="reload-slider-div">
                                <div class="row mt-2">
                                    <div class="col">
                                        <table class="table" id="img-table">

                                        </table>
                                    </div>
                                </div>
                            </div>
                            <form action="{{ route('admin.home_page.upload_image_hero_section') }}" id="my-dropzone"
                                enctype="multipart/formdata" class="dropzone create">
                                @csrf
                                <div class="fallback">
                                    <input name="file" type="file" multiple />
                                </div>
                                <input type="hidden" value="" name="event_id">
                            </form>
                            <div class=" mb-0" id="errpreimg">

                            </div>
                        </div>
                        <div class="col-lg-8 offset-lg-2">
                            <form id="heroForm"
                                action="{{ route('admin.home_page.update_hero_section', ['language' => request()->input('language')]) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                {{-- <div class="form-group">
                                    <label for="">{{ __('Background Image') . '*' }}</label>
                                    <br>
                                    <div class="thumb-preview">
                                        @if (!empty($data->background_image))
                                            <img src="{{ asset('assets/admin/img/hero-section/' . $data->background_image) }}"
                                                alt="background image" class="uploaded-background-img">
                                        @else
                                            <img src="{{ asset('assets/admin/img/noimage.jpg') }}" alt="..."
                                                class="uploaded-background-img">
                                        @endif
                                    </div>

                                    <div class="mt-3">
                                        <div role="button" class="btn btn-primary btn-sm upload-btn">
                                            {{ __('Choose Image') }}
                                            <input type="file" class="background-img-input" name="background_image">
                                        </div>
                                    </div>
                                    @error('background_image')
                                        <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                    @enderror
                                </div> --}}

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">{{ __('First Title') }}</label>
                                            <input type="text" class="form-control" name="first_title"
                                                value="{{ empty($data->first_title) ? '' : $data->first_title }}"
                                                placeholder="{{ __('Enter First Title') }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">{{ __('Second Title') }}</label>
                                            <input type="text" class="form-control" name="second_title"
                                                value="{{ empty($data->second_title) ? '' : $data->second_title }}"
                                                placeholder="{{ __('Enter Second Title') }}">
                                        </div>
                                    </div>
                                </div>

                                @if ($themeInfo->theme_version == 1 || $themeInfo->theme_version == 3)
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="">{{ __('Button Text') }}</label>
                                                <input type="text" class="form-control" name="first_button"
                                                    value="{{ empty($data->first_button) ? '' : $data->first_button }}"
                                                    placeholder="Enter First Button Name">
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($themeInfo->theme_version == 2)
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="">{{ __('Video URL') }}</label>
                                                <input type="url" class="form-control ltr" name="video_url"
                                                    value="{{ empty($data->video_url) ? '' : $data->video_url }}"
                                                    placeholder="{{ __('Enter Video URL') }}">
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($themeInfo->theme_version == 3)
                                    <div class="form-group">
                                        <label for="">{{ __('Image') }}</label>
                                        <br>
                                        <div class="thumb-preview">
                                            @if (!empty($data->image))
                                                <img src="{{ asset('assets/admin/img/hero-section/' . $data->image) }}"
                                                    alt="image" class="uploaded-img">
                                            @else
                                                <img src="{{ asset('assets/admin/img/noimage.jpg') }}" alt="..."
                                                    class="uploaded-img">
                                            @endif
                                        </div>

                                        <div class="mt-3">
                                            <div role="button" class="btn btn-primary btn-sm upload-btn">
                                                {{ __('Choose Image') }}
                                                <input type="file" class="img-input" name="image">
                                            </div>
                                        </div>
                                        @error('image')
                                            <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" form="heroForm" class="btn btn-success">
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
    @endphp
    <script>
        let languages = "{{ $languages }}";
    </script>
    <script src="{{ asset('assets/admin/js/hero_section_dropzone.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script>
@endsection

@section('variables')
    <script>
        "use strict";
        var languageCode = "{{ $language->code }}";
        var storeUrl = "{{ route('admin.home_page.upload_image_hero_section') }}";
        var removeUrl = "{{ route('admin.home_page.imagermvherosection') }}";

        var rmvdbUrl = "{{ route('admin.home_page.imgdbrmv') }}";
        var loadImgs = "{{ route('admin.home_page.images_hero_section', $language->code) }}";
    </script>
@endsection
