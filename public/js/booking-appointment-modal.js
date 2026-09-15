/**
 * Shared website-booking appointment modal (dashboard + booking calendar).
 * Expects #eventModal / #cancellationConfirmModal in the DOM.
 * Optional: window.consultantsData, window.bookingFcCalendar, window.onBookingAppointmentChanged.
 */
(function () {
    'use strict';

    if (window.openBookingAppointmentModal) {
        return;
    }

    var pendingCancellationData = null;
    var AJAY_ARUN_BLOCK_MESSAGE = 'You cannot assign this appointment to Ajay Calendar or Arun Calendar from 23 Apr 2026 to 3 May 2026. These consultants are not available for this period.';

    function fa(iconClass) {
        return typeof crmIconLegacy === 'function'
            ? crmIconLegacy('fas ' + iconClass)
            : '<i class="fas ' + iconClass + '"></i>';
    }

    function escapeHtml(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function jsonHeaders() {
        return {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken(),
        };
    }

    function refetchBookingCalendar() {
        if (typeof window.onBookingAppointmentChanged === 'function') {
            window.onBookingAppointmentChanged();
            return;
        }
        if (window.bookingFcCalendar && typeof window.bookingFcCalendar.refetchEvents === 'function') {
            window.bookingFcCalendar.refetchEvents();
        }
    }

    function getStatusClass(status) {
        var classes = {
            pending: 'warning',
            awaiting_confirmation: 'warning',
            paid: 'primary',
            confirmed: 'success',
            completed: 'info',
            cancelled: 'danger',
            no_show: 'dark',
            rescheduled: 'primary',
        };
        return classes[status] || 'secondary';
    }

    function showAlert(type, message) {
        var alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-' + type + ' alert-dismissible fade show';
        alertDiv.innerHTML = escapeHtml(message) +
            '<button type="button" class="close" data-bs-dismiss="alert"><span>&times;</span></button>';
        var container = document.querySelector('.section-body')
            || document.querySelector('.dashboard-calendar-section')
            || document.querySelector('.main-content');
        if (container) {
            container.insertBefore(alertDiv, container.firstChild);
            setTimeout(function () {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        } else {
            window.alert(message);
        }
    }

    function showModalAlert(type, message) {
        var modalBody = document.getElementById('eventModalBody');
        if (!modalBody) {
            showAlert(type, message);
            return;
        }
        var existing = modalBody.querySelector('.modal-inline-alert');
        if (existing) {
            existing.remove();
        }
        var alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-' + type + ' alert-dismissible fade show modal-inline-alert';
        alertDiv.style.cssText = 'margin:0 0 12px 0; border-radius:4px;';
        alertDiv.innerHTML = '<strong>' + escapeHtml(message) + '</strong>' +
            '<button type="button" class="close" style="padding:4px 8px;" onclick="this.parentElement.remove()"><span>&times;</span></button>';
        modalBody.insertBefore(alertDiv, modalBody.firstChild);
        setTimeout(function () {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 8000);
    }

    function showBootstrapModal(id) {
        var el = document.getElementById(id);
        if (!el) {
            return;
        }
        if (window.jQuery) {
            window.jQuery(el).modal('show');
            return;
        }
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(el).show();
        }
    }

    function hideBootstrapModal(id) {
        var el = document.getElementById(id);
        if (!el) {
            return;
        }
        if (window.jQuery) {
            window.jQuery(el).modal('hide');
            return;
        }
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var inst = bootstrap.Modal.getInstance(el);
            if (inst) {
                inst.hide();
            }
        }
    }

    function consultantOptionsHtml(props) {
        var consultants = Array.isArray(window.consultantsData) ? window.consultantsData : [];
        var unique = [];
        var seen = {};
        consultants.forEach(function (consultant) {
            if (consultant && consultant.id && !seen[consultant.id]) {
                seen[consultant.id] = true;
                unique.push(consultant);
            }
        });
        var currentId = props.consultant_id != null ? Number(props.consultant_id) : null;
        if (currentId && !seen[currentId]) {
            unique.push({
                id: currentId,
                name: props.consultant_name_raw || props.consultant,
                crm_display_label: props.consultant,
                calendar_type: props.consultant_calendar_type || '',
            });
        }
        return unique.map(function (consultant) {
            var isSelected = currentId != null && Number(consultant.id) === currentId;
            var label = consultant.crm_display_label || consultant.name;
            var isTourist = consultant.calendar_type === 'tourist';
            var typeSuffix = (!isTourist && consultant.calendar_type && consultant.calendar_type !== 'paid')
                ? ' (' + escapeHtml(consultant.calendar_type) + ')'
                : '';
            var titleAttr = isTourist ? ' title="Vijay(Tourist Visa)"' : '';
            return '<option value="' + escapeHtml(consultant.id) + '" data-calendar-type="' +
                escapeHtml(consultant.calendar_type || '') + '"' + titleAttr +
                (isSelected ? ' selected' : '') + '>' + escapeHtml(label) + typeSuffix + '</option>';
        }).join('');
    }

    window.openBookingAppointmentModal = function (info) {
        var event = info.event || info;
        var props = event.extendedProps || {};
        var originalDateTime = props.appointment_datetime || event.startStr || event.start;
        var utcDate = new Date(originalDateTime);
        var formattedDate = isNaN(utcDate.getTime()) ? '' : utcDate.toLocaleString('en-AU', {
            timeZone: 'Australia/Melbourne',
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
        });
        var duration = props.duration_minutes || 15;
        if (event.end) {
            var diffMinutes = Math.round((event.end.getTime() - event.start.getTime()) / (1000 * 60));
            if (diffMinutes > 0 && diffMinutes < 1440) {
                duration = diffMinutes;
            }
        }
        var clientName = props.client_name || event.title || 'Client';
        var clientNameDisplay = escapeHtml(clientName);
        if (props.client_id_encoded) {
            clientNameDisplay = '<a href="/clients/detail/' + encodeURIComponent(props.client_id_encoded) +
                '" target="_blank" style="color: #007bff; text-decoration: underline;">' + clientNameDisplay + '</a>';
        }
        var meetingTypeDisplay = props.meeting_type
            ? props.meeting_type.split('_').map(function (word) {
                return word.charAt(0).toUpperCase() + word.slice(1);
            }).join(' ')
            : 'N/A';
        var melbourneDate = isNaN(utcDate.getTime()) ? '' : utcDate.toLocaleDateString('en-CA', {
            timeZone: 'Australia/Melbourne',
        });
        var melbourneTime = isNaN(utcDate.getTime()) ? '' : utcDate.toLocaleTimeString('en-US', {
            timeZone: 'Australia/Melbourne',
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
        });
        var stableConsultantIdAttr = props.consultant_id != null ? String(props.consultant_id) : '';
        var eventId = event.id;
        var isPaid = !!props.is_paid;
        var finalAmount = parseFloat(props.final_amount || 0);
        var locationLabel = props.location
            ? props.location.charAt(0).toUpperCase() + props.location.slice(1)
            : 'N/A';
        var language = props.preferred_language
            ? props.preferred_language.charAt(0).toUpperCase() + props.preferred_language.slice(1).toLowerCase()
            : 'English';
        var status = props.status || 'pending';

        var modalBody = '<div class="appointment-details"><div class="row"><div class="col-md-6">' +
            '<p><strong>Client:</strong> ' + clientNameDisplay + '</p>' +
            '<p><strong>Email:</strong> ' + escapeHtml(props.client_email) + '</p>' +
            '<p><strong>Phone:</strong> ' + escapeHtml(props.client_phone) + '</p>' +
            '<p><strong>Service:</strong> ' + escapeHtml(props.service_type) + '</p>' +
            '<p><strong>Date & Time:</strong> ' + escapeHtml(formattedDate) + '</p>' +
            '<p><strong>Duration:</strong> ' + escapeHtml(duration) + ' minutes</p></div><div class="col-md-6">' +
            '<p><strong>Location:</strong> ' + escapeHtml(locationLabel) + '</p>' +
            '<p><strong>Meeting Type:</strong> <span id="meetingTypeDisplay-' + eventId +
            '" style="cursor: pointer; color: #007bff; text-decoration: underline;" onclick="showMeetingTypeDropdown(' +
            eventId + ', \'' + escapeHtml(props.meeting_type || '') + '\')" title="Click to change meeting type">' +
            escapeHtml(meetingTypeDisplay) + ' ' + fa('fa-edit') + '</span>' +
            '<select id="meetingTypeSelect-' + eventId + '" class="form-control form-control-sm d-none" style="max-width: 200px; display: inline-block;" onchange="updateAppointmentMeetingType(' +
            eventId + ', this.value)" data-is-paid="' + (isPaid ? '1' : '0') + '">' +
            '<option value="in_person"' + (props.meeting_type === 'in_person' ? ' selected' : '') + '>In Person</option>' +
            '<option value="phone"' + (props.meeting_type === 'phone' ? ' selected' : '') + '>Phone</option>' +
            (isPaid ? '<option value="video"' + (props.meeting_type === 'video' ? ' selected' : '') + '>Video</option>' : '') +
            '</select></p>' +
            '<p><strong>Preferred Language:</strong> ' + escapeHtml(language) + '</p>' +
            '<p><strong>Consultant:</strong> ' + escapeHtml(props.consultant || 'Not Assigned') + '</p>' +
            '<p><strong>Status:</strong> <span class="badge badge-' + getStatusClass(status) + '" id="statusBadge">' +
            escapeHtml(String(status).toUpperCase()) + '</span></p>' +
            '<p><strong>Payment:</strong> <span class="badge badge-' + (isPaid ? 'primary' : 'secondary') +
            '" id="paymentBadge">' + escapeHtml(props.payment_status || (isPaid ? 'Paid' : 'Free')) + '</span></p>' +
            (!isPaid && status === 'awaiting_confirmation'
                ? '<p id="confirmationReminderActions-' + eventId + '"><button type="button" class="btn btn-sm btn-outline-primary" onclick="sendAppointmentConfirmationReminder(' +
                    eventId + ')">' + fa('fa-envelope') + ' Reminder For Appointment Confirmation</button></p>'
                : '') +
            '</div></div><hr><div class="row mb-3"><div class="col-12"><h6>' + fa('fa-calendar-alt') +
            ' Reschedule Date & Time</h6><div class="form-row"><div class="form-group col-md-4">' +
            '<label class="small">Appointment Date</label><input type="date" class="form-control form-control-sm" id="rescheduleDate-' +
            eventId + '" value="' + escapeHtml(melbourneDate) + '" data-original-date="' + escapeHtml(melbourneDate) +
            '" onchange="validateWeekendDate(this, ' + eventId + ')"></div>' +
            '<div class="form-group col-md-4"><label class="small">Appointment Time</label>' +
            '<input type="time" class="form-control form-control-sm" id="rescheduleTime-' + eventId +
            '" value="' + escapeHtml(melbourneTime) + '" data-original-time="' + escapeHtml(melbourneTime) +
            '"></div><div class="form-group col-md-4 d-flex align-items-end">' +
            '<button type="button" class="btn btn-sm btn-primary w-100" onclick="rescheduleAppointmentDateTime(this, ' +
            eventId + ', \'' + escapeHtml(props.meeting_type || 'in_person') + '\', \'' +
            escapeHtml(props.preferred_language || 'English') + '\')">' + fa('fa-save') +
            ' Update Date & Time</button></div></div>' +
            '<small class="text-muted">' + fa('fa-info-circle') +
            ' Changes will sync with the Bansal website if the appointment is linked.</small></div></div><hr>' +
            '<div class="row"><div class="col-md-6"><h6>' + fa('fa-edit') +
            ' Change Status</h6><div class="btn-group-vertical w-100" role="group">' +
            '<button type="button" class="btn btn-sm btn-outline-success" onclick="updateAppointmentStatus(' + eventId +
            ', \'confirmed\')">' + fa('fa-check') + ' Mark as Confirmed</button>' +
            '<button type="button" class="btn btn-sm btn-outline-primary" onclick="updateAppointmentStatus(' + eventId +
            ', \'completed\')">' + fa('fa-check-circle') + ' Mark as Complete</button>' +
            (finalAmount > 0
                ? '<button type="button" class="btn btn-sm btn-outline-info" onclick="updateAppointmentStatus(' + eventId +
                    ', \'paid\')">' + fa('fa-dollar-sign') + ' Mark As Payment Done</button>' +
                    '<button type="button" class="btn btn-sm btn-outline-warning" onclick="updateAppointmentStatus(' + eventId +
                    ', \'pending\')">' + fa('fa-clock') + ' Mark As Payment Pending</button>'
                : '') +
            '<button type="button" class="btn btn-sm btn-outline-danger" onclick="updateAppointmentStatus(' + eventId +
            ', \'cancelled\')">' + fa('fa-times') + ' Mark as Cancelled</button>' +
            '<button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateAppointmentStatus(' + eventId +
            ', \'no_show\')">' + fa('fa-user-times') + ' Mark as No Show</button>' +
            (!isPaid || !finalAmount
                ? '<button type="button" class="btn btn-sm btn-outline-success" id="manualPaymentActions-' + eventId +
                    '" onclick="markAppointmentManualPayment(' + eventId + ')">' + fa('fa-hand-holding-usd') +
                    ' Manual Payment Received</button>'
                : '') +
            (!isPaid
                ? '<button type="button" class="btn btn-sm btn-outline-primary" id="requestPaymentActions-' + eventId +
                    '" onclick="requestAppointmentPayment(' + eventId + ')">' + fa('fa-envelope') +
                    ' Request Payment From Client</button>'
                : '') +
            '</div></div><div class="col-md-6"><h6>' + fa('fa-exchange-alt') +
            ' Change Calendar Type</h6><div class="form-group"><select class="form-control form-control-sm" id="consultantSelect-' +
            eventId + '" data-stable-consultant-id="' + escapeHtml(stableConsultantIdAttr) +
            '" onchange="updateAppointmentConsultant(' + eventId + ', this.value)">' +
            '<option value="">Select Consultant...</option>' + consultantOptionsHtml(props) +
            '</select></div><div class="mt-2"><small class="text-muted">' + fa('fa-info-circle') +
            ' Changing consultant will move this appointment to the selected calendar type.</small></div></div></div></div>';

        var body = document.getElementById('eventModalBody');
        var detailsLink = document.getElementById('viewFullDetails');
        if (!body || !detailsLink) {
            return;
        }
        body.innerHTML = modalBody;
        detailsLink.href = props.detail_url || ('/booking/appointments/' + eventId);
        showBootstrapModal('eventModal');
    };

    function parseJsonResponse(response) {
        var ct = response.headers.get('content-type') || '';
        if (ct.indexOf('application/json') === -1) {
            return Promise.resolve({});
        }
        return response.json().catch(function () {
            return {};
        });
    }

    window.markAppointmentManualPayment = function (appointmentId) {
        if (!confirm('Do you want to manually update (Payment received manually) the payment type from Free to Paid?')) {
            return;
        }
        fetch('/booking/appointments/' + appointmentId + '/manual-payment', {
            method: 'POST',
            headers: jsonHeaders(),
            credentials: 'same-origin',
            body: JSON.stringify({}),
        }).then(function (response) {
            return parseJsonResponse(response).then(function (data) {
                if (!response.ok || data.success !== true) {
                    showAlert('danger', 'Failed to update payment: ' + (data.message || data.error || 'Unknown error'));
                    return;
                }
                hideBootstrapModal('eventModal');
                refetchBookingCalendar();
                showAlert('success', data.message || 'Payment type updated from Free to Paid.');
            });
        }).catch(function () {
            showAlert('danger', 'Failed to update payment. Please try again.');
        });
    };

    window.sendAppointmentConfirmationReminder = function (appointmentId) {
        if (!confirm('Send a reminder for appointment confirmation to this client?')) {
            return;
        }
        fetch('/booking/appointments/' + appointmentId + '/send-confirmation-reminder', {
            method: 'POST',
            headers: jsonHeaders(),
            credentials: 'same-origin',
            body: JSON.stringify({}),
        }).then(function (response) {
            return parseJsonResponse(response).then(function (data) {
                if (!response.ok || data.success !== true) {
                    showAlert('danger', 'Failed to send reminder: ' + (data.message || 'Unknown error'));
                    return;
                }
                alert(data.message || 'Reminder for appointment confirmation sent to the client.');
            });
        }).catch(function () {
            showAlert('danger', 'Failed to send reminder. Please try again.');
        });
    };

    window.requestAppointmentPayment = function (appointmentId) {
        if (!confirm('Do you want to send a $150 payment request email to this client?')) {
            return;
        }
        fetch('/booking/appointments/' + appointmentId + '/request-payment', {
            method: 'POST',
            headers: jsonHeaders(),
            credentials: 'same-origin',
            body: JSON.stringify({}),
        }).then(function (response) {
            return parseJsonResponse(response).then(function (data) {
                if (!response.ok || data.success !== true) {
                    showAlert('danger', 'Failed to send payment request: ' + (data.message || 'Unknown error'));
                    return;
                }
                showAlert('success', data.message || 'Payment request email sent to the client.');
            });
        }).catch(function () {
            showAlert('danger', 'Failed to send payment request. Please try again.');
        });
    };

    window.updateAppointmentStatus = function (appointmentId, newStatus) {
        if (newStatus === 'cancelled') {
            pendingCancellationData = { appointmentId: appointmentId };
            document.getElementById('cancelReasonInput').value = '';
            document.getElementById('cancelReasonError').classList.add('d-none');
            document.getElementById('sendCancellationEmailCheck').checked = true;
            showBootstrapModal('cancellationConfirmModal');
            return;
        }
        if (!confirm('Are you sure you want to change the status to "' + newStatus + '"?')) {
            return;
        }
        performStatusUpdate(appointmentId, newStatus, null, false);
    };

    function performStatusUpdate(appointmentId, newStatus, cancellationReason, sendCancellationConfirmation) {
        var requestData = { status: newStatus };
        if (cancellationReason) {
            requestData.cancellation_reason = cancellationReason;
        }
        if (newStatus === 'cancelled' && sendCancellationConfirmation) {
            requestData.send_cancellation_confirmation = true;
        }
        fetch('/booking/appointments/' + appointmentId + '/update-status', {
            method: 'POST',
            headers: jsonHeaders(),
            credentials: 'same-origin',
            body: JSON.stringify(requestData),
        }).then(function (response) {
            return parseJsonResponse(response).then(function (data) {
                if (!response.ok || !(data.success === true || data.status === true)) {
                    showAlert('danger', 'Failed to update status: ' + (data.message || 'Unknown error'));
                    return;
                }
                hideBootstrapModal('eventModal');
                refetchBookingCalendar();
                showAlert('success', 'Status updated successfully!');
            });
        }).catch(function () {
            showAlert('danger', 'Failed to update status. Please try again.');
        });
    }

    window.updateAppointmentConsultant = function (appointmentId, consultantId) {
        if (!consultantId) {
            return;
        }
        var select = document.getElementById('consultantSelect-' + appointmentId);
        if (!select) {
            return;
        }
        var stableConsultantId = select.getAttribute('data-stable-consultant-id') || '';
        if (!confirm('Are you sure you want to change the consultant? This will move the appointment to a different calendar.')) {
            select.value = stableConsultantId;
            return;
        }
        fetch('/booking/appointments/' + appointmentId + '/update-consultant', {
            method: 'POST',
            headers: jsonHeaders(),
            credentials: 'same-origin',
            body: JSON.stringify({
                consultant_id: consultantId,
                appointment_date_melbourne: (document.getElementById('rescheduleDate-' + appointmentId) || {}).value || null,
            }),
        }).then(function (response) {
            return parseJsonResponse(response).then(function (data) {
                if (!response.ok || !data.success) {
                    showAlert('danger', data.message || 'Failed to update consultant.');
                    select.value = stableConsultantId;
                    return;
                }
                hideBootstrapModal('eventModal');
                refetchBookingCalendar();
                showAlert('success', 'Consultant updated successfully! The appointment has been moved to the new calendar.');
            });
        }).catch(function () {
            select.value = stableConsultantId;
            showAlert('danger', 'Failed to update consultant. Please try again.');
        });
    };

    window.validateWeekendDate = function (dateInput) {
        if (!dateInput.value) {
            return;
        }
        var selectedDate = new Date(dateInput.value + 'T12:00:00');
        var dayOfWeek = selectedDate.getDay();
        if (dayOfWeek === 0 || dayOfWeek === 6) {
            dateInput.value = dateInput.getAttribute('data-original-date');
            showAlert('warning', 'Weekends (Saturday and Sunday) are not available for appointments. Please select a weekday.');
        }
    };

    window.rescheduleAppointmentDateTime = function (btnEl, appointmentId, meetingType, preferredLanguage) {
        var dateInput = document.getElementById('rescheduleDate-' + appointmentId);
        var timeInput = document.getElementById('rescheduleTime-' + appointmentId);
        if (!dateInput || !timeInput) {
            showAlert('danger', 'Date and time inputs not found.');
            return;
        }
        var newDate = dateInput.value;
        var newTime = timeInput.value;
        if (newDate === dateInput.getAttribute('data-original-date') && newTime === timeInput.getAttribute('data-original-time')) {
            showAlert('info', 'No changes detected. Date and time remain the same.');
            return;
        }
        if (!newDate || !newTime) {
            showAlert('danger', 'Please select both date and time.');
            return;
        }
        if (!confirm('Are you sure you want to reschedule this appointment to ' + newDate + ' at ' + newTime + '?')) {
            dateInput.value = dateInput.getAttribute('data-original-date');
            timeInput.value = timeInput.getAttribute('data-original-time');
            return;
        }
        var formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('appointment_date', newDate);
        formData.append('appointment_time', newTime);
        formData.append('meeting_type', meetingType);
        formData.append('preferred_language', preferredLanguage || 'English');
        fetch('/booking/appointments/' + appointmentId, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken(),
            },
            credentials: 'same-origin',
            body: formData,
        }).then(function (response) {
            return parseJsonResponse(response).then(function (data) {
                if (response.status === 409) {
                    showModalAlert('danger', data.message || 'This slot is already booked.');
                    return;
                }
                if (!response.ok && data.success === false) {
                    showAlert('danger', data.message || 'Failed to update appointment.');
                    return;
                }
                hideBootstrapModal('eventModal');
                refetchBookingCalendar();
                showAlert('success', data.message || 'Appointment date and time updated successfully!');
            });
        }).catch(function () {
            showAlert('danger', 'Failed to reschedule appointment. Please try again.');
        });
    };

    window.showMeetingTypeDropdown = function (appointmentId) {
        var display = document.getElementById('meetingTypeDisplay-' + appointmentId);
        var select = document.getElementById('meetingTypeSelect-' + appointmentId);
        if (display && select) {
            display.classList.add('d-none');
            select.classList.remove('d-none');
            select.focus();
        }
    };

    window.updateAppointmentMeetingType = function (appointmentId, newMeetingType) {
        if (!newMeetingType) {
            return;
        }
        fetch('/booking/appointments/' + appointmentId + '/update-meeting-type', {
            method: 'POST',
            headers: jsonHeaders(),
            credentials: 'same-origin',
            body: JSON.stringify({ meeting_type: newMeetingType }),
        }).then(function (response) {
            return parseJsonResponse(response).then(function (data) {
                if (!response.ok || !data.success) {
                    showAlert('danger', data.message || 'Failed to update meeting type.');
                    return;
                }
                hideBootstrapModal('eventModal');
                refetchBookingCalendar();
                showAlert('success', 'Meeting type updated successfully!');
            });
        }).catch(function () {
            showAlert('danger', 'Failed to update meeting type. Please try again.');
        });
    };

    document.addEventListener('click', function (event) {
        if (event.target && event.target.id === 'confirmCancelBtn') {
            if (!pendingCancellationData) {
                return;
            }
            var reason = document.getElementById('cancelReasonInput').value.trim();
            if (!reason) {
                document.getElementById('cancelReasonError').classList.remove('d-none');
                return;
            }
            document.getElementById('cancelReasonError').classList.add('d-none');
            var sendEmail = document.getElementById('sendCancellationEmailCheck').checked;
            hideBootstrapModal('cancellationConfirmModal');
            performStatusUpdate(pendingCancellationData.appointmentId, 'cancelled', reason, sendEmail);
            pendingCancellationData = null;
        }
    });

    window.showBookingAppointmentAlert = showAlert;
    window.showBookingAppointmentModalAlert = showModalAlert;
    window.AJAY_ARUN_BLOCK_MESSAGE = AJAY_ARUN_BLOCK_MESSAGE;
})();
