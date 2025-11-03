<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Kulonda</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --header-height: 60px;
            --primary-color: #2563eb;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s;
        }
        
        .admin-sidebar .brand {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 20px;
            font-weight: 700;
        }
        
        .admin-sidebar .nav {
            padding: 15px 0;
        }
        
        .admin-sidebar .nav-link {
            color: #cbd5e1;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        
        .admin-sidebar .nav-link:hover {
            background: var(--sidebar-hover);
            color: white;
        }
        
        .admin-sidebar .nav-link.active {
            background: var(--sidebar-hover);
            color: white;
            border-left-color: var(--primary-color);
        }
        
        .admin-sidebar .nav-link i {
            width: 20px;
            text-align: center;
        }
        
        /* Header */
        .admin-header {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 999;
        }
        
        /* Main Content */
        .admin-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 30px;
            min-height: calc(100vh - var(--header-height));
        }
        
        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 16px;
        }
        
        .stat-card.primary .stat-icon {
            background: #dbeafe;
            color: #2563eb;
        }
        
        .stat-card.success .stat-icon {
            background: #d1fae5;
            color: #059669;
        }
        
        .stat-card.warning .stat-icon {
            background: #fef3c7;
            color: #d97706;
        }
        
        .stat-card.danger .stat-icon {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
        }
        
        /* Alerts */
        .alert-card {
            border-left: 4px solid;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
        }
        
        .alert-card.alert-warning {
            background: #fffbeb;
            border-color: #f59e0b;
        }
        
        .alert-card.alert-danger {
            background: #fef2f2;
            border-color: #ef4444;
        }
        
        .alert-card.alert-info {
            background: #eff6ff;
            border-color: #3b82f6;
        }
        
        /* Chart Container */
        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        /* Table */
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .table-hover tbody tr:hover {
            background-color: #f8fafc;
        }
        
        /* Badge */
        .badge {
            padding: 6px 12px;
            font-weight: 600;
            border-radius: 6px;
        }
        
        /* Mobile */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            
            .admin-sidebar.show {
                transform: translateX(0);
            }
            
            .admin-content {
                margin-left: 0;
            }
            
            .admin-header {
                left: 0;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="admin-sidebar" id="adminSidebar">
        <div class="brand">
            <i class="fas fa-chart-line me-2"></i>
            Kulonda Admin
        </div>
        
        <nav class="nav flex-column">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            
            <div class="px-3 pt-3 pb-2 text-white-50 small text-uppercase">Documentos</div>
            
            <a href="{{ route('fiscal.documents.index') }}" class="nav-link {{ request()->routeIs('fiscal.documents.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice"></i>
                <span>Documentos Fiscais</span>
            </a>
            
            <div class="px-3 pt-3 pb-2 text-white-50 small text-uppercase">Relatórios</div>
            
            <a href="{{ route('admin.reports.index') }}" class="nav-link">
                <i class="fas fa-chart-bar"></i>
                <span>Relatórios</span>
            </a>
            
            <a href="{{ route('admin.reports.sales') }}" class="nav-link">
                <i class="fas fa-money-bill-wave"></i>
                <span>Vendas</span>
            </a>
            
            <a href="{{ route('admin.reports.taxes') }}" class="nav-link">
                <i class="fas fa-percentage"></i>
                <span>Impostos</span>
            </a>
            
            <a href="{{ route('admin.reports.customers') }}" class="nav-link">
                <i class="fas fa-users"></i>
                <span>Clientes</span>
            </a>
            
            <div class="px-3 pt-3 pb-2 text-white-50 small text-uppercase">Sistema</div>
            
            <a href="{{ route('admin.sequences.index') }}" class="nav-link">
                <i class="fas fa-hashtag"></i>
                <span>Sequências</span>
            </a>
            
            <a href="{{ route('admin.agt.logs') }}" class="nav-link">
                <i class="fas fa-exchange-alt"></i>
                <span>Integração AGT</span>
            </a>
            
            <a href="{{ route('admin.agt.status') }}" class="nav-link">
                <i class="fas fa-heartbeat"></i>
                <span>Status AGT</span>
            </a>
        </nav>
    </div>
    
    <!-- Header -->
    <div class="admin-header">
        <div>
            <button class="btn btn-link d-md-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h5 class="mb-0 d-none d-md-inline">@yield('header', 'Dashboard')</h5>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-external-link-alt me-1"></i>
                Ver Site
            </a>
            
            <div class="dropdown">
                <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle fa-lg"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text small">{{ auth()->user()->name ?? 'Admin' }}</span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt me-2"></i>Sair
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="admin-content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if (session('error') || $errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') ?? $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @yield('content')
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('adminSidebar').classList.toggle('show');
        });
        
        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
    
    @stack('scripts')
</body>
</html>
