@php
 $currency = App\Models\Currency::find($otherLinks[0]); $currency_list = get_yrsetting('currency_list');
@endphp
<form action="{{ route('admin.setting.currency.update') }}" method="post" enctype="multipart/form-data">
    @csrf @method('put')
    <div class="modal-body modal-padding-bottom modal-body-overflow-unset">
        <div class="form-group align-items-center">
            <label for="site-title" class="form-control-label">{{ __('Name') }}</label>
            <input type="text" class="form-control" name="name" id="name" value="{{ old('name') ? old('name') : $currency->name }}" placeholder="{{ __('Enter name') }}" required />
            <input type="hidden" name="id" placeholder="id" value="{{ $currency->id }}" class="form-control" id="site-title" required autofocus />
        </div>
        <div class="form-group align-items-center">
            <label class="form-control-label">{{ __('Symbol') }}</label>
            <select class="form-control select2" name="symbol" id="symbol" required>
                <option value="">{{ __("Select currency Symbol") }}</option>
                @foreach($currency_list as $key => $value)
                <option {{ $currency->symbol == $value['symbol'] ? "selected" : "" }} value="{{ $value['symbol'] }}">{{ $value['symbol'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group align-items-center">
            <label class="form-control-label">{{ __('Currency code') }}</label>
            <select class="form-control select2" id="code" name="code" value="{{ old('code') }}" required>
                <option value="">{{ __("Select currency code") }}</option>
                @foreach($currency_list as $key => $value)
                <option {{ $currency->code == $key ? "selected" : "" }} value="{{ $key }}">{{ $key }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group align-items-center">
            <label class="form-control-label">{{ __('Exchange rate') }} <small>({{ __('1 USD = ?') }})</small></label>
            <input type="number" class="form-control" name="exchange_rate" id="exchange_rate" step="any" min="0" value="{{ $currency->exchange_rate }}" placeholder="Exchange rate" required />
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-outline-primary">{{ __('Update') }}</button>
    </div>
</form>
