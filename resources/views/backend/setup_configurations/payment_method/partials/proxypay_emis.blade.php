<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="proxypay_emis">
    
    <!-- Environment -->
    <div class="form-group row">
        <input type="hidden" name="types[]" value="PROXYPAY_ENVIRONMENT">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Environment') }}</label>
        </div>
        <div class="col-md-8">
            <select class="form-control aiz-selectpicker" name="PROXYPAY_ENVIRONMENT" required>
                <option value="sandbox" @if (env('PROXYPAY_ENVIRONMENT') == 'sandbox') selected @endif>
                    {{ translate('Sandbox (Teste)') }}
                </option>
                <option value="production" @if (env('PROXYPAY_ENVIRONMENT') == 'production') selected @endif>
                    {{ translate('Production (Produção)') }}
                </option>
            </select>
        </div>
    </div>

    <!-- Sandbox Entity -->
    <div class="form-group row">
        <input type="hidden" name="types[]" value="PROXYPAY_ENTITY">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Sandbox Entity') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="PROXYPAY_ENTITY"
                value="{{ env('PROXYPAY_ENTITY') }}"
                placeholder="30061" required>
            <small class="text-muted">{{ translate('Entidade para ambiente de testes (ex: 30061)') }}</small>
        </div>
    </div>

    <!-- Sandbox API Key -->
    <div class="form-group row">
        <input type="hidden" name="types[]" value="PROXYPAY_SANDBOX_API_KEY">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Sandbox API Key') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="PROXYPAY_SANDBOX_API_KEY"
                value="{{ env('PROXYPAY_SANDBOX_API_KEY') }}"
                placeholder="{{ translate('API Key de Sandbox') }}">
        </div>
    </div>

    <!-- Production Entity -->
    <div class="form-group row">
        <input type="hidden" name="types[]" value="PROXYPAY_PRODUCTION_ENTITY">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Production Entity') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="PROXYPAY_PRODUCTION_ENTITY"
                value="{{ env('PROXYPAY_PRODUCTION_ENTITY') }}"
                placeholder="11367">
            <small class="text-muted">{{ translate('Entidade para ambiente de produção (ex: 11367)') }}</small>
        </div>
    </div>

    <!-- Production API Key -->
    <div class="form-group row">
        <input type="hidden" name="types[]" value="PROXYPAY_PRODUCTION_API_KEY">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Production API Key') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="PROXYPAY_PRODUCTION_API_KEY"
                value="{{ env('PROXYPAY_PRODUCTION_API_KEY') }}"
                placeholder="{{ translate('API Key de Produção') }}">
        </div>
    </div>

    <!-- Reference Validity Hours -->
    <div class="form-group row">
        <input type="hidden" name="types[]" value="PROXYPAY_REFERENCE_VALIDITY_HOURS">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Reference Validity (Hours)') }}</label>
        </div>
        <div class="col-md-8">
            <input type="number" class="form-control" name="PROXYPAY_REFERENCE_VALIDITY_HOURS"
                value="{{ env('PROXYPAY_REFERENCE_VALIDITY_HOURS', 2) }}"
                placeholder="2" min="1" max="72">
            <small class="text-muted">{{ translate('Validade da referência EMIS em horas (padrão: 2h)') }}</small>
        </div>
    </div>

    <!-- Polling Configuration -->
    <div class="form-group row">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Enable Polling') }}</label>
        </div>
        <div class="col-md-8">
            <label class="aiz-switch aiz-switch-success mb-0">
                <input value="1" name="PROXYPAY_POLLING_ENABLED" type="checkbox"
                    @if (env('PROXYPAY_POLLING_ENABLED', true)) checked @endif>
                <span class="slider round"></span>
            </label>
            <small class="d-block text-muted">{{ translate('Verificação automática de status a cada 10 segundos') }}</small>
        </div>
    </div>

    <!-- Webhook Configuration -->
    <div class="form-group row">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Enable Webhook') }}</label>
        </div>
        <div class="col-md-8">
            <label class="aiz-switch aiz-switch-success mb-0">
                <input value="1" name="PROXYPAY_WEBHOOK_ENABLED" type="checkbox"
                    @if (env('PROXYPAY_WEBHOOK_ENABLED', false)) checked @endif>
                <span class="slider round"></span>
            </label>
            <small class="d-block text-muted">{{ translate('Notificação instantânea do ProxyPay') }}</small>
        </div>
    </div>

    <!-- Webhook URL (read-only info) -->
    <div class="form-group row">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Webhook URL') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" value="{{ env('APP_URL') }}/webhook/proxypay" readonly>
            <small class="text-muted">{{ translate('Configure este URL no portal ProxyPay') }}</small>
        </div>
    </div>

    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
