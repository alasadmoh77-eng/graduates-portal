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

        $graduate->load(['graduate.major', 'academicRecord.levels.semesters.subjects']);

        $initialData = $graduate->academicRecord
            ? $graduate->academicRecord->toAlpinePayload()
            : $this->defaultPayload($graduate);

        return view('admin.academic-record.create', [
            'graduate' => $graduate,
            'initialData' => $initialData,
            'saveUrl' => route('admin.graduates.academic-record.update', $graduate),
            'subjectCatalog' => config('academic_subject_catalog.levels', []),
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

        return [
            'student' => [
                'name' => $user->name,
                'name_en' => $user->name,
                'id' => $g->university_id ?? '',
                'degree' => 'بكالوريوس',
                'degree_en' => "Bachelor's Degree",
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
                    'totalPoints' => '',
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
                    'totalPoints' => '',
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
                    'totalPoints' => '',
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
                    'totalPoints' => '',
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
