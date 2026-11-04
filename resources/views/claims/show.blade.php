@extends('layouts.app')

@section('title', 'Expense Claim: ' . $claim->claim_no)

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ approveModal: false, rejectModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded text-xs font-mono font-bold bg-gray-100 text-gray-800">{{ $claim->claim_no }}</span>
                @php
                    $clStatus = [
                        'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                        'submitted' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'draft' => 'bg-gray-100 text-gray-800 border-gray-200',
                        'rejected' => 'bg-red-100 text-red-800 border-red-200',
                    ];
                @endphp
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $clStatus[$claim->status] ?? 'bg-gray-100' }}">
                    {{ ucfirst($claim->status) }}
                </span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">₹{{ number_format($claim->total_amount, 2) }}</h1>
            <p class="text-xs text-gray-500 mt-1">
                Submitted by <strong class="text-gray-700">{{ $claim->submitter?->name }}</strong> on {{ $claim->claim_date?->format('d M Y') }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if($claim->status === 'draft')
                <form method="POST" action="{{ route('admin.claims.submit', $claim) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold shadow-sm transition">
                        Submit for Approval
                    </button>
                </form>
            @endif

            @if(in_array($claim->status, ['submitted', 'pending']))
                <button @click="approveModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-sm flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Approve Claim
                </button>
                <button @click="rejectModal = true" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-lg text-xs font-bold transition">
                    Reject
                </button>
            @endif

            <a href="{{ route('admin.claims.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-medium text-gray-700">Back</a>
        </div>
    </div>

    <!-- Metadata Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-3">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Project Information</span>
            <div>
                <span class="text-gray-400 text-xs block">Project</span>
                <a href="{{ route('admin.projects.show', $claim->project_id) }}" class="font-bold text-gray-900 text-sm hover:text-indigo-600">
                    {{ $claim->project?->project_name }} ({{ $claim->project?->project_code }})
                </a>
            </div>
            <div>
                <span class="text-gray-400 text-xs block">Client</span>
                <span class="text-gray-700 text-xs font-semibold">{{ $claim->project?->client?->company_name ?? 'N/A' }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-3">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Claim Details</span>
            <div>
                <span class="text-gray-400 text-xs block">Description / Purpose</span>
                <p class="text-gray-700 text-xs leading-relaxed">{{ $claim->description }}</p>
            </div>
            @if($claim->expense)
                <div class="p-2.5 bg-emerald-50 border border-emerald-200 rounded-lg text-xs text-emerald-800">
                    <strong>Posted to Project Expenses:</strong> Record #{{ $claim->expense->id }} (₹{{ number_format($claim->expense->amount, 2) }})
                </div>
            @endif
        </div>
    </div>

    <!-- Itemized Breakdown -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
        <h3 class="text-base font-bold text-gray-900">Itemized Expenses</h3>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-left">Category</th>
                        <th class="px-4 py-3 text-left">Expense Date</th>
                        <th class="px-4 py-3 text-left">Description</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($claim->items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-semibold text-gray-900 capitalize">{{ $item->expense_category }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $item->expense_date?->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $item->description ?: '-' }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">₹{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">No items recorded in this claim.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 font-bold text-gray-900">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right uppercase text-xs">Total Reimbursement:</td>
                        <td class="px-4 py-3 text-right text-sm text-indigo-700">₹{{ number_format($claim->total_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Approval Timeline -->
    @if($claim->approvals->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-3">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500">Approval Audit Trail</h3>
            <div class="space-y-3">
                @foreach($claim->approvals as $appr)
                    <div class="flex items-start justify-between p-3 rounded-lg border border-gray-100 bg-gray-50 text-xs">
                        <div>
                            <span class="font-bold text-gray-900">{{ $appr->user?->name ?? 'Approver' }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase ml-2 {{ $appr->action === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($appr->action === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ $appr->action }}
                            </span>
                            @if($appr->comments)
                                <p class="text-gray-600 mt-1 italic">"{{ $appr->comments }}"</p>
                            @endif
                        </div>
                        <span class="text-gray-400">{{ $appr->action_at?->format('d M Y, h:i A') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- APPROVE MODAL -->
    <div x-show="approveModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="approveModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
                <h3 class="text-lg font-bold text-gray-900">Approve Reimbursement Claim</h3>
                <p class="text-xs text-gray-500 mt-1">This will approve ₹{{ number_format($claim->total_amount, 2) }} and automatically post a corresponding project expense to Project {{ $claim->project?->project_code }}.</p>

                <form method="POST" action="{{ route('admin.claims.approve', $claim) }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Approval Comments:</label>
                        <textarea name="comments" rows="2" class="w-full px-3 py-2 border rounded-lg text-xs" placeholder="e.g. Verified receipts against travel policy. Approved."></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="approveModal = false" class="px-4 py-2 border rounded-lg text-xs">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold">Confirm & Post Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- REJECT MODAL -->
    <div x-show="rejectModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="rejectModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
                <h3 class="text-lg font-bold text-gray-900">Reject Reimbursement Claim</h3>
                <form method="POST" action="{{ route('admin.claims.reject', $claim) }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Rejection Reason *</label>
                        <textarea name="comments" required rows="2" class="w-full px-3 py-2 border rounded-lg text-xs" placeholder="e.g. Missing original GST tax invoices for accommodation"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="rejectModal = false" class="px-4 py-2 border rounded-lg text-xs">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold">Reject Claim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

