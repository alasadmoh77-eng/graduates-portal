<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Http\Requests\StoreDocumentRequest;
use App\Services\TrackingCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentRequestController extends Controller
{
    protected $trackingCodeService;

    public function __construct(TrackingCodeService $trackingCodeService)
    {
        $this->trackingCodeService = $trackingCodeService;
    }

    /**
     * Display a listing of the requests for the logged-in graduate.
     */
    public function index(Request $request)
    {
        $query = DocumentRequest::with(['documentType', 'issuedDocument'])
            ->where('user_id', Auth::id())
            ->latest();

        if ($request->filled('filter')) {
            match ($request->filter) {
                'in_progress' => $query->whereIn('status', ['SUBMITTED', 'UNDER_REVIEW', 'APPROVED']),
                'ready' => $query->whereIn('status', ['READY', 'ISSUED']),
                'rejected' => $query->where('status', 'REJECTED'),
                default => null,
            };
        }

        $requests = $query->paginate(10)->withQueryString();
        $filter = $request->get('filter', 'all');

        return view('graduate.documents.index', compact('requests', 'filter'));
    }

    /**
     * Show the form for creating a new request.
     */
    public function create()
    {
        $types = DocumentType::whereIn('code', ['ACADEMIC_RECORD', 'GRADES_CERTIFICATE'])->get();
        return view('graduate.documents.create', compact('types'));
    }

    /**
     * Store a newly created request in storage.
     */
    public function store(StoreDocumentRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['tracking_code'] = $this->trackingCodeService->generateNextCode();
        $data['status'] = 'SUBMITTED';

        DocumentRequest::create($data);

        return redirect()->route('graduate.documents.index')
            ->with('success', __('app.document_request_submitted'));
    }

    /**
     * Display the specified request.
     */
    public function show(DocumentRequest $document)
    {
        // Security check
        if ($document->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بمشاهدة هذا الطلب.');
        }

        $document->load([
            'documentType',
            'issuedDocument',
            'logs' => fn ($q) => $q->reorder()->with('admin')->orderBy('created_at', 'asc'),
        ]);

        return view('graduate.documents.show', compact('document'));
    }

    /**
     * Download issued document
     */
    public function download(DocumentRequest $document)
    {
        // Security check
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        if (!in_array($document->status, ['READY', 'ISSUED']) || !$document->issuedDocument) {
            return back()->with('error', 'المستند غير جاهز للتحميل بعد.');
        }

        $path = $document->issuedDocument->pdf_path;
        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return back()->with('error', 'ملف المستند غير موجود على الخادم.');
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->download($path, "Document-{$document->tracking_code}.pdf");
    }
}
