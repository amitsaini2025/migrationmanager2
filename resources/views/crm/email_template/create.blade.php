@extends('layouts.crm_client_detail')
@section('title', 'Create Email Template')

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
			<form action="{{ url('email_templates/store') }}" method="POST" name="add-template" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Create Email Template</h4>
								<div class="card-header-action">
									<a href="{{route('email.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-body">
								<div id="accordion">
									<div class="accordion">
										<div class="accordion-header" role="button" data-toggle="collapse" data-target="#primary_info" aria-expanded="true">
											<h4>Template Information</h4>
										</div>
										<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
											<div class="row">
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group">
														<label for="title">Name <span class="span_req">*</span></label>
														<input type="text" name="title" value="" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Template Name">
														@if ($errors->has('title'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('title') }}</strong>
															</span> 
														@endif
													</div>
												</div>
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group">
														<label for="subject">Subject <span class="span_req">*</span></label>
														<input type="text" name="subject" value="" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Subject">
														@if ($errors->has('subject'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('subject') }}</strong>
															</span> 
														@endif
													</div>
												</div>
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group">
														<label for="description">Description <span class="span_req">*</span></label>
														<textarea id="email_template_description" name="description" data-valid="required" class="form-control tinymce-editor" placeholder="Enter template description" style="height: 200px;"></textarea>
														@if ($errors->has('description'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('description') }}</strong>
															</span> 
														@endif
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group float-right">
									<button type="submit" class="btn btn-primary" onClick="saveEmailTemplate()"><i class="fa fa-save"></i> Save Template</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</section>
</div>
@endsection

@push('scripts')
<!-- TinyMCE Editor -->
<script src="{{asset('js/tinymce/js/tinymce/tinymce.min.js')}}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    tinymce.init({
        selector: '#email_template_description',
        license_key: 'gpl',
        height: 300,
        menubar: false,
        plugins: [
            'lists', 'link', 'autolink'
        ],
        toolbar: 'bold italic underline strikethrough | forecolor | bullist numlist | link',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }',
        placeholder: 'Enter template description',
        branding: false,
        promotion: false,
        // Color palette matching Summernote colors
        color_map: [
            "000000", "Black",
            "333333", "Dark Gray",
            "666666", "Medium Gray",
            "999999", "Light Gray",
            "CCCCCC", "Very Light Gray",
            "E0E0E0", "Pale Gray",
            "F5F5F5", "Off White",
            "FFFFFF", "White",
            "DC2626", "Red",
            "EA580C", "Orange",
            "D97706", "Amber",
            "059669", "Green",
            "0891B2", "Cyan",
            "2563EB", "Blue",
            "7C3AED", "Purple",
            "DB2777", "Pink",
            "EF4444", "Light Red",
            "F97316", "Light Orange",
            "F59E0B", "Light Amber",
            "10B981", "Light Green",
            "06B6D4", "Light Cyan",
            "3B82F6", "Light Blue",
            "8B5CF6", "Light Purple",
            "EC4899", "Light Pink"
        ],
        setup: function(editor) {
            // Sync TinyMCE content with textarea for form validation
            editor.on('change', function() {
                editor.save();
            });
        }
    });
    
    // Function to save TinyMCE content before form validation
    window.saveEmailTemplate = function() {
        if (tinymce.get('email_template_description')) {
            tinymce.get('email_template_description').save();
        }
        customValidate('add-template');
    };
});
</script>
@endpush