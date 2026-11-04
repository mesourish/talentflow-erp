<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    Database\Seeders
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Document;
use App\Models\DocumentAccessLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class DocumentSeeder
 *
 * Enterprise Seeder responsible for DocumentSeeder business logic and data operations.
 *
 * @package Database\Seeders
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $docManager = User::where('email', 'documents@demo.test')->first() ?? User::first();
        $contracts = Contract::all();
        $projects = Project::all();

        // Ensure storage directory exists
        Storage::disk('local')->makeDirectory('documents/sample');

        foreach ($contracts->take(10) as $contract) {
            $fileName = "contract_{$contract->contract_no}.pdf";
            $storagePath = "documents/contract/{$contract->id}/{$fileName}";

            // Create a mock document in local storage
            Storage::disk('local')->put(
                $storagePath,
                "%PDF-1.4 Mock Contract Document for {$contract->contract_title}\nContract No: {$contract->contract_no}\nTerritory: {$contract->territory}\nStatus: Signed"
            );

            $doc = Document::firstOrCreate(
                [
                    'documentable_type' => Contract::class,
                    'documentable_id' => $contract->id,
                    'category' => 'contract',
                ],
                [
                    'original_name' => "Signed_{$contract->contract_no}.pdf",
                    'stored_name' => Str::random(40) . '.pdf',
                    'disk' => 'local',
                    'file_path' => $storagePath,
                    'mime_type' => 'application/pdf',
                    'file_size' => 245000,
                    'version_no' => 1,
                    'is_confidential' => true,
                    'uploaded_by' => $docManager->id,
                ]
            );

            DocumentAccessLog::create([
                'document_id' => $doc->id,
                'user_id' => $docManager->id,
                'action' => 'upload',
                'accessed_at' => now()->subDays(5),
                'ip_address' => '127.0.0.1',
            ]);
        }

        foreach ($projects->take(5) as $proj) {
            $fileName = "project_brief_{$proj->project_code}.pdf";
            $storagePath = "documents/project/{$proj->id}/{$fileName}";

            Storage::disk('local')->put(
                $storagePath,
                "%PDF-1.4 Mock Project Brief for {$proj->project_name}\nCode: {$proj->project_code}\nBudget: {$proj->budget_amount}"
            );

            Document::firstOrCreate(
                [
                    'documentable_type' => Project::class,
                    'documentable_id' => $proj->id,
                    'category' => 'project_brief',
                ],
                [
                    'original_name' => "Creative_Brief_{$proj->project_code}.pdf",
                    'stored_name' => Str::random(40) . '.pdf',
                    'disk' => 'local',
                    'file_path' => $storagePath,
                    'mime_type' => 'application/pdf',
                    'file_size' => 180000,
                    'version_no' => 1,
                    'is_confidential' => false,
                    'uploaded_by' => $docManager->id,
                ]
            );
        }
    }
}

