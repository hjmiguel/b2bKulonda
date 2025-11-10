@extends('frontend.layouts.app')

@section('content')
<section class="pt-5 mb-4">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-6 col-xl-5 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <div class="text-warning mb-4">
                            <i class="las la-exclamation-triangle" style="font-size: 5rem;"></i>
                        </div>
                        
                        <h2 class="text-warning mb-3">Referência Expirada</h2>
                        
                        <p class="text-muted mb-4">
                            A referência de pagamento expirou. 
                            Por favor, faça um novo pedido para gerar uma nova referência.
                        </p>

                        <div class="bg-light p-3 rounded mb-4">
                            <div class="row">
                                <div class="col-6 text-left">
                                    <small class="text-muted">Referência:</small><br>
                                    <strong>{{ $reference->reference }}</strong>
                                </div>
                                <div class="col-6 text-right">
                                    <small class="text-muted">Expirou em:</small><br>
                                    <strong>{{ $reference->end_datetime->format('d/m/Y H:i') }}</strong>
                                </div>
                            </div>
                        </div>

                        <p class="text-muted small mb-4">
                            <i class="las la-info-circle"></i>
                            Referências Multicaixa têm validade de 24 horas após a criação.
                        </p>

                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('cart') }}" class="btn btn-primary">
                                <i class="las la-shopping-cart"></i>
                                Voltar ao Carrinho
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                <i class="las la-home"></i>
                                Ir para Loja
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
