// TinyMCE Configuration for Email Modals
var tinymceEmailConfig = {
    license_key: 'gpl',
    height: 300,
    menubar: false,
    plugins: ['lists', 'link', 'autolink'],
    toolbar: 'bold italic underline strikethrough | forecolor | bullist numlist | link',
    convert_urls: false,
    extended_valid_elements: 'table[border|cellpadding|cellspacing|width|style|class|align],thead,tbody,tfoot,tr[class|style],td[class|style|colspan|rowspan|align|valign|width],th[class|style|colspan|rowspan|align|valign|width],colgroup,col[span|width],hr[style|width]',
    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }',
    branding: false,
    promotion: false,
    color_map: [
        "000000", "Black", "333333", "Dark Gray", "666666", "Medium Gray",
        "999999", "Light Gray", "CCCCCC", "Very Light Gray", "E0E0E0", "Pale Gray",
        "F5F5F5", "Off White", "FFFFFF", "White", "DC2626", "Red",
        "EA580C", "Orange", "D97706", "Amber", "059669", "Green",
        "0891B2", "Cyan", "2563EB", "Blue", "7C3AED", "Purple",
        "DB2777", "Pink", "EF4444", "Light Red", "F97316", "Light Orange",
        "F59E0B", "Light Amber", "10B981", "Light Green", "06B6D4", "Light Cyan",
        "3B82F6", "Light Blue", "8B5CF6", "Light Purple", "EC4899", "Light Pink"
    ],
    setup: function(editor) {
        editor.on('change', function() {
            editor.save();
        });
    }
};

// Initialize TinyMCE for all email modals
function initTinyMCEForModals() {
    if (typeof tinymce === 'undefined') {
        var inShownModal = $('#compose_email_message, #sendmsg_message, #matter_email_message, #uploadmail_message').closest('.modal.show').length;
        if (inShownModal && typeof window.ensureTinyMCELoaded === 'function') {
            window.ensureTinyMCELoaded().then(initTinyMCEForModals);
        }
        return;
    }
    // Compose Email Modal
    if ($('#compose_email_message').length && !tinymce.get('compose_email_message')) {
        tinymce.init({
            ...tinymceEmailConfig,
            selector: '#compose_email_message',
            init_instance_callback: function(editor) {
                // Handle modal show event
                $('#emailmodal').on('shown.bs.modal', function() {
                    editor.focus();
                });
            }
        });
    }
    
    // Send Message Modal
    if ($('#sendmsg_message').length && !tinymce.get('sendmsg_message')) {
        tinymce.init({
            ...tinymceEmailConfig,
            selector: '#sendmsg_message',
            init_instance_callback: function(editor) {
                $('#sendmsgmodal').on('shown.bs.modal', function() {
                    editor.focus();
                });
            }
        });
    }
    
    // Application Email Modal
    if ($('#matter_email_message').length && !tinymce.get('matter_email_message')) {
        tinymce.init({
            ...tinymceEmailConfig,
            selector: '#matter_email_message',
            init_instance_callback: function(editor) {
                $('#matteremailmodal').on('shown.bs.modal', function() {
                    editor.focus();
                });
            }
        });
    }
    
    // Upload Mail Modal
    if ($('#uploadmail_message').length && !tinymce.get('uploadmail_message')) {
        tinymce.init({
            ...tinymceEmailConfig,
            selector: '#uploadmail_message',
            init_instance_callback: function(editor) {
                $('#uploadmail').on('shown.bs.modal', function() {
                    editor.focus();
                });
            }
        });
    }
}

window.initTinyMCEForModals = initTinyMCEForModals;

// Helper functions to save TinyMCE content before form validation
window.saveComposeEmail = function() {
    if (typeof tinymce !== 'undefined' && tinymce.get('compose_email_message')) {
        tinymce.get('compose_email_message').save();
    }
    customValidate('sendmail');
};

window.saveSendMessage = function() {
    if (typeof tinymce !== 'undefined' && tinymce.get('sendmsg_message')) {
        tinymce.get('sendmsg_message').save();
    }
    customValidate('sendmsg');
};

window.saveApplicationEmail = function() {
    if (typeof tinymce !== 'undefined' && tinymce.get('matter_email_message')) {
        tinymce.get('matter_email_message').save();
    }
    customValidate('appkicationsendmail');
};

window.saveUploadMail = function() {
    if (typeof tinymce !== 'undefined' && tinymce.get('uploadmail_message')) {
        tinymce.get('uploadmail_message').save();
    }
    customValidate('uploadmail');
};

// Helper function to set TinyMCE content (can be called from anywhere)
window.setTinyMCEContent = function(editorId, content) {
    if (typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
        tinymce.get(editorId).setContent(content || '');
    } else {
        $('#' + editorId).val(content || '');
        // Try to initialize if not already initialized
        setTimeout(function() {
            initTinyMCEForModals();
            if (typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
                tinymce.get(editorId).setContent(content || '');
            }
        }, 200);
    }
};

// Initialize TinyMCE when DOM is ready
$(document).ready(function() {
    // Call getallactivities after page load if pending (from receipt save)
    var pendingClientId = localStorage.getItem('pendingGetActivities');
    if (pendingClientId && typeof getallactivities === 'function') {
        // Wait for page to fully load and account tab to be active
        setTimeout(function() {
            var activeTab = localStorage.getItem('activeTab');
            
            if (activeTab === 'accounts' || activeTab === 'account') {
                getallactivities(pendingClientId);
                localStorage.removeItem('pendingGetActivities');
            } else {
                // Retry after tab activation
                setTimeout(function() {
                    if (typeof getallactivities === 'function') {
                        getallactivities(pendingClientId);
                        localStorage.removeItem('pendingGetActivities');
                    }
                }, 1000);
            }
        }, 500);
    }
    
    initTinyMCEForModals();
    
    // Re-initialize when modals are shown (in case they're dynamically loaded)
    $('#emailmodal, #sendmsgmodal, #matteremailmodal, #uploadmail').on('shown.bs.modal', function() {
        setTimeout(function() {
            initTinyMCEForModals();
        }, 100);
    });
    
    // When compose modal opens: wait for CRM template/checklist lists, then apply matter defaults.
    // Checklist attachment checkboxes stay unchecked until the user selects them.
    $('#emailmodal').on('shown.bs.modal', function() {
        var runComposeShown = function() {
        var $templateSelect = $('#emailmodal select.selecttemplate');
        if (typeof window.initComposeEmailTemplateSelect === 'function') {
            if (!$('#compose_client_matter_id').val() && typeof window.restoreComposeEmailTemplateCrmOptions === 'function') {
                window.restoreComposeEmailTemplateCrmOptions($templateSelect);
            }
            window.initComposeEmailTemplateSelect($templateSelect);
        }
        var clientMatterId = $('#compose_client_matter_id').val();
        if (!clientMatterId || !window.ClientDetailConfig || !window.ClientDetailConfig.urls || !window.ClientDetailConfig.urls.getComposeDefaults) {
            window.composeChecklistFilterIds = null;
            if ($('#mychecklist-datatable').length && $.fn.DataTable && $.fn.DataTable.isDataTable('#mychecklist-datatable')) {
                $('#mychecklist-datatable').DataTable().draw();
            }
            $('#emailmodal').removeData('composeMacroValues').removeData('pdfUrlForSign').removeData('fromSignatureSend');
            $('#compose_signing_url').val('');
            return;
        }
        $.get(window.ClientDetailConfig.urls.getComposeDefaults, { client_matter_id: clientMatterId })
            .done(function(res) {
                var $checklistCbs = $('#emailmodal .checklistfile-cb');
                if (res.macro_values) {
                    var macroVals = res.macro_values;
                    var pdfUrl = ($('#emailmodal').data('pdfUrlForSign') || $('#compose_signing_url').val() || macroVals.PDF_url_for_sign || '').trim();
                    if (pdfUrl) {
                        macroVals = Object.assign({}, macroVals, { PDF_url_for_sign: pdfUrl });
                        $('#compose_signing_url').val(pdfUrl);
                        $('#emailmodal').data('pdfUrlForSign', pdfUrl);
                    }
                    $('#emailmodal').data('composeMacroValues', macroVals);
                } else {
                    $('#emailmodal').removeData('composeMacroValues');
                }
                if (res.matter_templates !== undefined && $templateSelect.length) {
                    // Replace dropdown with matter-specific options only: First Email first, then Matter Other Email Templates
                    $templateSelect.empty().append($('<option value="">Select</option>'));
                    (res.matter_templates || []).forEach(function(t) {
                        $templateSelect.append($('<option></option>').attr('value', t.id).text(t.name || 'Template'));
                    });
                    if (typeof window.syncComposeEmailTemplateSelectFromDom === 'function') {
                        window.syncComposeEmailTemplateSelectFromDom($templateSelect);
                    }
                    // Reply/Forward from client email tab sets preserveReplyForwardBody so quoted content is not replaced by a template
                    if (!$('#emailmodal').data('preserveReplyForwardBody')) {
                        var fromSignature = $('#emailmodal').data('fromSignatureSend');
                        var toSelect = res.template ? res.template.id : (res.matter_templates && res.matter_templates[0] ? res.matter_templates[0].id : null);
                        if (toSelect) {
                            $templateSelect.val(toSelect).trigger('change');
                            if (fromSignature) $('#emailmodal').removeData('fromSignatureSend');
                        }
                    } else {
                        // Keep body/subject from reply/forward; reset template UI without loading a template (empty val skips AJAX in .selecttemplate handler).
                        $templateSelect.val('').trigger('change');
                    }
                }
                // Filter checklist table by matter using DataTables API
                window.composeChecklistFilterIds = (res.checklist_ids && res.checklist_ids.length) ? res.checklist_ids : [];
                if ($('#mychecklist-datatable').length && $.fn.DataTable && $.fn.DataTable.isDataTable('#mychecklist-datatable')) {
                    $('#mychecklist-datatable').DataTable().draw();
                }
                $checklistCbs.prop('checked', false);
            })
            .fail(function() {
                window.composeChecklistFilterIds = null;
                if ($('#mychecklist-datatable').length && $.fn.DataTable && $.fn.DataTable.isDataTable('#mychecklist-datatable')) {
                    $('#mychecklist-datatable').DataTable().draw();
                }
            });
        };
        if (typeof window.ensureComposeOptionListsLoaded === 'function') {
            $.when(window.ensureComposeOptionListsLoaded()).always(runComposeShown);
        } else {
            runComposeShown();
        }
    });

    $('#emailmodal').on('hidden.bs.modal', function() {
        $('#compose_signing_url').val('');
        $(this).removeData('pdfUrlForSign').removeData('fromSignatureSend');
    });
});
