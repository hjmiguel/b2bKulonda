<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="proxypay">

    <div class="form-group row">
        <input type="hidden" name="types[]" value="PROXYPAY_SANDBOX">
        <div class="col-lg-4">
            <label class="col-from-label">{{ translate('Sandbox Mode') }}</label>
        </div>
        <div class="col-lg-8">
            <label class="aiz-switch aiz-switch-success mb-0">
                <input type="checkbox" name="PROXYPAY_SANDBOX" @if (get_setting('proxypay_sandbox') == 1) checked @endif>
                <span class="slider round"></span>
            </label>
        </div>
    </div>

    <div class="form-group row">
        <input type="hidden" name="types[]" value="PROXYPAY_ENTITY">
        <div class="col-lg-4">
            <label class="col-from-label">{{ translate('Entity (Sandbox)') }}</label>
        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="PROXYPAY_ENTITY"
                   value="{{ env('PROXYPAY_ENTITY') }}"
                   placeholder="30061" required>
        </div>
    </div>

    <div class="form-group row">
        <input type="hidden" name="types[]" value="PROXYPAY_SANDBOX_API_KEY">
        <div class="col-lg-4">
            <label class="col-from-label">{{ translate('Sandbox API Key') }}</label>
        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="PROXYPAY_SANDBOX_API_KEY"
                   value="{{ env('PROXYPAY_SANDBOX_API_KEY') }}"
                   placeholder="Sandbox API Key" required>
        </div>
    </div>

    <div class="form-group row">
        <input type="hidden" name="types[]" value="PROXYPAY_PRODUCTION_ENTITY">
        <div class="col-lg-4">
            <label class="col-from-label">{{ translate('Entity (Production)') }}</label>
        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="PROXYPAY_PRODUCTION_ENTITY"
                   value="{{ env('PROXYPAY_PRODUCTION_ENTITY') }}"
                   placeholder="11367">
        </div>
    </div>

    <div class="form-group row">
        <input type="hidden" name="types[]" value="PROXYPAY_PRODUCTION_API_KEY">
        <div class="col-lg-4">
            <label class="col-from-label">{{ translate('Production API Key') }}</label>
        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="PROXYPAY_PRODUCTION_API_KEY"
                   value="{{ env('PROXYPAY_PRODUCTION_API_KEY') }}"
                   placeholder="Production API Key">
        </div>
    </div>

    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
