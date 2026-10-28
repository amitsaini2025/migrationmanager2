@if(!empty($profileOnly))
    <div class="alert alert-warning d-flex flex-wrap align-items-center justify-content-between mx-3 mt-3 mb-0" role="status">
        <span>You can view this profile and personal details, and add notes. Documents, accounts, workflow, and the other sections stay closed.</span>
        <button type="button" class="btn btn-sm btn-primary mt-2 mt-sm-0" id="crm-profile-request-full">Request 15-minute access</button>
    </div>
    <style>
        .profile-only-file #personaldetails-tab input,
        .profile-only-file #personaldetails-tab select,
        .profile-only-file #personaldetails-tab textarea,
        .profile-only-file #personaldetails-tab button,
        .profile-only-file #companydetails-tab input,
        .profile-only-file #companydetails-tab select,
        .profile-only-file #companydetails-tab textarea,
        .profile-only-file #companydetails-tab button {
            pointer-events: none;
        }
        .profile-only-file .opennoteform,
        .profile-only-file .editdatetime,
        .profile-only-file .deletenote,
        .profile-only-file .pinnote {
            display: none;
        }
    </style>
    <script>
        document.getElementById('crm-profile-request-full')?.addEventListener('click', function () {
            if (typeof window.openCrmAccessModal !== 'function') {
                return;
            }
            window.openCrmAccessModal({
                cid: {{ (int) ($fetchedData->id ?? 0) }},
                name: @json(trim(($fetchedData->first_name ?? '').' '.($fetchedData->last_name ?? ''))),
                record_type: @json($fetchedData->type ?? 'client'),
                id: @json(($encodeId ?? '').'/Client'),
                redirect_to: window.location.href
            });
        });
    </script>
@endif
