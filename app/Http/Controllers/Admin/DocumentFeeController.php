<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DocumentFeeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = Auth::user();
                if (!$user || !in_array($user->role, ['admin', 'super_admin', 'finance_admin'])) {
                    abort(403, __('app.unauthorized_document_fees'));
                }
                return $next($request);
            }),
        ];
    }

    public function index()
    {
        $documentTypes = DocumentType::all();
        return view('admin.document-fees.index', compact('documentTypes'));
    }

    public function update(Request $request, DocumentType $documentType)
    {
        $paymentRequired = $request->boolean('payment_required');

        $rules = [
            'payment_required' => 'required',
        ];

        if ($paymentRequired) {
            $rules['fee_amount'] = 'required|numeric|min:1';
        }

        $validated = $request->validate($rules);

        $feeAmount = $paymentRequired
            ? $validated['fee_amount']
            : 0;

        $documentType->update([
            'payment_required' => $paymentRequired,
            'fee_amount' => $feeAmount,
        ]);

        $message = $paymentRequired
            ? __('app.document_fee_updated_success')
            : __('app.document_free_success');

        return redirect()->route('admin.document-fees.index')->with('success', $message);
    }
}
