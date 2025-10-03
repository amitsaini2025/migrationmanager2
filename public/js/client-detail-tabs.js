/**
 * Client Detail Page - Tab Management JavaScript
 * Handles tab switching, URL updates, matter switching, and document filtering
 */

(function($) {
    'use strict';

    // Global variables
    let selectedMatter = '';
    let clientId = '';
    let matterId = '';

    /**
     * Initialize client detail page
     */
    function initClientDetailPage(config) {
        clientId = config.clientId;
        matterId = config.matterId;
        selectedMatter = config.selectedMatter || '';

        // Initialize event handlers
        initTabSwitching();
        initMatterSwitching();
        initBrowserNavigation();
        initPageLoad(config.activeTab);
        
        // Hide grid data by default
        $('.grid_data').hide();
    }

    /**
     * Function to update URL without reloading page
     */
    function updateTabUrl(tabId) {
        let newUrl = '/admin/clients/detail/' + clientId;
        if (matterId && matterId !== '') {
            newUrl += '/' + matterId;
        }
        newUrl += '/' + tabId;
        window.history.pushState({tab: tabId}, '', newUrl);
    }

    /**
     * Function to get current matter reference number from dropdown or checkbox
     */
    function getCurrentMatterRefNo() {
        if ($('.general_matter_checkbox_client_detail:checked').length) {
            return $('.general_matter_checkbox_client_detail:checked').data('clientuniquematterno');
        } else {
            return $('#sel_matter_id_client_detail option:selected').data('clientuniquematterno');
        }
    }

    /**
     * Initialize tab switching functionality
     */
    function initTabSwitching() {
        // Handle main tab switching (both horizontal and vertical tabs)
        $(document).on('click', '.tab-button, .vertical-tab-button, .client-nav-button', function() {
            // Remove active class from all buttons and panes
            $('.tab-button, .vertical-tab-button, .client-nav-button').removeClass('active');
            $('.tab-pane').removeClass('active');

            // Add active class to clicked button
            $(this).addClass('active');

            // Show corresponding pane
            const tabId = $(this).data('tab');
            $(`#${tabId}-tab`).addClass('active');
            
            // Update URL
            updateTabUrl(tabId);

            // Show/hide Activity Feed based on tab
            if (tabId === 'personaldetails') {
                $('#activity-feed').show();
                $('#main-content').css('flex', '1');
            } else {
                handleMatterSpecificTab(tabId);
                $('#activity-feed').hide();
            }

            // Store the active tab in localStorage when a tab is clicked
            localStorage.setItem('activeTab', tabId);
        });
    }

    /**
     * Handle matter-specific tab content filtering
     */
    function handleMatterSpecificTab(tabId) {
        // Get selected matter
        if ($('.general_matter_checkbox_client_detail').is(':checked')) {
            selectedMatter = $('.general_matter_checkbox_client_detail').val();
        } else {
            selectedMatter = $('#sel_matter_id_client_detail').val();
        }

        // Get active subtab
        const activeSubTab = $('.subtab-button.active').data('subtab');

        // Filter Notes by matter
        if (tabId === 'noteterm') {
            filterNotesByMatter(selectedMatter);
        }

        // Filter Visa Documents by matter
        if (tabId === 'visadocuments') {
            filterVisaDocumentsByMatter(selectedMatter);
        }

        // Filter Emails by matter
        if (tabId === 'conversations' && activeSubTab === 'inbox') {
            filterEmailsByMatter(selectedMatter, 'inbox');
        }

        if (tabId === 'conversations' && activeSubTab === 'sent') {
            filterEmailsByMatter(selectedMatter, 'sent');
        }

        // Handle Client Portal tab click specifically
        if (tabId === 'application') {
            showClientMatterApplicationData(selectedMatter);
        }
    }

    /**
     * Filter notes by matter
     */
    function filterNotesByMatter(matterId) {
        if (matterId !== "") {
            $('#noteterm-tab').find('.note-card-redesign').each(function() {
                if ($(this).data('matterid') == matterId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            $('#noteterm-tab').find('.note-card-redesign').each(function() {
                if ($(this).data('matterid') == '') {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    }

    /**
     * Filter visa documents by matter
     */
    function filterVisaDocumentsByMatter(matterId) {
        if (matterId !== "") {
            $('#visadocuments-tab .migdocumnetlist1').find('.drow').each(function() {
                if ($(this).data('matterid') == matterId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            $('#visadocuments-tab .migdocumnetlist1').find('.drow').hide();
        }
    }

    /**
     * Filter emails by matter
     */
    function filterEmailsByMatter(matterId, folder) {
        const selector = folder === 'inbox' ? '#inbox-subtab #email-list' : '#sent-subtab #email-list1';
        
        if (matterId !== "") {
            $(selector).find('.email-card').each(function() {
                if ($(this).data('matterid') == matterId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            $(selector).find('.email-card').hide();
        }
    }

    /**
     * Initialize matter switching functionality
     */
    function initMatterSwitching() {
        // Handle matter dropdown change
        $('#sel_matter_id_client_detail').on('change', function() {
            selectedMatter = $(this).val();
            const uniqueMatterNo = $(this).find('option:selected').data('clientuniquematterno');
            const activeTab = $('.tab-button.active, .vertical-tab-button.active, .client-nav-button.active').data('tab') || 'personaldetails';
            
            // Build new URL with matter and tab
            let baseUrl = '/admin/clients/detail/' + clientId;

            if (selectedMatter !== '' && uniqueMatterNo) {
                // Append the new matter ID and active tab to the base URL
                window.location.href = baseUrl + '/' + uniqueMatterNo + '/' + activeTab;
            } else {
                // If no matter is selected, redirect to the base URL with just the tab
                window.location.href = baseUrl + '/' + activeTab;
            }
        });

        // Handle general matter checkbox change
        $(document).on('change', '.general_matter_checkbox_client_detail', function() {
            if (this.checked) {
                $('#sel_matter_id_client_detail').prop('disabled', true).trigger('change');
                $('#sel_matter_id_client_detail').removeAttr('data-valid').trigger('change');
                selectedMatter = $(this).val();

                const uniqueMatterNo = $(this).data('clientuniquematterno');
                const activeTab = $('.tab-button.active, .vertical-tab-button.active, .client-nav-button.active').data('tab') || 'personaldetails';
                
                // Build new URL
                const baseUrl = '/admin/clients/detail/' + clientId;

                if (selectedMatter !== '' && uniqueMatterNo) {
                    window.location.href = baseUrl + '/' + uniqueMatterNo + '/' + activeTab;
                } else {
                    window.location.href = baseUrl + '/' + activeTab;
                }

                // Filter content by matter
                handleMatterSpecificTab(activeTab);
            } else {
                $('#sel_matter_id_client_detail').prop('disabled', false).trigger('change');
                $('#sel_matter_id_client_detail').attr('data-valid', 'required').trigger('change');
                selectedMatter = "";
            }
        });

        // Prevent multiple checkboxes from being checked
        $(document).on('click', '.general_matter_checkbox_client_detail', function() {
            $('.general_matter_checkbox_client_detail').not(this).prop('checked', false);
        });
    }

    /**
     * Handle browser back/forward buttons
     */
    function initBrowserNavigation() {
        window.addEventListener('popstate', function(event) {
            if (event.state && event.state.tab) {
                const tabId = event.state.tab;
                const $targetButton = $(`.tab-button[data-tab="${tabId}"], .vertical-tab-button[data-tab="${tabId}"], .client-nav-button[data-tab="${tabId}"]`);
                if ($targetButton.length) {
                    // Manually trigger tab change without updating URL again
                    $('.tab-button, .vertical-tab-button, .client-nav-button').removeClass('active');
                    $('.tab-pane').removeClass('active');
                    $targetButton.addClass('active');
                    $(`#${tabId}-tab`).addClass('active');
                    
                    // Handle matter-specific content
                    if (tabId !== 'personaldetails') {
                        handleMatterSpecificTab(tabId);
                    }
                }
            }
        });
    }

    /**
     * Initialize page load - activate tab from URL
     */
    function initPageLoad(activeTabFromUrl) {
        $(document).ready(function() {
            if (activeTabFromUrl && activeTabFromUrl !== 'personaldetails') {
                // Find and click the button for the tab from URL
                const $targetButton = $(`.tab-button[data-tab="${activeTabFromUrl}"], .vertical-tab-button[data-tab="${activeTabFromUrl}"], .client-nav-button[data-tab="${activeTabFromUrl}"]`);
                if ($targetButton.length) {
                    $targetButton.click();
                }
            }
        });
    }

    /**
     * Handle Client Portal tab - Load application data
     */
    function showClientMatterApplicationData(selectedMatter) {
        // Show loading message in the application tab
        $('#application-tab').html('<h4>Please wait, upserting application record...</h4>');
        
        // Step 1: Insert/Update record in applications table
        $.ajax({
            url: '/admin/load-application-insert-update-data',
            type: 'POST',
            data: {
                client_id: clientId,
                client_matter_id: selectedMatter
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(upsertResponse) {
                // Handle success - load application content
                console.log('Application data loaded successfully');
            },
            error: function(xhr, status, error) {
                $('#application-tab').html('<h4 class="text-danger">Error loading application data. Please try again.</h4>');
                console.error('Error:', error);
            }
        });
    }

    /**
     * Initialize matter selection from URL on page load
     */
    function initMatterFromUrl(matterIdInUrl) {
        if (matterIdInUrl === null) {
            // Case 1: No matter ID in URL - select first available option
            let firstNonEmptyOption = $('#sel_matter_id_client_detail option').filter(function() {
                return $(this).val() !== '';
            }).first();

            if (firstNonEmptyOption.length) {
                $('#sel_matter_id_client_detail').val(firstNonEmptyOption.val()).trigger('change');
                selectedMatter = firstNonEmptyOption.val();
            } else {
                // If no dropdown options, check the checkbox
                let firstCheckbox = $('.general_matter_checkbox_client_detail').first();
                if (firstCheckbox.length) {
                    firstCheckbox.prop('checked', true).trigger('change');
                    selectedMatter = firstCheckbox.val();
                } else {
                    selectedMatter = '';
                }
            }
        } else {
            // Case 2: Matter ID exists in URL
            let matchFound = false;

            // Check dropdown for matching option
            $('#sel_matter_id_client_detail option').each(function() {
                const uniqueMatterNo = $(this).data('clientuniquematterno');
                if (uniqueMatterNo === matterIdInUrl) {
                    $('#sel_matter_id_client_detail').val($(this).val()).trigger('change');
                    selectedMatter = $(this).val();
                    matchFound = true;
                }
            });

            // If not found in dropdown, check checkboxes
            if (!matchFound) {
                $('.general_matter_checkbox_client_detail').each(function() {
                    const uniqueMatterNo = $(this).data('clientuniquematterno');
                    if (uniqueMatterNo === matterIdInUrl) {
                        $(this).prop('checked', true).trigger('change');
                        selectedMatter = $(this).val();
                        matchFound = true;
                        return false;
                    }
                });

                // If still not found, select first available option
                if (!matchFound) {
                    let firstNonEmptyOption = $('#sel_matter_id_client_detail option').filter(function() {
                        return $(this).val() !== '';
                    }).first();

                    if (firstNonEmptyOption.length) {
                        $('#sel_matter_id_client_detail').val(firstNonEmptyOption.val()).trigger('change');
                        selectedMatter = firstNonEmptyOption.val();
                    } else {
                        let firstCheckbox = $('.general_matter_checkbox_client_detail').first();
                        if (firstCheckbox.length) {
                            firstCheckbox.prop('checked', true).trigger('change');
                            selectedMatter = firstCheckbox.val();
                        } else {
                            selectedMatter = '';
                        }
                    }
                }
            }
        }
    }

    // Expose initialization function to global scope
    window.ClientDetailTabs = {
        init: initClientDetailPage,
        initMatterFromUrl: initMatterFromUrl,
        filterNotesByMatter: filterNotesByMatter,
        filterVisaDocumentsByMatter: filterVisaDocumentsByMatter,
        filterEmailsByMatter: filterEmailsByMatter
    };

})(jQuery);

