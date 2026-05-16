<?php

namespace App\Services;

use App\Models\DocumentRequest;
use Carbon\Carbon;

class TrackingCodeService
{
    public function generateNextCode(): string
    {
        $year = Carbon::now()->year;
        $prefix = "DOC-{$year}-";

        $lastRequest = DocumentRequest::where('tracking_code', 'like', "{$prefix}%")
            ->orderBy('tracking_code', 'desc')
            ->first();

        if (!$lastRequest) {
            $number = 1;
        } else {
            // Extract the number from DOC-YYYY-NNNNN
            $lastNumber = (int) substr($lastRequest->tracking_code, -5);
            $number = $lastNumber + 1;
        }

        return $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
