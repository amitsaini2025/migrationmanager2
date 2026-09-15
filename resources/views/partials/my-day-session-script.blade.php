@auth('admin')
<script>
    window.MyDaySession = {
        clientId: @json($myDayClientId ?? null),
        clientMatterId: @json($myDayMatterId ?? null),
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
