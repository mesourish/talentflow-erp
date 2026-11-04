<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    App\Http\Controllers\Talent
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace App\Http\Controllers\Talent;

use App\Http\Controllers\Controller;
use App\Models\TalentCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class TalentCategoryController
 *
 * Categorizes artist profiles into actors, playback singers, sports icons, and influencers.
 *
 * @package App\Http\Controllers\Talent
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class TalentCategoryController extends Controller
{
    public function index(): View
    {
        $categories = TalentCategory::withCount('celebrities')->get();
        return view('talent.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:talent_categories,name',
            'description' => 'nullable|string',
            'status' => 'required|string|max:30',
        ]);

        TalentCategory::create($validated);

        return back()->with('success', 'Talent category created.');
    }

    public function update(Request $request, TalentCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:talent_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'status' => 'required|string|max:30',
        ]);

        $category->update($validated);

        return back()->with('success', 'Talent category updated.');
    }
}

