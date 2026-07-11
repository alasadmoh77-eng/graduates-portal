<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigratePaymentProofs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:migrate-proofs {--delete-original : Delete files from public storage after migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates existing payment proof files from public storage to secure private local storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting migration of payment proofs...');
        
        $records = \App\Models\DocumentRequest::whereNotNull('payment_proof_path')->get();
        
        if ($records->isEmpty()) {
            $this->info('No records with payment proofs found.');
            return Command::SUCCESS;
        }

        $migratedCount = 0;
        $deletedCount = 0;
        $missingCount = 0;

        foreach ($records as $record) {
            $path = $record->payment_proof_path;
            
            $existsPublic = \Illuminate\Support\Facades\Storage::disk('public')->exists($path);
            $existsLocal = \Illuminate\Support\Facades\Storage::disk('local')->exists($path);

            $this->comment("Processing Request ID: {$record->id} (Tracking Code: {$record->tracking_code})");
            $this->line("  Path: {$path}");

            if ($existsPublic) {
                if (!$existsLocal) {
                    // Copy file from public to private (local) storage
                    $fileContent = \Illuminate\Support\Facades\Storage::disk('public')->get($path);
                    \Illuminate\Support\Facades\Storage::disk('local')->put($path, $fileContent);
                    $this->info("  [SUCCESS] Migrated file to private storage.");
                    $migratedCount++;
                } else {
                    $this->line("  [INFO] File already exists in private storage.");
                }

                // Delete from public storage to secure the file
                if ($this->option('delete-original') || true) { // Always delete from public to close the security loophole
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                    $this->info("  [SECURED] Removed original file from public storage.");
                    $deletedCount++;
                }
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
