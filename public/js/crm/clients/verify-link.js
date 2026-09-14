/**
 * Isolated Verify Link sender — does not touch appointment / email / SMS handlers.
 */
(function () {
    'use strict';

    var MODAL_ID = 'verifyLinkChannelModal';
    var SEND_BTN_ID = 'verifyLinkChannelSendBtn';
    var pendingButton = null;
    var sending = false;

    function toast(type, message) {
        if (typeof iziToast !== 'undefined') {
            iziToast[type]({ message: message, position: 'topRight' });
            return;
        }
        window.alert(message);
    }

    function selectedChannel() {
        var checked = document.querySelector('#' + MODAL_ID + ' input[name="verify_link_channel"]:checked');
        return checked ? checked.value : '';
    }

    function displayValue(value) {
        var text = (value || '').toString().trim();
        return text !== '' ? text : 'not on file';
    }

    function updateChannelLabels() {
        var config = window.ClientDetailConfig || {};
        var emailLabel = document.getElementById('verifyLinkChannelEmailLabel');
        var phoneLabel = document.getElementById('verifyLinkChannelSmsLabel');
        if (emailLabel) {
            emailLabel.textContent = 'Primary Email Address - ' + displayValue(config.primaryEmail);
        }
        if (phoneLabel) {
            phoneLabel.textContent = 'Primary Phone no - ' + displayValue(config.primaryPhone);
        }
    }

    function showModal() {
        var modal = document.getElementById(MODAL_ID);
        if (!modal) {
            return;
        }

        if (typeof window.jQuery !== 'undefined' && window.jQuery.fn.modal) {
            window.jQuery(modal).modal('show');
            return;
        }

        if (typeof window.bootstrap !== 'undefined' && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modal).show();
            return;
        }

        modal.classList.add('show');
        modal.style.display = 'block';
        modal.removeAttribute('aria-hidden');
    }

    function hideModal() {
        var modal = document.getElementById(MODAL_ID);
        if (!modal) {
            return;
        }

        if (typeof window.jQuery !== 'undefined' && window.jQuery.fn.modal) {
            window.jQuery(modal).modal('hide');
            return;
        }

        if (typeof window.bootstrap !== 'undefined' && window.bootstrap.Modal) {
            var instance = window.bootstrap.Modal.getInstance(modal);
            if (instance) {
                instance.hide();
            }
            return;
        }

        modal.classList.remove('show');
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
    }

    function setSendingState(isSending) {
        sending = isSending;
        var sendBtn = document.getElementById(SEND_BTN_ID);
        var cancelBtn = document.querySelector('#' + MODAL_ID + ' .modal-footer .btn-secondary');
        var closeBtn = document.querySelector('#' + MODAL_ID + ' .btn-close');
        var radios = document.querySelectorAll('#' + MODAL_ID + ' input[name="verify_link_channel"]');

        if (sendBtn) {
            sendBtn.disabled = isSending;
            sendBtn.setAttribute('aria-busy', isSending ? 'true' : 'false');
            sendBtn.innerHTML = isSending
                ? '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Sending...'
                : 'Send';
        }
        if (cancelBtn) {
            cancelBtn.disabled = isSending;
        }
        if (closeBtn) {
            closeBtn.disabled = isSending;
        }
        radios.forEach(function (radio) {
            radio.disabled = isSending;
        });
    }

    function sendVerificationLink($btn, channel) {
        var config = window.ClientDetailConfig || {};
        var url = (config.urls && config.urls.sendVerifyLink) || '';
        var clientId = config.clientId;
        if (!url || !clientId) {
            setSendingState(false);
            toast('error', 'Unable to send verification link.');
            return;
        }

        $btn.data('busy', true);
        setSendingState(true);
        window.jQuery.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: config.csrfToken,
                client_id: clientId,
                channel: channel
            },
            success: function (res) {
                toast('success', (res && res.message) || 'Verification link sent.');
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Could not send verification link.';
                toast('error', msg);
            },
            complete: function () {
                $btn.data('busy', false);
                setSendingState(false);
                hideModal();
                pendingButton = null;
            }
        });
    }

    function ensureModal() {
        if (document.getElementById(MODAL_ID)) {
            return;
        }

        var wrapper = document.createElement('div');
        wrapper.innerHTML =
            '<div class="modal fade" id="' + MODAL_ID + '" tabindex="-1" aria-hidden="true">' +
                '<div class="modal-dialog">' +
                    '<div class="modal-content">' +
                        '<div class="modal-header">' +
                            '<h5 class="modal-title">Send Verify Link</h5>' +
                            '<button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>' +
                        '</div>' +
                        '<div class="modal-body">' +
                            '<p class="mb-3">Where should this verification link be sent?</p>' +
                            '<div class="form-check">' +
                                '<input class="form-check-input" type="radio" name="verify_link_channel" id="verifyLinkChannelEmail" value="email">' +
                                '<label class="form-check-label" for="verifyLinkChannelEmail" id="verifyLinkChannelEmailLabel">Primary Email Address</label>' +
                            '</div>' +
                            '<div class="form-check">' +
                                '<input class="form-check-input" type="radio" name="verify_link_channel" id="verifyLinkChannelSms" value="sms">' +
                                '<label class="form-check-label" for="verifyLinkChannelSms" id="verifyLinkChannelSmsLabel">Primary Phone no</label>' +
                            '</div>' +
                        '</div>' +
                        '<div class="modal-footer">' +
                            '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>' +
                            '<button type="button" class="btn btn-primary" id="' + SEND_BTN_ID + '">Send</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';

        document.body.appendChild(wrapper.firstElementChild);

        var sendBtn = document.getElementById(SEND_BTN_ID);
        if (sendBtn) {
            sendBtn.addEventListener('click', function () {
                if (sending) {
                    return;
                }
                var channel = selectedChannel();
                if (channel !== 'email' && channel !== 'sms') {
                    toast('error', 'Please choose Primary email or Primary phone.');
                    return;
                }
                var $btn = pendingButton;
                if ($btn) {
                    sendVerificationLink($btn, channel);
                }
            });
        }
    }

    function bind() {
        if (typeof window.jQuery === 'undefined') {
            return;
        }

        ensureModal();

        window.jQuery(document).on('click', '.send-verify-link', function (event) {
            event.preventDefault();
            event.stopPropagation();
            var $btn = window.jQuery(this);
            if (sending || $btn.data('busy')) {
                return;
            }
            pendingButton = $btn;
            setSendingState(false);
            var radios = document.querySelectorAll('#' + MODAL_ID + ' input[name="verify_link_channel"]');
            radios.forEach(function (radio) {
                radio.checked = false;
            });
            updateChannelLabels();
            showModal();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bind);
    } else {
        bind();
    }
})();
