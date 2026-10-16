/**
 * Permission-gated bulk download for personal / visa / nomination document panes.
 * Requires window.ClientDetailConfig.canBulkDownloadClientDocuments and
 * window.ClientDetailConfig.urls.bulkDownloadDocuments.
 */
(function(window, $) {
    'use strict';

    if (!$) {
        return;
    }

    function canBulkDownload() {
        return !!(window.ClientDetailConfig && window.ClientDetailConfig.canBulkDownloadClientDocuments);
    }

    function clientId() {
        var id = window.ClientDetailConfig && window.ClientDetailConfig.clientId;
        return id ? String(id) : '';
    }

    function bulkDownloadUrl() {
        var urls = window.ClientDetailConfig && window.ClientDetailConfig.urls;
        return (urls && urls.bulkDownloadDocuments) ? urls.bulkDownloadDocuments : '/documents/bulk-download';
    }

    function csrfToken() {
        if (window.ClientDetailConfig && window.ClientDetailConfig.csrfToken) {
            return window.ClientDetailConfig.csrfToken;
        }
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function downloadIconHtml() {
        if (typeof window.crmI === 'function') {
            return window.crmI('fas fa-download') + ' ';
        }
        if (typeof window.crmIconLegacy === 'function') {
            return window.crmIconLegacy('fas fa-download') + ' ';
        }
        return '';
    }

    /**
     * Legacy column indexes (0=checklist, 1=file, 2=actions) shift by +1 when
     * the bulk-download checkbox column is present.
     */
    window.clientDocTableColumnIndex = function(legacyIndex) {
        var offset = canBulkDownload() ? 1 : 0;
        return legacyIndex + offset;
    };

    window.enableBulkDownloadCheckboxForRow = function($row, docId) {
        if (!canBulkDownload() || !$row || !$row.length || !docId) {
            return;
        }
        var $cell = $row.find('td.bulk-download-select-cell').first();
        if (!$cell.length) {
            return;
        }
        var id = String(docId);
        $cell.html(
            '<input type="checkbox" class="bulk-download-doc-checkbox" value="' + id +
            '" data-document-id="' + id + '" aria-label="Select for bulk download">'
        );
        syncBulkDownloadButton(paneFor($row));
    };

    window.bulkDownloadButtonHtml = function(extraAttrs) {
        if (!canBulkDownload()) {
            return '';
        }
        var attrs = extraAttrs ? (' ' + extraAttrs) : '';
        return '<button type="button" class="btn btn-info bulk-download-btn" disabled' + attrs + '>' +
            downloadIconHtml() + 'Bulk Download</button>';
    };

    window.bulkDownloadSelectHeaderHtml = function() {
        if (!canBulkDownload()) {
            return '';
        }
        return '<th class="bulk-download-select-col" style="width:36px;"></th>';
    };

    window.bulkDownloadSelectCellHtml = function(docId, hasFile) {
        if (!canBulkDownload()) {
            return '';
        }
        if (!hasFile || !docId) {
            return '<td class="bulk-download-select-cell" style="width:36px;"></td>';
        }
        var id = String(docId);
        return '<td class="bulk-download-select-cell" style="width:36px;vertical-align:middle;">' +
            '<input type="checkbox" class="bulk-download-doc-checkbox" value="' + id +
            '" data-document-id="' + id + '" aria-label="Select for bulk download"></td>';
    };

    function paneFor($el) {
        return $el.closest('.subtab2-pane, .subtab6-pane, .checklist-table-container');
    }

    function syncBulkDownloadButton($pane) {
        if (!$pane || !$pane.length) {
            return;
        }
        var checked = $pane.find('.bulk-download-doc-checkbox:checked').length;
        $pane.find('.bulk-download-btn').prop('disabled', checked < 1);
    }

    function filenameFromDisposition(header) {
        if (!header) {
            return 'documents.zip';
        }
        var match = /filename\*?=(?:UTF-8''|")?([^\";]+)/i.exec(header);
        if (match && match[1]) {
            try {
                return decodeURIComponent(match[1].replace(/"/g, '').trim());
            } catch (e) {
                return match[1].replace(/"/g, '').trim();
            }
        }
        return 'documents.zip';
    }

    function showBulkDownloadError(message) {
        if (typeof window.showClientDocFeedback === 'function') {
            window.showClientDocFeedback('error', message, 'Bulk Download');
            return;
        }
        if (window.jQuery && typeof window.jQuery.toast === 'function') {
            window.jQuery.toast({ heading: 'Bulk Download', text: message, icon: 'error' });
            return;
        }
        window.alert(message);
    }

    async function runBulkDownload($btn) {
        var $pane = paneFor($btn);
        var ids = [];
        $pane.find('.bulk-download-doc-checkbox:checked').each(function() {
            var id = parseInt($(this).val(), 10);
            if (id > 0) {
                ids.push(id);
            }
        });

        if (ids.length < 1) {
            showBulkDownloadError('Select at least one document to download.');
            return;
        }

        var cid = clientId();
        if (!cid) {
            showBulkDownloadError('Client context is missing.');
            return;
        }

        $btn.prop('disabled', true);
        var originalHtml = $btn.html();
        $btn.html(downloadIconHtml() + 'Preparing…');

        var formData = new FormData();
        formData.append('client_id', cid);
        ids.forEach(function(id) {
            formData.append('document_ids[]', String(id));
        });

        try {
            var response = await fetch(bulkDownloadUrl(), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/zip, application/octet-stream, application/json'
                },
                body: formData,
                credentials: 'same-origin'
            });

            if (!response.ok) {
                var message = 'Bulk download failed.';
                try {
                    var data = await response.json();
                    if (data && data.message) {
                        message = data.message;
                    }
                } catch (e) {
                    // ignore non-JSON error bodies
                }
                showBulkDownloadError(message);
                return;
            }

            var blob = await response.blob();
            var filename = filenameFromDisposition(response.headers.get('Content-Disposition'));
            var objectUrl = window.URL.createObjectURL(blob);
            var link = document.createElement('a');
            link.href = objectUrl;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(objectUrl);
        } catch (err) {
            showBulkDownloadError('Bulk download failed. Please try again.');
        } finally {
            $btn.html(originalHtml);
            syncBulkDownloadButton($pane);
        }
    }

    function bindBulkDownloadHandlers() {
        $(document)
            .off('change.bulkDownloadDocs', '.bulk-download-doc-checkbox')
            .on('change.bulkDownloadDocs', '.bulk-download-doc-checkbox', function() {
                syncBulkDownloadButton(paneFor($(this)));
            });

        $(document)
            .off('click.bulkDownloadDocs', '.bulk-download-btn')
            .on('click.bulkDownloadDocs', '.bulk-download-btn', function(e) {
                e.preventDefault();
                runBulkDownload($(this));
            });
    }

    window.bindBulkDownloadHandlers = bindBulkDownloadHandlers;
    window.syncBulkDownloadButtonState = syncBulkDownloadButton;

    $(function() {
        if (canBulkDownload()) {
            bindBulkDownloadHandlers();
        }
    });
})(window, window.jQuery);
