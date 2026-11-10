@if(\App\Support\AccountsListAccess::isAccountant(Auth::user()) && !empty($clientAdminId))
    <a href="{{ route('clients.accountsOpen', ['id' => $clientAdminId]) }}" title="Open this client file for 15 minutes">{{ $label }}</a>
@else
    {{ $label }}
@endif
