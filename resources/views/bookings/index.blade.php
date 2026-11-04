@extends('layouts.app')

@section('title', 'Bookings & Master Calendar')

@section('content')
<div class="space-y-6" x-data="{ viewMode: 'calendar' }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bookings & Scheduling</h1>
            <p class="text-sm text-gray-500 mt-1">Full agency calendar, shoot schedules, appearance dates, and conflict detection</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- View Mode Switcher -->
            <div class="bg-gray-100 p-1 rounded-lg flex items-center text-xs font-semibold">
                <button @click="viewMode = 'calendar'; $nextTick(() => { if (window.bookingCalendarInstance) window.bookingCalendarInstance.updateSize(); })" :class="viewMode === 'calendar' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-600 hover:text-gray-900'" class="px-3 py-1.5 rounded-md transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Calendar View
                </button>
                <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-600 hover:text-gray-900'" class="px-3 py-1.5 rounded-md transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    List View
                </button>
            </div>

            <a href="{{ route('admin.bookings.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Booking
            </a>
        </div>
    </div>

    <!-- CALENDAR VIEW -->
    <div x-show="viewMode === 'calendar'" x-cloak class="space-y-4">
        <!-- Calendar Controls & Talent Filter -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-gray-600 uppercase">Filter by Celebrity:</label>
                <select id="calendarCelebrityFilter" class="py-1.5 px-3 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Celebrities</option>
                    @foreach($celebrities as $celeb)
                        <option value="{{ $celeb->id }}">{{ $celeb->stage_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-4 text-xs font-medium">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-600"></span> Confirmed</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-500"></span> Tentative</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-600"></span> Completed</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-gray-500"></span> Blocked Out</span>
            </div>
        </div>

        <!-- FullCalendar Canvas Card -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div id="bookingCalendar" style="min-height: 700px;"></div>
        </div>
    </div>

    <!-- TABLE LIST VIEW -->
    <div x-show="viewMode === 'table'" x-cloak style="display: none;" class="space-y-4">
        <!-- Filter Bar -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <form method="GET" action="{{ route('admin.bookings.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, booking code, or venue..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Celebrity</label>
                    <div class="flex gap-2">
                        <select name="celebrity_id" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Talent</option>
                            @foreach($celebrities as $c)
                                <option value="{{ $c->id }}" {{ request('celebrity_id') == $c->id ? 'selected' : '' }}>{{ $c->stage_name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Bookings Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-semibold text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Booking Code</th>
                            <th class="px-4 py-3 text-left">Title & Project</th>
                            <th class="px-4 py-3 text-left">Celebrities</th>
                            <th class="px-4 py-3 text-left">Date & Time</th>
                            <th class="px-4 py-3 text-left">Venue</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($bookings as $booking)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-xs font-bold text-indigo-600">
                                    <a href="{{ route('admin.bookings.show', $booking) }}">{{ $booking->booking_code }}</a>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-900">{{ $booking->title }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $booking->project ? $booking->project->project_name : 'No Project' }}
                                        @if($booking->project?->client)
                                            ({{ $booking->project->client->company_name }})
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($booking->celebrities as $celeb)
                                            <span class="px-2 py-0.5 rounded text-xs bg-purple-50 text-purple-700 border border-purple-100 font-medium">
                                                {{ $celeb->stage_name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-700 whitespace-nowrap">
                                    <div>{{ $booking->starts_at?->format('d M Y') }}</div>
                                    <div class="text-gray-400">{{ $booking->starts_at?->format('h:i A') }} - {{ $booking->ends_at?->format('h:i A') }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">
                                    <div>{{ $booking->venue_name ?: 'TBD' }}</div>
                                    <div class="text-gray-400">{{ $booking->timezone }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $bStatus = [
                                            'confirmed' => 'bg-emerald-100 text-emerald-800',
                                            'tentative' => 'bg-amber-100 text-amber-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            'completed' => 'bg-indigo-100 text-indigo-800',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $bStatus[$booking->status] ?? 'bg-gray-100' }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="text-indigo-600 hover:underline text-xs font-semibold">View</a>
                                    <a href="{{ route('admin.bookings.edit', $booking) }}" class="text-gray-600 hover:underline text-xs">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">No bookings found matching filter criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('bookingCalendar');
        const filterSelect = document.getElementById('calendarCelebrityFilter');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            timeZone: 'local',
            events: function(info, successCallback, failureCallback) {
                const celebId = filterSelect ? filterSelect.value : '';
                let url = '{{ route("admin.calendar.events") }}?start=' + encodeURIComponent(info.startStr) + '&end=' + encodeURIComponent(info.endStr);
                if (celebId) {
                    url += '&celebrity_id=' + encodeURIComponent(celebId);
                }

                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('HTTP status ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => successCallback(data))
                    .catch(err => {
                        console.error('Calendar events fetch error:', err);
                        failureCallback(err);
                    });
            },
            eventClick: function(info) {
                if (info.event.url) {
                    info.jsEvent.preventDefault();
                    window.location.href = info.event.url;
                }
            },
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: 'short'
            }
        });

        calendar.render();
        window.bookingCalendarInstance = calendar;

        if (filterSelect) {
            filterSelect.addEventListener('change', function() {
                calendar.refetchEvents();
            });
        }
    });
</script>
@endpush
@endsection

