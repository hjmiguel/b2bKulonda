<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th>Número</th>
                <th>Tipo</th>
                <th>Cliente</th>
                <th>Data Emissão</th>
                <th>Vencimento</th>
                <th class="text-end">Valor</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Pagamento</th>
                <th class="text-center">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($documents as $doc)
            <tr>
                <td>
                    <a href="{{ route('fiscal.documents.show', $doc->id) }}" class="text-decoration-none">
                        <strong>{{ $doc->document_number ?? 'Rascunho' }}</strong>
                    </a>
                </td>
                <td>
                    <span class="badge bg-secondary">{{ $doc->document_type }}</span>
                </td>
                <td>
                    {{ $doc->customer_name }}
                    @if($doc->customer_nif)
                        <br><small class="text-muted">NIF: {{ $doc->customer_nif }}</small>
                    @endif
                </td>
                <td>{{ $doc->issue_date->format('d/m/Y') }}</td>
                <td>
                    @if($doc->due_date)
                        {{ $doc->due_date->format('d/m/Y') }}
                        @if($doc->due_date->isPast() && $doc->payment_status !== 'paid')
                            <span class="badge bg-danger">Vencido</span>
                        @endif
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td class="text-end">
                    <strong>{{ number_format($doc->total, 2, ',', '.') }} Kz</strong>
                    @if($doc->discount > 0)
                        <br><small class="text-success">Desc: {{ number_format($doc->discount, 2, ',', '.') }} Kz</small>
                    @endif
                </td>
                <td class="text-center">
                    @if($doc->status === 'issued')
                        <span class="badge bg-success">Emitido</span>
                    @elseif($doc->status === 'cancelled')
                        <span class="badge bg-danger">Anulado</span>
                    @else
                        <span class="badge bg-warning">Rascunho</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($doc->payment_status === 'paid')
                        <span class="badge bg-success">
                            <i class="fas fa-check"></i> Pago
                        </span>
                    @elseif($doc->payment_status === 'partial')
                        <span class="badge bg-warning">
                            <i class="fas fa-clock"></i> Parcial
                        </span>
                    @elseif($doc->payment_status === 'overdue')
                        <span class="badge bg-danger">
                            <i class="fas fa-exclamation-triangle"></i> Vencido
                        </span>
                    @else
                        <span class="badge bg-secondary">
                            <i class="fas fa-hourglass-half"></i> Pendente
                        </span>
                    @endif
                </td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <a href="{{ route('fiscal.documents.show', $doc->id) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        
                        @if($doc->status === 'issued')
                            <a href="{{ route('fiscal.documents.pdf', $doc->id) }}" class="btn btn-sm btn-outline-danger" target="_blank" title="PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        @endif

                        @if($doc->status === 'draft')
                            <form action="{{ route('fiscal.documents.issue', $doc->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Emitir" onclick="return confirm('Deseja emitir este documento?')">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                        @endif

                        @if($doc->canBeCancelled())
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Anular" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $doc->id }}">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>

            <!-- Cancel Modal -->
            @if($doc->canBeCancelled())
            <div class="modal fade" id="cancelModal{{ $doc->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('fiscal.documents.cancel', $doc->id) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Anular Documento</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Tem certeza que deseja anular o documento <strong>{{ $doc->document_number }}</strong>?</p>
                                <div class="mb-3">
                                    <label for="cancellation_reason{{ $doc->id }}" class="form-label">Motivo da Anulação *</label>
                                    <textarea name="cancellation_reason" id="cancellation_reason{{ $doc->id }}" class="form-control" rows="3" required></textarea>
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
            @endforeach
        </tbody>
    </table>
</div>
