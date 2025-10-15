@extends('layouts.admin_client_detail_dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/address-autocomplete.css') }}">
    <link rel="stylesheet" href="{{asset('css/client-forms.css')}}">
    <link rel="stylesheet" href="{{asset('css/clients/edit-client-components.css')}}">
    <link rel="stylesheet" href="{{asset('css/leads/lead-form.css')}}">
@endpush

@section('content')
    <div class="crm-container">
        <div class="main-content">

            <!-- Display General Errors -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Mobile Sidebar Toggle -->
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Sidebar Navigation -->
            <div class="sidebar-navigation" id="sidebarNav">
                <div class="nav-header">
                    <h3><i class="fas fa-user-plus"></i> Create New Lead</h3>
                </div>
                <nav class="nav-menu">
                    <button class="nav-item active" onclick="scrollToSection('personalSection')">
                        <i class="fas fa-user-circle"></i>
                        <span>Personal</span>
                    </button>
                    <button class="nav-item" onclick="scrollToSection('visaPassportSection')">
                        <i class="fas fa-id-card"></i>
                        <span>Visa, Passport & Citizenship</span>
                    </button>
                    <button class="nav-item" onclick="scrollToSection('addressTravelSection')">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Address & Travel</span>
                    </button>
                    <button class="nav-item" onclick="scrollToSection('skillsEducationSection')">
                        <i class="fas fa-briefcase"></i>
                        <span>Skills & Education</span>
                    </button>
                    <button class="nav-item" onclick="scrollToSection('otherInformationSection')">
                        <i class="fas fa-info-circle"></i>
                        <span>Other Information</span>
                    </button>
                    <button class="nav-item" onclick="scrollToSection('familySection')">
                        <i class="fas fa-users"></i>
                        <span>Family Information</span>
                    </button>
                </nav>
                
                <!-- Actions in Sidebar -->
                <div class="sidebar-actions">
                    <button class="nav-item back-btn" onclick="window.history.back()">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </button>
                    <button type="submit" form="createLeadForm" class="nav-item save-btn">
                        <i class="fas fa-save"></i>
                        <span>Save Lead</span>
                    </button>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="main-content-area">
                <form id="createLeadForm" action="{{ route('admin.leads.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- ==================== PERSONAL SECTION ==================== --}}
                    <section id="personalSection" class="content-section">
                        <!-- Basic Information -->
                        <section class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-user-circle"></i> Basic Information</h3>
                            </div>
                            
                            <div class="content-grid">
                                <div class="form-group">
                                    <label for="firstName">First Name <span class="text-danger">*</span></label>
                                    <input type="text" id="firstName" name="first_name" value="{{ old('first_name') }}" required>
                                    @error('first_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="lastName">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" id="lastName" name="last_name" value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="dob">Date of Birth</label>
                                    <input type="text" id="dob" name="dob" value="{{ old('dob') }}" class="date-picker" placeholder="dd/mm/yyyy">
                                    @error('dob')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="age">Age</label>
                                    <input type="text" id="age" name="age" value="{{ old('age') }}" readonly>
                                </div>

                                <div class="form-group">
                                    <label>Gender <span class="text-danger">*</span></label>
                                    <div class="radio-group">
                                        <label><input type="radio" name="gender" value="Male" {{ old('gender') == 'Male' ? 'checked' : '' }} required> Male</label>
                                        <label><input type="radio" name="gender" value="Female" {{ old('gender') == 'Female' ? 'checked' : '' }}> Female</label>
                                        <label><input type="radio" name="gender" value="Other" {{ old('gender') == 'Other' ? 'checked' : '' }}> Other</label>
                                    </div>
                                    @error('gender')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="maritalStatus">Marital Status</label>
                                    <select id="maritalStatus" name="marital_status">
                                        <option value="">Select Marital Status</option>
                                        <option value="Single" {{ old('marital_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                        <option value="Married" {{ old('marital_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                        <option value="De Facto" {{ old('marital_status') == 'De Facto' ? 'selected' : '' }}>De Facto</option>
                                        <option value="Divorced" {{ old('marital_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                        <option value="Widowed" {{ old('marital_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                        <option value="Separated" {{ old('marital_status') == 'Separated' ? 'selected' : '' }}>Separated</option>
                                    </select>
                                </div>
                            </div>
                        </section>

                        <!-- Phone Numbers -->
                        <section class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-phone"></i> Phone Numbers</h3>
                            </div>
                            
                            <div id="phoneNumbersContainer">
                                <!-- Initially empty; will be populated by JavaScript on page load -->
                            </div>
                            @error('phone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            
                            <button type="button" class="add-item-btn" onclick="addPhoneNumber()">
                                <i class="fas fa-plus-circle"></i> Add Phone Number
                            </button>
                        </section>

                        <!-- Email Addresses -->
                        <section class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-envelope"></i> Email Addresses</h3>
                            </div>
                            
                            <div id="emailAddressesContainer">
                                <!-- Initially empty; will be populated by JavaScript on page load -->
                            </div>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            
                            <button type="button" class="add-item-btn" onclick="addEmailAddress()">
                                <i class="fas fa-plus-circle"></i> Add Email Address
                            </button>
                        </section>
                    </section>

                    {{-- ==================== VISA, PASSPORT & CITIZENSHIP SECTION ==================== --}}
                    <section id="visaPassportSection" class="content-section">
                        <!-- Passports -->
                        <section class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-passport"></i> Passports</h3>
                            </div>
                            
                            <div id="passportsContainer">
                                <!-- Initially empty; will be populated by JavaScript on page load -->
                            </div>
                            
                            <button type="button" class="add-item-btn" onclick="addPassport()">
                                <i class="fas fa-plus-circle"></i> Add Passport
                            </button>
                        </section>

                        <!-- Visas -->
                        <section class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-id-card"></i> Visas</h3>
                            </div>
                            
                            <div id="visasContainer">
                                <!-- Initially empty; will be populated by JavaScript on page load -->
                            </div>
                            
                            <button type="button" class="add-item-btn" onclick="addVisa()">
                                <i class="fas fa-plus-circle"></i> Add Visa
                            </button>
                        </section>
                    </section>

                    {{-- ==================== ADDRESS & TRAVEL SECTION ==================== --}}
                    <section id="addressTravelSection" class="content-section">
                        <!-- Addresses -->
                        <section class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-map-marker-alt"></i> Addresses</h3>
                            </div>
                            
                            <div id="addressesContainer">
                                <!-- Initially empty; will be populated by JavaScript on page load -->
                            </div>
                            
                            <button type="button" class="add-item-btn" onclick="addAddress()">
                                <i class="fas fa-plus-circle"></i> Add Address
                            </button>
                        </section>

                        <!-- Travel History -->
                        <section class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-plane"></i> Travel History</h3>
                            </div>
                            
                            <div id="travelContainer">
                                <!-- Initially empty; will be populated by JavaScript on page load -->
                            </div>
                            
                            <button type="button" class="add-item-btn" onclick="addTravel()">
                                <i class="fas fa-plus-circle"></i> Add Travel Entry
                            </button>
                        </section>
                    </section>

                    {{-- ==================== SKILLS & EDUCATION SECTION ==================== --}}
                    <section id="skillsEducationSection" class="content-section">
                        <!-- Test Scores -->
                        <section class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-graduation-cap"></i> Test Scores</h3>
                            </div>
                            
                            <div id="testScoresContainer">
                                <!-- Initially empty; will be populated by JavaScript on page load -->
                            </div>
                            
                            <button type="button" class="add-item-btn" onclick="addTestScore()">
                                <i class="fas fa-plus-circle"></i> Add Test Score
                            </button>
                        </section>

                        <!-- Qualifications -->
                        <section class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-certificate"></i> Qualifications</h3>
                            </div>
                            
                            <div id="qualificationsContainer">
                                <!-- Initially empty; will be populated by JavaScript on page load -->
                            </div>
                            
                            <button type="button" class="add-item-btn" onclick="addQualification()">
                                <i class="fas fa-plus-circle"></i> Add Qualification
                            </button>
                        </section>

                        <!-- Work Experience -->
                        <section class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-briefcase"></i> Work Experience</h3>
                            </div>
                            
                            <div id="experienceContainer">
                                <!-- Initially empty; will be populated by JavaScript on page load -->
                            </div>
                            
                            <button type="button" class="add-item-btn" onclick="addExperience()">
                                <i class="fas fa-plus-circle"></i> Add Work Experience
                            </button>
                        </section>

                        <!-- Occupations -->
                        <section class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-user-tie"></i> Nominated Occupations</h3>
                            </div>
                            
                            <div id="occupationsContainer">
                                <!-- Initially empty; will be populated by JavaScript on page load -->
                            </div>
                            
                            <button type="button" class="add-item-btn" onclick="addOccupation()">
                                <i class="fas fa-plus-circle"></i> Add Occupation
                            </button>
                        </section>
                    </section>

                    {{-- ==================== OTHER INFORMATION SECTION ==================== --}}
                    <section id="otherInformationSection" class="content-section">
                        <section class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-info-circle"></i> Lead Information</h3>
                            </div>
                            
                            <div class="content-grid">
                                <div class="form-group">
                                    <label for="leadSource">Lead Source</label>
                                    <select id="leadSource" name="lead_source">
                                        <option value="">Select Source</option>
                                        <option value="Website">Website</option>
                                        <option value="Referral">Referral</option>
                                        <option value="Social Media">Social Media</option>
                                        <option value="Advertisement">Advertisement</option>
                                        <option value="Walk-in">Walk-in</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="leadQuality">Lead Quality</label>
                                    <select id="leadQuality" name="lead_quality">
                                        <option value="">Select Quality</option>
                                        <option value="hot">Hot</option>
                                        <option value="warm">Warm</option>
                                        <option value="cold">Cold</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="service">Service Interested</label>
                                    <input type="text" id="service" name="service" value="{{ old('service') }}">
                                </div>

                                <div class="form-group">
                                    <label for="assignTo">Assign To</label>
                                    <select id="assignTo" name="assign_to">
                                        <option value="">Select Agent</option>
                                        <!-- Populate from agents -->
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="commentsNote">Notes / Comments</label>
                                <textarea id="commentsNote" name="comments_note" rows="5">{{ old('comments_note') }}</textarea>
                            </div>
                        </section>
                    </section>

                    {{-- ==================== FAMILY INFORMATION SECTION ==================== --}}
                    <section id="familySection" class="content-section">
                        <!-- Family Members -->
                        <section class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-users"></i> Family Members</h3>
                            </div>
                            
                            <div id="familyMembersContainer">
                                <!-- Initially empty; will be populated by JavaScript on page load -->
                            </div>
                            
                            <button type="button" class="add-item-btn" onclick="addFamilyMember()">
                                <i class="fas fa-plus-circle"></i> Add Family Member
                            </button>
                        </section>
                    </section>

                    <!-- Form Actions (Hidden for floating button) -->
                    <div class="form-actions" style="margin-top: 30px; padding: 20px; background: white; border-radius: 8px; display: flex; gap: 15px; justify-content: flex-end; visibility: hidden;">
                        <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="hiddenSubmitBtn">
                            <i class="fas fa-save"></i> Save Lead
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Floating Save Button -->
    <div class="floating-save-container">
        <div class="floating-save-buttons">
            <button type="button" class="btn btn-floating btn-cancel" onclick="window.history.back()">
                <i class="fas fa-times"></i>
                <span>Cancel</span>
            </button>
            <button type="button" class="btn btn-floating btn-save" id="floatingSaveBtn">
                <i class="fas fa-save"></i>
                <span>Save Lead</span>
            </button>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/leads/lead-form-navigation.js') }}"></script>
    <script src="{{ asset('js/leads/lead-form.js') }}"></script>
    <script src="{{ asset('js/address-autocomplete.js') }}"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const floatingSaveBtn = document.getElementById('floatingSaveBtn');
            const hiddenSubmitBtn = document.getElementById('hiddenSubmitBtn');
            const form = document.getElementById('createLeadForm');
            const floatingContainer = document.querySelector('.floating-save-container');
            
            // Handle floating save button click
            floatingSaveBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Show loading state
                floatingSaveBtn.classList.add('loading');
                floatingSaveBtn.innerHTML = '<i class="fas fa-spinner"></i><span>Saving...</span>';
                
                // Trigger form submission
                hiddenSubmitBtn.click();
            });
            
            // Add scroll-based visibility control
            let lastScrollTop = 0;
            let ticking = false;
            
            function updateFloatingButton() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                const windowHeight = window.innerHeight;
                const documentHeight = document.documentElement.scrollHeight;
                
                // Show button when not at the very top or bottom
                if (scrollTop > 100 && scrollTop < documentHeight - windowHeight - 100) {
                    floatingContainer.classList.remove('hidden');
                    floatingContainer.classList.add('visible');
                } else if (scrollTop <= 100) {
                    floatingContainer.classList.add('hidden');
                    floatingContainer.classList.remove('visible');
                }
                
                lastScrollTop = scrollTop;
                ticking = false;
            }
            
            function requestTick() {
                if (!ticking) {
                    requestAnimationFrame(updateFloatingButton);
                    ticking = true;
                }
            }
            
            window.addEventListener('scroll', requestTick);
            
            // Initialize button state
            updateFloatingButton();
            
            // Add keyboard shortcut for save (Ctrl+S)
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                    e.preventDefault();
                    floatingSaveBtn.click();
                }
            });
            
            // Add visual feedback for form changes
            const formInputs = form.querySelectorAll('input, select, textarea');
            let formChanged = false;
            
            formInputs.forEach(input => {
                input.addEventListener('change', function() {
                    formChanged = true;
                    updateSaveButtonState();
                });
                
                input.addEventListener('input', function() {
                    formChanged = true;
                    updateSaveButtonState();
                });
            });
            
            function updateSaveButtonState() {
                if (formChanged) {
                    floatingSaveBtn.style.background = 'linear-gradient(135deg, #dc3545 0%, #fd7e14 100%)';
                    floatingSaveBtn.querySelector('span').textContent = 'Save Changes';
                } else {
                    floatingSaveBtn.style.background = 'linear-gradient(135deg, #6777ef 0%, #47c363 100%)';
                    floatingSaveBtn.querySelector('span').textContent = 'Save Lead';
                }
            }
        });
    </script>
    
    <script>
    // Initialize form with at least one field in each required sections
    document.addEventListener('DOMContentLoaded', function() {
        // Add initial phone and email fields
        const phoneContainer = document.getElementById('phoneNumbersContainer');
        const emailContainer = document.getElementById('emailAddressesContainer');
        
        if (phoneContainer && phoneContainer.children.length === 0) {
            addPhoneNumber();
        }
        if (emailContainer && emailContainer.children.length === 0) {
            addEmailAddress();
        }
        
        // DOB to Age calculation
        const dobField = document.getElementById('dob');
        if (dobField) {
            dobField.addEventListener('change', function() {
                const dob = this.value;
                if (dob) {
                    const parts = dob.split('/');
                    if (parts.length === 3) {
                        const birthDate = new Date(parts[2], parts[1] - 1, parts[0]);
                        const today = new Date();
                        let age = today.getFullYear() - birthDate.getFullYear();
                        const monthDiff = today.getMonth() - birthDate.getMonth();
                        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                            age--;
                        }
                        document.getElementById('age').value = age;
                    }
                }
            });
        }
    });
    
    </script>
@endpush

