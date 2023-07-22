<!-- add currency modal -->
@php
 $currency_list = get_yrsetting('currency_list');
@endphp
<form action="{{ route('admin.setting.currency.store') }}" method="post" enctype="multipart/form-data">
    @csrf @method('post')
    <div class="modal-body modal-padding-bottom modal-body-overflow-unset">
        <div class="form-group align-items-center">
            <label for="name" class="form-control-label">{{ __('Currency Name') }}</label>

            <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" placeholder="{{ __('Enter name') }}" required />
        </div>
        <div class="form-group align-items-center">
            <label for="symbol" class="form-control-label">{{ __('Symbol') }}</label>
            <select class="form-control select2" name="symbol" id="symbol" required>
                <option value="">{{ __("Select currency symbol") }}</option>
                @foreach($currency_list as $key => $value)
                <option value="{{ $value['symbol'] }}">{{ $value['symbol'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group align-items-center">
            <label class="form-control-label">{{ __('Currency Code') }}</label>
            <select class="form-control select2" id="code" name="code" value="{{ old('code') }}" required>
                <option value="">{{ __("Select currency code") }}</option>
                @foreach($currency_list as $key => $value)
                <option value="{{ $key }}">{{ $key }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group align-items-center">
            <label class="form-control-label">{{ __('Exchange Rate') }} <small>({{ __('1 USD = ?') }})</small></label>
            <input type="number" class="form-control" name="exchange_rate" step="any" min="0" id="exchange_rate" value="{{ old('exchange_rate') }}" placeholder="{{ __('Exchange rate') }}" required />
        </div>
    </div>
    <div class="modal-footer modal-padding-bottom">
        <button type="submit" class="btn btn-outline-primary">{{ __('Save') }}</button>
    </div>
</form>
