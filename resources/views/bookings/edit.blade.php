@extends('layouts.app')

@section('title', 'Edit Booking: ' . $booking->booking_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Booking: {{ $booking->booking_code }}</h1>
            <p class="text-sm text-gray-500 mt-1">Update schedule, venue location, or lead coordinator</p>
        </div>
        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Booking
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

    <form method="POST" action="{{ route('admin.bookings.update', $booking) }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Title -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Event / Shoot Title *</label>
                <input type="text" name="title" value="{{ old('title', $booking->title) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Booking Type -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Booking Type *</label>
                <select name="booking_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="shoot" {{ old('booking_type', $booking->booking_type) == 'shoot' ? 'selected' : '' }}>Photo / Video Shoot</option>
                    <option value="appearance" {{ old('booking_type', $booking->booking_type) == 'appearance' ? 'selected' : '' }}>Stage / Event Appearance</option>
                    <option value="performance" {{ old('booking_type', $booking->booking_type) == 'performance' ? 'selected' : '' }}>Live Performance</option>
                    <option value="rehearsal" {{ old('booking_type', $booking->booking_type) == 'rehearsal' ? 'selected' : '' }}>Rehearsal / Fitting</option>
                    <option value="travel" {{ old('booking_type', $booking->booking_type) == 'travel' ? 'selected' : '' }}>Travel / Transit Day</option>
                </select>
            </div>

            <!-- Coordinator -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Lead Coordinator</label>
                <select name="coordinator_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Coordinator...</option>
                    @foreach($coordinators as $c)
                        <option value="{{ $c->id }}" {{ old('coordinator_id', $booking->coordinator_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Starts At -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Starts At *</label>
                <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $booking->starts_at?->format('Y-m-d\TH:i')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Ends At -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Ends At *</label>
                <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $booking->ends_at?->format('Y-m-d\TH:i')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Venue Name -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Venue Name</label>
                <input type="text" name="venue_name" value="{{ old('venue_name', $booking->venue_name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Timezone & Status -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Timezone</label>
                    <input type="text" name="timezone" value="{{ old('timezone', $booking->timezone) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="tentative" {{ old('status', $booking->status) == 'tentative' ? 'selected' : '' }}>Tentative</option>
                        <option value="confirmed" {{ old('status', $booking->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ old('status', $booking->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            <!-- Venue Address -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Venue Address & Gates</label>
                <textarea name="venue_address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('venue_address', $booking->venue_address) }}</textarea>
            </div>

            <!-- Notes -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Call Sheet & Catering Notes</label>
                <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes', $booking->notes) }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.bookings.show', $booking) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">Save Updates</button>
        </div>
    </form>
</div>
@endsection

