<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FiscalDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;

class ReportsController extends Controller
{
    /**
     * Display reports index
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Generate sales report
     */
    public function sales(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'document_type' => 'nullable|string',
            'format' => 'nullable|in:html,pdf,excel',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $documentType = $request->document_type;
        $format = $request->get('format', 'html');

        // Build query
        $query = FiscalDocument::with(['items', 'order'])
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->where('status', 'issued');

        if ($documentType) {
            $query->where('document_type', $documentType);
        }

        $documents = $query->orderBy('issue_date', 'desc')->get();

        // Calculate totals
        $totals = [
            'count' => $documents->count(),
            'subtotal' => $documents->sum('subtotal'),
            'tax' => $documents->sum('tax'),
            'discount' => $documents->sum('discount'),
            'total' => $documents->sum('total'),
        ];

        // Group by date
        $dailySales = $documents->groupBy(function($doc) {
            return $doc->issue_date->format('Y-m-d');
        })->map(function($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total'),
            ];
        });

        $data = compact('documents', 'totals', 'dailySales', 'startDate', 'endDate', 'documentType');

        if ($format === 'pdf') {
            return $this->generatePDF('admin.reports.sales-pdf', $data, "relatorio-vendas-{$startDate->format('Y-m-d')}-{$endDate->format('Y-m-d')}.pdf");
        }

        return view('admin.reports.sales', $data);
    }

    /**
     * Generate tax report
     */
    public function taxes(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'nullable|in:html,pdf',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $format = $request->get('format', 'html');

        // Get tax breakdown by rate
        $taxBreakdown = FiscalDocument::select(
                'tax_rate',
                DB::raw('count(*) as document_count'),
                DB::raw('sum(subtotal) as taxable_amount'),
                DB::raw('sum(tax) as tax_amount'),
                DB::raw('sum(total) as total_amount')
            )
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->where('status', 'issued')
            ->groupBy('tax_rate')
            ->orderBy('tax_rate')
            ->get();

        // Get tax by document type
        $taxByType = FiscalDocument::select(
                'document_type',
                DB::raw('count(*) as document_count'),
                DB::raw('sum(tax) as tax_amount'),
                DB::raw('sum(total) as total_amount')
            )
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->where('status', 'issued')
            ->groupBy('document_type')
            ->get();

        // Calculate totals
        $totals = [
            'taxable_base' => $taxBreakdown->sum('taxable_amount'),
            'total_tax' => $taxBreakdown->sum('tax_amount'),
            'total_with_tax' => $taxBreakdown->sum('total_amount'),
        ];

        $data = compact('taxBreakdown', 'taxByType', 'totals', 'startDate', 'endDate');

        if ($format === 'pdf') {
            return $this->generatePDF('admin.reports.taxes-pdf', $data, "relatorio-impostos-{$startDate->format('Y-m-d')}.pdf");
        }

        return view('admin.reports.taxes', $data);
    }

    /**
     * Generate customer report
     */
    public function customers(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'min_amount' => 'nullable|numeric|min:0',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $minAmount = $request->get('min_amount', 0);

        $customers = FiscalDocument::select(
                'customer_name',
                'customer_nif',
                'customer_email',
                'customer_phone',
                DB::raw('count(*) as document_count'),
                DB::raw('sum(total) as total_revenue'),
                DB::raw('avg(total) as avg_ticket'),
                DB::raw('max(issue_date) as last_purchase')
            )
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->where('status', 'issued')
            ->whereNotNull('customer_name')
            ->groupBy('customer_name', 'customer_nif', 'customer_email', 'customer_phone')
            ->havingRaw('sum(total) >= ?', [$minAmount])
            ->orderByDesc('total_revenue')
            ->paginate(50);

        return view('admin.reports.customers', compact('customers', 'startDate', 'endDate', 'minAmount'));
    }

    /**
     * Generate AGT submission report
     */
    public function agtSubmissions(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|in:all,submitted,pending,failed',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $status = $request->get('status', 'all');

        $query = FiscalDocument::whereBetween('issue_date', [$startDate, $endDate])
            ->where('status', 'issued');

        switch ($status) {
            case 'submitted':
                $query->whereNotNull('agt_hash');
                break;
            case 'pending':
                $query->whereNull('agt_hash');
                break;
            case 'failed':
                $query->whereNull('agt_hash')
                    ->where('created_at', '<', Carbon::now()->subHours(2));
                break;
        }

        $documents = $query->orderBy('issue_date', 'desc')->paginate(50);

        $stats = [
            'total' => FiscalDocument::whereBetween('issue_date', [$startDate, $endDate])
                ->where('status', 'issued')->count(),
            'submitted' => FiscalDocument::whereBetween('issue_date', [$startDate, $endDate])
                ->where('status', 'issued')
                ->whereNotNull('agt_hash')->count(),
            'pending' => FiscalDocument::whereBetween('issue_date', [$startDate, $endDate])
                ->where('status', 'issued')
                ->whereNull('agt_hash')->count(),
        ];

        return view('admin.reports.agt-submissions', compact('documents', 'stats', 'startDate', 'endDate', 'status'));
    }

    /**
     * Generate document sequence report
     */
    public function sequences(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $sequences = DB::table('fiscal_sequences')
            ->where('year', $year)
            ->orderBy('document_type')
            ->orderBy('serie')
            ->get();

        // Get actual document counts
        $actualCounts = FiscalDocument::select(
                'document_type',
                'serie',
                'year',
                DB::raw('count(*) as actual_count')
            )
            ->where('year', $year)
            ->groupBy('document_type', 'serie', 'year')
            ->get()
            ->keyBy(function($item) {
                return "{$item->document_type}-{$item->serie}";
            });

        return view('admin.reports.sequences', compact('sequences', 'actualCounts', 'year'));
    }

    /**
     * Generate PDF from view
     */
    protected function generatePDF(string $view, array $data, string $filename)
    {
        $pdf = PDF::loadView($view, $data);
        return $pdf->download($filename);
    }
}
