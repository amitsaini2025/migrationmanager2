{{-- ========================================
    ALL TASK-RELATED MODALS
    This file contains all task modals for the client detail page
    ======================================== --}}

{{-- 1. View Task Modal --}}
<!-- Task Modal -->
<div class="modal fade custom_modal" id="opentaskview" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content taskview">

		</div>
	</div>
</div>

{{-- 2. Create Task Modal --}}
<div class="modal fade create_task custom_modal" id="opentaskmodal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="taskModalLabel">Create New Task</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="#" name="taskform" autocomplete="off" id="tasktermform" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="mailid" value="">

					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="title">Title <span class="span_req">*</span></label>
								<input type="text" name="title" value="" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Title">
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="category">Category <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control category select2" name="category">
									<option value="">Choose Category</option>
									<option value="Reminder">Reminder</option>
									<option value="Call">Call</option>
									<option value="Follow Up">Follow Up</option>
									<option value="Email">Email</option>
									<option value="Meeting">Meeting</option>
									<option value="Support">Support</option>
									<option value="Others">Others</option>
								</select>
								<span class="custom-error category_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="assignee">Assignee</label>
								<select data-valid="" class="form-control assignee select2" name="assignee">
									<option value="">Select</option>
									<?php
									$headoffice = \App\Models\Admin::where('role','!=',7)->get();
									foreach($headoffice as $holist){
										?>
										<option value="{{$holist->id}}">{{$holist->first_name}} ({{$holist->email}})</option>
										<?php
									}
									?>
								</select>
								<span class="custom-error assignee_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="priority">Priority</label>
								<select data-valid="" class="form-control priority select2" name="priority">
									<option value="">Choose Priority</option>
									<option value="Low">Low</option>
									<option value="Normal">Normal</option>
									<option value="High">High</option>
									<option value="Urgent">Urgent</option>
								</select>
								<span class="custom-error priority_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="due_date">Due Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<input type="text" name="due_date" value="" class="form-control datepicker" data-valid="" autocomplete="off" placeholder="Select Date">
								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error due_date_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="due_time">Due Time</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-clock"></i>
										</div>
									</div>
									<input type="time" name="due_time" class="form-control" data-valid="" autocomplete="off" placeholder="Select Time">
								</div>
								<span class="custom-error due_time_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Description</label>
								<textarea class="form-control" name="description"></textarea>
								<span class="custom-error description_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12 ifselecttask">
							<div class="form-group">
								<label class="d-block" for="related_to">Related To</label>
								<div class="form-check form-check-inline">
									<input  type="radio" id="contact" value="Contact" name="related_to" checked>
									<label style="padding-left:6" class="form-check-label" for="contact">Contact</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="" type="radio" id="partner" value="Partner" name="related_to">
									<label style="padding-left:6" class="form-check-label" for="partner">Partner</label>
								</div>
							{{--	<div class="form-check form-check-inline">
									<input class="" type="radio" id="application" value="Application" name="related_to">
									<label style="padding-left:6" class="form-check-label" for="application">Application</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="" type="radio" id="internal" value="Internal" name="related_to">
									<label style="padding-left:6" class="form-check-label" for="internal">Internal</label>
								</div>--}}
								@if ($errors->has('related_to'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('related_to') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6 is_contact ifselecttask">
							<div class="form-group">
								<label for="contact_name">Contact Name <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control contact_name js-data-example-ajaxcontact" name="contact_name[]">

								</select>
								<span class="custom-error contact_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6 is_partner ifselecttask">
							<div class="form-group">
								<label for="partner_name">Partner Name <span class="span_req">*</span></label>
								<select data-valid="" class="form-control partner_name select2" name="partner_name">
									<option value="">Choose Partner</option>
									<option value="Amit">Amit</option>
								</select>
								<span class="custom-error partner_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6 is_application ifselecttask">
							<div class="form-group">
								<label for="client_name">Client Name <span class="span_req">*</span></label>
								<select data-valid="" class="form-control client_name select2" name="client_name">
									<option value="">Choose Client</option>
									<option value="Amit">Amit</option>
								</select>
								<span class="custom-error client_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6 is_application ifselecttask">
							<div class="form-group">
								<label for="application">Application <span class="span_req">*</span></label>
								<select data-valid="" class="form-control application select2" name="application">
									<option value="">Choose Application</option>
									<option value="Demo">Demo</option>
								</select>
								<span class="custom-error application_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6 is_application ifselecttask">
							<div class="form-group">
								<label for="stage">Stage <span class="span_req">*</span></label>
								<select data-valid="" class="form-control stage select2" name="stage">
									<option value="">Choose Stage</option>
									<option value="Stage 1">Stage 1</option>
								</select>
								<span class="custom-error stage_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="followers">Followers <span class="span_req">*</span></label>
								<select data-valid="" multiple class="form-control followers  select2" name="followers">

									<?php
										$headoffice = \App\Models\Admin::where('role','!=',7)->get();
									foreach($headoffice as $holist){
										?>
										<option value="{{$holist->id}}">{{$holist->first_name}} {{$holist->last_name}} ({{$holist->email}})</option>
										<?php
									}
									?>
								</select>
								<span class="custom-error followers_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="attachments">Attachments</label>
								<div class="custom-file">
									<input type="file" name="attachments" class="custom-file-input" id="attachments">
									<label class="custom-file-label showattachment" for="attachments">Choose file</label>
								</div>
								<span class="custom-error attachments_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
						<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
							<button onclick="customValidate('taskform')" type="button" class="btn btn-primary">Create</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

