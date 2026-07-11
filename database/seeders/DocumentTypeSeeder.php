<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\DocumentType;
use App\Models\DocumentRequest;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name_ar' => 'سجل أكاديمي', 
                'name_en' => 'Academic Record', 
                'code' => 'ACADEMIC_RECORD', 
                'fee_mock' => 3000.00, 
                'eta_days' => 5,
                'fee_amount' => 2000,
                'currency' => 'YER',
                'payment_required' => true,
            ],
            [
                'name_ar' => 'شهادة الدرجات والتقديرات', 
                'name_en' => 'Grades Certificate', 
                'code' => 'GRADES_CERTIFICATE', 
                'fee_mock' => 5000.00, 
                'eta_days' => 7,
                'fee_amount' => 3000,
                'currency' => 'YER',
                'payment_required' => true,
            ],
        ];

        // 1. Check existing types in DB
        $existingTypes = DocumentType::orderBy('id')->get();
        
        $academicId = null;
        $gradesId = null;
        
        if ($existingTypes->count() >= 2) {
            // Repurpose the first two existing IDs to our desired ones to avoid foreign key crash
            $existingTypes[0]->update($types[0]);
            $academicId = $existingTypes[0]->id;
            
            $existingTypes[1]->update($types[1]);
            $gradesId = $existingTypes[1]->id;
        } else {
            // Seed normally if <2 existed
            $academicRecord = DocumentType::updateOrCreate(['code' => $types[0]['code']], $types[0]);
            $gradesCertificate = DocumentType::updateOrCreate(['code' => $types[1]['code']], $types[1]);
            $academicId = $academicRecord->id;
            $gradesId = $gradesCertificate->id;
        }

        // 2. Map all existing requests to the first valid document type (Academic Record)
        // so that dropping old records doesn't cause Integrity Constraint Violations.
        DocumentRequest::whereNotIn('document_type_id', [$academicId, $gradesId])
                       ->update(['document_type_id' => $academicId]);

        // 3. Delete all old document types strictly to keep only the two
        DocumentType::whereNotIn('code', ['ACADEMIC_RECORD', 'GRADES_CERTIFICATE'])->delete();
    }
}
