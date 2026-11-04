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
use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\ClientContact;
use App\Services\CodeGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class ClientController
 *
 * Manages brand client profiles, corporate details, GSTIN tax identifiers, and client contacts.
 *
 * @package App\Http\Controllers\CRM
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class ClientController extends Controller
{
    public function __construct(
        protected CodeGenerator $codeGenerator
    ) {}

    public function index(Request $request): View
    {
        $query = Client::withCount(['contacts', 'projects', 'quotations']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('client_code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($industry = $request->input('industry')) {
            $query->where('industry', $industry);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $clients = $query->latest()->paginate(10)->withQueryString();
        $industries = Client::whereNotNull('industry')->distinct()->pluck('industry');

        return view('crm.clients.index', compact('clients', 'industries'));
    }

    public function create(): View
    {
        $suggestedCode = $this->codeGenerator->clientCode();
        return view('crm.clients.create', compact('suggestedCode'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_code' => 'required|string|max:30|unique:clients,client_code',
            'company_name' => 'required|string|max:200',
            'client_type' => 'required|string|max:50',
            'industry' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:30',
            'billing_address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:100',
            'payment_terms_days' => 'required|integer|min:0',
            'status' => 'required|string|max:30',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id() ?? 1;

        $client = Client::create($validated);

        // Optional primary contact creation
        if ($request->filled('contact_name')) {
            ClientContact::create([
                'client_id' => $client->id,
                'name' => $request->input('contact_name'),
                'designation' => $request->input('contact_designation'),
                'email' => $request->input('contact_email'),
                'phone' => $request->input('contact_phone'),
                'is_primary' => true,
            ]);
        }

        ActivityLog::log('client_created', "Created client {$client->company_name} ({$client->client_code})", $client);

        return redirect()->route('admin.clients.show', $client)->with('success', 'Client created successfully.');
    }

    public function show(Client $client): View
    {
        $client->load([
            'contacts',
            'leads' => fn($q) => $q->latest()->take(5),
            'quotations' => fn($q) => $q->latest()->take(5),
            'projects' => fn($q) => $q->latest()->take(5),
            'invoices' => fn($q) => $q->latest()->take(5),
            'activities' => fn($q) => $q->with('user')->latest()->take(5),
        ]);

        return view('crm.clients.show', compact('client'));
    }

    public function edit(Client $client): View
    {
        return view('crm.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:200',
            'client_type' => 'required|string|max:50',
            'industry' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:30',
            'billing_address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:100',
            'payment_terms_days' => 'required|integer|min:0',
            'status' => 'required|string|max:30',
            'notes' => 'nullable|string',
        ]);

        $client->update($validated);

        ActivityLog::log('client_updated', "Updated client details for {$client->company_name}", $client);

        return redirect()->route('admin.clients.show', $client)->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        if ($client->projects()->exists() || $client->invoices()->exists()) {
            return back()->with('error', 'Cannot delete client with linked projects or invoices. Deactivate instead.');
        }

        $clientName = $client->company_name;
        $client->delete();

        ActivityLog::log('client_deleted', "Deleted client {$clientName}");

        return redirect()->route('admin.clients.index')->with('success', 'Client removed successfully.');
    }
}

