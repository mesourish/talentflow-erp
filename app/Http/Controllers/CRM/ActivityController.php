<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    App\Http\Controllers\CRM
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class ActivityController
 *
 * Tracks CRM client interactions, calls, brand meetings, follow-up scheduling, and customer communication logs.
 *
 * @package App\Http\Controllers\CRM
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class ActivityController extends Controller
{
    public function index(): View
    {
        $activities = Activity::with(['user', 'client', 'lead', 'celebrity', 'project'])
            ->latest('activity_at')
            ->paginate(15);

        return view('crm.activities.index', compact('activities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'lead_id' => 'nullable|exists:leads,id',
            'celebrity_id' => 'nullable|exists:celebrities,id',
            'project_id' => 'nullable|exists:projects,id',
            'activity_type' => 'required|string|max:50',
            'subject' => 'required|string|max:200',
            'description' => 'nullable|string',
            'activity_at' => 'required|date',
            'follow_up_at' => 'nullable|date',
            'status' => 'required|string|max:30',
        ]);

        $validated['user_id'] = auth()->id() ?? 1;

        $activity = Activity::create($validated);

        ActivityLog::log('crm_activity_logged', "Logged {$activity->activity_type}: {$activity->subject}", $activity);

        return back()->with('success', 'Activity logged successfully.');
    }

    public function update(Request $request, Activity $activity): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|max:30',
            'follow_up_at' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $activity->update($validated);

        return back()->with('success', 'Activity updated.');
    }
}

