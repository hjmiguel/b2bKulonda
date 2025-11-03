<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FiscalDocument;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard with statistics
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year
        $dateRange = $this->getDateRange($period);

        $stats = [
            'overview' => $this->getOverviewStats($dateRange),
            'documents_by_type' => $this->getDocumentsByType($dateRange),
            'documents_by_status' => $this->getDocumentsByStatus($dateRange),
            'revenue_trend' => $this->getRevenueTrend($period, $dateRange),
            'top_customers' => $this->getTopCustomers($dateRange),
            'agt_integration' => $this->getAGTIntegrationStats($dateRange),
            'recent_documents' => $this->getRecentDocuments(),
            'alerts' => $this->getSystemAlerts(),
        ];

        return view('admin.dashboard.index', compact('stats', 'period'));
    }

    /**
     * Get date range based on period
     */
    protected function getDateRange(string $period): array
    {
        $end = Carbon::now();
        
        switch ($period) {
            case 'day':
                $start = Carbon::today();
                break;
            case 'week':
                $start = Carbon::now()->startOfWeek();
                break;
            case 'year':
                $start = Carbon::now()->startOfYear();
                break;
            case 'month':
            default:
                $start = Carbon::now()->startOfMonth();
                break;
        }

        return ['start' => $start, 'end' => $end];
    }

    /**
     * Get overview statistics
     */
    protected function getOverviewStats(array $dateRange): array
    {
        $documents = FiscalDocument::whereBetween('issue_date', [$dateRange['start'], $dateRange['end']]);
        
        return [
            'total_documents' => $documents->count(),
            'total_revenue' => $documents->where('status', 'issued')->sum('total'),
            'issued_documents' => $documents->where('status', 'issued')->count(),
            'cancelled_documents' => $documents->where('status', 'cancelled')->count(),
            'pending_payment' => FiscalDocument::where('payment_status', 'pending')
                ->whereBetween('issue_date', [$dateRange['start'], $dateRange['end']])
                ->sum('total'),
            'paid_amount' => FiscalDocument::where('payment_status', 'paid')
                ->whereBetween('issue_date', [$dateRange['start'], $dateRange['end']])
                ->sum('total'),
        ];
    }

    /**
     * Get documents grouped by type
     */
    protected function getDocumentsByType(array $dateRange): array
    {
        return FiscalDocument::select('document_type', DB::raw('count(*) as count'), DB::raw('sum(total) as total'))
            ->whereBetween('issue_date', [$dateRange['start'], $dateRange['end']])
            ->where('status', 'issued')
            ->groupBy('document_type')
            ->get()
            ->keyBy('document_type')
            ->toArray();
    }

    /**
     * Get documents grouped by status
     */
    protected function getDocumentsByStatus(array $dateRange): array
    {
        return FiscalDocument::select('status', DB::raw('count(*) as count'))
            ->whereBetween('issue_date', [$dateRange['start'], $dateRange['end']])
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
    }

    /**
     * Get revenue trend over time
     */
    protected function getRevenueTrend(string $period, array $dateRange): array
    {
        $groupBy = $this->getGroupByFormat($period);
        
        return FiscalDocument::select(
                DB::raw("{$groupBy} as date"),
                DB::raw('sum(total) as revenue'),
                DB::raw('count(*) as count')
            )
            ->whereBetween('issue_date', [$dateRange['start'], $dateRange['end']])
            ->where('status', 'issued')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    /**
     * Get SQL group by format based on period
     */
    protected function getGroupByFormat(string $period): string
    {
        switch ($period) {
            case 'day':
                return "DATE_FORMAT(issue_date, '%Y-%m-%d %H:00')";
            case 'week':
            case 'month':
                return "DATE(issue_date)";
            case 'year':
                return "DATE_FORMAT(issue_date, '%Y-%m')";
            default:
                return "DATE(issue_date)";
        }
    }

    /**
     * Get top customers by revenue
     */
    protected function getTopCustomers(array $dateRange): array
    {
        return FiscalDocument::select(
                'customer_name',
                'customer_nif',
                DB::raw('count(*) as document_count'),
                DB::raw('sum(total) as total_revenue')
            )
            ->whereBetween('issue_date', [$dateRange['start'], $dateRange['end']])
            ->where('status', 'issued')
            ->whereNotNull('customer_name')
            ->groupBy('customer_name', 'customer_nif')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get AGT integration statistics
     */
    protected function getAGTIntegrationStats(array $dateRange): array
    {
        $total = FiscalDocument::whereBetween('issue_date', [$dateRange['start'], $dateRange['end']])
            ->where('status', 'issued')
            ->count();

        $submitted = FiscalDocument::whereBetween('issue_date', [$dateRange['start'], $dateRange['end']])
            ->where('status', 'issued')
            ->whereNotNull('agt_hash')
            ->count();

        $pending = $total - $submitted;

        return [
            'total_documents' => $total,
            'submitted_to_agt' => $submitted,
            'pending_submission' => $pending,
            'success_rate' => $total > 0 ? round(($submitted / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get recent documents
     */
    protected function getRecentDocuments(): array
    {
        return FiscalDocument::with(['order', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get system alerts and warnings
     */
    protected function getSystemAlerts(): array
    {
        $alerts = [];

        // Check for documents pending AGT submission
        $pendingAGT = FiscalDocument::where('status', 'issued')
            ->whereNull('agt_hash')
            ->where('issue_date', '>', Carbon::now()->subDays(7))
            ->count();

        if ($pendingAGT > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Documentos Pendentes AGT',
                'message' => "{$pendingAGT} documentos emitidos aguardam submissão ao AGT.",
                'action_url' => route('admin.agt.logs'),
            ];
        }

        // Check for cancelled documents
        $recentCancelled = FiscalDocument::where('status', 'cancelled')
            ->where('updated_at', '>', Carbon::now()->subDay())
            ->count();

        if ($recentCancelled > 5) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Documentos Cancelados',
                'message' => "{$recentCancelled} documentos foram cancelados nas últimas 24h.",
                'action_url' => route('fiscal.documents.index', ['status' => 'cancelled']),
            ];
        }

        // Check for unpaid invoices
        $overdueInvoices = FiscalDocument::where('payment_status', 'pending')
            ->where('payment_due_date', '<', Carbon::now())
            ->count();

        if ($overdueInvoices > 0) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'Faturas Vencidas',
                'message' => "{$overdueInvoices} faturas com pagamento em atraso.",
                'action_url' => route('fiscal.documents.index', ['payment_status' => 'overdue']),
            ];
        }

        return $alerts;
    }
}
