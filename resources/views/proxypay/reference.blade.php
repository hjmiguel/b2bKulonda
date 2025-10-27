{{-- ProxyPay EMIS - View Reutilizável --}}
@extends(config('proxypay.layout', 'layouts.app'))

@section('title', 'Pagamento ProxyPay')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h2><i class="fas fa-receipt"></i> Referência EMIS</h2>
                </div>
                <div class="card-body p-4">
                    <div class="row text-center mb-4">
                        <div class="col-md-6">
                            <h5>Entidade</h5>
                            <h1 class="text-primary">{{ $reference->entity }}</h1>
                        </div>
                        <div class="col-md-6">
                            <h5>Referência</h5>
                            <h1 class="text-primary">{{ $reference->reference }}</h1>
                        </div>
                    </div>
                    <div class="row text-center mb-4">
                        <div class="col-md-6">
                            <h5>Valor</h5>
                            <h2 class="text-success">{{ $reference->formatted_amount }}</h2>
                        </div>
                        <div class="col-md-6">
                            <h5>Válida Até</h5>
                            <p>{{ $reference->end_datetime->format('d/m/Y H:i') }}</p>
                            <p id="countdown" class="text-danger font-weight-bold"></p>
                        </div>
                    </div>
                    <div class="text-center mt-4" id="status">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2">Aguardando pagamento...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const refId = '{{ $reference->reference_id }}';
const expiry = new Date('{{ $reference->end_datetime->toIso8601String() }}');
let interval = setInterval(() => {
    fetch(`/payment/proxypay/check/${refId}`)
        .then(r => r.json())
        .then(d => {
            if (d.status === 'paid') {
                clearInterval(interval);
                document.getElementById('status').innerHTML = '<i class="fas fa-check-circle text-success"></i> Pago! Redirecionando...';
                setTimeout(() => location.href = d.redirect_url, 2000);
            }
        });
    const diff = expiry - new Date();
    if (diff > 0) {
        const h = Math.floor(diff/3600000);
        const m = Math.floor((diff%3600000)/60000);
        const s = Math.floor((diff%60000)/1000);
        document.getElementById('countdown').textContent = `${h}h ${m}m ${s}s`;
    } else {
        document.getElementById('countdown').textContent = 'EXPIRADA';
        clearInterval(interval);
    }
}, 10000);
</script>
@endpush
@endsection
