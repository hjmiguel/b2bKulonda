@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">
                    <i class="fas fa-file-invoice"></i> Documentos Fiscais
                </h1>
                <div>
                    <a href="{{ route('fiscal.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                    <a href="{{ route('fiscal.documents.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Documento
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('fiscal.documents.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label for="document_type" class="form-label">Tipo</label>
                    <select name="document_type" id="document_type" class="form-select">
                        <option value="">Todos</option>
                        <option value="FR" {{ request('document_type') == 'FR' ? 'selected' : '' }}>FR - Fatura Recibo</option>
                        <option value="FT" {{ request('document_type') == 'FT' ? 'selected' : '' }}>FT - Fatura</option>
                        <option value="FS" {{ request('document_type') == 'FS' ? 'selected' : '' }}>FS - Fatura Simplificada</option>
                        <option value="NC" {{ request('document_type') == 'NC' ? 'selected' : '' }}>NC - Nota de Crédito</option>
                        <option value="ND" {{ request('document_type') == 'ND' ? 'selected' : '' }}>ND - Nota de Débito</option>
                        <option value="RC" {{ request('document_type') == 'RC' ? 'selected' : '' }}>RC - Recibo</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="status" class="form-label">Estado</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Rascunho</option>
                        <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>Emitido</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Anulado</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="payment_status" class="form-label">Pagamento</label>
                    <select name="payment_status" id="payment_status" class="form-select">
                        <option value="">Todos</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Pago</option>
                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Parcial</option>
                        <option value="overdue" {{ request('payment_status') == 'overdue' ? 'selected' : '' }}>Vencido</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="date_from" class="form-label">Data De</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <label for="date_to" class="form-label">Data Até</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Documents List -->
    <div class="card">
        <div class="card-body">
            @if($documents->count() > 0)
                @include('fiscal.partials.documents-table', ['documents' => $documents])
                
                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <div>
                        Mostrando {{ $documents->firstItem() }} a {{ $documents->lastItem() }} de {{ $documents->total() }} documentos
                    </div>
                    <div>
                        {{ $documents->links() }}
                    </div>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                    <p class="mb-0">Nenhum documento encontrado com os filtros aplicados.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
