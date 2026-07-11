<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\IssuedDocument;
use App\Services\DocumentIssuanceService;

class RegenerateIssuedDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:regenerate {--id= : Regenerate specific document request ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate PDF files for issued documents using the latest templates while preserving serial numbers and QR tokens';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->option('id');
        $issuanceService = app(DocumentIssuanceService::class);
        $admin = \App\Models\User::where('role', 'admin')->first();
        if (!$admin) {
            $this->error('No admin user found to perform this action.');
            return Command::FAILURE;
        }

        if ($id) {
            $record = IssuedDocument::where('document_request_id', $id)->first();
            if (!$record) {
                $this->error("No issued document found for request ID: {$id}");
                return Command::FAILURE;
            }
            $records = collect([$record]);
        } else {
            $records = IssuedDocument::all();
        }

        if ($records->isEmpty()) {
            $this->info('No issued documents found to regenerate.');
            return Command::SUCCESS;
        }

        $this->info("Starting regeneration of {$records->count()} document(s)...");

        foreach ($records as $record) {
            $request = $record->documentRequest;
            $this->comment("Regenerating request ID: {$request->id} (Serial: {$record->serial_number})");
            try {
                $issuanceService->issue($request, $admin->id);
                $this->info("  [SUCCESS] Regenerated {$record->serial_number}");
            } catch (\Exception $e) {
                $this->error("  [ERROR] Failed to regenerate {$record->serial_number}: {$e->getMessage()}");
            }
        }

        $this->info('Regeneration complete.');
        return Command::SUCCESS;
    }
}