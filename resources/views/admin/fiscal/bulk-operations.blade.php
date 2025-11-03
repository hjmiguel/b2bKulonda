@extends("admin.layouts.app")

@section("content")
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3">
                <i class="fas fa-tasks"></i> Operacoes em Massa
            </h1>
            <p class="text-muted">Executar acoes em multiplos documentos fiscais simultaneamente</p>
        </div>
    </div>

    @if(session("success"))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session("success") }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session("error"))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> {{ session("error") }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-envelope"></i> Enviar Emails</h5>
                </div>
                <div class="card-body">
                    <p>Enviar PDFs por email para clientes de documentos selecionados</p>
                    <form action="{{ route("admin.fiscal.bulk.email") }}" method="POST" onsubmit="return confirm("Confirma envio de emails?")">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Filtrar por Tipo</label>
                            <select name="document_type" class="form-select">
                                <option value="">Todos os tipos</option>
                                <option value="FR">Fatura Recibo</option>
                                <option value="FT">Fatura</option>
                                <option value="FS">Fatura Simplificada</option>
                                <option value="NC">Nota de Credito</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Periodo</label>
                            <select name="period" class="form-select" required>
                                <option value="today">Hoje</option>
                                <option value="yesterday">Ontem</option>
                                <option value="week">Ultima Semana</option>
                                <option value="month">Ultimo Mes</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane"></i> Enviar Emails
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-sync-alt"></i> Reenviar para AGT</h5>
                </div>
                <div class="card-body">
                    <p>Reenviar documentos falhados para AGT</p>
                    <form action="{{ route("admin.fiscal.bulk.retry-agt") }}" method="POST" onsubmit="return confirm("Confirma reenvio para AGT?")">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Status AGT</label>
                            <select name="agt_status" class="form-select" required>
                                <option value="failed">Falhados</option>
                                <option value="pending">Pendentes</option>
                                <option value="all">Todos</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Limite</label>
                            <input type="number" name="limit" class="form-control" value="50" min="1" max="500">
                            <small class="text-muted">Max 500 documentos por vez</small>
                        </div>
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="fas fa-redo"></i> Reenviar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-ban"></i> Cancelar Documentos</h5>
                </div>
                <div class="card-body">
                    <p>Cancelar multiplos documentos com razao</p>
                    <form action="{{ route("admin.fiscal.bulk.cancel") }}" method="POST" onsubmit="return confirm("ATENCAO: Acao irreversivel! Confirma cancelamento?")">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">IDs dos Documentos</label>
                            <textarea name="document_ids" class="form-control" rows="3" required placeholder="1,2,3,4,5..."></textarea>
                            <small class="text-muted">IDs separados por virgula</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Razao do Cancelamento</label>
                            <textarea name="cancellation_reason" class="form-control" rows="2" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-times-circle"></i> Cancelar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-file-pdf"></i> Regenerar PDFs</h5>
                </div>
                <div class="card-body">
                    <p>Regenerar PDFs de documentos existentes</p>
                    <form action="{{ route("admin.fiscal.bulk.regenerate-pdf") }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Criterio</label>
                            <select name="criteria" class="form-select" required>
                                <option value="missing">Apenas faltando PDF</option>
                                <option value="all">Todos do periodo</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Periodo</label>
                            <select name="period" class="form-select" required>
                                <option value="today">Hoje</option>
                                <option value="week">Ultima Semana</option>
                                <option value="month">Ultimo Mes</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info w-100">
                            <i class="fas fa-file-pdf"></i> Regenerar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-download"></i> Export em Massa</h5>
                </div>
                <div class="card-body">
                    <p>Exportar documentos para CSV ou Excel</p>
                    <form action="{{ route("admin.fiscal.bulk.export") }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Formato</label>
                            <select name="format" class="form-select" required>
                                <option value="csv">CSV</option>
                                <option value="xlsx">Excel XLSX</option>
                                <option value="pdf_zip">PDFs em ZIP</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Periodo</label>
                            <input type="date" name="start_date" class="form-control mb-2" required>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-history"></i> Historico de Operacoes</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Operacao</th>
                            <th>Usuario</th>
                            <th>Documentos</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bulkOperations ?? [] as $operation)
                        <tr>
                            <td>{{ $operation->created_at->format("d/m/Y H:i") }}</td>
                            <td><span class="badge bg-secondary">{{ $operation->type }}</span></td>
                            <td>{{ $operation->user->name }}</td>
                            <td>{{ $operation->document_count }}</td>
                            <td>
                                @if($operation->status === "completed")
                                <span class="badge bg-success">Concluido</span>
                                @elseif($operation->status === "failed")
                                <span class="badge bg-danger">Falhou</span>
                                @else
                                <span class="badge bg-warning">Processando</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Nenhuma operacao registrada</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
</style>
@endsection
