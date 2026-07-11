<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateIssuedDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:migrate-pdfs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates existing issued document PDFs from public storage to secure private local storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting migration of issued document PDFs...');

        $records = \App\Models\IssuedDocument::all();

        if ($records->isEmpty()) {
            $this->info('No issued documents found in the database.');
            return Command::SUCCESS;
        }

        $migratedCount = 0;
        $deletedCount = 0;
        $missingCount = 0;

        foreach ($records as $record) {
            $path = $record->pdf_path;

            $existsPublic = \Illuminate\Support\Facades\Storage::disk('public')->exists($path);
            $existsLocal = \Illuminate\Support\Facades\Storage::disk('local')->exists($path);

            $this->comment("Processing Document ID: {$record->id} (Serial Number: {$record->serial_number})");
            $this->line("  Path: {$path}");

            if ($existsPublic) {
                if (!$existsLocal) {
                    // Make sure parent directory exists on local disk
                    $dir = dirname($path);
                    if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($dir)) {
                        \Illuminate\Support\Facades\Storage::disk('local')->makeDirectory($dir);
                    }

                    // Copy file from public to private (local) storage
                    $fileContent = \Illuminate\Support\Facades\Storage::disk('public')->get($path);
                    \Illuminate\Support\Facades\Storage::disk('local')->put($path, $fileContent);
                    $this->info("  [SUCCESS] Migrated file to private storage.");
                    $migratedCount++;
                } else {
                    $this->line("  [INFO] File already exists in private storage.");
                }

                // Delete from public storage to secure the file
                \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                $this->info("  [SECURED] Removed original file from public storage.");
                $deletedCount++;
            } else {
                if ($existsLocal) {
                    $this->line("  [OK] File exists in private storage, missing from public (already migrated).");
                } else {
                    $this->warn("  [WARNING] File does not exist in public or private storage!");
                    $missingCount++;
                }
            }
        }

        $this->info('=== Migration Summary ===');
        $this->info("Total records checked: " . $records->count());
        $this->info("Migrated files: {$migratedCount}");
        $this->info("Secured (deleted from public): {$deletedCount}");
        $this->info("Missing files: {$missingCount}");

        return Command::SUCCESS;
    }
}
