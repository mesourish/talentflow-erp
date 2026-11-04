@extends('layouts.app')

@section('title', 'Raise New Invoice')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="invoiceForm()">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Raise Client Tax Invoice</h1>
            <p class="text-sm text-gray-500 mt-1">Generate GST invoice against client campaign or event milestones</p>
        </div>
        <a href="{{ route('admin.invoices.index') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Invoices
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            <p class="font-semibold mb-1">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.invoices.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Invoice Number -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Invoice Number *</label>
                <input type="text" name="invoice_no" value="{{ old('invoice_no', $suggestedNo) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono bg-gray-50 focus:bg-white focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Client -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Client *</label>
                <select name="client_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Client...</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->company_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Project -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Project *</label>
                <select name="project_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Project...</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ (old('project_id', $selectedProjectId) == $p->id) ? 'selected' : '' }}>
                            {{ $p->project_name }} ({{ $p->project_code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Currency -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Currency</label>
                <input type="text" name="currency" value="INR" readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-700">
            </div>

            <!-- Invoice Date & Due Date -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Invoice Date *</label>
                <input type="date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Payment Due Date *</label>
                <input type="date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+15 days'))) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <!-- LINE ITEMS -->
        <div class="border-t border-gray-100 pt-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Invoice Line Items</h3>
                    <p class="text-xs text-gray-500">Add services, talent fees, production, and GST tax percentage</p>
                </div>
                <button type="button" @click="addItem()" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold rounded-lg text-xs flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Line Item
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                        <!-- Description -->
                        <div class="sm:col-span-5">
                            <label class="block text-[11px] font-semibold uppercase text-gray-500 mb-1">Service / Deliverable Description</label>
                            <input type="text" :name="'items[' + index + '][description]'" x-model="item.description" required placeholder="e.g. Celebrity Endorsement Fee (Phase 1)" class="w-full px-2.5 py-1.5 border border-gray-300 rounded-lg text-xs bg-white">
                        </div>

                        <!-- Quantity -->
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold uppercase text-gray-500 mb-1">Qty</label>
                            <input type="number" step="0.01" :name="'items[' + index + '][quantity]'" x-model.number="item.quantity" required class="w-full px-2.5 py-1.5 border border-gray-300 rounded-lg text-xs bg-white text-right">
                        </div>

                        <!-- Unit Price -->
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold uppercase text-gray-500 mb-1">Rate (₹)</label>
                            <input type="number" step="0.01" :name="'items[' + index + '][unit_price]'" x-model.number="item.unit_price" required class="w-full px-2.5 py-1.5 border border-gray-300 rounded-lg text-xs bg-white text-right">
                        </div>

                        <!-- Tax % -->
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold uppercase text-gray-500 mb-1">GST %</label>
                            <select :name="'items[' + index + '][tax_rate]'" x-model.number="item.tax_rate" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-xs bg-white text-right">
                                <option value="0">0% (Nil)</option>
                                <option value="5">5%</option>
                                <option value="12">12%</option>
                                <option value="18" selected>18% (Standard)</option>
                                <option value="28">28%</option>
                            </select>
                        </div>

                        <!-- Remove -->
                        <div class="sm:col-span-1 flex justify-center pt-4 sm:pt-0">
                            <button type="button" @click="removeItem(index)" :disabled="items.length === 1" class="text-red-500 hover:text-red-700 disabled:opacity-30 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Tax and Grand Total Summary -->
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-2 max-w-sm ml-auto text-xs">
                <div class="flex justify-between text-gray-600">
                    <span>Taxable Subtotal:</span>
                    <span class="font-semibold text-gray-900" x-text="'₹' + subtotal().toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>GST (18% / Applicable):</span>
                    <span class="font-semibold text-gray-900" x-text="'₹' + taxTotal().toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                </div>
                <div class="flex justify-between text-base font-bold text-gray-900 border-t pt-2">
                    <span>Grand Total:</span>
                    <span class="text-indigo-600" x-text="'₹' + grandTotal().toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                </div>
            </div>
        </div>

        <!-- Notes / Wire Instructions -->
        <div>
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Invoice Remarks & Banking Instructions</label>
            <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes', "Payment terms: Net 15 Days. Bank: HDFC Bank, A/C: 50200012345678, IFSC: HDFC0000060, Branch: Fort, Mumbai. GSTIN: 27AABCT1234F1Z5") }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.invoices.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">Generate Invoice</button>
        </div>
    </form>
</div>

<script>
    function invoiceForm() {
        return {
            items: [
                { description: 'Celebrity Endorsement & Appearance Fee', quantity: 1, unit_price: 950000, tax_rate: 18 },
                { description: 'Campaign Production & Crew Management', quantity: 1, unit_price: 150000, tax_rate: 18 }
            ],
            addItem() {
                this.items.push({
                    description: '',
                    quantity: 1,
                    unit_price: 0,
                    tax_rate: 18
                });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },
            subtotal() {
                return this.items.reduce((sum, item) => sum + ((parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0)), 0);
            },
            taxTotal() {
                return this.items.reduce((sum, item) => {
                    const line = (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0);
                    return sum + (line * ((parseFloat(item.tax_rate) || 0) / 100));
                }, 0);
            },
            grandTotal() {
                return this.subtotal() + this.taxTotal();
            }
        };
    }
</script>
@endsection

