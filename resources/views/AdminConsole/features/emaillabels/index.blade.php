@extends('layouts.crm_client_detail')
@section('title', 'Email Labels')
 
@section('content')

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
				<div class="col-3 col-md-3 col-lg-3">
			        @include('../Elements/CRM/setting')
		        </div>       
				<div class="col-9 col-md-9 col-lg-9">
					<div class="card">
						<div class="card-header">
							<h4>Email Labels</h4>
							<div class="card-header-action">
								<a href="{{route('adminconsole.features.emaillabels.create')}}" class="btn btn-primary">Create Email Label</a>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive common_table"> 
								<table class="table text_wrap">
								<thead>
									<tr>
										<th>Label</th>
										<th>Name</th>
										<th>Type</th>
										<th>Created By</th>
										<th>Status</th>
										<th>Last Updated</th>
										<th>Action</th>
									</tr> 
								</thead>
								@if(@$totalData !== 0)
								<?php $i=0; ?>
								<tbody class="tdata">	
								@foreach (@$lists as $list)
									<tr id="id_{{@$list->id}}">
										<td>
											<span class="badge" style="background-color: {{@$list->color}}20; border: 1px solid {{@$list->color}}; color: {{@$list->color}}; padding: 5px 10px; border-radius: 4px;">
												<i class="{{@$list->icon ?? 'fas fa-tag'}}"></i> {{@$list->name}}
											</span>
										</td>
										<td>{{ @$list->name == "" ? config('constants.empty') : Str::limit(@$list->name, '50', '...') }}</td>
										<td>
											@if(@$list->type == 'system')
												<span class="badge badge-info">System</span>
											@else
												<span class="badge badge-secondary">Custom</span>
											@endif
										</td>
										<td>{{@$list->user ? @$list->user->first_name . ' ' . @$list->user->last_name : 'System'}}</td>
										<td>
											@if(@$list->is_active)
												<span class="badge badge-success">Active</span>
											@else
												<span class="badge badge-danger">Inactive</span>
											@endif
										</td>
										<td>@if($list->updated_at != '') {{date('Y-m-d H:i', strtotime($list->updated_at))}} @else - @endif</td>
										
										<td>
											<div class="dropdown d-inline">
												<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
												<div class="dropdown-menu">
													@if(@$list->type != 'system')
														<a class="dropdown-item has-icon" href="{{route('adminconsole.features.emaillabels.edit', base64_encode(convert_uuencode(@$list->id)))}}"><i class="far fa-edit"></i> Edit</a>
													@else
														<span class="dropdown-item text-muted"><i class="far fa-edit"></i> Edit (System labels cannot be edited)</span>
													@endif
												</div>
											</div>								  
										</td>
									</tr>	
								@endforeach	 
								</tbody>
								@else
								<tbody>
									<tr>
										<td style="text-align:center;" colspan="7">
											No Record found
										</td>
									</tr>
								</tbody>
								@endif
							</table> 
						</div>
						<div class="card-footer">
							{{ @$lists->links() }}
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
 
@endsection
@push('scripts') 
<script>
jQuery(document).ready(function($){	
	$('.cb-element').change(function () {		
	if ($('.cb-element:checked').length == $('.cb-element').length){
	  $('#checkbox-all').prop('checked',true);
	}
	else {
	  $('#checkbox-all').prop('checked',false);
	}
	});	
});	
</script>
@endpush

