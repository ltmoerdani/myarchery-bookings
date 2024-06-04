@extends('backend.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@includeIf('backend.partials.rtl-style')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Club Management') }}</h4>
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
                <a href="#">{{ __('Club Management') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <div class="card-title d-inline-block">{{ __('Clubs') }}</div>
                        <div>
                            <a href="#" data-toggle="modal" data-target="#createModal"
                                class="btn btn-primary btn-sm float-lg-right float-left"><i class="fas fa-plus"></i>
                                {{ __('Add Club') }}</a>
                            <button class="btn btn-danger float-right btn-sm mr-2 d-none bulk-delete"
                                data-href="{{ route('admin.club_management.bulk_delete_club') }}">
                                <i class="flaticon-interface-5"></i> {{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (count($clubs) == 0)
                                <h3 class="text-center">{{ __('NO Club FOUND') . '!' }}</h3>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped mt-3" id="basic-datatables">
                                        <thead>
                                            <tr>
                                                <th scope="col">
                                                    <input type="checkbox" class="bulk-check" data-val="all">
                                                </th>
                                                <th scope="col">{{ __('Logo') }}</th>
                                                <th scope="col">{{ __('Name') }}</th>
                                                <th scope="col">{{ __('Place Name') }}</th>
                                                <th scope="col">{{ __('Address') }}</th>
                                                <th scope="col">{{ __('Description') }}</th>
                                                <th scope="col">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($clubs as $club)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="bulk-check"
                                                            data-val="{{ $club->id }}">
                                                    </td>
                                                    <td>
                                                        @if (empty($club->logo))
                                                            <img src="{{ asset('assets/admin/img/noimage.jpg') }}"
                                                                alt="" class="rounded-circle customer-img">
                                                        @else
                                                            <img src="{{ $club->logo }}" alt="logo"
                                                                class="rounded-circle customer-img">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $club->name }}
                                                    </td>
                                                    <td>
                                                        {{ $club->place_name }}
                                                    </td>
                                                    <td>
                                                        {{ $club->address }}
                                                    </td>
                                                    <td>
                                                        {{ $club->description }}
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-row">
                                                            <a class="btn btn-secondary mt-1 btn-xs mr-1 editBtn"
                                                                href="#" data-toggle="modal" data-target="#editModal"
                                                                data-id="{{ $club->id }}"
                                                                data-name="{{ $club->name }}"
                                                                data-place_name="{{ $club->place_name }}"
                                                                data-address="{{ $club->address }}"
                                                                data-description="{{ $club->description }}"
                                                                data-club_logo="{{ $club->logo }}">
                                                                <span class="btn-label">
                                                                    <i class="fas fa-edit"></i>
                                                                </span>
                                                            </a>

                                                            <form class="deleteForm d-inline-block"
                                                                action="{{ route('admin.club_management.delete_club', ['id' => $club->id]) }}"
                                                                method="post">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="btn btn-danger mt-1 btn-xs deleteBtn">
                                                                    <span class="btn-label">
                                                                        <i class="fas fa-trash"></i>
                                                                    </span>
                                                                </button>
                                                            </form>
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

                <div class="card-footer"></div>
            </div>
        </div>
    </div>
    @include('backend.club.create')
    @include('backend.club.edit')
@endsection
