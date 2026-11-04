@extends('layouts.app')

@section('title', 'Invoice: ' . $invoice->invoice_no)

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ paymentModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded text-xs font-mono font-bold bg-gray-100 text-gray-800">{{ $invoice->invoice_no }}</span>
                @php
                    $invBadge = [
                        'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'partially_paid' => 'bg-amber-100 text-amber-800 border-amber-200',
                        'issued' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'unpaid' => 'bg-amber-100 text-amber-800 border-amber-200',
                        'overdue' => 'bg-red-100 text-red-800 border-red-200',
                        'draft' => 'bg-gray-100 text-gray-800 border-gray-200',
                        'cancelled' => 'bg-gray-200 text-gray-700',
                    ];
                @endphp
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border uppercase {{ $invBadge[$invoice->status] ?? 'bg-gray-100' }}">
                    {{ str_replace('_', ' ', $invoice->status) }}
                </span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">₹{{ number_format($invoice->grand_total, 2) }}</h1>
            <p class="text-xs text-gray-500 mt-1">
                Issued on {{ $invoice->invoice_date?->format('d M Y') }} • Due on {{ $invoice->due_date?->format('d M Y') }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if($invoice->balance_due > 0 && $invoice->status !== 'cancelled')
                <button @click="paymentModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-sm flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Record Payment
                </button>
            @endif

            <a href="{{ route('admin.invoices.pdf', $invoice) }}" target="_blank" class="px-3.5 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                PDF Invoice
            </a>

            <a href="{{ route('admin.invoices.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-medium text-gray-700">Back</a>
        </div>
    </div>

    <!-- Client & Project Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Client Card -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-2 text-xs">
            <span class="font-bold uppercase tracking-wider text-gray-400 block">Billed To (Client)</span>
            <p class="font-bold text-gray-900 text-sm">{{ $invoice->client?->company_name }}</p>
            <p class="text-gray-600">{{ $invoice->client?->billing_address ?? 'Address on file' }}</p>
            <p class="text-gray-600">GSTIN: <span class="font-mono font-semibold">{{ $invoice->client?->tax_number ?? 'Unregistered' }}</span></p>
            <p class="text-gray-600">Email: {{ $invoice->client?->billing_email }}</p>
        </div>

        <!-- Project & Status Card -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-2 text-xs">
            <span class="font-bold uppercase tracking-wider text-gray-400 block">Project & Terms</span>
            <div>
                <span class="text-gray-400">Associated Project:</span>
                @if($invoice->project)
                    <a href="{{ route('admin.projects.show', $invoice->project) }}" class="font-bold text-indigo-600 hover:underline block text-sm">
                        {{ $invoice->project->project_name }} ({{ $invoice->project->project_code }})
                    </a>
                @else
                    <span class="font-semibold text-gray-700 block">Direct / Retainer</span>
                @endif
            </div>
            <div class="pt-2 border-t flex justify-between">
                <span class="text-gray-500">Amount Received:</span>
                <span class="font-bold text-emerald-600">₹{{ number_format($invoice->amount_paid, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Balance Due:</span>
                <span class="font-bold text-red-600">₹{{ number_format($invoice->balance_due, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
        <h3 class="text-base font-bold text-gray-900">Invoice Items</h3>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-left">Description</th>
                        <th class="px-4 py-3 text-right">Qty</th>
                        <th class="px-4 py-3 text-right">Rate</th>
                        <th class="px-4 py-3 text-right">GST %</th>
                        <th class="px-4 py-3 text-right">Tax (₹)</th>
                        <th class="px-4 py-3 text-right">Total (₹)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($invoice->items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $item->description }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-4 py-3 text-right text-gray-500">{{ $item->tax_rate }}%</td>
                            <td class="px-4 py-3 text-right text-gray-600">₹{{ number_format($item->tax_amount, 2) }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">₹{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary Totals -->
        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 max-w-xs ml-auto space-y-2 text-xs">
            <div class="flex justify-between text-gray-600">
                <span>Taxable Subtotal:</span>
                <span class="font-semibold text-gray-900">₹{{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>GST Tax (18%):</span>
                <span class="font-semibold text-gray-900">₹{{ number_format($invoice->tax_total, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm font-bold text-gray-900 border-t pt-2">
                <span>Grand Total:</span>
                <span class="text-indigo-600">₹{{ number_format($invoice->grand_total, 2) }}</span>
            </div>
            <div class="flex justify-between text-xs text-emerald-600 font-semibold">
                <span>Amount Paid:</span>
                <span>₹{{ number_format($invoice->amount_paid, 2) }}</span>
            </div>
            <div class="flex justify-between text-xs text-red-600 font-bold border-t pt-1">
                <span>Balance Due:</span>
                <span>₹{{ number_format($invoice->balance_due, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Payments Received History -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
        <h3 class="text-base font-bold text-gray-900">Payment Transactions</h3>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-left">Method</th>
                        <th class="px-4 py-3 text-left">Transaction / UTR #</th>
                        <th class="px-4 py-3 text-left">Recorded By</th>
                        <th class="px-4 py-3 text-right">Amount Received</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoice->payments as $pmt)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-600">{{ $pmt->payment_date?->format('d M Y') }}</td>
                            <td class="px-4 py-3 capitalize font-medium text-gray-800">{{ str_replace('_', ' ', $pmt->payment_method) }}</td>
                            <td class="px-4 py-3 font-mono text-gray-700">{{ $pmt->payment_reference ?: 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $pmt->receiver?->name ?? 'Finance' }}</td>
                            <td class="px-4 py-3 text-right font-bold text-emerald-600">₹{{ number_format($pmt->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-400">No payments recorded against this invoice yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Remittance Notes -->
    @if($invoice->notes)
        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 text-xs text-gray-600 space-y-1">
            <span class="font-bold text-gray-700 block uppercase">Remittance & Banking Details:</span>
            <p class="whitespace-pre-line leading-relaxed">{{ $invoice->notes }}</p>
        </div>
    @endif

    <!-- RECORD PAYMENT MODAL -->
    <div x-show="paymentModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="paymentModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Record Client Payment</h3>
                    <button @click="paymentModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <form method="POST" action="{{ route('admin.invoices.payments.store', $invoice) }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Payment Amount (₹) *</label>
                        <input type="number" step="0.01" max="{{ $invoice->balance_due }}" name="amount" value="{{ $invoice->balance_due }}" required class="w-full px-3 py-2 border rounded-lg text-sm font-bold text-emerald-600">
                        <span class="text-[11px] text-gray-500">Remaining Balance: ₹{{ number_format($invoice->balance_due, 2) }}</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Payment Date *</label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Payment Mode *</label>
                        <select name="payment_method" required class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="bank_transfer" selected>NEFT / RTGS / IMPS Wire</option>
                            <option value="upi">UPI / Net Banking</option>
                            <option value="cheque">Cheque / Demand Draft</option>
                            <option value="credit_card">Corporate Credit Card</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Transaction Ref / UTR / Cheque #</label>
                        <input type="text" name="payment_reference" placeholder="e.g. UTR-HDFC982341209" class="w-full px-3 py-2 border rounded-lg text-sm font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Notes / Remarks</label>
                        <textarea name="notes" rows="2" class="w-full px-3 py-2 border rounded-lg text-xs" placeholder="e.g. Received partial advance installment"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="paymentModal = false" class="px-4 py-2 border rounded-lg text-xs">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold">Confirm & Update Balance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

