@extends('layouts.crm_client_detail')
@section('title', 'Request access')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="card">
                <div class="card-body py-5 text-center">
                    <h5 class="mb-2">You do not have authority to open this</h5>
                    <p class="text-muted mb-0">This client or lead is visible so you know it exists. Opening the file needs authority. If 15-minute access is enabled on your account, you can open it now. Otherwise request access.</p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
