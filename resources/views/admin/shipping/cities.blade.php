@extends('admin.partials.master')
@section('title')
    {{ __('Cities') }}
@endsection
@section('shipping_active')
    active
@endsection
@section('available-cities')
    active
@endsection
@php
    $a       = null;
    $q              = null;
    if(isset($_GET['a'])){
        $a          = $_GET['a'];
    }
    if(isset($_GET['q'])){
        $q          = $_GET['q'];
    }
@endphp
@section('main-content')
    <section class="section">
        <div class="section-body">
            <div class="d-flex justify-content-between">
                <div class="d-block">
                    <h2 class="section-title">{{ __('Cities') }}</h2>
                    <p class="section-lead">
                        {{ __('You have total') . ' ' . $cities->total() . ' ' . __('City') }}
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="{{ hasPermission('city_create') ? 'col-7 col-md-7 col-lg-7' : 'col-7 col-md-7 col-lg-8 middle' }}">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ __('Cities') }}</h4>
                            <div class="card-header-form">
                                <form class="form-inline" id="sorting">
                                    <div class="form-group">
                                        <select class="form-control select2 sorting" name="a">
                                            <option value="">{{ __('Filter By State') }}</option>
                                            @foreach($states as $key => $state)
                                                <option {{ $a != null ? ($state->id == $a ? "selected" : "" ) :''}} value="{{ $state->id }}">{{ $state->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="q" value="{{ $q != null ? $q : "" }}" placeholder="{{ __('Search') }}">
                                        <div class="input-group-btn">
                                            <button class="btn btn-outline-primary"><i class="bx bx-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-md">
                                    <tbody>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('State') }}</th>
                                        <th>{{ __('Country') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Cost') }}</th>
                                        @if(hasPermission('city_update') || hasPermission('city_delete'))
                                        <th>{{ __('Option') }}</th>
                                        @endif
                                    </tr>
                                    @foreach($cities as $key => $value)
                                        <tr id="{{ $key }}">
                                            <td> {{ $cities->firstItem() + $key }} </td>
                                            <td> {{ $value->name }} </td>
                                            <td> {{ @$value->state->name }} </td>
                                            <td> {{ @$value->country->name }} </td>
                                            <td> <label class="custom-switch mt-2 {{ hasPermission('city_update') ? '' : 'cursor-not-allowed' }}">
                                                    <input type="checkbox" name="custom-switch-checkbox" value="city-status-change/{{$value->id}}"
                                                            {{ hasPermission('city_update') ? '' : 'disabled' }}
                                                           {{ $value->status == 1 ? 'checked' : '' }} class="{{ hasPermission('city_update') ? 'status-change' : '' }} custom-switch-input">
                                                    <span class="custom-switch-indicator"></span>
                                                </label>
                                            </td>
                                            <td> {{ $value->cost }} </td>
                                            <td>
                                                @if(hasPermission('city_update'))
                                                    <a href="{{ route('city.edit', $value->id) }}" class="btn btn-outline-secondary btn-circle"
                                                        data-toggle="tooltip" title=""
                                                        data-original-title="{{ __('Edit') }}"><i class="bx bx-edit"></i>
                                                    </a>
                                                @endif
                                                @if(hasPermission('city_delete'))
                                                 <a href="javascript:void(0)"
                                                    onclick="delete_row('delete/cities/', {{ $value->id }})"
                                                    class="btn btn-outline-danger btn-circle" data-toggle="tooltip"
                                                    title="" data-original-title="{{ __('Delete') }}">
                                                    <i class='bx bx-trash'></i>
                                                </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <nav class="d-inline-block">
                                {{ $cities->appends(Request::except('page'))->links('pagination::bootstrap-4') }}
                            </nav>
                        </div>
                    </div>
                </div>
            @if(hasPermission('city_create'))
                <div class="col-5 col-md-5 col-lg-5">
                    <div class="card" >
                        <div class="card-header input-title">
                            <h4>{{ __('Add City') }}</h4>
                        </div>
                        <div class="card-body card-body-paddding">
                            <form method="POST" action="{{ route('city.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group" >
                                    <label for="state_id">{{ __('State') }}</label>
                                    <select class="form-control select2" name="state_id" id ="state_id" required>
                                        <option value="">{{ __('Select State') }}</option>
                                        @foreach($states as $key => $state)
                                            <option {{ old('state_id') ? ($state->id == old('state_id') ? "selected" : "" ) :''}} value="{{ $state->id }}">{{ $state->name }}</option>
                                        @endforeach

                                    </select>
                                    @if ($errors->has('state_id'))
                                        <div class="invalid-feedback">
                                            <p>{{ $errors->first('state_id') }}</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="name">{{ __('Name') }}</label>
                                    <input type="text" name="name" id="name" placeholder="{{__('Enter city name')}}" value="{{ old('name') }}" class="form-control" required>
                                    @if ($errors->has('name'))
                                        <div class="invalid-feedback">
                                            <p>{{ $errors->first('name') }}</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="cost">{{ __('Cost') }}</label>
                                    <input type="number" step="any" name="cost" id="cost" placeholder="{{ __('Cost on this city') }}" value="{{ old('cost') }}" class="form-control" required>
                                    @if ($errors->has('cost'))
                                        <div class="invalid-feedback">
                                            <p>{{ $errors->first('cost') }}</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="form-group text-right">
                                    <button type="submit" class="btn btn-outline-primary" tabindex="4">
                                        {{ __('Save') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            </div>
        </div>
    </section>
@endsection

@include('admin.common.delete-ajax')

