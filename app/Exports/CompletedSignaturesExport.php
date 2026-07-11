<?php

namespace App\Exports;

use App\Models\IssuedDocument;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CompletedSignaturesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return IssuedDocument::whereNotNull('all_signed_at')
            ->whereNotNull('document_request_id')
            ->with(['documentRequest.user.graduate.major', 'documentRequest.documentType', 'signatures.user'])
            ->latest('all_signed_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'الرقم التسلسلي',
            'اسم الخريج',
            'الرقم الجامعي',
            'نوع الوثيقة',
            'تاريخ اكتمال التوقيعات',
            'الموقعون',
            'تواريخ التوقيع',
            'الحالة',
        ];
    }

    public function map($doc): array
    {
        $request = $doc->documentRequest;
        $signersInfo = $doc->signatures->map(function ($sig) {
            return $sig->role_title . ' - ' . ($sig->user->name ?? '—') . ' (' . $sig->signed_at->format('Y-m-d H:i') . ')';
        })->implode("\n");

        $signersNames = $doc->signatures->pluck('role_title')->implode(' | ');

        return [
            $doc->serial_number,
            $request->user->name ?? '—',
            $request->user->graduate->university_id ?? '—',
            app()->getLocale() == 'ar' ? ($request->documentType->name_ar ?? '—') : ($request->documentType->name_en ?? '—'),
            $doc->all_signed_at->format('Y-m-d H:i'),
            $signersNames,
            $signersInfo,
            $request->status ?? '—',
        ];
    }
}
