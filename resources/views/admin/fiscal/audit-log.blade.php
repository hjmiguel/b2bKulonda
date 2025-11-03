@extends("admin.layouts.app")

@section("content")
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3">
                <i class="fas fa-history"></i> Historico e Auditoria
            </h1>
            <p class="text-muted">Log completo de todas as acoes realizadas no sistema fiscal</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route("admin.fiscal.audit") }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tipo de Acao</label>
                        <select name="action_type" class="form-select">
                            <option value="">Todas as acoes</option>
                            <option value="created">Criacao</option>
                            <option value="updated">Atualizacao</option>
                            <option value="deleted">Exclusao</option>
                            <option value="cancelled">Cancelamento</option>
                            <option value="sent_agt">Envio AGT</option>
                            <option value="email_sent">Email Enviado</option>
                            <option value="pdf_generated">PDF Gerado</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Usuario</label>
                        <select name="user_id" class="form-select">
                            <option value="">Todos os usuarios</option>
                            @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Data Inicio</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request("start_date") }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Data Fim</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request("end_date") }}">
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total de Acoes</h6>
                            <h3 class="mb-0">{{ $stats["total"] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-list fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Hoje</h6>
                            <h3 class="mb-0">{{ $stats["today"] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-calendar-day fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Esta Semana</h6>
                            <h3 class="mb-0">{{ $stats["week"] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-calendar-week fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Este Mes</h6>
                            <h3 class="mb-0">{{ $stats["month"] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-calendar-alt fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-table"></i> Log de Auditoria</h5>
            <div>
                <a href="{{ route("admin.fiscal.audit.export") }}" class="btn btn-sm btn-success">
                    <i class="fas fa-download"></i> Exportar CSV
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th width="140">Data/Hora</th>
                            <th width="120">Usuario</th>
                            <th width="100">Acao</th>
                            <th width="80">Tipo Doc</th>
                            <th width="120">Numero Doc</th>
                            <th>Descricao</th>
                            <th width="100">IP</th>
                            <th width="60">Detalhes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($auditLogs ?? [] as $log)
                        <tr>
                            <td><small>{{ $log->created_at->format("d/m/Y H:i:s") }}</small></td>
                            <td><small>{{ $log->user->name ?? "Sistema" }}</small></td>
                            <td>
                                @php
                                $badges = [
                                    "created" => "success",
                                    "updated" => "info",
                                    "deleted" => "danger",
                                    "cancelled" => "warning",
                                    "sent_agt" => "primary",
                                    "email_sent" => "secondary",
                                ];
                                @endphp
                                <span class="badge bg-{{ $badges[$log->action_type] ?? "secondary" }}">
                                    {{ ucfirst($log->action_type) }}
                                </span>
                            </td>
                            <td><small>{{ $log->document_type ?? "-" }}</small></td>
                            <td><small>{{ $log->document_number ?? "-" }}</small></td>
                            <td><small>{{ Str::limit($log->description, 50) }}</small></td>
                            <td><small class="text-muted">{{ $log->ip_address }}</small></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailsModal-{{ $log->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="detailsModal-{{ $log->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detalhes da Auditoria</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <dl class="row">
                                            <dt class="col-sm-3">Data/Hora:</dt>
                                            <dd class="col-sm-9">{{ $log->created_at->format("d/m/Y H:i:s") }}</dd>

                                            <dt class="col-sm-3">Usuario:</dt>
                                            <dd class="col-sm-9">{{ $log->user->name ?? "Sistema" }} ({{ $log->user->email ?? "N/A" }})</dd>

                                            <dt class="col-sm-3">Acao:</dt>
                                            <dd class="col-sm-9"><span class="badge bg-{{ $badges[$log->action_type] ?? "secondary" }}">{{ ucfirst($log->action_type) }}</span></dd>

                                            <dt class="col-sm-3">Documento:</dt>
                                            <dd class="col-sm-9">{{ $log->document_type }} {{ $log->document_number }}</dd>

                                            <dt class="col-sm-3">Descricao:</dt>
                                            <dd class="col-sm-9">{{ $log->description }}</dd>

                                            <dt class="col-sm-3">IP Address:</dt>
                                            <dd class="col-sm-9">{{ $log->ip_address }}</dd>

                                            <dt class="col-sm-3">User Agent:</dt>
                                            <dd class="col-sm-9"><small>{{ $log->user_agent }}</small></dd>

                                            @if($log->old_values)
                                            <dt class="col-sm-3">Valores Antigos:</dt>
                                            <dd class="col-sm-9"><pre class="bg-light p-2">{{ json_encode(json_decode($log->old_values), JSON_PRETTY_PRINT) }}</pre></dd>
                                            @endif

                                            @if($log->new_values)
                                            <dt class="col-sm-3">Valores Novos:</dt>
                                            <dd class="col-sm-9"><pre class="bg-light p-2">{{ json_encode(json_decode($log->new_values), JSON_PRETTY_PRINT) }}</pre></dd>
                                            @endif
                                        </dl>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Nenhum registro encontrado
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($auditLogs) && $auditLogs->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $auditLogs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.opacity-50 {
    opacity: 0.5;
}
pre {
    max-height: 300px;
    overflow-y: auto;
}
</style>
@endsection
