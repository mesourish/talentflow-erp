@extends('layouts.app')

@section('title', 'Schedule New Booking')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Schedule New Booking</h1>
            <p class="text-sm text-gray-500 mt-1">Book celebrity dates with conflict checking and venue logistics</p>
        </div>
        <a href="{{ route('admin.bookings.index') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Calendar
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

    <form method="POST" action="{{ route('admin.bookings.store') }}" id="bookingForm" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Booking Code -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Booking Code *</label>
                <input type="text" name="booking_code" value="{{ old('booking_code', $suggestedCode) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono bg-gray-50 focus:bg-white focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Project Selection -->
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

            <!-- Title -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Event / Shoot Title *</label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Day 1 Studio Shoot / Gala Red Carpet Appearance" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Booking Type -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Booking Type *</label>
                <select name="booking_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="shoot" {{ old('booking_type') == 'shoot' ? 'selected' : '' }}>Photo / Video Shoot</option>
                    <option value="appearance" {{ old('booking_type') == 'appearance' ? 'selected' : '' }}>Stage / Event Appearance</option>
                    <option value="performance" {{ old('booking_type') == 'performance' ? 'selected' : '' }}>Live Performance</option>
                    <option value="rehearsal" {{ old('booking_type') == 'rehearsal' ? 'selected' : '' }}>Rehearsal / Fitting</option>
                    <option value="travel" {{ old('booking_type') == 'travel' ? 'selected' : '' }}>Travel / Transit Day</option>
                </select>
            </div>

            <!-- Coordinator -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Lead Coordinator</label>
                <select name="coordinator_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Coordinator...</option>
                    @foreach($coordinators as $c)
                        <option value="{{ $c->id }}" {{ old('coordinator_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->email }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Starts At -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Starts At *</label>
                <input type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at', date('Y-m-d\TH:00', strtotime('+1 day 09:00'))) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Ends At -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Ends At *</label>
                <input type="datetime-local" id="ends_at" name="ends_at" value="{{ old('ends_at', date('Y-m-d\TH:00', strtotime('+1 day 18:00'))) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Venue Name -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Venue Name</label>
                <input type="text" name="venue_name" value="{{ old('venue_name') }}" placeholder="e.g. Mehboob Studio 2 / Taj Palace" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Timezone & Status -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Timezone</label>
                    <input type="text" name="timezone" value="{{ old('timezone', 'Asia/Kolkata') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="tentative" {{ old('status') == 'tentative' ? 'selected' : '' }}>Tentative</option>
                        <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    </select>
                </div>
            </div>

            <!-- Venue Address -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Venue Full Address & Gate Instructions</label>
                <textarea name="venue_address" rows="2" placeholder="Full address, entry gate, parking directions..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('venue_address') }}</textarea>
            </div>
        </div>

        <!-- CELEBRITIES ROSTER ASSIGNMENT WITH CONFLICT CHECK -->
        <div class="border-t border-gray-100 pt-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Assign Celebrities to this Booking *</h3>
                    <p class="text-xs text-gray-500">Select talent and specify agreed booking remuneration</p>
                </div>
                <button type="button" id="btnCheckConflict" class="px-3.5 py-1.5 bg-amber-50 border border-amber-300 text-amber-800 rounded-lg text-xs font-semibold hover:bg-amber-100 flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Check Conflict / Availability
                </button>
            </div>

            <!-- Conflict Banner (Dynamic via JS) -->
            <div id="conflictAlert" class="hidden p-4 rounded-xl border"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-80 overflow-y-auto border border-gray-200 rounded-xl p-4">
                @foreach($celebrities as $celeb)
                    <div class="flex items-start justify-between p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="celebrity_ids[]" value="{{ $celeb->id }}" class="celeb-checkbox rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4" {{ (is_array(old('celebrity_ids')) && in_array($celeb->id, old('celebrity_ids'))) ? 'checked' : '' }}>
                            <div>
                                <span class="font-bold text-sm text-gray-900 block">{{ $celeb->stage_name }}</span>
                                <span class="text-xs text-gray-500 font-mono">{{ $celeb->celebrity_code }} • {{ $celeb->talentCategory?->name }}</span>
                            </div>
                        </label>
                        <div class="w-32">
                            <input type="number" step="0.01" name="agreed_fees[{{ $celeb->id }}]" value="{{ old('agreed_fees.' . $celeb->id) }}" placeholder="Fee (₹)" class="w-full px-2 py-1 border border-gray-300 rounded text-xs text-right focus:ring-indigo-500">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- CONFLICT OVERRIDE SECTION (If user chooses to override conflict) -->
        <div id="overrideSection" class="p-4 bg-amber-50/50 border border-amber-200 rounded-xl space-y-3">
            <div class="flex items-center gap-2">
                <input type="checkbox" name="allow_override" id="allow_override" value="1" class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                <label for="allow_override" class="text-xs font-bold text-gray-900 cursor-pointer">
                    I authorize override of schedule conflict warnings (if any)
                </label>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Override Justification Reason:</label>
                <input type="text" name="override_reason" value="{{ old('override_reason') }}" placeholder="e.g. VIP client exception, sequential morning/evening schedule approved by artist manager" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs">
            </div>
        </div>

        <!-- Notes -->
        <div>
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Call Sheet & Catering Notes</label>
            <textarea name="notes" rows="3" placeholder="Styling requirements, vanity van allocations, security guidelines..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes') }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">Schedule Booking</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('btnCheckConflict').addEventListener('click', function() {
        const checkedBoxes = Array.from(document.querySelectorAll('.celeb-checkbox:checked')).map(cb => cb.value);
        const startsAt = document.getElementById('starts_at').value;
        const endsAt = document.getElementById('ends_at').value;
        const alertBox = document.getElementById('conflictAlert');

        if (checkedBoxes.length === 0) {
            alert('Please select at least one celebrity to check schedule availability.');
            return;
        }
        if (!startsAt || !endsAt) {
            alert('Please specify both Starts At and Ends At date and time.');
            return;
        }

        alertBox.className = 'p-4 rounded-xl border bg-gray-50 text-gray-700 text-xs block';
        alertBox.innerHTML = 'Checking availability with scheduling engine...';

        fetch('{{ route("admin.bookings.check-availability") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                celebrity_ids: checkedBoxes,
                starts_at: startsAt,
                ends_at: endsAt
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.has_conflict) {
                alertBox.className = 'p-4 rounded-xl border bg-red-50 border-red-200 text-red-800 text-xs block space-y-1';
                let html = '<strong>Schedule Conflict Detected!</strong><ul class="list-disc list-inside mt-1">';
                data.conflicts.forEach(c => {
                    html += `<li><strong>${c.celebrity}</strong> is unavailable: ${c.reason} (${c.conflict_time})</li>`;
                });
                html += '</ul><p class="mt-2 text-red-700 font-medium">To proceed regardless, check the override checkbox below and enter a justification.</p>';
                alertBox.innerHTML = html;
            } else {
                alertBox.className = 'p-4 rounded-xl border bg-emerald-50 border-emerald-200 text-emerald-800 text-xs block';
                alertBox.innerHTML = '<strong>No Conflicts Found!</strong> All selected celebrities are free and available for this schedule slot.';
            }
        })
        .catch(err => {
            alertBox.className = 'p-4 rounded-xl border bg-red-50 border-red-200 text-red-800 text-xs block';
            alertBox.innerHTML = 'Error communicating with availability check service.';
        });
    });
</script>
@endsection

