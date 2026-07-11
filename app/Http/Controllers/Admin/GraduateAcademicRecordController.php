<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGraduateAcademicRecordRequest;
use App\Models\User;
use App\Services\AcademicRecordStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GraduateAcademicRecordController extends Controller
{
    public function __construct(
        protected AcademicRecordStorageService $storageService
    ) {}

    public function edit(User $graduate): View
    {
        abort_unless($graduate->role === 'graduate' && $graduate->graduate !== null, 404);

        $graduate->load(['graduate.major.faculty', 'academicRecord.levels.semesters.subjects']);

        $initialData = $graduate->academicRecord
            ? $graduate->academicRecord->toAlpinePayload()
            : $this->defaultPayload($graduate);

        $majorNameEn = $graduate->graduate->major?->name_en;
        $majorSlug = \App\Support\AcademicSubjectCatalog::getMajorSlug($majorNameEn);
        $subjectCatalog = \App\Support\AcademicSubjectCatalog::levels($majorSlug);

        $latestRequest = \App\Models\DocumentRequest::where('user_id', $graduate->id)
            ->whereHas('documentType', function($q) {
                $q->where('code', 'ACADEMIC_RECORD');
            })
            ->latest()
            ->first();

        return view('admin.academic-record.create', [
            'graduate' => $graduate,
            'initialData' => $initialData,
            'saveUrl' => route('admin.graduates.academic-record.update', $graduate),
            'subjectCatalog' => $subjectCatalog,
            'requestNumber' => $latestRequest?->tracking_code,
            'documentNumber' => $latestRequest?->issuedDocument?->serial_number,
        ]);
    }

    public function update(StoreGraduateAcademicRecordRequest $request, User $graduate): RedirectResponse|JsonResponse
    {
        abort_unless($graduate->role === 'graduate' && $graduate->graduate !== null, 404);

        $this->storageService->sync($graduate, $request->validated());

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'message' => __('app.academic_record_saved')]);
        }

        return redirect()
            ->route('admin.graduates.academic-record.edit', $graduate)
            ->with('success', __('app.academic_record_saved'));
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultPayload(User $user): array
    {
        $g = $user->graduate;
        $major = $g->major;
        
        $degreeAr = $major?->degree_name_ar ?: 'بكالوريوس';
        $degreeEn = $major?->degree_name_en ?: "Bachelor's Degree";

        return [
            'student' => [
                'name' => $user->name,
                'name_en' => $user->name,
                'id' => $g->university_id ?? '',
                'degree' => $degreeAr,
                'degree_en' => $degreeEn,
                'total' => '',
                'gpa' => '',
                'rating' => '',
                'honors' => '',
                'gradYear' => $g->graduation_year ? (string) $g->graduation_year : '',
                'enrollmentYear' => '',
                'dora' => 'يونيو',
            ],
            'levels' => [
                [
                    'name' => 'الأول',
                    'year' => '',
                    'avg' => '',
                    'result' => '',
                    'semesters' => [
                        ['subjects' => [['catalog_key' => '', 'name' => '', 'hours' => '', 'score' => '', 'rating' => '']]],
                        ['subjects' => [['catalog_key' => '', 'name' => '', 'hours' => '', 'score' => '', 'rating' => '']]],
                    ],
                ],
                [
                    'name' => 'الثاني',
                    'year' => '',
                    'avg' => '',
                    'result' => '',
                    'semesters' => [
                        ['subjects' => [['catalog_key' => '', 'name' => '', 'hours' => '', 'score' => '', 'rating' => '']]],
                        ['subjects' => [['catalog_key' => '', 'name' => '', 'hours' => '', 'score' => '', 'rating' => '']]],
                    ],
                ],
                [
                    'name' => 'الثالث',
                    'year' => '',
                    'avg' => '',
                    'result' => '',
                    'semesters' => [
                        ['subjects' => [['catalog_key' => '', 'name' => '', 'hours' => '', 'score' => '', 'rating' => '']]],
                        ['subjects' => [['catalog_key' => '', 'name' => '', 'hours' => '', 'score' => '', 'rating' => '']]],
                    ],
                ],
                [
                    'name' => 'الرابع',
                    'year' => '',
                    'avg' => '',
                    'result' => '',
                    'semesters' => [
                        ['subjects' => [['catalog_key' => '', 'name' => '', 'hours' => '', 'score' => '', 'rating' => '']]],
                        ['subjects' => [['catalog_key' => '', 'name' => '', 'hours' => '', 'score' => '', 'rating' => '']]],
                    ],
                ],
            ],
        ];
    }
}
