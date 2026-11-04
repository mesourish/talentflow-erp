<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    App\Http\Controllers\Documents
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Contract;
use App\Models\Document;
use App\Models\Project;
use App\Services\DocumentUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class DocumentController
 *
 * Secures enterprise DMS file uploads, category classifications, and document access tracking.
 *
 * @package App\Http\Controllers\Documents
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class DocumentController extends Controller
{
    public function __construct(
        protected DocumentUploadService $uploadService
    ) {}

    public function index(Request $request): View
    {
        $query = Document::with(['documentable', 'uploader']);

        if ($search = $request->input('search')) {
            $query->where('original_name', 'like', "%{$search}%");
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $documents = $query->latest()->paginate(15)->withQueryString();
        $categories = ['contract', 'agreement', 'licence', 'receipt', 'project_brief', 'nda', 'invoice', 'other'];

        $projects = Project::whereIn('status', ['planned', 'active'])->get();
        $contracts = Contract::all();

        return view('documents.index', compact('documents', 'categories', 'projects', 'contracts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|max:20480',
            'owner_type' => 'required|string',
            'owner_id' => 'required|integer',
            'category' => 'required|string|max:100',
            'is_confidential' => 'nullable|boolean',
        ]);

        $ownerClass = match ($request->input('owner_type')) {
            'project' => Project::class,
            'contract' => Contract::class,
            default => Project::class,
        };

        $owner = $ownerClass::findOrFail($request->input('owner_id'));

        $doc = $this->uploadService->upload(
            $request->file('file'),
            $owner,
            $request->input('category'),
            $request->boolean('is_confidential', true)
        );

        ActivityLog::log('document_uploaded', "Uploaded document {$doc->original_name} to " . class_basename($owner), $doc);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function download(Document $document): StreamedResponse
    {
        return $this->uploadService->download($document);
    }

    public function destroy(Document $document): RedirectResponse
    {
        $docName = $document->original_name;
        $document->delete();

        ActivityLog::log('document_deleted', "Removed document {$docName}");

        return back()->with('success', 'Document deleted.');
    }
}

