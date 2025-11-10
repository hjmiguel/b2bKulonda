@extends('admin.layouts.app')

@section('title', 'Dashboard Administrativo')
@section('header', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Dashboard Administrativo</h2>
    
    <div class="btn-group" role="group">
        <a href="?period=day" class="btn btn-sm btn-outline-primary {{ $period == 'day' ? 'active' : '' }}">Hoje</a>
        <a href="?period=week" class="btn btn-sm btn-outline-primary {{ $period == 'week' ? 'active' : '' }}">Semana</a>
        <a href="?period=month" class="btn btn-sm btn-outline-primary {{ $period == 'month' ? 'active' : '' }}">Mês</a>
        <a href="?period=year" class="btn btn-sm btn-outline-primary {{ $period == 'year' ? 'active' : '' }}">Ano</a>
    </div>
</div>

<!-- Alerts -->
@if (count($stats['alerts']) > 0)
<div class="mb-4">
    <h5 class="mb-3">Alertas do Sistema</h5>
    @foreach ($stats['alerts'] as $alert)
        <div class="alert-card alert-{{ $alert['type'] }}">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <strong>{{ $alert['title'] }}</strong>
                    <p class="mb-0 mt-1">{{ $alert['message'] }}</p>
                </div>
                <a href="{{ $alert['action_url'] }}" class="btn btn-sm btn-{{ $alert['type'] }}">Ver Detalhes</a>
            </div>
        </div>
    @endforeach
</div>
@endif

<!-- Overview Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card primary">
            <div class="stat-icon">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['overview']['total_documents'], 0, ',', '.') }}</div>
            <div class="stat-label">Total de Documentos</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card success">
            <div class="stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['overview']['total_revenue'], 0, ',', '.') }} Kz</div>
            <div class="stat-label">Receita Total</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card warning">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['overview']['pending_payment'], 0, ',', '.') }} Kz</div>
            <div class="stat-label">Pagamentos Pendentes</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card danger">
            <div class="stat-icon">
                <i class="fas fa-ban"></i>
            </div>
            <div class="stat-value">{{ $stats['overview']['cancelled_documents'] }}</div>
            <div class="stat-label">Documentos Cancelados</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Revenue Trend Chart -->
    <div class="col-md-8">
        <div class="chart-container">
            <h5 class="mb-4">Tendência de Receita</h5>
            <canvas id="revenueTrendChart" style="max-height: 300px;"></canvas>
        </div>
    </div>
    
    <!-- Documents by Type -->
    <div class="col-md-4">
        <div class="chart-container">
            <h5 class="mb-4">Documentos por Tipo</h5>
            <canvas id="documentsTypeChart" style="max-height: 300px;"></canvas>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- AGT Integration Stats -->
    <div class="col-md-6">
        <div class="table-container">
            <h5 class="mb-3">Integração AGT</h5>
            
            <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                <span>Taxa de Sucesso</span>
                <strong class="text-success fs-4">{{ $stats['agt_integration']['success_rate'] }}%</strong>
            </div>
            
            <table class="table table-borderless">
                <tbody>
                    <tr>
                        <td>Total de Documentos</td>
                        <td class="text-end"><strong>{{ $stats['agt_integration']['total_documents'] }}</strong></td>
                    </tr>
                    <tr class="table-success">
                        <td><i class="fas fa-check-circle text-success me-2"></i>Submetidos ao AGT</td>
                        <td class="text-end"><strong>{{ $stats['agt_integration']['submitted_to_agt'] }}</strong></td>
                    </tr>
                    <tr class="table-warning">
                        <td><i class="fas fa-clock text-warning me-2"></i>Aguardando Submissão</td>
                        <td class="text-end"><strong>{{ $stats['agt_integration']['pending_submission'] }}</strong></td>
                    </tr>
                </tbody>
            </table>
            
            <a href="{{ route('admin.agt.logs') }}" class="btn btn-outline-primary btn-sm w-100 mt-2">
                Ver Logs AGT <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
    
    <!-- Top Customers -->
    <div class="col-md-6">
        <div class="table-container">
            <h5 class="mb-3">Top 5 Clientes</h5>
            
            @if (count($stats['top_customers']) > 0)
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th class="text-end">Docs</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (array_slice($stats['top_customers'], 0, 5) as $customer)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $customer['customer_name'] }}</div>
                                <small class="text-muted">NIF: {{ $customer['customer_nif'] ?? 'N/A' }}</small>
                            </td>
                            <td class="text-end">{{ $customer['document_count'] }}</td>
                            <td class="text-end fw-bold">{{ number_format($customer['total_revenue'], 0, ',', '.') }} Kz</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <a href="{{ route('admin.reports.customers') }}" class="btn btn-outline-primary btn-sm w-100 mt-2">
                    Ver Todos os Clientes <i class="fas fa-arrow-right ms-1"></i>
                </a>
            @else
                <p class="text-muted text-center py-4">Nenhum cliente no período selecionado</p>
            @endif
        </div>
    </div>
</div>

<!-- Recent Documents -->
<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Documentos Recentes</h5>
        <a href="{{ route('fiscal.documents.index') }}" class="btn btn-sm btn-outline-primary">
            Ver Todos <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>
    
    @if (count($stats['recent_documents']) > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Tipo</th>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th class="text-end">Total</th>
                        <th>Status</th>
                        <th>AGT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stats['recent_documents'] as $doc)
                    <tr>
                        <td>
                            <a href="{{ route('fiscal.documents.show', $doc['id']) }}" class="fw-bold text-decoration-none">
                                {{ $doc['document_number'] ?? 'Rascunho' }}
                            </a>
                        </td>
                        <td><span class="badge bg-secondary">{{ $doc['document_type'] }}</span></td>
                        <td>{{ Str::limit($doc['customer_name'], 30) }}</td>
                        <td>{{ \Carbon\Carbon::parse($doc['issue_date'])->format('d/m/Y H:i') }}</td>
                        <td class="text-end fw-bold">{{ number_format($doc['total'], 2, ',', '.') }} Kz</td>
                        <td>
                            @if ($doc['status'] == 'issued')
                                <span class="badge bg-success">Emitido</span>
                            @elseif ($doc['status'] == 'cancelled')
                                <span class="badge bg-danger">Cancelado</span>
                            @else
                                <span class="badge bg-secondary">Rascunho</span>
                            @endif
                        </td>
                        <td>
                            @if ($doc['agt_hash'])
                                <i class="fas fa-check-circle text-success" title="Submetido ao AGT"></i>
                            @else
                                <i class="fas fa-clock text-warning" title="Aguardando submissão"></i>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-muted text-center py-4">Nenhum documento encontrado</p>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Revenue Trend Chart
const revenueTrendCtx = document.getElementById('revenueTrendChart').getContext('2d');
const revenueTrendData = @json($stats['revenue_trend']);

new Chart(revenueTrendCtx, {
    type: 'line',
    data: {
        labels: revenueTrendData.map(item => item.date),
        datasets: [{
            label: 'Receita (Kz)',
            data: revenueTrendData.map(item => item.revenue),
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37, 99, 235, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString() + ' Kz';
                    }
                }
            }
        }
    }
});

// Documents by Type Chart
const documentsTypeCtx = document.getElementById('documentsTypeChart').getContext('2d');
const documentsTypeData = @json($stats['documents_by_type']);

new Chart(documentsTypeCtx, {
    type: 'doughnut',
    data: {
        labels: Object.keys(documentsTypeData),
        datasets: [{
            data: Object.values(documentsTypeData).map(item => item.count),
            backgroundColor: [
                '#2563eb',
                '#059669',
                '#d97706',
                '#dc2626',
                '#7c3aed',
                '#0891b2'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endpush
