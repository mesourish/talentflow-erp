<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    App\Http\Controllers\Bookings
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\BookingCelebrity;
use App\Models\Celebrity;
use App\Models\CelebrityAvailability;
use App\Models\Project;
use App\Models\User;
use App\Services\BookingAvailabilityService;
use App\Services\CodeGenerator;
use Carbon\Carbon;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Class BookingController
 *
 * Manages booking calendar scheduling, event timelines, talent assignment, and conflict detection.
 *
 * @package App\Http\Controllers\Bookings
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class BookingController extends Controller
{
    public function __construct(
        protected CodeGenerator $codeGenerator,
        protected BookingAvailabilityService $availabilityService
    ) {}

    public function index(Request $request): View
    {
        $query = Booking::with(['project.client', 'coordinator', 'celebrities']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('booking_code', 'like', "%{$search}%")
                  ->orWhere('venue_name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($celebId = $request->input('celebrity_id')) {
            $query->whereHas('celebrities', fn($cq) => $cq->where('celebrities.id', $celebId));
        }

        $bookings = $query->latest('starts_at')->paginate(12)->withQueryString();
        $celebrities = Celebrity::where('profile_status', 'active')->orderBy('stage_name')->get();
        $statuses = ['tentative', 'confirmed', 'completed', 'cancelled'];

        return view('bookings.index', compact('bookings', 'celebrities', 'statuses'));
    }

    public function create(Request $request): View
    {
        $suggestedCode = $this->codeGenerator->bookingCode();
        $projects = Project::whereIn('status', ['planned', 'active'])->orderBy('project_name')->get();
        $celebrities = Celebrity::where('profile_status', 'active')->orderBy('stage_name')->get();
        $coordinators = User::where('is_active', true)->orderBy('name')->get();

        $selectedProjectId = $request->input('project_id');

        return view('bookings.create', compact('suggestedCode', 'projects', 'celebrities', 'coordinators', 'selectedProjectId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'booking_code' => 'required|string|max:50|unique:bookings,booking_code',
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:200',
            'booking_type' => 'required|string|max:100',
            'venue_name' => 'nullable|string|max:200',
            'venue_address' => 'nullable|string',
            'timezone' => 'required|string|max:60',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'coordinator_id' => 'nullable|exists:users,id',
            'status' => 'required|string|max:30',
            'notes' => 'nullable|string',
            'celebrity_ids' => 'required|array|min:1',
            'celebrity_ids.*' => 'exists:celebrities,id',
            'agreed_fees' => 'nullable|array',
            'allow_override' => 'nullable|boolean',
            'override_reason' => 'nullable|string',
        ]);

        $start = Carbon::parse($validated['starts_at']);
        $end = Carbon::parse($validated['ends_at']);

        // Check conflicts
        $check = $this->availabilityService->checkMultiCelebrityAvailability($validated['celebrity_ids'], $start, $end);

        if ($check['has_conflict'] && empty($validated['allow_override'])) {
            return back()->withInput()->with('warning', 'Booking schedule conflict detected for one or more celebrities. Please review availability or provide an authorized override reason.');
        }

        $booking = DB::transaction(function () use ($validated, $check) {
            $booking = Booking::create([
                'booking_code' => $validated['booking_code'],
                'project_id' => $validated['project_id'],
                'title' => $validated['title'],
                'booking_type' => $validated['booking_type'],
                'venue_name' => $validated['venue_name'],
                'venue_address' => $validated['venue_address'],
                'timezone' => $validated['timezone'],
                'starts_at' => $validated['starts_at'],
                'ends_at' => $validated['ends_at'],
                'status' => $validated['status'],
                'coordinator_id' => $validated['coordinator_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id() ?? 1,
            ]);

            foreach ($validated['celebrity_ids'] as $celebId) {
                $fee = $validated['agreed_fees'][$celebId] ?? null;
                BookingCelebrity::create([
                    'booking_id' => $booking->id,
                    'celebrity_id' => $celebId,
                    'assignment_role' => 'Assigned Artist',
                    'agreed_fee' => $fee,
                    'arrival_at' => Carbon::parse($validated['starts_at'])->subMinutes(30),
                ]);
            }

            if ($check['has_conflict'] && !empty($validated['allow_override'])) {
                ActivityLog::log(
                    'booking_conflict_override',
                    "Booking {$booking->booking_code} created with override reason: " . $validated['override_reason'],
                    $booking
                );
            } else {
                ActivityLog::log('booking_created', "Created Booking {$booking->title} ({$booking->booking_code})", $booking);
            }

            return $booking;
        });

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Booking scheduled successfully.');
    }

    public function show(Booking $booking): View
    {
        $booking->load(['project.client', 'coordinator', 'creator', 'celebrities']);
        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking): View
    {
        $booking->load('celebrities');
        $projects = Project::whereIn('status', ['planned', 'active'])->orderBy('project_name')->get();
        $celebrities = Celebrity::where('profile_status', 'active')->orderBy('stage_name')->get();
        $coordinators = User::where('is_active', true)->orderBy('name')->get();

        return view('bookings.edit', compact('booking', 'projects', 'celebrities', 'coordinators'));
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'booking_type' => 'required|string|max:100',
            'venue_name' => 'nullable|string|max:200',
            'venue_address' => 'nullable|string',
            'timezone' => 'required|string|max:60',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'coordinator_id' => 'nullable|exists:users,id',
            'status' => 'required|string|max:30',
            'notes' => 'nullable|string',
        ]);

        $booking->update($validated);

        ActivityLog::log('booking_updated', "Updated booking details for {$booking->booking_code}", $booking);

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Booking updated successfully.');
    }

    public function confirm(Request $request, Booking $booking): RedirectResponse
    {
        return DB::transaction(function () use ($booking) {
            $booking = Booking::with('celebrities')->lockForUpdate()->findOrFail($booking->id);

            // Re-check conflict in transaction
            $celebrityIds = $booking->celebrities->pluck('id')->toArray();
            $check = $this->availabilityService->checkMultiCelebrityAvailability(
                $celebrityIds,
                $booking->starts_at,
                $booking->ends_at,
                $booking->id
            );

            if ($check['has_conflict'] && !request()->filled('override_reason')) {
                return back()->with('error', 'Cannot confirm booking: Schedule conflict detected. Provide an authorized override reason to force confirmation.');
            }

            $booking->update([
                'status' => 'confirmed',
            ]);

            ActivityLog::log('booking_confirmed', "Booking {$booking->booking_code} status updated to confirmed.", $booking);

            return back()->with('success', 'Booking confirmed successfully.');
        });
    }

    public function checkAvailability(Request $request): JsonResponse
    {
        $celebrityIds = (array) $request->input('celebrity_ids', []);
        $start = $request->input('starts_at');
        $end = $request->input('ends_at');
        $ignoreBookingId = $request->input('booking_id');

        if (empty($celebrityIds) || !$start || !$end) {
            return response()->json(['success' => false, 'message' => 'Missing required parameters.'], 422);
        }

        $result = $this->availabilityService->checkMultiCelebrityAvailability($celebrityIds, $start, $end, $ignoreBookingId);

        return response()->json([
            'success' => true,
            'has_conflict' => $result['has_conflict'],
            'conflicts' => $result['details'],
        ]);
    }

    public function calendarEvents(Request $request): JsonResponse
    {
        $startStr = $request->input('start');
        $endStr = $request->input('end');

        $query = Booking::with(['project', 'celebrities']);
        if ($startStr && $endStr) {
            // Replace space with + in case + was converted to space during URL decoding
            $cleanStart = str_replace(' ', '+', $startStr);
            $cleanEnd = str_replace(' ', '+', $endStr);

            try {
                $start = Carbon::parse($cleanStart);
                $end = Carbon::parse($cleanEnd);
                $query->where('starts_at', '<=', $end)
                      ->where('ends_at', '>=', $start);
            } catch (\Throwable $e) {
                // Fallback gracefully without throwing 500 error
            }
        }

        if ($celebId = $request->input('celebrity_id')) {
            $query->whereHas('celebrities', fn($q) => $q->where('celebrities.id', $celebId));
        }

        $bookings = $query->get();

        $events = [];
        foreach ($bookings as $b) {
            $color = match ($b->status) {
                'confirmed' => '#10b981', // Emerald green
                'tentative' => '#f59e0b', // Warning orange
                'completed' => '#3b82f6', // Primary blue
                'cancelled' => '#ef4444', // Danger red
                default => '#6b7280',
            };

            $celebNames = $b->celebrities->pluck('stage_name')->join(', ');

            $events[] = [
                'id' => $b->id,
                'title' => "{$b->title} (" . ($celebNames ?: 'No Talent') . ")",
                'start' => $b->starts_at ? $b->starts_at->toIso8601String() : null,
                'end' => $b->ends_at ? $b->ends_at->toIso8601String() : null,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'url' => route('admin.bookings.show', $b),
                'extendedProps' => [
                    'booking_code' => $b->booking_code,
                    'status' => ucfirst($b->status),
                    'venue' => $b->venue_name,
                    'project' => $b->project ? $b->project->project_name : 'N/A',
                ],
            ];
        }

        // Also add Celebrity Availability Blocked periods into calendar
        $availQuery = CelebrityAvailability::with('celebrity');
        if ($celebId) {
            $availQuery->where('celebrity_id', $celebId);
        }
        $availBlocks = $availQuery->get();

        foreach ($availBlocks as $block) {
            if (!$block->celebrity || !$block->starts_at || !$block->ends_at) {
                continue;
            }
            $events[] = [
                'id' => 'block_' . $block->id,
                'title' => "[BLOCKED] {$block->celebrity->stage_name}: " . ($block->reason ?: 'Unavailable'),
                'start' => $block->starts_at->toIso8601String(),
                'end' => $block->ends_at->toIso8601String(),
                'backgroundColor' => '#6b7280',
                'borderColor' => '#4b5563',
                'allDay' => false,
            ];
        }

        return response()->json($events);
    }
}

