<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    App\Services
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace App\Services;

use App\Models\Booking;
use App\Models\CelebrityAvailability;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class BookingAvailabilityService
 *
 * Domain service checking calendar overlaps, block-out dates, and scheduling conflicts for talent.
 *
 * @package App\Services
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class BookingAvailabilityService
{
    /**
     * Check if a celebrity has booking conflict or unavailable periods.
     */
    public function getCelebrityConflicts(
        int $celebrityId,
        Carbon|string $start,
        Carbon|string $end,
        ?int $ignoreBookingId = null
    ): array {
        $startTime = Carbon::parse($start);
        $endTime = Carbon::parse($end);

        // 1. Check existing tentative or confirmed bookings
        $bookingQuery = Booking::whereIn('status', ['tentative', 'confirmed'])
            ->where('starts_at', '<', $endTime)
            ->where('ends_at', '>', $startTime)
            ->whereHas('celebrities', function ($q) use ($celebrityId) {
                $q->where('celebrities.id', $celebrityId);
            });

        if ($ignoreBookingId) {
            $bookingQuery->where('id', '!=', $ignoreBookingId);
        }

        $conflictingBookings = $bookingQuery->with('project')->get();

        // 2. Check blocked or unavailable windows in celebrity_availability
        $unavailabilityQuery = CelebrityAvailability::where('celebrity_id', $celebrityId)
            ->whereIn('availability_type', ['blocked', 'unavailable'])
            ->where('starts_at', '<', $endTime)
            ->where('ends_at', '>', $startTime);

        $conflictingAvailability = $unavailabilityQuery->get();

        $hasConflict = $conflictingBookings->isNotEmpty() || $conflictingAvailability->isNotEmpty();

        return [
            'has_conflict' => $hasConflict,
            'bookings' => $conflictingBookings,
            'availability_blocks' => $conflictingAvailability,
        ];
    }

    /**
     * Check conflicts across a list of celebrities for a proposed booking.
     */
    public function checkMultiCelebrityAvailability(
        array $celebrityIds,
        Carbon|string $start,
        Carbon|string $end,
        ?int $ignoreBookingId = null
    ): array {
        $conflicts = [];
        $hasAnyConflict = false;

        foreach ($celebrityIds as $celebrityId) {
            $result = $this->getCelebrityConflicts((int) $celebrityId, $start, $end, $ignoreBookingId);
            if ($result['has_conflict']) {
                $hasAnyConflict = true;
                $conflicts[$celebrityId] = $result;
            }
        }

        return [
            'has_conflict' => $hasAnyConflict,
            'details' => $conflicts,
        ];
    }
}

