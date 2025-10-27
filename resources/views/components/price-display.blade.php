@props(['product', 'size' => 'normal', 'showLabel' => false])

@if (should_show_price())
    <div {{ $attributes->merge(['class' => 'price-wrapper']) }}>
        @if ($product->auction_product == 0)
            {{-- Produto Normal --}}
            @if (home_base_price($product) != home_discounted_base_price($product))
                <del class="fw-400 text-secondary mr-1 {{ $size == 'large' ? 'fs-16' : 'fs-14' }}">
                    {{ home_base_price($product) }}
                </del>
            @endif
            <span class="fw-700 text-primary {{ $size == 'large' ? 'fs-20' : 'fs-14' }}">
                {{ home_discounted_base_price($product) }}
            </span>
        @elseif ($product->auction_product == 1)
            {{-- Produto de Leilão --}}
            @if ($showLabel)
                <span class="text-muted small">{{ translate('Starting Bid') }}:</span>
            @endif
            <span class="fw-700 text-primary {{ $size == 'large' ? 'fs-20' : 'fs-14' }}">
                {{ single_price($product->starting_bid) }}
            </span>
        @endif
    </div>
@else
    {{-- Usuário não logado --}}
    <div {{ $attributes->merge(['class' => 'price-hidden-wrapper text-center']) }}>
        <a href="{{ route('user.login') }}" class="text-primary fw-500 {{ $size == 'large' ? 'fs-16' : 'fs-13' }}">
            <i class="la la-lock mr-1"></i>
            {{ translate('Login to see price') }}
        </a>
    </div>
@endif
