@extends('layouts.app')

@section('title', 'Booking: ' . $booking->title)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded text-xs font-mono font-bold bg-gray-100 text-gray-800">{{ $booking->booking_code }}</span>
                @php
                    $bStatus = [
                        'confirmed' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'tentative' => 'bg-amber-100 text-amber-800 border-amber-200',
                        'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                        'completed' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                    ];
                @endphp
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $bStatus[$booking->status] ?? 'bg-gray-100' }}">
                    {{ ucfirst($booking->status) }}
                </span>
                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700 uppercase">
                    {{ $booking->booking_type }}
                </span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $booking->title }}</h1>
            <p class="text-xs text-gray-500 mt-1">
                Project: <a href="{{ route('admin.projects.show', $booking->project_id) }}" class="text-indigo-600 font-semibold hover:underline">{{ $booking->project?->project_name }}</a>
                @if($booking->project?->client)
                    • Client: <span class="text-gray-700 font-medium">{{ $booking->project->client->company_name }}</span>
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if($booking->status === 'tentative')
                <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-sm flex items-center gap-1.5 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Confirm Booking
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.bookings.edit', $booking) }}" class="px-3 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 flex items-center gap-1">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                Edit
            </a>
            <a href="{{ route('admin.bookings.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-medium text-gray-700">Calendar</a>
        </div>
    </div>

    <!-- Booking Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Schedule & Venue Details -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm md:col-span-2 space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">Event & Venue Logistics</h3>

            <div class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-gray-400 block uppercase">Date & Start Time</span>
                    <span class="font-bold text-gray-900 text-sm">{{ $booking->starts_at?->format('l, d M Y') }}</span>
                    <span class="text-gray-600 block">{{ $booking->starts_at?->format('h:i A') }} ({{ $booking->timezone }})</span>
                </div>
                <div>
                    <span class="text-gray-400 block uppercase">End Time</span>
                    <span class="font-bold text-gray-900 text-sm">{{ $booking->ends_at?->format('l, d M Y') }}</span>
                    <span class="text-gray-600 block">{{ $booking->ends_at?->format('h:i A') }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block uppercase">Venue</span>
                    <span class="font-semibold text-gray-900">{{ $booking->venue_name ?: 'Venue TBD' }}</span>
                    <span class="text-gray-500 block">{{ $booking->venue_address ?: 'No specific address recorded' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block uppercase">Lead Coordinator</span>
                    <span class="font-semibold text-gray-900">{{ $booking->coordinator?->name ?? 'Unassigned' }}</span>
                    <span class="text-gray-500 block">{{ $booking->coordinator?->email }}</span>
                </div>
            </div>

            @if($booking->notes)
                <div class="pt-4 border-t border-gray-100">
                    <span class="text-xs font-bold text-gray-500 uppercase block mb-1">Call Sheet & Instructions</span>
                    <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-100">{{ $booking->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Meta / Creator Info -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">Audit Info</h3>
            <div class="space-y-3 text-xs text-gray-600">
                <div>
                    <span class="text-gray-400 block">Created By</span>
                    <span class="font-medium text-gray-900">{{ $booking->creator?->name ?? 'System' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block">Created At</span>
                    <span class="font-medium text-gray-900">{{ $booking->created_at?->format('d M Y, h:i A') }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block">Last Updated</span>
                    <span class="font-medium text-gray-900">{{ $booking->updated_at?->format('d M Y, h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Celebrities Roster -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900">Celebrity Call Sheet & Roster</h3>
                <p class="text-xs text-gray-500">Talent assigned to this appearance or shoot session</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700">
                {{ $booking->celebrities->count() }} Celebrity(s)
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-left">Celebrity</th>
                        <th class="px-4 py-3 text-left">Code & Category</th>
                        <th class="px-4 py-3 text-left">Assignment Role</th>
                        <th class="px-4 py-3 text-left">Report / Arrival Time</th>
                        <th class="px-4 py-3 text-right">Agreed Fee</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($booking->celebrities as $celeb)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-bold text-gray-900">
                                <a href="{{ route('admin.celebrities.show', $celeb) }}" class="text-indigo-600 hover:underline">
                                    {{ $celeb->stage_name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-gray-500 font-mono">
                                {{ $celeb->celebrity_code }} • {{ $celeb->talentCategory?->name }}
                            </td>
                            <td class="px-4 py-3 text-gray-700 font-medium">
                                {{ $celeb->pivot->assignment_role ?? 'Assigned Artist' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $celeb->pivot->arrival_at ? \Carbon\Carbon::parse($celeb->pivot->arrival_at)->format('d M, h:i A') : 'TBD' }}
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">
                                @if($celeb->pivot->agreed_fee)
                                    ₹{{ number_format($celeb->pivot->agreed_fee, 2) }}
                                @else
                                    <span class="text-gray-400 font-normal">Project Contract Scope</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-400">No celebrities assigned to this booking yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

