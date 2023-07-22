@extends('admin.partials.master')

@section('title')
    {{__('POS')}}
@endsection
@section('pos_services_active')
    sidebar_active
@endsection
@push('style')
    <style>
        .modal-backdrop.fade.show{
            display : none !important;
        }
    </style>
@endpush
@section('main-content')
    <section class="section">
        <div id="app">
            <pos_system :products="{{ json_encode($products) }}" :vat_tax="{{ $vat_tax }}" :walking_customer="{{ $walkingCustomer }}"
                        :lang="{{ json_encode($lang) }}" :settings="{{ json_encode($currency_setting) }}" :active_currency="{{ json_encode($activeCurrency) }}"
            ></pos_system>
        </div>

        <input type="hidden" name="url" id="url" value="{{ url('/') }}">
        <input type="hidden" name="vat_type" id="vat_type" value="{{ $vat_type }}">
    </section>
@endsection

@push('script')
    <script src="{{static_asset('admin/js/app.js')}}"></script>
@endpush
