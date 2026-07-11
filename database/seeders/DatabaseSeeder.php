<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MajorSeeder::class,
            DocumentTypeSeeder::class,
            UserSeeder::class,
            SampleDocumentSeeder::class,
            MoreSampleDataSeeder::class,
        ]);
    }
}
