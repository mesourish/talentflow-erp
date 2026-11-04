<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    App\Http\Controllers\Projects
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Class ProjectTaskController
 *
 * Tracks task allocations, priority deadlines, assignees, and project delivery workflows.
 *
 * @package App\Http\Controllers\Projects
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class ProjectTaskController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'task_title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|string|max:30',
            'status' => 'required|string|max:30',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ]);

        $project->tasks()->create($validated);

        return back()->with('success', 'Task added to project.');
    }

    public function update(Request $request, Project $project, ProjectTask $task): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|max:30',
            'priority' => 'nullable|string|max:30',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        if ($validated['status'] === 'done' && $task->status !== 'done') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'done') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);

        return back()->with('success', 'Task updated.');
    }

    public function destroy(Project $project, ProjectTask $task): RedirectResponse
    {
        $task->delete();
        return back()->with('success', 'Task removed.');
    }
}

