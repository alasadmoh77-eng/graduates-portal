<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IssuedDocument;

class VerificationController extends Controller
{
    /**
     * Show verification form or handle auto-token from QR
     */
    public function show(Request $request, $token = null)
    {
        $document = null;
        if ($token) {
            $document = IssuedDocument::with(['documentRequest.user.graduate.major', 'documentRequest.documentType'])
                ->where('qr_token', $token)
                ->orWhere('serial_number', $token)
                ->orWhereHas('documentRequest', function ($query) use ($token) {
                    $query->where('tracking_code', $token);
                })
                ->first();
        }
        
        return view('verify', compact('document', 'token'));
    }

    /**
     * Process verification search
     */
    public function verify(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        $token = $request->token;

        $document = IssuedDocument::with(['documentRequest.user.graduate.major', 'documentRequest.documentType'])
            ->where('qr_token', $token)
            ->orWhere('serial_number', $token)
            ->orWhereHas('documentRequest', function ($query) use ($token) {
                $query->where('tracking_code', $token);
            })
            ->first();

        return view('verify', compact('document', 'token'));
    }
}
