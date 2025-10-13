/**
 * EOI/ROI Management JavaScript
 * Handles CRUD operations for EOI/ROI records and points calculation
 */

(function() {
    'use strict';

    // State management
    const state = {
        clientId: null,
        selectedEoiId: null,
        eoiRecords: [],
        currentPoints: null
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        console.log('[EOI-ROI] DOM ready, initializing...');
        initEoiRoi();
        
        // Also try to load points after a short delay to ensure everything is ready
        setTimeout(function() {
            if (state.clientId) {
                console.log('[EOI-ROI] Delayed points load triggered');
                loadPoints();
            }
        }, 1000);
    });

    function initEoiRoi() {
        // Get client ID from page data
        const clientDetailElement = document.querySelector('[data-client-id]');
        if (clientDetailElement) {
            state.clientId = clientDetailElement.dataset.clientId;
            console.log('[EOI-ROI] Client ID detected:', state.clientId);
        } else {
            console.error('[EOI-ROI] Client ID not found - data-client-id attribute missing');
        }

        // Initialize components
        initializeSelect2();
        initializeDatepickers();
        bindEventHandlers();
        
        // Load EOI records when tab is activated
        $('#eoiroi').on('show', function() {
            console.log('[EOI-ROI] Tab activated, loading records...');
            loadEoiRecords();
        });
        
        // If tab is already visible, load records
        if ($('#eoiroi').is(':visible')) {
            console.log('[EOI-ROI] Tab already visible, loading records...');
            loadEoiRecords();
        }
        
        // Also try to load points immediately if we have a client ID
        if (state.clientId) {
            console.log('[EOI-ROI] Client ID available, loading points...');
            loadPoints();
        }
    }

    function initializeSelect2() {
        $('#eoi-states').select2({
            placeholder: 'Select state(s)',
            allowClear: true
        });
    }

    function initializeDatepickers() {
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });
    }

    function bindEventHandlers() {
        // Add new EOI button
        $('#btn-add-eoi').on('click', showAddForm);

        // Form submission
        $('#eoi-roi-form').on('submit', handleFormSubmit);

        // Cancel button
        $('#btn-cancel-eoi').on('click', hideForm);

        // Delete button
        $('#btn-delete-eoi').on('click', handleDelete);

        // Table row click
        $(document).on('click', '#eoi-roi-tbody tr:not(.no-data-row)', handleRowClick);

        // Password toggle
        $('#toggle-password').on('click', togglePasswordVisibility);

        // Points refresh
        $('#btn-refresh-points').on('click', refreshPoints);

        // Points subclass selector
        $('#points-subclass-selector').on('change', handleSubclassChange);
    }

    // Load EOI records from API
    function loadEoiRecords() {
        if (!state.clientId) {
            console.error('Client ID not found');
            return;
        }

        const url = `/admin/clients/${state.clientId}/eoi-roi`;

        $.ajax({
            url: url,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    state.eoiRecords = response.data;
                    renderEoiTable();
                    
                    // Always load points calculation
                    loadPoints();
                }
            },
            error: function(xhr) {
                console.error('Error loading EOI records:', xhr);
                showNotification('Error loading EOI records', 'error');
            }
        });
    }

    // Render EOI table
    function renderEoiTable() {
        const tbody = $('#eoi-roi-tbody');
        tbody.empty();

        if (state.eoiRecords.length === 0) {
            tbody.html(`
                <tr class="no-data-row">
                    <td colspan="9" class="text-center">
                        <i class="fas fa-info-circle"></i> No EOI/ROI records found. Click "Add New EOI" to get started.
                    </td>
                </tr>
            `);
            return;
        }

        state.eoiRecords.forEach(function(eoi) {
            const row = $('<tr>').attr('data-eoi-id', eoi.id);
            
            row.html(`
                <td>${eoi.eoi_number || 'N/A'}</td>
                <td>${eoi.formatted_subclasses || 'N/A'}</td>
                <td>${eoi.formatted_states || 'N/A'}</td>
                <td>${eoi.occupation || 'N/A'}</td>
                <td><strong>${eoi.points || 0}</strong></td>
                <td>${eoi.submission_date || 'N/A'}</td>
                <td>${eoi.roi || 'N/A'}</td>
                <td><span class="badge-status ${eoi.status}">${capitalizeFirst(eoi.status)}</span></td>
                <td>
                    <button class="btn btn-sm btn-primary" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            `);
            
            tbody.append(row);
        });
    }

    // Show add form
    function showAddForm() {
        resetForm();
        $('#form-title').text('Add New EOI Record');
        $('#btn-delete-eoi').hide();
        $('#eoi-roi-form-section').slideDown();
        state.selectedEoiId = null;
    }

    // Hide form
    function hideForm() {
        $('#eoi-roi-form-section').slideUp();
        resetForm();
    }

    // Reset form
    function resetForm() {
        $('#eoi-roi-form')[0].reset();
        $('#eoi-id').val('');
        $('#eoi-states').val(null).trigger('change');
        $('input[name="eoi_subclasses[]"]').prop('checked', false);
        state.selectedEoiId = null;
    }

    // Handle row click
    function handleRowClick(e) {
        const row = $(e.currentTarget);
        const eoiId = row.data('eoi-id');
        
        loadEoiRecord(eoiId);
    }

    // Load single EOI record
    function loadEoiRecord(eoiId) {
        const url = `/admin/clients/${state.clientId}/eoi-roi/${eoiId}`;

        $.ajax({
            url: url,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    populateForm(response.data);
                    $('#form-title').text('Edit EOI Record');
                    $('#btn-delete-eoi').show();
                    $('#eoi-roi-form-section').slideDown();
                    state.selectedEoiId = eoiId;
                }
            },
            error: function(xhr) {
                console.error('Error loading EOI record:', xhr);
                showNotification('Error loading EOI record', 'error');
            }
        });
    }

    // Populate form with data
    function populateForm(data) {
        $('#eoi-id').val(data.id);
        $('#eoi-number').val(data.eoi_number);
        $('#eoi-occupation').val(data.occupation);
        $('#eoi-points').val(data.points);
        $('#eoi-submission-date').val(data.submission_date);
        $('#eoi-invitation-date').val(data.invitation_date);
        $('#eoi-nomination-date').val(data.nomination_date);
        $('#eoi-roi').val(data.roi);
        $('#eoi-status').val(data.status);

        // Set subclasses
        $('input[name="eoi_subclasses[]"]').prop('checked', false);
        if (data.eoi_subclasses) {
            data.eoi_subclasses.forEach(function(subclass) {
                $(`input[name="eoi_subclasses[]"][value="${subclass}"]`).prop('checked', true);
            });
        }

        // Set states
        if (data.eoi_states) {
            $('#eoi-states').val(data.eoi_states).trigger('change');
        }
    }

    // Handle form submission
    function handleFormSubmit(e) {
        e.preventDefault();

        // Validate at least one subclass is selected
        const checkedSubclasses = $('input[name="eoi_subclasses[]"]:checked');
        if (checkedSubclasses.length === 0) {
            showNotification('Please select at least one subclass', 'error');
            return false;
        }

        // Validate at least one state is selected
        const selectedStates = $('#eoi-states').val();
        if (!selectedStates || selectedStates.length === 0) {
            showNotification('Please select at least one state', 'error');
            return false;
        }

        const formData = new FormData($('#eoi-roi-form')[0]);
        const url = `/admin/clients/${state.clientId}/eoi-roi`;

        // Show loading
        $('#btn-save-eoi').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    hideForm();
                    loadEoiRecords();
                    loadPoints(); // Refresh points
                }
            },
            error: function(xhr) {
                console.error('Error saving EOI record:', xhr);
                let errorMsg = 'Error saving EOI record';
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMsg = errors.join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                showNotification(errorMsg, 'error');
            },
            complete: function() {
                $('#btn-save-eoi').prop('disabled', false).html('<i class="fas fa-save"></i> Save EOI');
            }
        });

        return false;
    }

    // Handle delete
    function handleDelete() {
        if (!state.selectedEoiId) {
            showNotification('No EOI record selected', 'error');
            return;
        }

        if (!confirm('Are you sure you want to delete this EOI record? This action cannot be undone.')) {
            return;
        }

        const url = `/admin/clients/${state.clientId}/eoi-roi/${state.selectedEoiId}`;

        $.ajax({
            url: url,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    hideForm();
                    loadEoiRecords();
                    loadPoints();
                }
            },
            error: function(xhr) {
                console.error('Error deleting EOI record:', xhr);
                showNotification('Error deleting EOI record', 'error');
            }
        });
    }

    // Load points calculation
    function loadPoints(subclass) {
        if (!state.clientId) {
            console.error('[EOI-ROI] Cannot load points - client ID not set');
            return;
        }

        subclass = subclass || $('#points-subclass-selector').val() || '189';

        const url = `/admin/clients/${state.clientId}/eoi-roi/calculate-points?subclass=${subclass || ''}`;
        console.log('[EOI-ROI] Loading points from URL:', url);

        $('#points-summary-content').html('<div class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Calculating points...</div>');

        $.ajax({
            url: url,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('[EOI-ROI] Points calculation response:', response);
                if (response.success) {
                    state.currentPoints = response.data;
                    renderPointsSummary(response.data);
                } else {
                    console.error('[EOI-ROI] Points calculation failed:', response.message);
                    $('#points-summary-content').html('<div class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> Points calculation failed: ' + (response.message || 'Unknown error') + '</div>');
                }
            },
            error: function(xhr) {
                console.error('[EOI-ROI] Error calculating points:', xhr);
                let errorMsg = 'Error calculating points';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.status === 404) {
                    errorMsg = 'Points calculation endpoint not found (404)';
                } else if (xhr.status === 500) {
                    errorMsg = 'Server error during points calculation (500)';
                }
                $('#points-summary-content').html('<div class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> ' + errorMsg + '</div>');
            }
        });
    }

    // Render points summary
    function renderPointsSummary(data) {
        const content = $('#points-summary-content');
        
        // Map category names to display names
        const categoryNames = {
            'age': 'Age',
            'english': 'English Test',
            'education': 'Education',
            'employment': 'Work Experience',
            'bonuses': 'Other Bonuses',
            'partner': 'Partner Skills',
            'nomination': 'Nomination'
        };

        // Build the main table rows
        let tableRows = '';
        for (const [category, info] of Object.entries(data.breakdown)) {
            const displayCategory = categoryNames[category] || category.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
            let details = info.detail || 'Not claimed';
            
            // Special handling for employment (split AU/OS)
            if (category === 'employment' && info.au_years !== undefined && info.os_years !== undefined) {
                details = '';
                if (info.au_years > 0) {
                    details += `Australian: ${info.au_years} years`;
                }
                if (info.os_years > 0) {
                    if (details) details += ', ';
                    details += `Overseas: ${info.os_years} years`;
                }
                if (!details) details = 'Not claimed';
            }
            
            // Special handling for bonuses (split into individual items)
            if (category === 'bonuses' && info.items && info.items.length > 0) {
                details = info.items.join(', ');
            } else if (category === 'bonuses' && info.points === 0) {
                details = 'Not claimed';
            }
            
            tableRows += `
                <tr>
                    <td class="points-category">${displayCategory}</td>
                    <td class="points-details">${details}</td>
                    <td class="points-value">${info.points}</td>
                </tr>
            `;
        }

        // Build warnings section
        let warningsHtml = '';
        if (data.warnings && data.warnings.length > 0) {
            warningsHtml += '<div class="points-warnings-title">Upcoming Changes</div>';
            
            data.warnings.forEach(function(warning) {
                const iconClass = warning.severity === 'high' ? 'fas fa-exclamation-triangle' : 'fas fa-info-circle';
                warningsHtml += `
                    <div class="points-warning severity-${warning.severity}">
                        <i class="${iconClass} points-warning-icon"></i>
                        ${warning.message}
                    </div>
                `;
            });
        }

        const html = `
            <div class="points-summary-layout">
                <div class="points-summary-main">
                    <table class="points-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Details</th>
                                <th>Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${tableRows}
                        </tbody>
                    </table>
                </div>
                
                <div class="points-summary-sidebar">
                    <div class="points-total-display">
                        <div class="points-total-number">${data.total}</div>
                        <div class="points-total-label">Current total</div>
                    </div>
                    
                    ${warningsHtml}
                    
                    <div class="points-info-text">
                        Points calculated based on current client data from Personal Details, Qualifications, Experience, and Test Scores sections.
                    </div>
                </div>
            </div>
        `;

        content.html(html);
    }

    // Refresh points
    function refreshPoints() {
        loadPoints();
    }

    // Handle subclass change
    function handleSubclassChange() {
        const subclass = $('#points-subclass-selector').val();
        loadPoints(subclass);
    }

    // Toggle password visibility
    function togglePasswordVisibility() {
        const input = $('#eoi-password');
        const icon = $('#toggle-password i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    }

    // Show notification
    function showNotification(message, type) {
        type = type || 'info';
        
        // Use toastr if available
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else {
            alert(message);
        }
    }

    // Capitalize first letter
    function capitalizeFirst(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // Export for external access if needed
    window.EoiRoi = {
        reload: function() {
            console.log('[EOI-ROI] Manual reload triggered');
            loadEoiRecords();
        },
        refreshPoints: function() {
            console.log('[EOI-ROI] Manual refresh points triggered');
            loadPoints();
        },
        init: initEoiRoi
    };

})();

