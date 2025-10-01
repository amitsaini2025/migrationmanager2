<!-- Email Handling Interface -->
<div class="email-interface-container">
    <!-- Top Control Bar -->
    <div class="email-control-bar">
        <div class="control-section upload-section">
            <label for="emailFileInput" class="file-input-label">Choose files</label>
            <input type="file" id="emailFileInput" class="file-input" accept=".msg" multiple style="display: none;">
            <span class="file-status" id="fileStatus">No file chosen</span>
            <button class="upload-btn" id="uploadBtn" disabled>Upload</button>
        </div>
        
        <div class="control-section search-section">
            <label for="emailSearchInput">Search:</label>
            <input type="text" id="emailSearchInput" class="search-input" placeholder="Search emails...">
        </div>
        
        <div class="control-section filter-section">
            <label for="labelFilter">Label:</label>
            <select id="labelFilter" class="filter-select">
                <option value="">All labels</option>
                <option value="inbox">Inbox</option>
                <option value="sent">Sent</option>
            </select>
            
            <label for="sortFilter">Sort:</label>
            <select id="sortFilter" class="filter-select">
                <option value="date">Date</option>
                <option value="subject">Subject</option>
                <option value="sender">Sender</option>
            </select>
        </div>
        
        <div class="control-section action-section">
            <button class="apply-btn" id="applyBtn">Apply</button>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="email-main-content">
        <!-- Left Email List Pane -->
        <div class="email-list-pane">
            <div class="email-list-header">
                <span class="results-count" id="resultsCount">0 results</span>
                <div class="pagination-controls">
                    <button class="pagination-btn" id="prevBtn">Prev</button>
                    <span class="page-info" id="pageInfo">1/1</span>
                    <button class="pagination-btn" id="nextBtn">Next</button>
                </div>
            </div>
            
            <div class="email-list" id="emailList">
                <!-- Email items will be populated here by JavaScript -->
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <div class="empty-state-text">
                        <h3>No emails found</h3>
                        <p>Upload .msg files to get started with email management.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Content Viewing Pane -->
        <div class="email-content-pane">
            <div class="email-content-placeholder" id="emailContentPlaceholder">
                <div class="placeholder-content">
                    <i class="fas fa-envelope-open"></i>
                    <h3>Select an email to view its contents</h3>
                </div>
            </div>
            
            <div class="email-content-view" id="emailContentView" style="display: none;">
                <!-- Email content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Include necessary CSS and JavaScript -->
<link rel="stylesheet" href="{{ asset('css/email-handling.css') }}">
{{-- @vite(['resources/js/app.js']) --}}  {{-- Commented out temporarily to fix 404 error --}}

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Email handling interface loaded');
    
    // Debug: Check if elements exist
    const fileInput = document.getElementById('emailFileInput');
    const uploadBtn = document.getElementById('uploadBtn');
    const fileStatus = document.getElementById('fileStatus');
    
    console.log('File input found:', !!fileInput);
    console.log('Upload button found:', !!uploadBtn);
    console.log('File status found:', !!fileStatus);
    
    // Debug: Check if modules are available
    console.log('initializeUpload available:', typeof window.initializeUpload);
    console.log('initializeSearch available:', typeof window.initializeSearch);
    console.log('loadEmails available:', typeof window.loadEmails);
    
    // Initialize modules if they exist
    if (typeof window.initializeUpload === 'function') {
        console.log('Initializing upload module...');
        window.initializeUpload();
    } else {
        console.error('Upload module not available!');
    }
    
    if (typeof window.initializeSearch === 'function') {
        console.log('Initializing search module...');
        window.initializeSearch();
    } else {
        console.error('Search module not available!');
    }
    
    // Load emails on page load
    if (typeof window.loadEmails === 'function') {
        console.log('Loading emails...');
        window.loadEmails();
    } else {
        console.error('Load emails function not available!');
    }
    
    // Debug: Add manual event listener for file input
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            console.log('File input change detected');
            console.log('Files selected:', this.files.length);
            if (this.files.length > 0) {
                console.log('File names:', Array.from(this.files).map(f => f.name));
            }
        });
    }
});
</script> 