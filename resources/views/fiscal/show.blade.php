@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">
                    <a href="{{ route('fiscal.documents.index') }}" class="text-decoration-none text-muted">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    Documento Fiscal: {{ $document->document_number ?? 'Rascunho' }}
                </h1>
                <div>
                    @if($document->status === 'issued')
                        <a href="{{ route('fiscal.documents.pdf', $document->id) }}" class="btn btn-danger" target="_blank">
                            <i class="fas fa-file-pdf"></i> Baixar PDF
                        </a>
                    @endif
                    
                    @if($document->status === 'draft')
                        <form action="{{ route('fiscal.documents.issue', $document->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('Deseja emitir este documento?')">
                                <i class="fas fa-check"></i> Emitir Documento
                            </button>
                        </form>
                    @endif

                    @if($document->canBeCancelled())
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="fas fa-times"></i> Anular
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Document Info -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-invoice"></i> Informações do Documento
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Tipo:</strong><br>
                            <span class="badge bg-secondary fs-6">{{ $document->document_type }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Número:</strong><br>
                            {{ $document->document_number ?? '-' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Série:</strong><br>
                            {{ $document->serie }}
                        </div>
                        <div class="col-md-3">
                            <strong>Ano:</strong><br>
                            {{ $document->year }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Data Emissão:</strong><br>
                            {{ $document->issue_date->format('d/m/Y') }}
                        </div>
                        <div class="col-md-4">
                            <strong>Data Vencimento:</strong><br>
                            {{ $document->due_date ? $document->due_date->format('d/m/Y') : '-' }}
                        </div>
                        <div class="col-md-4">
                            <strong>Estado:</strong><br>
                            @if($document->status === 'issued')
                                <span class="badge bg-success">Emitido</span>
                            @elseif($document->status === 'cancelled')
                                <span class="badge bg-danger">Anulado</span>
                            @else
                                <span class="badge bg-warning">Rascunho</span>
                            @endif
                        </div>
                    </div>

                    @if($document->notes)
                    <div class="alert alert-info">
                        <strong>Observações:</strong><br>
                        {{ $document->notes }}
                    </div>
                    @endif

                    @if($document->status === 'cancelled' && $document->cancellation_reason)
                    <div class="alert alert-danger">
                        <strong>Motivo da Anulação:</strong><br>
                        {{ $document->cancellation_reason }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Customer Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user"></i> Dados do Cliente
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nome:</strong> {{ $document->customer_name }}</p>
                            <p><strong>NIF:</strong> {{ $document->customer_nif ?? '-' }}</p>
                            <p><strong>Email:</strong> {{ $document->customer_email ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Telefone:</strong> {{ $document->customer_phone ?? '-' }}</p>
                            <p><strong>Endereço:</strong> {{ $document->customer_address ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Itens do Documento
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Produto</th>
                                    <th class="text-center">Qtd</th>
                                    <th class="text-end">Preço Unit.</th>
                                    <th class="text-center">IVA</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($document->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $item->product_name }}</strong>
                                        @if($item->product_code)
                                            <br><small class="text-muted">Cód: {{ $item->product_code }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($item->unit_price, 2, ',', '.') }} Kz</td>
                                    <td class="text-center">{{ number_format($item->tax_rate, 0) }}%</td>
                                    <td class="text-end"><strong>{{ number_format($item->total, 2, ',', '.') }} Kz</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Subtotal:</strong></td>
                                    <td class="text-end"><strong>{{ number_format($document->subtotal, 2, ',', '.') }} Kz</strong></td>
                                </tr>
                                @if($document->discount > 0)
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Desconto:</strong></td>
                                    <td class="text-end text-success"><strong>-{{ number_format($document->discount, 2, ',', '.') }} Kz</strong></td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="5" class="text-end"><strong>IVA:</strong></td>
                                    <td class="text-end"><strong>{{ number_format($document->tax, 2, ',', '.') }} Kz</strong></td>
                                </tr>
                                <tr class="table-primary">
                                    <td colspan="5" class="text-end"><strong>TOTAL:</strong></td>
                                    <td class="text-end"><strong class="fs-5">{{ number_format($document->total, 2, ',', '.') }} Kz</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Payment Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-money-bill"></i> Pagamento
                    </h5>
                </div>
                <div class="card-body">
                    <p><strong>Estado:</strong><br>
                        @if($document->payment_status === 'paid')
                            <span class="badge bg-success fs-6">Pago</span>
                        @elseif($document->payment_status === 'partial')
                            <span class="badge bg-warning fs-6">Parcial</span>
                        @elseif($document->payment_status === 'overdue')
                            <span class="badge bg-danger fs-6">Vencido</span>
                        @else
                            <span class="badge bg-secondary fs-6">Pendente</span>
                        @endif
                    </p>
                    
                    <p><strong>Método:</strong><br>
                        {{ $document->payment_method ? strtoupper($document->payment_method) : '-' }}
                    </p>

                    @if($document->payment_date)
                    <p><strong>Data Pagamento:</strong><br>
                        {{ $document->payment_date->format('d/m/Y') }}
                    </p>
                    @endif

                    @if($document->payment_reference)
                    <p><strong>Referência:</strong><br>
                        {{ $document->payment_reference }}
                    </p>
                    @endif
                </div>
            </div>

            <!-- AGT Info -->
            @if($document->status === 'issued')
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-shield-alt"></i> Informações AGT
                    </h5>
                </div>
                <div class="card-body">
                    @if($document->agt_hash)
                    <p><strong>Hash AGT:</strong><br>
                        <small class="font-monospace">{{ substr($document->agt_hash, 0, 40) }}...</small>
                    </p>
                    @endif

                    @if($document->previous_hash)
                    <p><strong>Hash Anterior:</strong><br>
                        <small class="font-monospace">{{ substr($document->previous_hash, 0, 40) }}...</small>
                    </p>
                    @endif

                    @if($document->agt_atcud)
                    <p><strong>ATCUD:</strong><br>
                        {{ $document->agt_atcud }}
                    </p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Related Documents -->
            @if($document->relatedDocument || $document->related_document_number)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-link"></i> Documento Relacionado
                    </h5>
                </div>
                <div class="card-body">
                    @if($document->relatedDocument)
                        <a href="{{ route('fiscal.documents.show', $document->relatedDocument->id) }}">
                            {{ $document->relatedDocument->document_number }}
                        </a>
                    @else
                        {{ $document->related_document_number }}
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Cancel Modal -->
@if($document->canBeCancelled())
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('fiscal.documents.cancel', $document->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Anular Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja anular o documento <strong>{{ $document->document_number }}</strong>?</p>
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Motivo da Anulação *</label>
                        <textarea name="cancellation_reason" id="cancellation_reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Anular Documento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
