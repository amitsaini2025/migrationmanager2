@extends('layouts.admin_client_detail')
@section('title', 'Send Document for Signature')

@section('styles')
<style>
    .send-document-container {
        max-width: 900px;
        margin: 30px auto;
        padding: 20px;
    }
    
    .page-header {
        margin-bottom: 30px;
    }
    
    .page-header h1 {
        font-size: 24px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .page-header .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
        font-size: 14px;
    }
    
    .form-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 30px;
    }
    
    .form-section {
        margin-bottom: 30px;
        padding-bottom: 30px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .form-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .form-section-title {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .form-section-title i {
        color: #667eea;
    }
    
    .form-group label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
    }
    
    .form-control {
        border-radius: 6px;
        border: 1px solid #ced4da;
        padding: 10px 15px;
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .file-upload-area {
        border: 2px dashed #ced4da;
        border-radius: 10px;
        padding: 40px;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .file-upload-area:hover {
        border-color: #667eea;
        background: #f0f2ff;
    }
    
    .file-upload-area.dragging {
        border-color: #667eea;
        background: #e8ecff;
    }
    
    .file-upload-icon {
        font-size: 48px;
        color: #667eea;
        margin-bottom: 15px;
    }
    
    .file-selected {
        margin-top: 15px;
        padding: 15px;
        background: #e8ecff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .file-selected i {
        color: #28a745;
        margin-right: 10px;
    }
    
    .btn-remove-file {
        background: #dc3545;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
    }
    
    .association-search {
        position: relative;
    }
    
    .association-type-selector {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .association-type-btn {
        flex: 1;
        padding: 12px;
        border: 2px solid #e9ecef;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-weight: 500;
    }
    
    .association-type-btn:hover {
        border-color: #667eea;
    }
    
    .association-type-btn.active {
        border-color: #667eea;
        background: #e8ecff;
        color: #667eea;
    }
    
    .suggestion-box {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ced4da;
        border-radius: 6px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        margin-top: 5px;
    }
    
    .suggestion-item {
        padding: 10px 15px;
        cursor: pointer;
        transition: background 0.2s ease;
    }
    
    .suggestion-item:hover {
        background: #f8f9fa;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 30px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }
    
    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        color: white;
    }
    
    .btn-cancel {
        background: #6c757d;
        color: white;
        padding: 12px 30px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        margin-right: 15px;
    }
    
    .form-help-text {
        font-size: 13px;
        color: #6c757d;
        margin-top: 5px;
    }
    
    .priority-selector {
        display: flex;
        gap: 10px;
    }
    
    .priority-option {
        flex: 1;
        padding: 10px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .priority-option input[type="radio"] {
        display: none;
    }
    
    .priority-option:hover {
        border-color: #667eea;
    }
    
    .priority-option.selected {
        border-color: #667eea;
        background: #e8ecff;
    }
</style>
@endsection

@section('main-content')
<div class="send-document-container">
    <div class="page-header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.signatures.index') }}">Signature Dashboard</a></li>
                <li class="breadcrumb-item active">Send New Document</li>
            </ol>
        </nav>
        <h1>📤 Send Document for Signature</h1>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <strong>Please fix the following errors:</strong>
        <ul style="margin-bottom: 0; margin-top: 10px;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.signatures.store') }}" method="POST" enctype="multipart/form-data" id="sendDocumentForm">
        @csrf
        
        <div class="form-card">
            <!-- Document Upload Section -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-file-upload"></i>
                    Document
                </h3>
                
                <div class="form-group">
                    <label for="file">Upload Document <span style="color: #dc3545;">*</span></label>
                    <div class="file-upload-area" id="fileUploadArea" onclick="document.getElementById('file').click()">
                        <div class="file-upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <h4>Click to upload or drag and drop</h4>
                        <p class="form-help-text">PDF, DOC, DOCX up to 10MB</p>
                    </div>
                    <input type="file" id="file" name="file" accept=".pdf,.doc,.docx" required style="display: none;">
                    <div id="fileSelected" class="file-selected" style="display: none;">
                        <span><i class="fas fa-check-circle"></i> <span id="fileName"></span></span>
                        <button type="button" class="btn-remove-file" onclick="removeFile()">Remove</button>
                    </div>
                </div>
                
                <div class="form-group" style="margin-top: 20px;">
                    <label for="title">Document Title (Optional)</label>
                    <input type="text" class="form-control" id="title" name="title" 
                           placeholder="e.g., Service Agreement 2024" value="{{ old('title') }}">
                    <small class="form-help-text">If not provided, the filename will be used</small>
                </div>
                
                <div class="form-group" style="margin-top: 20px;">
                    <label for="document_type">Document Type</label>
                    <select class="form-control" id="document_type" name="document_type">
                        <option value="general" {{ old('document_type') == 'general' ? 'selected' : '' }}>General</option>
                        <option value="agreement" {{ old('document_type') == 'agreement' ? 'selected' : '' }}>Agreement</option>
                        <option value="nda" {{ old('document_type') == 'nda' ? 'selected' : '' }}>NDA</option>
                        <option value="contract" {{ old('document_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                    </select>
                </div>
            </div>

            <!-- Signer Information Section -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-user-edit"></i>
                    Signer Information
                </h3>
                
                <div class="form-group">
                    <label for="signer_name">Signer Name <span style="color: #dc3545;">*</span></label>
                    <input type="text" class="form-control" id="signer_name" name="signer_name" 
                           placeholder="John Doe" required value="{{ old('signer_name') }}">
                </div>
                
                <div class="form-group" style="margin-top: 15px;">
                    <label for="signer_email">Signer Email <span style="color: #dc3545;">*</span></label>
                    <input type="email" class="form-control" id="signer_email" name="signer_email" 
                           placeholder="john@example.com" required value="{{ old('signer_email') }}">
                    <small class="form-help-text">The signing link will be sent to this email</small>
                </div>
            </div>

            <!-- Association Section -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-link"></i>
                    Association (Optional)
                </h3>
                
                <div class="association-type-selector">
                    <label class="association-type-btn" id="noneTypeBtn">
                        <input type="radio" name="association_type" value="" checked>
                        <div>None (Ad-hoc)</div>
                    </label>
                    <label class="association-type-btn" id="clientTypeBtn">
                        <input type="radio" name="association_type" value="client">
                        <div>Client</div>
                    </label>
                    <label class="association-type-btn" id="leadTypeBtn">
                        <input type="radio" name="association_type" value="lead">
                        <div>Lead</div>
                    </label>
                </div>
                
                <div id="associationSearch" style="display: none;">
                    <div class="form-group">
                        <label for="association_id">Select <span id="associationTypeLabel">Entity</span></label>
                        <select class="form-control" id="association_id" name="association_id">
                            <option value="">-- Select --</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Additional Options Section -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-cog"></i>
                    Additional Options
                </h3>
                
                <div class="form-group">
                    <label>Priority</label>
                    <div class="priority-selector">
                        <label class="priority-option" id="lowPriority">
                            <input type="radio" name="priority" value="low">
                            <div style="font-weight: 600;">Low</div>
                        </label>
                        <label class="priority-option selected" id="normalPriority">
                            <input type="radio" name="priority" value="normal" checked>
                            <div style="font-weight: 600;">Normal</div>
                        </label>
                        <label class="priority-option" id="highPriority">
                            <input type="radio" name="priority" value="high">
                            <div style="font-weight: 600;">High</div>
                        </label>
                    </div>
                </div>
                
                <div class="form-group" style="margin-top: 20px;">
                    <label for="due_at">Due Date (Optional)</label>
                    <input type="datetime-local" class="form-control" id="due_at" name="due_at" 
                           min="{{ now()->format('Y-m-d\TH:i') }}" value="{{ old('due_at') }}">
                    <small class="form-help-text">Set a deadline for signing (will show as overdue if not signed by this date)</small>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div style="text-align: right; margin-top: 30px;">
                <a href="{{ route('admin.signatures.index') }}" class="btn btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Send for Signature
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// File upload handling
document.getElementById('file').addEventListener('change', function(e) {
    if (this.files.length > 0) {
        const file = this.files[0];
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSelected').style.display = 'flex';
        document.getElementById('fileUploadArea').style.display = 'none';
        
        // Auto-fill title if empty
        if (!document.getElementById('title').value) {
            document.getElementById('title').value = file.name.replace(/\.[^/.]+$/, "");
        }
    }
});

function removeFile() {
    document.getElementById('file').value = '';
    document.getElementById('fileSelected').style.display = 'none';
    document.getElementById('fileUploadArea').style.display = 'block';
}

// Drag and drop handling
const uploadArea = document.getElementById('fileUploadArea');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    uploadArea.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    uploadArea.addEventListener(eventName, () => uploadArea.classList.add('dragging'), false);
});

['dragleave', 'drop'].forEach(eventName => {
    uploadArea.addEventListener(eventName, () => uploadArea.classList.remove('dragging'), false);
});

uploadArea.addEventListener('drop', function(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    document.getElementById('file').files = files;
    document.getElementById('file').dispatchEvent(new Event('change'));
});

// Association type handling
const clients = @json($clients);
const leads = @json($leads);

document.querySelectorAll('input[name="association_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        // Update button styles
        document.querySelectorAll('.association-type-btn').forEach(btn => btn.classList.remove('active'));
        this.closest('.association-type-btn').classList.add('active');
        
        const type = this.value;
        const searchDiv = document.getElementById('associationSearch');
        const select = document.getElementById('association_id');
        
        if (type === '') {
            searchDiv.style.display = 'none';
            select.innerHTML = '<option value="">-- Select --</option>';
        } else {
            searchDiv.style.display = 'block';
            document.getElementById('associationTypeLabel').textContent = type === 'client' ? 'Client' : 'Lead';
            
            // Populate select
            const data = type === 'client' ? clients : leads;
            select.innerHTML = '<option value="">-- Select --</option>';
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = `${item.first_name} ${item.last_name} (${item.email})`;
                select.appendChild(option);
            });
        }
    });
});

// Priority selector
document.querySelectorAll('.priority-option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('.priority-option').forEach(opt => opt.classList.remove('selected'));
        this.classList.add('selected');
        this.querySelector('input[type="radio"]').checked = true;
    });
});

// Initialize none as active
document.getElementById('noneTypeBtn').classList.add('active');
</script>
@endsection

