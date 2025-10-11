{{-- ========================================
    ALL NOTE-RELATED MODALS
    This file contains all note modals for the client detail page
    ======================================== --}}

{{-- 1. Create Note Modal (Simple) --}}
<!-- Update note Modal -->
<div class="modal fade custom_modal" id="create_note" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Create Note</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/create-note')}}" name="notetermform" autocomplete="off" id="notetermform" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
				<input type="hidden" name="noteid" value="">
				<input type="hidden" name="mailid" value="0">
				<input type="hidden" name="vtype" value="client">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="task_group">Type <span class="span_req">*</span></label>
								<select name="task_group" class="form-control" data-valid="required" id="noteType">
								    <option value="">Please Select Note</option>
								    <option value="Call">Call</option>
								    <option value="Email">Email</option>
								    <option value="In-Person">In-Person</option>
								    <option value="Others">Others</option>
								    <option value="Attention">Attention</option>
								</select>
								<!-- Container for additional inputs -->
						        <div id="additionalFields"></div>
								<span class="custom-error task_group_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Description <span class="span_req">*</span></label>
								<textarea  class="summernote-simple" name="description" data-valid="required"></textarea>
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<!--<div class="col-12 col-md-12 col-lg-12 is_not_note" style="display:none;">
							<div class="form-group">
								<label class="d-block" for="">Related To</label>
								<div class="form-check form-check-inline">
									<input class="" type="radio" id="note_contact" value="Contact" name="related_to_note" checked>
									<label style="padding-left: 6px;" class="form-check-label" for="note_contact">Contact</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="" type="radio" id="note_partner" value="Partner" name="related_to_note">
									<label style="padding-left: 6px;" class="form-check-label" for="note_partner">Partner</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="" type="radio" id="note_application" value="Application" name="related_to_note">
									<label style="padding-left: 6px;" class="form-check-label" for="note_application">Application</label>
								</div>

							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12 is_not_note" style="display:none;">
							<div class="form-group">
								<label for="contact_name">Contact Name <span class="span_req">*</span></label>
								<select data-valid="" class="form-control contact_name js-data-example-ajaxcc" name="contact_name[]">

								</select>
								<span class="custom-error contact_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>-->
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('notetermform')" type="button" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- 2. Create Note with Matter Selection --}}
<!-- Create note Modal -->
<div class="modal fade custom_modal" id="create_note_d" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Create Note</h5>
                <button type="button" class="btn btn-primary btn-block float-right btn-assignuser" data-container="body" data-role="popover" data-placement="bottom" data-html="true" style="border-radius:4px !important;width:70px;display:inline-block;margin-left:30px;margin-top:-5px;"> Action</button>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/create-note')}}" name="notetermform_n" autocomplete="off" id="notetermform_n" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="client_id" id="client_id" value="{{$fetchedData->id}}">
                    <input type="hidden" name="noteid" value="">
                    <input type="hidden" name="mailid" value="0">
                    <input type="hidden" name="vtype" value="client">
					<div class="row">
                        <div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<?php
                                $general_matter_list_arr = DB::table('client_matters')
                                ->select('client_matters.id','client_matters.client_unique_matter_no')
                                ->where('client_matters.client_id',@$fetchedData->id)
                                ->where('client_matters.sel_matter_id',1)
                                ->get();
                                //dd( $general_matter_list_arr);
                                if( !empty($general_matter_list_arr) && count($general_matter_list_arr)>0 ){ ?>
                                    <label for="matter_id">Select Matter <span class="span_req">*</span></label>
                                    @foreach($general_matter_list_arr as $generallist)
                                        <div class="form-check">
                                            <input class="form-check-input general_matter_checkbox" type="checkbox" name="matter_id" id="general_matter_checkbox_{{$generallist->id}}" value="{{$generallist->id}}">
                                            <label class="form-check-label" for="general_matter_checkbox_{{$generallist->id}}">General Matter ({{@$generallist->client_unique_matter_no}})</label>
                                        </div>
                                    @endforeach
                                <?php
                                }
                                ?>

                                <label class="form-check-label" for="">Select any client matter</label>
                                <select name="matter_id" id="matter_id" class="form-control">
								    <option value="">Select Client Matters</option>
                                    <?php
                                    $matter_list_arr = DB::table('client_matters')
                                    ->leftJoin('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
                                    ->select('client_matters.id','client_matters.client_unique_matter_no','matters.title')
                                    ->where('client_matters.matter_status',1)
                                    ->where('client_matters.client_id',@$fetchedData->id)
                                    //->where('matters.status',1)
                                    ->where('client_matters.sel_matter_id','!=',1)
                                    ->get();
                                    ?>
								    @foreach($matter_list_arr as $matterlist)
                                        <option value="{{$matterlist->id}}">{{@$matterlist->title}}({{@$matterlist->client_unique_matter_no}})</option>
                                    @endforeach
								</select>
								<span class="custom-error matter_id_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

                        <input type="hidden" name="title" value="Matter Discussion">

                        <div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="task_group">Type<span class="span_req">*</span></label>
								<select name="task_group" class="form-control" data-valid="required" id="noteType">
                                    <option value="">Please Select</option>
                                    <option value="Call">Call</option>
                                    <option value="Email">Email</option>
                                    <option value="In-Person">In-Person</option>
                                    <option value="Others">Others</option>
                                    <option value="Attention">Attention</option>
                                </select>

                                <!-- Container for additional inputs -->
						        <div id="additionalFields"></div>

								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Description <span class="span_req">*</span></label>
								<textarea  class="summernote-simple" id="note_description" name="description" data-valid="required"></textarea>
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

                        <div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('notetermform_n')" type="button" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- 3. View Note Modal --}}
<!-- Note & Terms Modal -->
<div class="modal fade custom_modal" id="view_note" tabindex="-1" role="dialog" aria-labelledby="view_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="note_col">
					<div class="note_content">
						<h5></h5>
						<p></p>
						<div class="extra_content">

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- 4. View Application Note Modal --}}
<!-- Note & Terms Modal -->
<div class="modal fade custom_modal" id="view_application_note" tabindex="-1" role="dialog" aria-labelledby="view_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="note_col">
					<div class="note_content">
						<h5></h5>
						<p></p>
						<div class="extra_content">

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- 5. Create Application Note Modal --}}
<div class="modal fade custom_modal" id="create_applicationnote" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Create Note</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/create-app-note')}}" name="appnotetermform" autocomplete="off" id="appnotetermform" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
				<input type="hidden" name="noteid" id="noteid" value="">
				<input type="hidden" name="type" id="type" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="title">Title <span class="span_req">*</span></label>
								<input type="text" name="title" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Title">
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Description <span class="span_req">*</span></label>
								<textarea class="summernote-simple" name="description" data-valid="required"></textarea>
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('appnotetermform')" type="button" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

