<!DOCTYPE html>
<html lang="en">
<head>
    <!-- CSS Libraries -->
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>{{ __('Login') }} | {{ settingHelper('admin_panel_title') != '' ?  settingHelper('admin_panel_title') : __('Yoori') }}</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ static_asset('admin/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ static_asset('admin/css/toastr.min.css') }}">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ static_asset('admin/css/yoori.css') }}">
    <link rel="stylesheet" href="{{ static_asset('admin/css/style.css') }}">
    <link rel="stylesheet" href="{{ static_asset('admin/css/custom.css') }}">
</head>
@php
$path_check = request()->path();
@endphp
<body>
<div id="app">
    <section class="section">
        <div class="container mt-5">
            <div class="row">
                <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                    <div class="login-brand">
                        @php
                            $logo = settingHelper('admin_dark_logo')
                        @endphp
                        <img src="{{($logo != [] && @is_file_exists($logo['image_100x38'])) ? static_asset($logo['image_100x38']) : static_asset('images/default/dark-logo.png') }}" alt="logo" width="100">
                    </div>

                    <div class="card card-primary">
                        <div class="card-header"><h4>{{ __('Login') }}</h4></div>

                        <div class="card-body">
                            <form method="POST" class="login_form" action="{{route('admin.seller.login')}}" >
                                @csrf
                                <div class="form-group">
                                    <label for="email">{{__('Email')}}</label>
                                    <input id="email" type="email" class="form-control" value="{{isDemoServer() ? request()->path() == 'admin/login' ? 'admin@spagreen.net' : (request()->path() == 'seller/login' ? 'seller@spagreen.net' : old('email')) : ''}}" name="email" tabindex="1"
                                           required autofocus>
                                    @if($errors->has('email'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('email') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <div class="d-block">
                                        <label for="password" class="control-label">{{__('Password')}}</label>
                                    </div>
                                    <input id="password"  value="{{isDemoServer() ? request()->path() == 'admin/login' ? '123456' : (request()->path() == 'seller/login' ? '123456' : old('password')) : ''}}" type="password" class="form-control" name="password"
                                           tabindex="2" required>
                                    @if($errors->has('password'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('password') }}
                                        </div>
                                    @endif
                                </div>

                                @if( settingHelper('is_recaptcha_activated') == 1 )
                                    <div class="col-lg-12 text-center mb-4">
                                        <div class="row">
                                            {!! NoCaptcha::renderJs() !!}
                                            {!! NoCaptcha::display() !!}
                                        </div>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
                                        <label class="custom-control-label" for="remember-me">{{__('Remember Me')}}</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                                        {{__('Login')}}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
</div>
<script src="{{ static_asset('admin/js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ static_asset('admin/js/bootstrap.min.js') }}"></script>

<script type="text/javascript" src="{{ static_asset('admin/js/toastr.min.js') }}"></script>
{!! Toastr::message() !!}
<!-- Template JS File -->
<script src="{{ static_asset('admin/js/scripts.js') }}"></script>
@include('admin.partials.message')

<!-- Page Specific JS File -->
</body>
</html>
