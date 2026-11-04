<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    App\Http\Controllers\Quotations
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace App\Http\Controllers\Quotations;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Celebrity;
use App\Models\Client;
use App\Models\Lead;
use App\Models\Quotation;
use App\Models\QuotationApproval;
use App\Models\QuotationItem;
use App\Services\CodeGenerator;
use App\Services\QuotationCalculator;
use App\Services\QuotationConversionService;
use Barryvdh\DomPDF\Facade\Pdf;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Class QuotationController
 *
 * Builds multi-artist commercial proposals, automated line items, GST taxation, and project conversions.
 *
 * @package App\Http\Controllers\Quotations
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class QuotationController extends Controller
{
    public function __construct(
        protected CodeGenerator $codeGenerator,
        protected QuotationCalculator $calculator,
        protected QuotationConversionService $conversionService
    ) {}

    public function index(Request $request): View
    {
        $query = Quotation::with(['client', 'creator']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('quotation_no', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($cq) => $cq->where('company_name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $quotations = $query->latest('quotation_date')->paginate(12)->withQueryString();
        $statuses = ['draft', 'sent', 'under_negotiation', 'approved', 'rejected', 'converted', 'expired'];

        return view('quotations.index', compact('quotations', 'statuses'));
    }

    public function create(): View
    {
        $suggestedNo = $this->codeGenerator->quotationNumber();
        $clients = Client::where('status', 'active')->orderBy('company_name')->get();
        $celebrities = Celebrity::with('activeRates')->where('profile_status', 'active')->orderBy('stage_name')->get();
        $leads = Lead::whereIn('stage', ['new', 'contacted', 'proposal', 'negotiation'])->get();

        return view('quotations.create', compact('suggestedNo', 'clients', 'celebrities', 'leads'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'quotation_no' => 'required|string|max:50|unique:quotations,quotation_no',
            'client_id' => 'required|exists:clients,id',
            'lead_id' => 'nullable|exists:leads,id',
            'quotation_date' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:quotation_date',
            'currency' => 'required|string|size:3',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.service_name' => 'required|string|max:150',
            'items.*.celebrity_id' => 'nullable|exists:celebrities,id',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:30',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
        ]);

        $calculated = $this->calculator->calculate($validated['items']);

        $quotation = DB::transaction(function () use ($validated, $calculated) {
            $quote = Quotation::create([
                'quotation_no' => $validated['quotation_no'],
                'client_id' => $validated['client_id'],
                'lead_id' => $validated['lead_id'],
                'version_no' => 1,
                'status' => 'draft',
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'],
                'currency' => $validated['currency'],
                'subtotal' => $calculated['subtotal'],
                'discount_total' => $calculated['discount_total'],
                'tax_total' => $calculated['tax_total'],
                'grand_total' => $calculated['grand_total'],
                'terms_conditions' => $validated['terms_conditions'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id() ?? 1,
            ]);

            foreach ($calculated['items'] as $index => $item) {
                QuotationItem::create([
                    'quotation_id' => $quote->id,
                    'celebrity_id' => $item['celebrity_id'] ?? null,
                    'service_name' => $item['service_name'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'tax_amount' => $item['tax_amount'],
                    'line_total' => $item['line_total'],
                    'sort_order' => $index + 1,
                ]);
            }

            ActivityLog::log('quotation_created', "Created Quotation {$quote->quotation_no} for grand total INR {$quote->grand_total}", $quote);

            return $quote;
        });

        return redirect()->route('admin.quotations.show', $quotation)->with('success', 'Quotation drafted successfully.');
    }

    public function show(Quotation $quotation): View
    {
        $quotation->load([
            'client.contacts',
            'items.celebrity',
            'approvals.user',
            'project',
            'creator',
            'approver',
        ]);

        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation): View|RedirectResponse
    {
        if (in_array($quotation->status, ['converted'])) {
            return back()->with('error', 'Cannot edit an already converted quotation.');
        }

        $clients = Client::where('status', 'active')->orderBy('company_name')->get();
        $celebrities = Celebrity::with('activeRates')->where('profile_status', 'active')->orderBy('stage_name')->get();
        $leads = Lead::whereIn('stage', ['new', 'contacted', 'proposal', 'negotiation'])->get();

        return view('quotations.edit', compact('quotation', 'clients', 'celebrities', 'leads'));
    }

    public function update(Request $request, Quotation $quotation): RedirectResponse
    {
        if ($quotation->status === 'converted') {
            return back()->with('error', 'Converted quotations cannot be modified.');
        }

        $validated = $request->validate([
            'quotation_date' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:quotation_date',
            'currency' => 'required|string|size:3',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.service_name' => 'required|string|max:150',
            'items.*.celebrity_id' => 'nullable|exists:celebrities,id',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:30',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
        ]);

        $calculated = $this->calculator->calculate($validated['items']);

        DB::transaction(function () use ($quotation, $validated, $calculated) {
            $quotation->update([
                'version_no' => $quotation->version_no + 1,
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'],
                'currency' => $validated['currency'],
                'subtotal' => $calculated['subtotal'],
                'discount_total' => $calculated['discount_total'],
                'tax_total' => $calculated['tax_total'],
                'grand_total' => $calculated['grand_total'],
                'terms_conditions' => $validated['terms_conditions'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'draft', // Requires re-submission if revised
            ]);

            $quotation->items()->delete();

            foreach ($calculated['items'] as $index => $item) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'celebrity_id' => $item['celebrity_id'] ?? null,
                    'service_name' => $item['service_name'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'tax_amount' => $item['tax_amount'],
                    'line_total' => $item['line_total'],
                    'sort_order' => $index + 1,
                ]);
            }

            ActivityLog::log('quotation_updated', "Quotation {$quotation->quotation_no} revised to v{$quotation->version_no}", $quotation);
        });

        return redirect()->route('admin.quotations.show', $quotation)->with('success', 'Quotation revised successfully.');
    }

    public function submit(Request $request, Quotation $quotation): RedirectResponse
    {
        $quotation->update(['status' => 'sent']);

        QuotationApproval::create([
            'quotation_id' => $quotation->id,
            'user_id' => auth()->id() ?? 1,
            'action' => 'submitted',
            'comments' => $request->input('comments', 'Submitted for review and client sign-off.'),
            'action_at' => now(),
        ]);

        ActivityLog::log('quotation_submitted', "Quotation {$quotation->quotation_no} submitted for approval.", $quotation);

        return back()->with('success', 'Quotation submitted successfully.');
    }

    public function approve(Request $request, Quotation $quotation): RedirectResponse
    {
        $quotation->update([
            'status' => 'approved',
            'approved_by' => auth()->id() ?? 1,
            'approved_at' => now(),
        ]);

        QuotationApproval::create([
            'quotation_id' => $quotation->id,
            'user_id' => auth()->id() ?? 1,
            'action' => 'approved',
            'comments' => $request->input('comments', 'Commercial pricing approved.'),
            'action_at' => now(),
        ]);

        ActivityLog::log('quotation_approved', "Quotation {$quotation->quotation_no} approved.", $quotation);

        return back()->with('success', 'Quotation approved.');
    }

    public function reject(Request $request, Quotation $quotation): RedirectResponse
    {
        $quotation->update(['status' => 'rejected']);

        QuotationApproval::create([
            'quotation_id' => $quotation->id,
            'user_id' => auth()->id() ?? 1,
            'action' => 'rejected',
            'comments' => $request->input('comments', 'Rejected / needs repricing.'),
            'action_at' => now(),
        ]);

        ActivityLog::log('quotation_rejected', "Quotation {$quotation->quotation_no} rejected.", $quotation);

        return back()->with('info', 'Quotation marked as rejected.');
    }

    public function convert(Request $request, Quotation $quotation): RedirectResponse
    {
        try {
            $project = $this->conversionService->convert(
                $quotation->id,
                $request->input('project_name'),
                $request->input('project_type')
            );

            return redirect()->route('admin.projects.show', $project)->with('success', "Quotation converted successfully into Project {$project->project_code}!");
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function pdf(Quotation $quotation): Response
    {
        $quotation->load(['client.contacts', 'items.celebrity', 'creator']);

        $pdf = Pdf::loadView('quotations.pdf', compact('quotation'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("Quotation_{$quotation->quotation_no}.pdf");
    }

    public function calculateAjax(Request $request): JsonResponse
    {
        $items = $request->input('items', []);
        try {
            $result = $this->calculator->calculate($items);
            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}

