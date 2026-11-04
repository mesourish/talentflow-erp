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
use App\Models\ActivityLog;
use App\Models\Celebrity;
use App\Models\CelebrityRate;
use App\Models\CelebritySocialProfile;
use App\Models\TalentCategory;
use App\Services\CodeGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class CelebrityController
 *
 * Curates talent roster, social reach metrics, rate cards, and confidential management contact records.
 *
 * @package App\Http\Controllers\Talent
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class CelebrityController extends Controller
{
    public function __construct(
        protected CodeGenerator $codeGenerator
    ) {}

    public function index(Request $request): View
    {
        $query = Celebrity::with(['category', 'rates' => fn($q) => $q->where('is_active', true)]);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('stage_name', 'like', "%{$search}%")
                  ->orWhere('talent_code', 'like', "%{$search}%")
                  ->orWhere('base_city', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($status = $request->input('availability_status')) {
            $query->where('availability_status', $status);
        }

        $celebrities = $query->paginate(12)->withQueryString();
        $categories = TalentCategory::where('status', 'active')->orderBy('name')->get();

        return view('talent.celebrities.index', compact('celebrities', 'categories'));
    }

    public function create(): View
    {
        $suggestedCode = $this->codeGenerator->talentCode();
        $categories = TalentCategory::where('status', 'active')->orderBy('name')->get();

        return view('talent.celebrities.create', compact('suggestedCode', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'talent_code' => 'required|string|max:30|unique:celebrities,talent_code',
            'category_id' => 'required|exists:talent_categories,id',
            'stage_name' => 'required|string|max:150',
            'legal_name' => 'nullable|string|max:150',
            'biography' => 'nullable|string',
            'base_city' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:30',
            'manager_name' => 'nullable|string|max:150',
            'manager_contact' => 'nullable|string|max:50',
            'profile_image' => 'nullable|url|max:255',
            'availability_status' => 'required|string|max:30',
            'profile_status' => 'required|string|max:30',
            'notes' => 'nullable|string',
        ]);

        $celebrity = Celebrity::create($validated);

        // Add standard default rate cards if provided
        if ($request->filled('standard_rate')) {
            CelebrityRate::create([
                'celebrity_id' => $celebrity->id,
                'service_type' => 'Brand Endorsement',
                'description' => 'Standard commercial engagement fee',
                'currency' => 'INR',
                'standard_fee' => $request->input('standard_rate'),
                'effective_from' => now()->toDateString(),
                'is_active' => true,
            ]);
        }

        ActivityLog::log('talent_created', "Celebrity profile {$celebrity->stage_name} ({$celebrity->talent_code}) created.", $celebrity);

        return redirect()->route('admin.celebrities.show', $celebrity)->with('success', 'Celebrity profile created successfully.');
    }

    public function show(Celebrity $celebrity): View
    {
        $celebrity->load([
            'category',
            'rates',
            'socialProfiles',
            'availability' => fn($q) => $q->latest()->take(5),
            'bookings' => fn($q) => $q->with('project')->latest('starts_at')->take(6),
            'payouts' => fn($q) => $q->with('project')->latest()->take(6),
            'contracts' => fn($q) => $q->with('client')->latest()->take(5),
        ]);

        $canViewSensitive = auth()->user()->hasAnyRole(['Super Admin', 'Management', 'Talent Manager']);

        return view('talent.celebrities.show', compact('celebrity', 'canViewSensitive'));
    }

    public function edit(Celebrity $celebrity): View
    {
        $categories = TalentCategory::where('status', 'active')->orderBy('name')->get();
        return view('talent.celebrities.edit', compact('celebrity', 'categories'));
    }

    public function update(Request $request, Celebrity $celebrity): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:talent_categories,id',
            'stage_name' => 'required|string|max:150',
            'legal_name' => 'nullable|string|max:150',
            'biography' => 'nullable|string',
            'base_city' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:30',
            'manager_name' => 'nullable|string|max:150',
            'manager_contact' => 'nullable|string|max:50',
            'profile_image' => 'nullable|url|max:255',
            'availability_status' => 'required|string|max:30',
            'profile_status' => 'required|string|max:30',
            'notes' => 'nullable|string',
        ]);

        $celebrity->update($validated);

        ActivityLog::log('talent_updated', "Updated profile for {$celebrity->stage_name}", $celebrity);

        return redirect()->route('admin.celebrities.show', $celebrity)->with('success', 'Profile updated successfully.');
    }

    public function storeRate(Request $request, Celebrity $celebrity): RedirectResponse
    {
        $validated = $request->validate([
            'service_type' => 'required|string|max:100',
            'standard_fee' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'description' => 'nullable|string',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
        ]);

        $celebrity->rates()->create($validated);

        return back()->with('success', 'Rate card entry added.');
    }
}

