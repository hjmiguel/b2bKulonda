@extends('frontend.layouts.app')

@section('content')
<section class="pt-4 mb-4">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-8 col-xl-6 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">
                            <i class="las la-credit-card"></i>
                            Pagamento Multicaixa
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <!-- Status -->
                        <div id="payment-status" class="mb-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted">Aguardando confirmação do pagamento...</p>
                        </div>

                        <!-- Instruções -->
                        <div class="alert alert-info mb-4">
                            <h5 class="alert-heading">
                                <i class="las la-info-circle"></i>
                                Como pagar:
                            </h5>
                            <ol class="text-left mb-0">
                                <li>Abra o <strong>Multicaixa Express</strong> no seu telemóvel</li>
                                <li>Selecione <strong>Pagamentos</strong></li>
                                <li>Escolha <strong>Pagamento de Serviços</strong></li>
                                <li>Insira os dados abaixo:</li>
                            </ol>
                        </div>

                        <!-- Dados de Pagamento -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <p class="text-muted mb-1">Entidade</p>
                                <h2 class="text-primary mb-0" id="entity-number">{{ $reference->entity }}</h2>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="text-muted mb-1">Referência</p>
                                <h2 class="text-primary mb-0" id="reference-number">{{ $reference->reference }}</h2>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="text-muted mb-1">Valor a Pagar</p>
                                <h3 class="text-success mb-0" id="amount">{{ format_price($reference->amount) }}</h3>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="text-muted mb-1">Válida Até</p>
                                <p class="mb-0">{{ $reference->end_datetime->format('d/m/Y H:i') }}</p>
                                <p id="countdown" class="text-danger font-weight-bold mb-0"></p>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="las la-clock"></i>
                            A página será atualizada automaticamente quando o pagamento for confirmado.
                        </div>

                        <a href="{{ route('home') }}" class="btn btn-secondary">
                            <i class="las la-arrow-left"></i>
                            Voltar à Loja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script>
(function() {
    const referenceId = '{{ $reference->reference_id }}';
    const expiryDate = new Date('{{ $reference->end_datetime->toIso8601String() }}');
    let pollingInterval = null;
    let countdownInterval = null;

    // Função de polling para verificar status do pagamento
    function checkPaymentStatus() {
        fetch('/proxypay/check-payment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                reference_id: referenceId
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Payment status:', data);
            
            if (data.success && data.status === 'paid') {
                // Pagamento confirmado!
                clearInterval(pollingInterval);
                clearInterval(countdownInterval);
                
                document.getElementById('payment-status').innerHTML = 
                    '<div class="text-success">' +
                    '<i class="las la-check-circle" style="font-size: 3rem;"></i>' +
                    '<h4 class="mt-2">Pagamento Confirmado!</h4>' +
                    '<p>Redirecionando...</p>' +
                    '</div>';
                
                // Redirecionar para página de confirmação
                setTimeout(() => {
                    window.location.href = data.redirect_url || '{{ route("order_confirmed") }}';
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Error checking payment:', error);
        });
    }

    // Função de contagem regressiva
    function updateCountdown() {
        const now = new Date();
        const diff = expiryDate - now;
        
        if (diff > 0) {
            const hours = Math.floor(diff / 3600000);
            const minutes = Math.floor((diff % 3600000) / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            
            document.getElementById('countdown').textContent = 
                hours + 'h ' + minutes + 'm ' + seconds + 's';
        } else {
            document.getElementById('countdown').textContent = 'EXPIRADA';
            document.getElementById('payment-status').innerHTML = 
                '<div class="text-danger">' +
                '<i class="las la-times-circle" style="font-size: 3rem;"></i>' +
                '<h4 class="mt-2">Referência Expirada</h4>' +
                '</div>';
            
            clearInterval(pollingInterval);
            clearInterval(countdownInterval);
            
            setTimeout(() => {
                window.location.href = '{{ route("payment.expired", $reference->reference_id) }}';
            }, 3000);
        }
    }

    // Iniciar polling (a cada 10 segundos)
    pollingInterval = setInterval(checkPaymentStatus, 10000);
    
    // Iniciar countdown (a cada 1 segundo)
    countdownInterval = setInterval(updateCountdown, 1000);
    
    // Primeira verificação imediata
    checkPaymentStatus();
    updateCountdown();

    // Limpar intervals ao sair da página
    window.addEventListener('beforeunload', function() {
        if (pollingInterval) clearInterval(pollingInterval);
        if (countdownInterval) clearInterval(countdownInterval);
    });
})();
</script>
@endsection
