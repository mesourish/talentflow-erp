@extends('layouts.app')

@section('title', 'Submit Expense Claim')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="claimForm()">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Submit Reimbursement Claim</h1>
            <p class="text-sm text-gray-500 mt-1">Submit travel, lodging, meal, and shoot production receipts for reimbursement</p>
        </div>
        <a href="{{ route('admin.claims.index') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Claims
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

    <form method="POST" action="{{ route('admin.claims.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Claim Number -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Claim Number *</label>
                <input type="text" name="claim_no" value="{{ old('claim_no', $suggestedNo) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono bg-gray-50 focus:bg-white focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Project Selection -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Associated Project *</label>
                <select name="project_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Project...</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ (old('project_id', $selectedProjectId) == $p->id) ? 'selected' : '' }}>
                            {{ $p->project_name }} ({{ $p->project_code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Claim Date -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Claim Date *</label>
                <input type="date" name="claim_date" value="{{ old('claim_date', date('Y-m-d')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Currency -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Currency</label>
                <input type="text" name="currency" value="INR" readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-700">
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Claim Purpose / Summary *</label>
                <textarea name="description" rows="2" placeholder="e.g. Travel and hotel accommodations for Aanya Sen shoot in Mumbai" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
            </div>
        </div>

        <!-- DYNAMIC EXPENSE ITEMS -->
        <div class="border-t border-gray-100 pt-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Expense Items & Receipts</h3>
                    <p class="text-xs text-gray-500">Itemize individual expenses incurred during project execution</p>
                </div>
                <button type="button" @click="addItem()" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold rounded-lg text-xs flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Item
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                        <!-- Category -->
                        <div class="sm:col-span-3">
                            <label class="block text-[11px] font-semibold uppercase text-gray-500 mb-1">Category</label>
                            <select :name="'items[' + index + '][expense_category]'" x-model="item.category" required class="w-full px-2.5 py-1.5 border border-gray-300 rounded-lg text-xs bg-white">
                                <option value="travel">Travel & Flights</option>
                                <option value="accommodation">Hotel & Lodging</option>
                                <option value="meals">Food & Catering</option>
                                <option value="logistics">Local Cab / Transport</option>
                                <option value="styling">Styling / Wardrobe</option>
                                <option value="production">Production Supplies</option>
                                <option value="other">Other Incidentals</option>
                            </select>
                        </div>

                        <!-- Date -->
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold uppercase text-gray-500 mb-1">Date</label>
                            <input type="date" :name="'items[' + index + '][expense_date]'" x-model="item.date" required class="w-full px-2.5 py-1.5 border border-gray-300 rounded-lg text-xs bg-white">
                        </div>

                        <!-- Description -->
                        <div class="sm:col-span-4">
                            <label class="block text-[11px] font-semibold uppercase text-gray-500 mb-1">Description / Bill Ref</label>
                            <input type="text" :name="'items[' + index + '][description]'" x-model="item.description" placeholder="e.g. Indigo flight tickets / Hotel Taj bill" class="w-full px-2.5 py-1.5 border border-gray-300 rounded-lg text-xs bg-white">
                        </div>

                        <!-- Amount -->
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold uppercase text-gray-500 mb-1">Amount (₹)</label>
                            <input type="number" step="0.01" :name="'items[' + index + '][amount]'" x-model.number="item.amount" required class="w-full px-2.5 py-1.5 border border-gray-300 rounded-lg text-xs bg-white text-right font-semibold">
                        </div>

                        <!-- Delete Button -->
                        <div class="sm:col-span-1 flex justify-center pt-4 sm:pt-0">
                            <button type="button" @click="removeItem(index)" :disabled="items.length === 1" class="text-red-500 hover:text-red-700 disabled:opacity-30 p-1" title="Delete Line">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Grand Total -->
            <div class="flex justify-end p-4 bg-indigo-50/50 rounded-xl border border-indigo-100">
                <div class="text-right">
                    <span class="text-xs font-semibold text-gray-500 uppercase">Total Claim Amount</span>
                    <p class="text-xl font-bold text-indigo-700" x-text="'₹' + totalAmount().toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.claims.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">Save & Draft Claim</button>
        </div>
    </form>
</div>

<script>
    function claimForm() {
        return {
            items: [
                { category: 'travel', date: '{{ date("Y-m-d") }}', description: 'Indigo Flight tickets', amount: 35000 },
                { category: 'accommodation', date: '{{ date("Y-m-d") }}', description: 'Taj Land\'s End Mumbai 2 Nights', amount: 65000 }
            ],
            addItem() {
                this.items.push({
                    category: 'travel',
                    date: '{{ date("Y-m-d") }}',
                    description: '',
                    amount: 0
                });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },
            totalAmount() {
                return this.items.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);
            }
        };
    }
</script>
@endsection

