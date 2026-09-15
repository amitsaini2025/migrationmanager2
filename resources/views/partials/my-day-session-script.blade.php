@auth('admin')
@php
    $resolvedMatterId = $myDayMatterId ?? null;
    if (! $resolvedMatterId && ! empty($matter_list_arr) && ! empty($myDayRef) && $myDayRef !== 'file') {
        foreach ($matter_list_arr as $matterRow) {
            if ((string) ($matterRow->client_unique_matter_no ?? '') === (string) $myDayRef) {
                $resolvedMatterId = $matterRow->id;
                break;
            }
        }
    }
    if (! $resolvedMatterId && ! empty($latestClientMatterId)) {
        $resolvedMatterId = $latestClientMatterId;
    }
@endphp
<script>
    window.MyDaySession = {
        clientId: @json($myDayClientId ?? null),
        clientMatterId: @json($resolvedMatterId),
        ref: @json($myDayRef ?? 'file'),
        csrf: @json(csrf_token()),
        routes: {
            heartbeat: @json(route('dashboard.my-day.sessions.heartbeat')),
            blur: @json(route('dashboard.my-day.sessions.blur')),
            idleCutBase: @json(url('/dashboard/my-day/sessions'))
        }
    };
</script>
<script defer src="{{ URL::asset('js/crm/clients/file-time-session.js') }}?v={{ file_exists(public_path('js/crm/clients/file-time-session.js')) ? filemtime(public_path('js/crm/clients/file-time-session.js')) : time() }}"></script>
@endauth
