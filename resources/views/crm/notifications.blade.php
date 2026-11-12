@extends('layouts.crm_client_detail')
@section('title', 'Notifications')

@section('content')

<style>
/* Status column: CSS-based indicators that always display (no Font Awesome dependency) */
.notification-status-read,
.notification-status-unread {
	display: inline-block;
	width: 12px;
	height: 12px;
	border-radius: 50%;
	vertical-align: middle;
}
.notification-status-read { background-color: #28a745; }
.notification-status-unread { background-color: #007bff; }
</style>

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="custom-error-msg">
			</div>
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Notifications</h4>
							<div class="card-header-action" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
								<a href="{{ url('/all-notifications') }}" class="btn btn-sm {{ request('unread') === '1' ? 'btn-outline-primary' : 'btn-primary' }}">All</a>
								<a href="{{ url('/all-notifications?unread=1') }}" class="btn btn-sm {{ request('unread') === '1' ? 'btn-primary' : 'btn-outline-primary' }}">Unread</a>
								<form method="POST" action="{{ url('/all-notifications/mark-all-read') }}" style="display:inline;margin:0;">
									@csrf
									<button type="submit" class="btn btn-sm btn-outline-secondary">Mark all read</button>
								</form>
								<form method="POST" action="{{ url('/all-notifications/clear') }}" style="display:inline;margin:0;" onsubmit="return confirm('Clear all of your notifications?');">
									@csrf
									<button type="submit" class="btn btn-sm btn-outline-danger">Clear</button>
								</form>
								<span class="badge badge-primary">{{ $lists->total() }} Total</span>
							</div>
						</div>
						<div class="card-body">
							@if(!empty($seesAll))
							<p class="text-muted" style="margin-top:0;">Showing every staff notification. Mark all read and Clear only change yours.</p>
							@endif
							@if($lists->count() > 0)
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
										  <th width="50">Status</th>
										  <th>Message</th>
										  @if(!empty($seesAll))
										  <th width="180">For</th>
										  @endif
										  <th width="200">Date</th>
										  <th width="80"></th>
										</tr> 
									</thead>
									<tbody class="tdata">	
										@foreach ($lists as $list)
										<tr id="id_{{@$list->id}}" style="{{ ($list->receiver_status ?? 0) == 0 ? 'background-color: #f8f9fa; font-weight: 600;' : '' }}"> 
											<td class="text-center">
												@if(($list->receiver_status ?? 0) == 1)
													<span class="notification-status-read" data-bs-toggle="tooltip" title="Read" aria-label="Read"></span>
												@else
													<span class="notification-status-unread" data-bs-toggle="tooltip" title="Unread" aria-label="Unread"></span>
												@endif
											</td>
											<td>
												<a href="{{ \App\Support\NotificationUrl::withQuery($list->url, ['t' => $list->id]) }}">
													{{$list->message}}
												</a>
											</td>
											@if(!empty($seesAll))
											<td>
												{{ trim(($list->receiver->first_name ?? '').' '.($list->receiver->last_name ?? '')) ?: '—' }}
											</td>
											@endif
											<td>
												{{date('d/m/Y h:i A', strtotime($list->created_at))}}
											</td>
											<td>
												@if((int) $list->receiver_id === (int) Auth::id())
												<form method="POST" action="{{ url('/all-notifications/'.$list->id.'/clear') }}" style="margin:0;">
													@csrf
													<button type="submit" class="btn btn-sm btn-link">Clear</button>
												</form>
												@endif
											</td>
										</tr>	
										@endforeach	
									</tbody>
								</table>
							</div>
							@else
							<div class="text-center" style="padding: 40px;">
								@icon('fa-bell', ['style' => 'width: 48px; height: 48px; color: #ddd;'])
								<h5 class="mt-3">No Notifications</h5>
								<p class="text-muted">You don't have any notifications yet.</p>
							</div>
							@endif
						</div>
						@if($lists->count() > 0)
						<div class="card-footer">
							{!! $lists->withQueryString()->links() !!}
						</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

@endsection