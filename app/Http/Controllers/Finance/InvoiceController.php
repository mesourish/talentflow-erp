<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    App\Http\Controllers\Finance
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Project;
use App\Services\CodeGenerator;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use InvalidArgumentException;

/**
 * Class InvoiceController
 *
 * Generates GST-compliant tax invoices, client billing balances, PDF exports, and payment receipts.
 *
 * @package App\Http\Controllers\Finance
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class InvoiceController extends Controller
{
    public function __construct(
        protected CodeGenerator $codeGenerator,
        protected InvoiceService $invoiceService
    ) {}

    public function index(Request $request): View
    {
        $query = Invoice::with(['client', 'project', 'payments']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($cq) => $cq->where('company_name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $invoices = $query->latest('invoice_date')->paginate(12)->withQueryString();
        $statuses = ['draft', 'issued', 'unpaid', 'partially_paid', 'paid', 'overdue', 'cancelled'];

        $totalInvoiced = (float) Invoice::where('status', '!=', 'cancelled')->sum('grand_total');
        $totalCollected = (float) Invoice::sum('amount_paid');
        $totalOutstanding = max(0, $totalInvoiced - $totalCollected);

        return view('finance.invoices.index', compact('invoices', 'statuses', 'totalInvoiced', 'totalCollected', 'totalOutstanding'));
    }

    public function create(Request $request): View
    {
        $suggestedNo = $this->codeGenerator->invoiceNumber();
        $clients = Client::where('status', 'active')->orderBy('company_name')->get();
        $projects = Project::whereIn('status', ['planned', 'active', 'completed'])->orderBy('project_name')->get();

        $selectedProjectId = $request->input('project_id');

        return view('finance.invoices.create', compact('suggestedNo', 'clients', 'projects', 'selectedProjectId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_no' => 'required|string|max:50|unique:invoices,invoice_no',
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'required|exists:projects,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'currency' => 'required|string|size:3',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
        ]);

        $subtotal = 0.0;
        $taxTotal = 0.0;

        foreach ($validated['items'] as &$item) {
            $qty = (float) $item['quantity'];
            $rate = (float) $item['unit_price'];
            $lineAmount = round($qty * $rate, 2);
            $taxRate = (float) ($item['tax_rate'] ?? 0);
            $taxAmount = round($lineAmount * ($taxRate / 100), 2);
            $lineTotal = round($lineAmount + $taxAmount, 2);

            $item['tax_amount'] = $taxAmount;
            $item['line_total'] = $lineTotal;

            $subtotal += $lineAmount;
            $taxTotal += $taxAmount;
        }

        $grandTotal = round($subtotal + $taxTotal, 2);

        $invoice = DB::transaction(function () use ($validated, $subtotal, $taxTotal, $grandTotal) {
            $inv = Invoice::create([
                'invoice_no' => $validated['invoice_no'],
                'client_id' => $validated['client_id'],
                'project_id' => $validated['project_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'currency' => $validated['currency'],
                'subtotal' => $subtotal,
                'discount_total' => 0.00,
                'tax_total' => $taxTotal,
                'grand_total' => $grandTotal,
                'amount_paid' => 0.00,
                'status' => 'issued',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $inv->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'tax_amount' => $item['tax_amount'],
                    'line_total' => $item['line_total'],
                ]);
            }

            ActivityLog::log('invoice_created', "Generated Invoice {$inv->invoice_no} for grand total INR {$inv->grand_total}", $inv);

            return $inv;
        });

        return redirect()->route('admin.invoices.show', $invoice)->with('success', 'Invoice generated successfully.');
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['client.contacts', 'project', 'items', 'payments.receiver']);
        return view('finance.invoices.show', compact('invoice'));
    }

    public function recordPayment(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'payment_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        try {
            $this->invoiceService->recordPayment($invoice->id, $validated);
            return back()->with('success', 'Payment recorded successfully! Invoice balance updated.');
        } catch (DomainException|InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function pdf(Invoice $invoice): Response
    {
        $invoice->load(['client.contacts', 'project', 'items', 'payments']);

        $pdf = Pdf::loadView('finance.invoices.pdf', compact('invoice'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("Invoice_{$invoice->invoice_no}.pdf");
    }
}

