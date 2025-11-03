@extends("admin.layouts.app")

@section("content")
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-3">
                    <i class="fas fa-chart-line"></i> Estatisticas e Metricas
                </h1>
                <p class="text-muted">Analise completa do sistema de faturacao</p>
            </div>
            <div>
                <select class="form-select" id="periodSelector">
                    <option value="today">Hoje</option>
                    <option value="week">Esta Semana</option>
                    <option value="month" selected>Este Mes</option>
                    <option value="quarter">Este Trimestre</option>
                    <option value="year">Este Ano</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Documentos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats["total_documents"] ?? 0) }}
                            </div>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> +12% vs mes anterior
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Receita Total
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats["total_revenue"] ?? 0, 2) }} Kz
                            </div>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> +8.5% vs mes anterior
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Taxa Sucesso AGT
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats["agt_success_rate"] ?? 0, 1) }}%
                            </div>
                            <div class="progress progress-sm mt-2">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $stats["agt_success_rate"] ?? 0 }}%"></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                IVA Arrecadado
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats["total_iva"] ?? 0, 2) }} Kz
                            </div>
                            <small class="text-muted">
                                14% de {{ number_format($stats["total_revenue"] ?? 0, 2) }} Kz
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Documentos Emitidos Ultimos 30 Dias</h6>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active" data-chart-type="line">Linha</button>
                        <button type="button" class="btn btn-outline-primary" data-chart-type="bar">Barra</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="documentsChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Distribuicao por Tipo</h6>
                </div>
                <div class="card-body">
                    <canvas id="typeDistributionChart"></canvas>
                    <div class="mt-3">
                        @foreach($stats["by_type"] ?? [] as $type => $count)
                        <div class="d-flex justify-content-between mb-2">
                            <span><span class="badge bg-primary">{{ $type }}</span></span>
                            <span class="font-weight-bold">{{ number_format($count) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status de Pagamento</h6>
                </div>
                <div class="card-body">
                    <canvas id="paymentStatusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Receita Mensal 2025</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyRevenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top 10 Clientes</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Posicao</th>
                                    <th>Cliente</th>
                                    <th>NIF</th>
                                    <th class="text-end">Documentos</th>
                                    <th class="text-end">Receita Total</th>
                                    <th class="text-end">Ticket Medio</th>
                                    <th>Ultimo Documento</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats["top_customers"] ?? [] as $index => $customer)
                                <tr>
                                    <td>
                                        @if($index === 0)
                                        <span class="badge bg-warning">1</span>
                                        @elseif($index === 1)
                                        <span class="badge bg-secondary">2</span>
                                        @elseif($index === 2)
                                        <span class="badge bg-danger">3</span>
                                        @else
                                        <span class="text-muted">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $customer->customer_name }}</td>
                                    <td><small class="text-muted">{{ $customer->customer_nif }}</small></td>
                                    <td class="text-end">{{ $customer->document_count }}</td>
                                    <td class="text-end">{{ number_format($customer->total_revenue, 2) }} Kz</td>
                                    <td class="text-end">{{ number_format($customer->avg_ticket, 2) }} Kz</td>
                                    <td><small>{{ $customer->last_document_date }}</small></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Nenhum dado disponivel</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Performance do Sistema</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Tempo Medio Geracao PDF</small>
                            <small class="font-weight-bold">2.3s</small>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: 46%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Tempo Medio Envio AGT</small>
                            <small class="font-weight-bold">1.8s</small>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-info" style="width: 36%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Queue Processing Rate</small>
                            <small class="font-weight-bold">95%</small>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-primary" style="width: 95%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Erros e Alertas</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-exclamation-triangle text-danger"></i> Erros AGT</span>
                            <span class="badge bg-danger">{{ $stats["agt_errors"] ?? 0 }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-hourglass-half text-warning"></i> Filas Pendentes</span>
                            <span class="badge bg-warning">{{ $stats["pending_queues"] ?? 0 }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-envelope text-info"></i> Emails Falhados</span>
                            <span class="badge bg-info">{{ $stats["failed_emails"] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Acoes Rapidas</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route("admin.fiscal.bulk") }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-tasks"></i> Operacoes em Massa
                        </a>
                        <a href="{{ route("admin.fiscal.audit") }}" class="btn btn-info btn-sm">
                            <i class="fas fa-history"></i> Historico de Auditoria
                        </a>
                        <a href="{{ route("admin.fiscal.export") }}" class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Exportar Relatorio
                        </a>
                        <button class="btn btn-warning btn-sm" onclick="refreshStats()">
                            <i class="fas fa-sync-alt"></i> Atualizar Estatisticas
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx1 = document.getElementById("documentsChart").getContext("2d");
    new Chart(ctx1, {
        type: "line",
        data: {
            labels: {!! json_encode($chartData["documents_labels"] ?? []) !!},
            datasets: [{
                label: "Documentos",
                data: {!! json_encode($chartData["documents_data"] ?? []) !!},
                borderColor: "rgb(75, 192, 192)",
                tension: 0.1,
                fill: true,
                backgroundColor: "rgba(75, 192, 192, 0.1)"
            }]
        }
    });

    const ctx2 = document.getElementById("typeDistributionChart").getContext("2d");
    new Chart(ctx2, {
        type: "doughnut",
        data: {
            labels: {!! json_encode($chartData["type_labels"] ?? []) !!},
            datasets: [{
                data: {!! json_encode($chartData["type_data"] ?? []) !!},
                backgroundColor: ["#4e73df", "#1cc88a", "#36b9cc", "#f6c23e", "#e74a3b"]
            }]
        }
    });

    const ctx3 = document.getElementById("paymentStatusChart").getContext("2d");
    new Chart(ctx3, {
        type: "pie",
        data: {
            labels: ["Pago", "Pendente", "Atrasado", "Parcial"],
            datasets: [{
                data: {!! json_encode($chartData["payment_data"] ?? []) !!},
                backgroundColor: ["#28a745", "#ffc107", "#dc3545", "#17a2b8"]
            }]
        }
    });

    const ctx4 = document.getElementById("monthlyRevenueChart").getContext("2d");
    new Chart(ctx4, {
        type: "bar",
        data: {
            labels: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
            datasets: [{
                label: "Receita Kz",
                data: {!! json_encode($chartData["monthly_revenue"] ?? []) !!},
                backgroundColor: "rgba(54, 162, 235, 0.5)",
                borderColor: "rgb(54, 162, 235)",
                borderWidth: 1
            }]
        }
    });
});

function refreshStats() {
    location.reload();
}
</script>

<style>
.border-left-primary {
    border-left: 4px solid #4e73df !important;
}
.border-left-success {
    border-left: 4px solid #1cc88a !important;
}
.border-left-info {
    border-left: 4px solid #36b9cc !important;
}
.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}
.progress-sm {
    height: 0.5rem;
}
</style>
@endsection
