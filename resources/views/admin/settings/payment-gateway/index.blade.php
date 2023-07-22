@extends('admin.partials.master')
@section('payment-gateway')
    active
@endsection
@section('payment-gateway')
    active
@endsection
@section('title')
    {{ __('Payment-Gateway') }}
@endsection
@section('main-content')
    <section class="section">
        <div class="section-body">
            <h2 class="section-title">{{ __('Payment Methods') }}</h2>
            <div class="row">
                <div class="col-12 col-sm-12 col-md-4 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link {{ old('payment_method') == '' || old('payment_method') == 'paypal' ? 'active' : ''}}"
                                       id="paypal-tab" data-toggle="tab" href="#paypal" role="tab"
                                       aria-controls="contact" aria-selected="false">{{ __('Paypal') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ old('payment_method') == 'stripe' ? 'active' : '' }}"
                                       id="stripe-tab" data-toggle="tab" href="#stripe" role="tab"
                                       aria-controls="contact" aria-selected="false">{{ __('Stripe') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ old('payment_method')  == 'sslcommerz' ? 'active' : ''}}"
                                       id="sslcommerz-tab" data-toggle="tab" href="#sslcommerz" role="tab"
                                       aria-controls="contact" aria-selected="false">{{ __('SSLCOMMERZ') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ old('payment_method') == 'paytm' ? 'active' : '' }}"
                                       id="paytm-tab" data-toggle="tab" href="#paytm" role="tab" aria-controls="profile"
                                       aria-selected="false">{{ __('Paytm') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ old('payment_method') == 'jazz_cash' ? 'active' : ''}}"
                                       id="jazz_cash-tab" data-toggle="tab" href="#jazz_cash" role="tab"
                                       aria-controls="profile" aria-selected="false">{{ __('JazzCash') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ old('payment_method') == 'razorpay' ? 'active' : ''}}"
                                       id="razorpay-tab" data-toggle="tab" href="#razorpay" role="tab"
                                       aria-controls="profile" aria-selected="false">{{ __('Razorpay') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ old('payment_method') == 'Mollie' ? 'active' : ''}}"
                                       id="mollie-tab" data-toggle="tab" href="#mollie" role="tab"
                                       aria-controls="profile" aria-selected="false">{{ __('Mollie') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ old('payment_method') == 'paystack' ? 'active' : ''}}"
                                       id="paystack-tab" data-toggle="tab" href="#paystack" role="tab"
                                       aria-controls="profile" aria-selected="false">{{ __('Paystack') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ old('payment_method') == 'flutterwave' ? 'active' : ''}}"
                                       id="flutterwave-tab" data-toggle="tab" href="#flutterwave" role="tab"
                                       aria-controls="profile" aria-selected="false">{{ __('Flutter Wave') }}</a>
                                </li>
<!--                                <li class="nav-item">
                                    <a class="nav-link {{ old('payment_method') == 'mercado_pago' ? 'active' : ''}}"
                                       id="mercado_pago-tab" data-toggle="tab" href="#mercado_pago" role="tab"
                                       aria-controls="profile" aria-selected="false">{{ __('mercado_pago') }}</a>
                                </li>-->
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-8 col-lg-9">
                    <div class="tab-content no-padding" id="myTab2Content">
                        <div
                            class="tab-pane fade {{ old('payment_method') == '' || old('payment_method') == 'paypal' ? 'show active' : ''}}"
                            id="paypal" role="tabpane1" aria-labelledby="paypal-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h4>{{ __('Paypal Setting') }}</h4>
                                </div>
                                <div class="card-body col-md-10 middle">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6 pl-0">
                                                <div class="form-group">
                                                    <label
                                                        class="custom-switch mt-2 {{ hasPermission('payment_gateway_update') ? '' : 'cursor-not-allowed' }}">
                                                        <input type="checkbox"
                                                               name="custom-switch-checkbox"
                                                               value="admin-payment-status-change/{{ 'is_paypal_activated' }}"
                                                               {{ hasPermission('payment_gateway_update') ? '' : 'disabled' }}
                                                               class="{{ hasPermission('payment_gateway_update') ? 'status-change' : '' }} custom-switch-input " {{ settingHelper('is_paypal_activated') == 1 ? 'checked' : ''}} />
                                                        <span class="custom-switch-indicator"></span>
                                                        <span
                                                            class="custom-switch-description">{{ __('Activate') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if(hasPermission('payment_gateway_update'))
                                        <form action="{{ route('payment.gateway.update') }}" method="post"
                                              enctype="multipart/form-data">
                                            @method('put')
                                            @csrf
                                            @endif
                                            <div class="form-group">
                                                <label for="paypal_client_id">{{ __('Client ID ') }}</label>
                                                <input type="hidden" name="payment_method" value="paypal">
                                                <input type="text" class="form-control" name="paypal_client_id"
                                                       id="paypal_client_id"
                                                       value="{{ old('paypal_client_id') ? old('paypal_client_id') : settingHelper('paypal_client_id') }}"
                                                       placeholder="{{ __('Paypal Client ID') }}">
                                                @if ($errors->has('paypal_client_id'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('paypal_client_id') }}
                                                    </div>
                                                @endif
                                            </div>
                                            @if(hasPermission('payment_gateway_update'))
                                                <div class="text-md-right">
                                                    <button class="btn btn-outline-primary">{{ __('Save') }}</button>
                                                </div>
                                            @endif
                                            @if(hasPermission('payment_gateway_update'))
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade {{ old('payment_method') == 'stripe' ? 'show active' : '' }}"
                             id="stripe" role="tab" aria-labelledby="stripe-tab">
                            <div class="card">
                                <div class="card-header extra-padding">
                                    <h4>{{ __('Stripe Setting') }}</h4>
                                </div>
                                <div class="card-body col-md-10 middle">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6 pl-0">
                                                <div class="form-group">
                                                    <label
                                                        class="custom-switch mt-2 {{ hasPermission('payment_gateway_update') ? '' : 'cursor-not-allowed' }}">
                                                        <input type="checkbox"
                                                               name="custom-switch-checkbox"
                                                               value="admin-payment-status-change/{{ 'is_stripe_activated' }}"
                                                               {{ hasPermission('payment_gateway_update') ? '' : 'disabled' }}
                                                               class=" {{ hasPermission('payment_gateway_update') ? 'status-change' : '' }} custom-switch-input " {{ settingHelper('is_stripe_activated') == 1 ? 'checked' : ''}} />
                                                        <span class="custom-switch-indicator"></span>
                                                        <span
                                                            class="custom-switch-description">{{ __('Activate') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if(hasPermission('payment_gateway_update'))
                                        <form action="{{ route('payment.gateway.update') }}" method="post"
                                              enctype="multipart/form-data">
                                            @method('put')
                                            @csrf
                                            @endif
                                            <div class="form-group">
                                                <label for="stripe_key">{{ __('Stripe Key') }}</label>
                                                <input type="hidden" name="payment_method" value="stripe">
                                                <input type="text" class="form-control" name="stripe_key"
                                                       id="stripe_key"
                                                       value="{{ old('stripe_key') ? old('stripe_key') : settingHelper('stripe_key') }}"
                                                       placeholder="{{ __('Stripe Client Key') }}">
                                                @if ($errors->has('stripe_key'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('stripe_key') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="stripe_secret">{{ __('Stripe Secret') }}</label>
                                                <input type="text" class="form-control" id="stripe_secret"
                                                       name="stripe_secret"
                                                       value="{{ old('stripe_secret') ? old('stripe_secret') : settingHelper('stripe_secret') }}"
                                                       placeholder="{{ __('Stripe Client Secret') }}">
                                                @if ($errors->has('stripe_secret'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('stripe_secret') }}
                                                    </div>
                                                @endif
                                            </div>
                                            @if(hasPermission('payment_gateway_update'))
                                                <div class="text-md-right">
                                                    <button class="btn btn-outline-primary">{{ __('Save') }}</button>
                                                </div>
                                            @endif
                                            @if(hasPermission('payment_gateway_update'))
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade {{ old('payment_method')  == 'sslcommerz' ? 'show active' : ''}}"
                             id="sslcommerz" role="tab" aria-labelledby="sslcommerz-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h4>{{ __('SSLCOMMERZ Setting') }}</h4>
                                </div>
                                <div class="card-body col-md-10 middle">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="invalid-feedback text-danger">
                                                    {{ __("N.B: Before activating SSLCOMMERZ, please make sue you have Bangladeshi currency BDT activated") }}
                                                </div>
                                            </div>
                                            <div class="col-6 pl-0">
                                                <div class="form-group">
                                                    <label
                                                        class="custom-switch mt-2 {{ hasPermission('payment_gateway_update') ? '' : 'cursor-not-allowed' }}">
                                                        <input type="checkbox"
                                                               name="custom-switch-checkbox"
                                                               value="admin-payment-status-change/{{ 'is_sslcommerz_activated' }}"
                                                               {{ hasPermission('payment_gateway_update') ? '' : 'disabled' }}
                                                               class="{{ hasPermission('payment_gateway_update') ? 'status-change' : '' }} custom-switch-input " {{ settingHelper('is_sslcommerz_activated') == 1 ? 'checked' : ''}} />
                                                        <span class="custom-switch-indicator"></span>
                                                        <span
                                                            class="custom-switch-description">{{ __('Activate') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label
                                                        class="custom-switch mt-2 {{ hasPermission('payment_gateway_update') ? '' : 'cursor-not-allowed' }}">
                                                        <input type="checkbox"
                                                               name="custom-switch-checkbox"
                                                               value="admin-payment-status-change/{{ 'is_sslcommerz_sandbox_mode_activated' }}"
                                                               {{ hasPermission('payment_gateway_update') ? '' : 'disabled' }}
                                                               class="{{ hasPermission('payment_gateway_update') ? 'status-change' : '' }} custom-switch-input " {{ settingHelper('is_sslcommerz_sandbox_mode_activated') == 1 ? 'checked' : ''}} />
                                                        <span class="custom-switch-indicator"></span>
                                                        <span
                                                            class="custom-switch-description">{{ __('Sandbox Mode') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if(hasPermission('payment_gateway_update'))
                                        <form action="{{ route('payment.gateway.update') }}" method="post"
                                              enctype="multipart/form-data">
                                            @method('put')
                                            @csrf
                                            @endif
                                            <div class="form-group">
                                                <label for="sslcommerz_id">{{ __('SSLCOMMERZ ID') }}</label>
                                                <input type="hidden" name="payment_method" value="sslcommerz">
                                                <input type="text" class="form-control" id="sslcommerz_id"
                                                       name="sslcommerz_id"
                                                       value="{{ old('sslcommerz_id') ? old('sslcommerz_id') : settingHelper('sslcommerz_id') }}"
                                                       placeholder="{{ __('SSLCOMMERZ Store Id') }}">
                                                @if ($errors->has('sslcommerz_id'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('sslcommerz_id') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="sslcommerz_password">{{ __('SSLCOMMERZ Password') }}</label>
                                                <input type="password" class="form-control" id="sslcommerz_password"
                                                       name="sslcommerz_password"
                                                       value="{{ old('sslcommerz_password') ? old('sslcommerz_password')  : settingHelper('sslcommerz_password') }}"
                                                       placeholder="{{ __('SSLCOMMERZ Store Password') }}">
                                                @if ($errors->has('sslcommerz_password'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('sslcommerz_password') }}
                                                    </div>
                                                @endif
                                            </div>
                                            @if(hasPermission('payment_gateway_update'))
                                                <div class="text-md-right">
                                                    <button class="btn btn-outline-primary">{{ __('Save') }}</button>
                                                </div>
                                            @endif
                                            @if(hasPermission('payment_gateway_update'))
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade {{ old('payment_method') == 'paytm' ? 'show active' : '' }}"
                             id="paytm" role="tab" aria-labelledby="paytm-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h4>{{ __('Paytm Setting') }}</h4>
                                </div>
                                <div class="card-body col-md-10 middle">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="invalid-feedback text-danger">
                                                    {{ __("N.B: Before activating Paytm, please make sue you have Indian Rupee currency INR activated") }}
                                                </div>
                                            </div>
                                            <div class="col-6 pl-0">
                                                <div class="form-group">
                                                    <label
                                                        class="custom-switch mt-2 {{ hasPermission('payment_gateway_update') ? '' : 'cursor-not-allowed' }}">
                                                        <input type="checkbox"
                                                               name="custom-switch-checkbox"
                                                               value="admin-payment-status-change/{{ 'is_paytm_activated' }}"
                                                               {{ hasPermission('payment_gateway_update') ? '' : 'disabled' }}
                                                               class="{{ hasPermission('payment_gateway_update') ? 'status-change' : '' }} custom-switch-input " {{ settingHelper('is_paytm_activated') == 1 ? 'checked' : ''}} />
                                                        <span class="custom-switch-indicator"></span>
                                                        <span
                                                            class="custom-switch-description">{{ __('Activate') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if(hasPermission('payment_gateway_update'))
                                        <form action="{{ route('payment.gateway.update') }}" method="post"
                                              enctype="multipart/form-data">
                                            @method('put')
                                            @csrf
                                            @endif
                                            <div class="form-group">
                                                <label for="merchant_id">{{ __('MarChant ID') }}</label>
                                                <input type="hidden" name="payment_method" value="paytm">
                                                <input type="text" class="form-control" id="merchant_id"
                                                       name="merchant_id"
                                                       value="{{ old('merchant_id') ? old('merchant_id') : settingHelper('merchant_id') }}"
                                                       placeholder="{{ __('Merchant Id') }}">
                                                @if ($errors->has('merchant_id'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('merchant_id') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="merchant_key">{{ __('MarChant Key') }}</label>
                                                <input type="text" class="form-control" id="merchant_key"
                                                       name="merchant_key"
                                                       value="{{ old('merchant_key') ? old('merchant_key') : settingHelper('merchant_key') }}"
                                                       placeholder="{{ __('Merchant Key') }}">
                                                @if ($errors->has('merchant_key'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('merchant_key') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="merchant_website">{{ __('MarChant Website') }}</label>
                                                <input type="text" class="form-control" id="merchant_website"
                                                       name="merchant_website"
                                                       value="{{ old('merchant_website') ? old('merchant_website') : settingHelper('merchant_website') }}"
                                                       placeholder="{{ __('Merchant Website') }}">
                                                @if ($errors->has('merchant_website'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('merchant_website') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="channel">{{ __('Channel') }}</label>
                                                <input type="text" class="form-control" id="'channel" name="channel"
                                                       value="{{ old('channel') ? old('channel') : settingHelper('channel') }}"
                                                       placeholder="{{ __('Channel') }}">
                                                @if ($errors->has('channel'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('channel') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="industry_type">{{ __('Industry Type') }}</label>
                                                <input type="text" class="form-control" id="industry_type"
                                                       name="industry_type"
                                                       value="{{ old('industry_type') ? old('industry_type') : settingHelper('industry_type') }}"
                                                       placeholder="{{ __('Industry Type') }}">
                                                @if ($errors->has('industry_type'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('industry_type') }}
                                                    </div>
                                                @endif
                                            </div>

                                            @if(hasPermission('payment_gateway_update'))
                                                <div class="text-md-right">
                                                    <button class="btn btn-outline-primary">{{ __('Save') }}</button>
                                                </div>
                                            @endif
                                            @if(hasPermission('payment_gateway_update'))
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade {{ old('payment_method') == 'jazz_cash' ? 'show active' : ''}}"
                             id="jazz_cash" role="tab" aria-labelledby="jazz_cash">
                            <div class="card">
                                <div class="card-header">
                                    <h4>{{ __('JazzCash Setting') }}</h4>
                                </div>
                                <div class="card-body col-md-10 middle">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6 pl-0">
                                                <div class="form-group">
                                                    <label
                                                        class="custom-switch mt-2 {{ hasPermission('payment_gateway_update') ? '' : 'cursor-not-allowed' }}">
                                                        <input type="checkbox"
                                                               name="custom-switch-checkbox"
                                                               value="admin-payment-status-change/{{ 'is_jazz_cash_activated' }}"
                                                               {{ hasPermission('payment_gateway_update') ? '' : 'disabled' }}
                                                               class="{{ hasPermission('payment_gateway_update') ? 'status-change' : '' }} custom-switch-input " {{ settingHelper('is_jazz_cash_activated') == 1 ? 'checked' : ''}} />
                                                        <span class="custom-switch-indicator"></span>
                                                        <span
                                                            class="custom-switch-description">{{ __('Activate') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if(hasPermission('payment_gateway_update'))
                                        <form action="{{ route('payment.gateway.update') }}" method="post"
                                              enctype="multipart/form-data">
                                            @method('put')
                                            @csrf
                                            @endif
                                            <div class="form-group">
                                                <label for="jazz_cash_merchant_id">{{ __('Merchant Id') }}</label>
                                                <input type="hidden" name="payment_method" value="jazz_cash">
                                                <input type="text" class="form-control" name="jazz_cash_merchant_id"
                                                       id="jazz_cash_merchant_id"
                                                       value="{{ old('jazz_cash_merchant_id') ? old('jazz_cash_merchant_id') : settingHelper('jazz_cash_merchant_id')}}"
                                                       placeholder="{{ __('JazzCash Merchant Id') }}">
                                                @if ($errors->has('jazz_cash_merchant_id'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('jazz_cash_merchant_id') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="jazz_cash_password">{{ __('JazzCash Password') }}</label>
                                                <input type="text" class="form-control" name="jazz_cash_password"
                                                       id="jazz_cash_password"
                                                       value="{{ old('jazz_cash_password') ? old('jazz_cash_password') : settingHelper('jazz_cash_password') }}"
                                                       placeholder="{{ __('JazzCash Password') }}">
                                                @if ($errors->has('jazz_cash_password'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('jazz_cash_password') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="jazz_cash_integrity_salt">{{ __('Integrity Salt') }}</label>
                                                <input type="text" class="form-control" name="jazz_cash_integrity_salt"
                                                       id="jazz_cash_integrity_salt"
                                                       value="{{ old('jazz_cash_integrity_salt') ? old('jazz_cash_integrity_salt') : settingHelper('jazz_cash_integrity_salt') }}"
                                                       placeholder="{{ __('Integrity Salt') }}">
                                                @if ($errors->has('jazz_cash_integrity_salt'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('jazz_cash_integrity_salt') }}
                                                    </div>
                                                @endif
                                            </div>
                                            @if(hasPermission('payment_gateway_update'))
                                                <div class="text-md-right">
                                                    <button class="btn btn-outline-primary">{{ __('Save') }}</button>
                                                </div>
                                            @endif
                                            @if(hasPermission('payment_gateway_update'))
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade {{ old('payment_method') == 'razorpay' ? 'show active' : ''}}"
                             id="razorpay" role="tab" aria-labelledby="razorpay-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h4>{{ __('Razorpay Setting') }}</h4>
                                </div>
                                <div class="card-body col-md-10 middle">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6 pl-0">
                                                <div class="form-group">
                                                    <label
                                                        class="custom-switch mt-2 {{ hasPermission('payment_gateway_update') ? '' : 'cursor-not-allowed' }}">
                                                        <input type="checkbox"
                                                               name="custom-switch-checkbox"
                                                               value="admin-payment-status-change/{{ 'is_razorpay_activated' }}"
                                                               {{ hasPermission('payment_gateway_update') ? '' : 'disabled' }}
                                                               class="{{ hasPermission('payment_gateway_update') ? 'status-change' : '' }} custom-switch-input " {{ settingHelper('is_razorpay_activated') == 1 ? 'checked' : ''}} />
                                                        <span class="custom-switch-indicator"></span>
                                                        <span
                                                            class="custom-switch-description">{{ __('Activate') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if(hasPermission('payment_gateway_update'))
                                        <form method="post" action="{{ route('payment.gateway.update') }}"
                                              enctype="multipart/form-data">
                                            @method('put')
                                            @csrf
                                            @endif
                                            <div class="form-group">
                                                <label for="razorpay_key">{{ __('Razorpay  Key') }}</label>
                                                <input type="hidden" name="payment_method" value="razorpay">
                                                <input type="text" class="form-control" id="razorpay_key"
                                                       name="razorpay_key"
                                                       value="{{ old('razorpay_key') ? old('razorpay_key') :settingHelper('razorpay_key') }}"
                                                       placeholder="{{ __('Razorpay Key') }}">
                                                @if ($errors->has('razorpay_key'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('razorpay_key') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="razorpay_secret">{{ __('Razorpay  Secret') }}</label>
                                                <input type="text" class="form-control" id="razorpay_secret"
                                                       name="razorpay_secret"
                                                       value="{{ old('razorpay_secret') ? old('razorpay_secret') :settingHelper('razorpay_secret') }}"
                                                       placeholder="{{ __('Razorpay Secret') }}">
                                                @if ($errors->has('razorpay_secret'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('razorpay_secret') }}
                                                    </div>
                                                @endif
                                            </div>
                                            @if(hasPermission('payment_gateway_update'))
                                                <div class="text-md-right">
                                                    <button class="btn btn-outline-primary">{{ __('Save') }}</button>
                                                </div>
                                            @endif
                                            @if(hasPermission('payment_gateway_update'))
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade {{ old('payment_method') == 'mollie' ? 'show active' : ''}}"
                             id="mollie" role="tab" aria-labelledby="razorpay-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h4>{{ __('Mollie Setting') }}</h4>
                                </div>
                                <div class="card-body col-md-10 middle">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="invalid-feedback text-danger">
                                                    {{ __("N.B: Before activating Mollie, please make sue you have Europe currency EUR activated") }}
                                                </div>
                                            </div>
                                            <div class="col-6 pl-0">
                                                <div class="form-group">
                                                    <label
                                                        class="custom-switch mt-2 {{ hasPermission('payment_gateway_update') ? '' : 'cursor-not-allowed' }}">
                                                        <input type="checkbox"
                                                               name="custom-switch-checkbox"
                                                               value="admin-payment-status-change/{{ 'is_mollie_activated' }}"
                                                               {{ hasPermission('payment_gateway_update') ? '' : 'disabled' }}
                                                               class="{{ hasPermission('payment_gateway_update') ? 'status-change' : '' }} custom-switch-input " {{ settingHelper('is_mollie_activated') == 1 ? 'checked' : ''}} />
                                                        <span class="custom-switch-indicator"></span>
                                                        <span
                                                            class="custom-switch-description">{{ __('Activate') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if(hasPermission('payment_gateway_update'))
                                        <form method="post" action="{{ route('payment.gateway.update') }}"
                                              enctype="multipart/form-data">
                                            @method('put')
                                            @csrf
                                            @endif
                                            <div class="form-group">
                                                <label for="mollie_api_key">{{ __('Api  Key') }}</label>
                                                <input type="hidden" name="payment_method" value="mollie">
                                                <input type="text" class="form-control" id="mollie_api_key"
                                                       name="mollie_api_key"
                                                       value="{{ old('mollie_api_key') ? old('mollie_api_key') :settingHelper('mollie_api_key') }}"
                                                       placeholder="{{ __('Api  Key') }}">
                                                @if ($errors->has('mollie_api_key'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('mollie_api_key') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="mollie_partner_id">{{ __('Partner Id') }}</label>
                                                <input type="text" class="form-control" id="mollie_partner_id"
                                                       name="mollie_partner_id"
                                                       value="{{ old('mollie_partner_id') ? old('mollie_partner_id') :settingHelper('mollie_partner_id') }}"
                                                       placeholder="{{ __('Partner Id') }}">
                                                @if ($errors->has('mollie_partner_id'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('mollie_partner_id') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="mollie_profile_id">{{ __('Profile Id') }}</label>
                                                <input type="text" class="form-control" id="mollie_profile_id"
                                                       name="mollie_profile_id"
                                                       value="{{ old('mollie_profile_id') ? old('mollie_profile_id') :settingHelper('mollie_profile_id') }}"
                                                       placeholder="{{ __('Profile Id') }}">
                                                @if ($errors->has('mollie_profile_id'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('mollie_profile_id') }}
                                                    </div>
                                                @endif
                                            </div>
                                            @if(hasPermission('payment_gateway_update'))
                                                <div class="text-md-right">
                                                    <button class="btn btn-outline-primary">{{ __('Save') }}</button>
                                                </div>
                                            @endif
                                            @if(hasPermission('payment_gateway_update'))
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade {{ old('payment_method') == 'paystack' ? 'show active' : ''}}"
                             id="paystack" role="tab" aria-labelledby="razorpay-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h4>{{ __('Paystack Setting') }}</h4>
                                </div>
                                <div class="card-body col-md-10 middle">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="invalid-feedback text-danger">
                                                    {{ __("N.B: Before activating Paystack, please make sue you have NGN currency activated") }}
                                                </div>
                                            </div>
                                            <div class="col-6 pl-0">
                                                <div class="form-group">
                                                    <label
                                                        class="custom-switch mt-2 {{ hasPermission('payment_gateway_update') ? '' : 'cursor-not-allowed' }}">
                                                        <input type="checkbox"
                                                               name="custom-switch-checkbox"
                                                               value="admin-payment-status-change/{{ 'is_paystack_activated' }}"
                                                               {{ hasPermission('payment_gateway_update') ? '' : 'disabled' }}
                                                               class="{{ hasPermission('payment_gateway_update') ? 'status-change' : '' }} custom-switch-input " {{ settingHelper('is_paystack_activated') == 1 ? 'checked' : ''}} />
                                                        <span class="custom-switch-indicator"></span>
                                                        <span
                                                            class="custom-switch-description">{{ __('Activate') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if(hasPermission('payment_gateway_update'))
                                        <form method="post" action="{{ route('payment.gateway.update') }}"
                                              enctype="multipart/form-data">
                                            @method('put')
                                            @csrf
                                            @endif
                                            <div class="form-group">
                                                <label for="paystack_secret_key">{{ __('Secret  Key') }}</label>
                                                <input type="hidden" name="payment_method" value="paystack">
                                                <input type="text" class="form-control" id="paystack_secret_key"
                                                       name="paystack_secret_key"
                                                       value="{{ old('paystack_secret_key') ? old('paystack_secret_key') :settingHelper('paystack_secret_key') }}"
                                                       placeholder="{{ __('Secret  Key') }}">
                                                @if ($errors->has('paystack_secret_key'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('paystack_secret_key') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="paystack_public_key">{{ __('Public Key') }}</label>
                                                <input type="text" class="form-control" id="paystack_public_key"
                                                       name="paystack_public_key"
                                                       value="{{ old('paystack_public_key') ? old('paystack_public_key') :settingHelper('paystack_public_key') }}"
                                                       placeholder="{{ __('Public Key') }}">
                                                @if ($errors->has('paystack_public_key'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('paystack_public_key') }}
                                                    </div>
                                                @endif
                                            </div>
                                            @if(hasPermission('payment_gateway_update'))
                                                <div class="text-md-right">
                                                    <button class="btn btn-outline-primary">{{ __('Save') }}</button>
                                                </div>
                                            @endif
                                            @if(hasPermission('payment_gateway_update'))
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade {{ old('payment_method') == 'flutterwave' ? 'show active' : ''}}"
                             id="flutterwave" role="tab" aria-labelledby="razorpay-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h4>{{ __('Flutter Wave Setting') }}</h4>
                                </div>
                                <div class="card-body col-md-10 middle">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="invalid-feedback text-danger">
                                                    {{ __("N.B: Before activating Flutter wave, please make sue you have NGN currency activated") }}
                                                </div>
                                            </div>
                                            <div class="col-6 pl-0">
                                                <div class="form-group">
                                                    <label
                                                        class="custom-switch mt-2 {{ hasPermission('payment_gateway_update') ? '' : 'cursor-not-allowed' }}">
                                                        <input type="checkbox"
                                                               name="custom-switch-checkbox"
                                                               value="admin-payment-status-change/{{ 'is_flutterwave_activated' }}"
                                                               {{ hasPermission('payment_gateway_update') ? '' : 'disabled' }}
                                                               class="{{ hasPermission('payment_gateway_update') ? 'status-change' : '' }} custom-switch-input " {{ settingHelper('is_flutterwave_activated') == 1 ? 'checked' : ''}} />
                                                        <span class="custom-switch-indicator"></span>
                                                        <span
                                                            class="custom-switch-description">{{ __('Activate') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if(hasPermission('payment_gateway_update'))
                                        <form method="post" action="{{ route('payment.gateway.update') }}"
                                              enctype="multipart/form-data">
                                            @method('put')
                                            @csrf
                                            @endif
                                            <div class="form-group">
                                                <label for="flutterwave_secret_key">{{ __('Secret  Key') }}</label>
                                                <input type="hidden" name="payment_method" value="flutterwave">
                                                <input type="text" class="form-control" id="flutterwave_secret_key"
                                                       name="flutterwave_secret_key"
                                                       value="{{ old('flutterwave_secret_key') ? old('flutterwave_secret_key') :settingHelper('flutterwave_secret_key') }}"
                                                       placeholder="{{ __('Secret  Key') }}">
                                                @if ($errors->has('flutterwave_secret_key'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('flutterwave_secret_key') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="flutterwave_public_key">{{ __('Public Key') }}</label>
                                                <input type="text" class="form-control" id="flutterwave_public_key"
                                                       name="flutterwave_public_key"
                                                       value="{{ old('flutterwave_public_key') ? old('flutterwave_public_key') :settingHelper('flutterwave_public_key') }}"
                                                       placeholder="{{ __('Public Key') }}">
                                                @if ($errors->has('flutterwave_public_key'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('flutterwave_public_key') }}
                                                    </div>
                                                @endif
                                            </div>
                                            @if(hasPermission('payment_gateway_update'))
                                                <div class="text-md-right">
                                                    <button class="btn btn-outline-primary">{{ __('Save') }}</button>
                                                </div>
                                            @endif
                                            @if(hasPermission('payment_gateway_update'))
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
<!--                        <div class="tab-pane fade {{ old('payment_method') == 'mercado_pago' ? 'show active' : ''}}"
                             id="mercado_pago" role="tab" aria-labelledby="mercado_pago-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h4>{{ __('mercado_pago_setting') }}</h4>
                                </div>
                                <div class="card-body col-md-10 middle">
                                    <div class="col-12">
                                        <div class="row">
&lt;!&ndash;                                            <div class="col-md-12">
                                                <div class="invalid-feedback text-danger">
                                                    {{ __("N.B: Before activating Flutter wave, please make sue you have NGN currency activated") }}
                                                </div>
                                            </div>&ndash;&gt;
                                            <div class="col-6 pl-0">
                                                <div class="form-group">
                                                    <label
                                                        class="custom-switch mt-2 {{ hasPermission('payment_gateway_update') ? '' : 'cursor-not-allowed' }}">
                                                        <input type="checkbox"
                                                               name="custom-switch-checkbox"
                                                               value="admin-payment-status-change/{{ 'is_mercado_pago_activated' }}"
                                                               {{ hasPermission('payment_gateway_update') ? '' : 'disabled' }}
                                                               class="{{ hasPermission('payment_gateway_update') ? 'status-change' : '' }} custom-switch-input " {{ settingHelper('is_mercado_pago_activated') == 1 ? 'checked' : ''}} />
                                                        <span class="custom-switch-indicator"></span>
                                                        <span
                                                            class="custom-switch-description">{{ __('Activate') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if(hasPermission('payment_gateway_update'))
                                        <form method="post" action="{{ route('payment.gateway.update') }}"
                                              enctype="multipart/form-data">
                                            @method('put')
                                            @csrf
                                            @endif
                                            <div class="form-group">
                                                <label for="mercado_pago_secret_key">{{ __('Secret  Key') }}</label>
                                                <input type="hidden" name="payment_method" value="mercado_pago">
                                                <input type="text" class="form-control" id="mercado_pago_secret_key"
                                                       name="mercado_pago_secret_key"
                                                       value="{{ old('mercado_pago_secret_key') ? old('mercado_pago_secret_key') :settingHelper('mercado_pago_secret_key') }}"
                                                       placeholder="{{ __('Secret  Key') }}">
                                                @if ($errors->has('mercado_pago_secret_key'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('mercado_pago_secret_key') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="mercado_pago_public_key">{{ __('Public Key') }}</label>
                                                <input type="text" class="form-control" id="mercado_pago_public_key"
                                                       name="mercado_pago_public_key"
                                                       value="{{ old('mercado_pago_public_key') ? old('mercado_pago_public_key') :settingHelper('mercado_pago_public_key') }}"
                                                       placeholder="{{ __('Public Key') }}">
                                                @if ($errors->has('mercado_pago_public_key'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('mercado_pago_public_key') }}
                                                    </div>
                                                @endif
                                            </div>
                                            @if(hasPermission('payment_gateway_update'))
                                                <div class="text-md-right">
                                                    <button class="btn btn-outline-primary">{{ __('Save') }}</button>
                                                </div>
                                            @endif
                                            @if(hasPermission('payment_gateway_update'))
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>-->
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

