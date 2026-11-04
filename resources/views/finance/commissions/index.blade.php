@extends('layouts.app')

@section('title', 'Commissions & Celebrity Payouts')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'payouts', payoutModal: false, ruleModal: false, payModal: false, selectedPayoutId: null }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Commissions & Celebrity Payouts</h1>
            <p class="text-sm text-gray-500 mt-1">Agency commission automation (default 20%), TDS withholding, and talent fee disbursements</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="ruleModal = true" class="px-3.5 py-2 border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                New Commission Rule
            </button>
            <button @click="payoutModal = true" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Calculate Payout
            </button>
        </div>
    </div>

    <!-- Financial KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Agency Commission Retained</span>
            <p class="text-2xl font-bold text-indigo-700 mt-1">₹{{ number_format($totalCommissionEarned, 2) }}</p>
            <span class="text-xs text-gray-500">Gross revenue agency margin</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Disbursed to Celebrities</span>
            <p class="text-2xl font-bold text-emerald-600 mt-1">₹{{ number_format($totalPayouts, 2) }}</p>
            <span class="text-xs text-gray-500">Paid talent remittances</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Pending Disbursements</span>
            <p class="text-2xl font-bold text-amber-600 mt-1">₹{{ number_format($pendingPayouts, 2) }}</p>
            <span class="text-xs text-gray-500">Approved or awaiting payment</span>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 bg-white rounded-t-xl px-4 pt-2">
        <nav class="-mb-px flex space-x-8">
            <button @click="activeTab = 'payouts'" :class="activeTab === 'payouts' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-3 px-1 border-b-2 text-sm">
                Celebrity Payouts & Disbursements ({{ $payouts->total() }})
            </button>
            <button @click="activeTab = 'rules'" :class="activeTab === 'rules' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-3 px-1 border-b-2 text-sm">
                Commission Rules Matrix ({{ $rules->count() }})
            </button>
        </nav>
    </div>

    <!-- TAB 1: CELEBRITY PAYOUTS -->
    <div x-show="activeTab === 'payouts'" x-cloak class="space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-semibold text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Payout Code</th>
                            <th class="px-4 py-3 text-left">Celebrity</th>
                            <th class="px-4 py-3 text-left">Project</th>
                            <th class="px-4 py-3 text-right">Gross Amount</th>
                            <th class="px-4 py-3 text-right">Agency Comm</th>
                            <th class="px-4 py-3 text-right">TDS Withheld</th>
                            <th class="px-4 py-3 text-right font-bold">Net Payable</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($payouts as $payout)
                            <tr class="hover:bg-gray-50 text-xs">
                                <td class="px-4 py-3 font-mono font-bold text-gray-800">{{ $payout->payout_code }}</td>
                                <td class="px-4 py-3 font-bold text-purple-700">{{ $payout->celebrity?->stage_name }}</td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $payout->project?->project_name }}
                                </td>
                                <td class="px-4 py-3 text-right">₹{{ number_format($payout->gross_amount, 2) }}</td>
                                <td class="px-4 py-3 text-right text-indigo-600 font-semibold">
                                    ₹{{ number_format($payout->commission_amount, 2) }}
                                    <span class="text-gray-400 font-normal">({{ $payout->commission_percentage }}%)</span>
                                </td>
                                <td class="px-4 py-3 text-right text-gray-500">₹{{ number_format($payout->tds_amount, 2) }}</td>
                                <td class="px-4 py-3 text-right font-bold text-emerald-600">₹{{ number_format($payout->net_payable, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $pStatus = [
                                            'paid' => 'bg-emerald-100 text-emerald-800',
                                            'approved' => 'bg-blue-100 text-blue-800',
                                            'draft' => 'bg-gray-100 text-gray-800',
                                            'pending' => 'bg-amber-100 text-amber-800',
                                        ];
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $pStatus[$payout->status] ?? 'bg-gray-100' }}">
                                        {{ $payout->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right space-x-1.5 whitespace-nowrap">
                                    @if($payout->status === 'draft')
                                        <form method="POST" action="{{ route('admin.commissions.payouts.approve', $payout) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded text-xs font-semibold">Approve</button>
                                        </form>
                                    @endif

                                    @if(in_array($payout->status, ['approved', 'draft', 'pending']))
                                        <button @click="selectedPayoutId = {{ $payout->id }}; payModal = true" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-semibold">
                                            Disburse
                                        </button>
                                    @endif

                                    @if($payout->status === 'paid')
                                        <span class="text-gray-400 text-[11px]" title="Ref: {{ $payout->payment_reference }}">Disbursed ✓</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="px-4 py-8 text-center text-gray-500">No celebrity payouts recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100">
                {{ $payouts->links() }}
            </div>
        </div>
    </div>

    <!-- TAB 2: COMMISSION RULES -->
    <div x-show="activeTab === 'rules'" x-cloak class="space-y-4" style="display: none;">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                        <tr>
                            <th class="px-4 py-3 text-left">Rule Name</th>
                            <th class="px-4 py-3 text-left">Applies To</th>
                            <th class="px-4 py-3 text-left">Scope / Target</th>
                            <th class="px-4 py-3 text-right">Commission Rate</th>
                            <th class="px-4 py-3 text-left">Effective Period</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($rules as $rule)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-900">{{ $rule->name }}</td>
                                <td class="px-4 py-3 uppercase font-mono text-gray-600">{{ str_replace('_', ' ', $rule->applies_to) }}</td>
                                <td class="px-4 py-3 text-gray-700">
                                    @if($rule->celebrity)
                                        <span class="font-bold text-purple-700">{{ $rule->celebrity->stage_name }}</span>
                                    @elseif($rule->client)
                                        <span class="font-bold text-gray-900">{{ $rule->client->company_name }}</span>
                                    @else
                                        <span class="text-gray-400">Default Global Agency Rate</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-indigo-700">
                                    {{ $rule->commission_type === 'percentage' ? $rule->commission_value . '%' : '₹' . number_format($rule->commission_value, 2) }}
                                </td>
                                <td class="px-4 py-3 text-gray-500">
                                    {{ $rule->effective_from?->format('d M Y') }} - {{ $rule->effective_to?->format('d M Y') ?? 'Ongoing' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $rule->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $rule->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">No custom commission rules configured. Default 20% applies.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL: CALCULATE PAYOUT -->
    <div x-show="payoutModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="payoutModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
                <h3 class="text-lg font-bold text-gray-900">Calculate Talent Remuneration</h3>
                <p class="text-xs text-gray-500 mt-1">Applies standard 20% agency commission deduction and TDS tax withholding.</p>

                <form method="POST" action="{{ route('admin.commissions.payouts.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Project *</label>
                        <select name="project_id" required class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="">Select Project...</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->project_name }} ({{ $p->project_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Celebrity *</label>
                        <select name="celebrity_id" required class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="">Select Celebrity...</option>
                            @foreach($celebrities as $c)
                                <option value="{{ $c->id }}">{{ $c->stage_name }} ({{ $c->celebrity_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Gross Booking Fee (₹) *</label>
                        <input type="number" step="0.01" name="gross_amount" required placeholder="e.g. 1000000" class="w-full px-3 py-2 border rounded-lg text-sm font-bold text-gray-900">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Other Deductions / TDS (₹)</label>
                        <input type="number" step="0.01" name="other_deductions" value="0.00" class="w-full px-3 py-2 border rounded-lg text-sm">
                    </div>

                    <div class="p-3 bg-indigo-50/50 rounded-lg border border-indigo-100 text-xs text-indigo-900">
                        Default commission rate: <strong>20%</strong>. Net payable will be calculated and staged in draft status.
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="payoutModal = false" class="px-4 py-2 border rounded-lg text-xs">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold">Generate Payout</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: ADD COMMISSION RULE -->
    <div x-show="ruleModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="ruleModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
                <h3 class="text-lg font-bold text-gray-900">Create Commission Override Rule</h3>

                <form method="POST" action="{{ route('admin.commissions.rules.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Rule Name *</label>
                        <input type="text" name="name" placeholder="e.g. Aanya Sen Tier-1 Commission" required class="w-full px-3 py-2 border rounded-lg text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Applies To *</label>
                        <select name="applies_to" required class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="all">Universal (Default for all)</option>
                            <option value="celebrity">Specific Celebrity</option>
                            <option value="client">Specific Client</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Specific Celebrity (if applicable)</label>
                        <select name="celebrity_id" class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="">None</option>
                            @foreach($celebrities as $c)
                                <option value="{{ $c->id }}">{{ $c->stage_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Type *</label>
                            <select name="commission_type" required class="w-full px-3 py-2 border rounded-lg text-sm">
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (₹)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Value *</label>
                            <input type="number" step="0.01" name="commission_value" value="20" required class="w-full px-3 py-2 border rounded-lg text-sm font-bold">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Effective From *</label>
                            <input type="date" name="effective_from" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Effective To</label>
                            <input type="date" name="effective_to" class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="ruleModal = false" class="px-4 py-2 border rounded-lg text-xs">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold">Save Rule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: DISBURSE PAYOUT -->
    <div x-show="payModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="payModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
                <h3 class="text-lg font-bold text-gray-900">Mark Payout as Disbursed</h3>
                <p class="text-xs text-gray-500 mt-1">Record bank transfer UTR or payment reference for talent account audit.</p>

                <form :action="'{{ url('admin/commissions/payouts') }}/' + selectedPayoutId + '/paid'" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Bank UTR / Transaction Reference *</label>
                        <input type="text" name="payment_reference" required placeholder="e.g. UTR-HDFC202611048892" class="w-full px-3 py-2 border rounded-lg text-sm font-mono">
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="payModal = false" class="px-4 py-2 border rounded-lg text-xs">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold">Confirm Disbursement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

