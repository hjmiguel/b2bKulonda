@extends('frontend.layouts.app')

@section('content')
<section class="pt-5 mb-4">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-6 col-xl-5 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <div class="text-success mb-4">
                            <i class="las la-check-circle" style="font-size: 5rem;"></i>
                        </div>
                        
                        <h2 class="text-success mb-3">Pagamento Confirmado!</h2>
                        
                        <p class="text-muted mb-4">
                            Recebemos a confirmação do seu pagamento via Multicaixa.
                        </p>

                        <div class="bg-light p-3 rounded mb-4">
                            <div class="row">
                                <div class="col-6 text-left">
                                    <small class="text-muted">Referência:</small><br>
                                    <strong>{{ $reference->reference }}</strong>
                                </div>
                                <div class="col-6 text-right">
                                    <small class="text-muted">Valor:</small><br>
                                    <strong class="text-success">{{ format_price($reference->amount) }}</strong>
                                </div>
                            </div>
                        </div>

                        <p class="text-muted small mb-4">
                            Seu pedido está sendo processado. 
                            Você receberá um email de confirmação em breve.
                        </p>

                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('purchase_history.index') }}" class="btn btn-primary">
                                <i class="las la-shopping-bag"></i>
                                Meus Pedidos
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                <i class="las la-home"></i>
                                Voltar à Loja
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
