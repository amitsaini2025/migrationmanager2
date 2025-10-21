<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Admin;
use App\Models\Lead;
use App\Services\SignatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SignatureDashboardController extends Controller
{
    protected $signatureService;

    public function __construct(SignatureService $signatureService)
    {
        $this->signatureService = $signatureService;
    }

    public function index(Request $request)
    {
        $user = Auth::guard('admin')->user();
        
        // Get documents based on user permissions
        $query = Document::with(['creator', 'signers', 'documentable'])
            ->notArchived()
            ->orderBy('created_at', 'desc');

        // Apply filters based on user role and request
        if ($request->has('tab')) {
            switch ($request->tab) {
                case 'sent_by_me':
                    $query->forUser($user->id);
                    break;
                case 'all':
                    // Only admins can see all documents
                    if ($user->role !== 1) {
                        $query->forUser($user->id);
                    }
                    break;
                case 'pending':
                    $query->byStatus('sent');
                    break;
                case 'signed':
                    $query->byStatus('signed');
                    break;
            }
        } else {
            // Default: show user's documents
            $query->forUser($user->id);
        }

        // Additional filters
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('association')) {
            if ($request->association === 'associated') {
                $query->associated();
            } elseif ($request->association === 'adhoc') {
                $query->adhoc();
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%")
                  ->orWhere('primary_signer_email', 'like', "%{$search}%");
            });
        }

        $documents = $query->paginate(20);

        // Get counts for dashboard cards
        $counts = [
            'sent_by_me' => Document::forUser($user->id)->notArchived()->count(),
            'pending' => Document::forUser($user->id)->byStatus('sent')->notArchived()->count(),
            'signed' => Document::forUser($user->id)->byStatus('signed')->notArchived()->count(),
            'overdue' => Document::forUser($user->id)
                ->whereNotNull('due_at')
                ->where('due_at', '<', now())
                ->where('status', '!=', 'signed')
                ->notArchived()
                ->count(),
        ];

        // Add admin counts if user is admin
        if ($user->role === 1) {
            $counts['all'] = Document::notArchived()->count();
            $counts['all_pending'] = Document::byStatus('sent')->notArchived()->count();
        }

        return view('Admin.signatures.dashboard', compact('documents', 'counts', 'user'));
    }

    public function create()
    {
        $user = Auth::guard('admin')->user();
        
        // Check authorization
        $this->authorize('create', Document::class);
        
        // Get clients and leads for association dropdown
        $clients = Admin::where('role', '!=', 7)->get(['id', 'first_name', 'last_name', 'email']);
        $leads = Lead::get(['id', 'first_name', 'last_name', 'email']);

        return view('Admin.signatures.create', compact('clients', 'leads', 'user'));
    }

    public function store(Request $request)
    {
        // Check authorization
        $this->authorize('create', Document::class);
        
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'title' => 'nullable|string|max:255',
            'signer_email' => 'required|email',
            'signer_name' => 'required|string|min:2|max:100',
            'document_type' => 'nullable|string|in:agreement,nda,general,contract',
            'priority' => 'nullable|string|in:low,normal,high',
            'due_at' => 'nullable|date|after:now',
            'association_type' => 'nullable|string|in:client,lead',
            'association_id' => 'nullable|integer',
        ]);

        $user = Auth::guard('admin')->user();

        // Handle file upload
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        // Create document
        $document = Document::create([
            'file_name' => $fileName,
            'filetype' => $file->getClientMimeType(),
            'myfile' => $filePath,
            'file_size' => $file->getSize(),
            'status' => 'draft',
            'created_by' => $user->id,
            'origin' => 'ad_hoc',
            'title' => $request->title ?: $file->getClientOriginalName(),
            'document_type' => $request->document_type ?: 'general',
            'priority' => $request->priority ?: 'normal',
            'due_at' => $request->due_at,
            'primary_signer_email' => $request->signer_email,
            'signer_count' => 1,
            'last_activity_at' => now(),
        ]);

        // Set association if provided
        if ($request->association_type && $request->association_id) {
            $this->signatureService->associate(
                $document, 
                $request->association_type, 
                $request->association_id
            );
        }

        // Send document for signature using service
        $signers = [
            ['email' => $request->signer_email, 'name' => $request->signer_name]
        ];
        
        $success = $this->signatureService->send($document, $signers, [
            'subject' => 'Document Signature Request',
            'message' => 'Please sign the document: ' . $document->display_title
        ]);

        if ($success) {
            return redirect()->route('admin.signatures.index')
                ->with('success', 'Document sent for signature successfully!');
        } else {
            return redirect()->route('admin.signatures.index')
                ->with('error', 'Document created but email failed to send. Please try again.');
        }
    }

    public function show($id)
    {
        $document = Document::with(['creator', 'signers', 'documentable', 'signatureFields'])
            ->findOrFail($id);

        // Check authorization using policy
        $this->authorize('view', $document);

        return view('Admin.signatures.show', compact('document'));
    }

    public function sendReminder(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        
        // Check authorization
        $this->authorize('sendReminder', $document);
        
        $signerId = $request->signer_id;
        $signer = $document->signers()->findOrFail($signerId);

        // Use service to send reminder
        $success = $this->signatureService->remind($signer);

        if ($success) {
            return back()->with('success', 'Reminder sent successfully!');
        } else {
            return back()->with('error', 'Failed to send reminder. Please check limits and try again.');
        }
    }

    public function copyLink($id)
    {
        $document = Document::findOrFail($id);
        
        // Check authorization
        $this->authorize('view', $document);
        
        $signer = $document->signers()->first();
        
        if (!$signer) {
            return back()->with('error', 'No signer found for this document.');
        }

        $signingUrl = url("/sign/{$document->id}/{$signer->token}");
        
        return back()->with('success', "Signing link copied to clipboard: {$signingUrl}");
    }
}