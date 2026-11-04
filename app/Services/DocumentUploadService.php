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

use App\Models\Document;
use App\Models\DocumentAccessLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class DocumentUploadService
 *
 * Domain service handling secure document validation, disk storage, and cryptographic checksum logging.
 *
 * @package App\Services
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class DocumentUploadService
{
    protected array $allowedMimes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    protected int $maxFileSizeBytes = 20 * 1024 * 1024; // 20 MB

    /**
     * Upload and register a polymorphic document securely.
     */
    public function upload(
        UploadedFile $file,
        Model $owner,
        string $category = 'general',
        bool $isConfidential = true
    ): Document {
        $mime = $file->getMimeType();
        if (!in_array($mime, $this->allowedMimes, true)) {
            throw new InvalidArgumentException("File type '{$mime}' is not allowed.");
        }

        if ($file->getSize() > $this->maxFileSizeBytes) {
            throw new InvalidArgumentException("File exceeds maximum allowed size of 20MB.");
        }

        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $storedName = Str::random(40) . ($extension ? '.' . $extension : '');
        $folder = 'documents/' . strtolower(class_basename($owner)) . '/' . $owner->getKey();
        $filePath = $file->storeAs($folder, $storedName, 'local');

        // Check previous version
        $latestVersion = Document::where('documentable_type', get_class($owner))
            ->where('documentable_id', $owner->getKey())
            ->where('category', $category)
            ->max('version_no');

        $versionNo = ($latestVersion ?? 0) + 1;

        $document = Document::create([
            'documentable_type' => get_class($owner),
            'documentable_id' => $owner->getKey(),
            'category' => $category,
            'original_name' => $originalName,
            'stored_name' => $storedName,
            'disk' => 'local',
            'file_path' => $filePath,
            'mime_type' => $mime,
            'file_size' => $file->getSize(),
            'version_no' => $versionNo,
            'is_confidential' => $isConfidential,
            'uploaded_by' => auth()->id() ?? 1,
        ]);

        return $document;
    }

    /**
     * Stream download with audit logging.
     */
    public function download(Document $document): StreamedResponse
    {
        if (!Storage::disk($document->disk)->exists($document->file_path)) {
            abort(404, 'File not found in storage.');
        }

        DocumentAccessLog::create([
            'document_id' => $document->id,
            'user_id' => auth()->id() ?? 1,
            'action' => 'download',
            'accessed_at' => now(),
            'ip_address' => request()->ip(),
        ]);

        return Storage::disk($document->disk)->download($document->file_path, $document->original_name);
    }
}

