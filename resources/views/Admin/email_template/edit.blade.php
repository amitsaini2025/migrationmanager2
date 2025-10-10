@extends('layouts.admin')
@section('title', 'Email Template')

@section('content')
 
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">Email Template</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Email Template</li>
					</ol>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.container-fluid -->
	</div>
	<!-- /.content-header -->	
	<!-- Breadcrumb start-->
	<!--<ol class="breadcrumb">
		<li class="breadcrumb-item active">
			Home / <b>Dashboard</b>
		</li>
		@include('../Elements/Admin/breadcrumb')
	</ol>-->
	<!-- Breadcrumb end-->
	
	<!-- Main content --> 
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<!-- Flash Message Start -->
					<div class="server-error">
						@include('../Elements/flash-message')
					</div>
					<!-- Flash Message End -->
				</div> 
				<div class="col-md-12">
					<div class="card card-primary">
					  <div class="card-header">
						<h3 class="card-title">Email Template</h3>
					  </div> 
					  <!-- /.card-header -->
					  <!-- form start -->
						  <form action="{{ url('admin/edit_email_template') }}" method="POST" name="edit-holidaytype" autocomplete="off" enctype="multipart/form-data">
							@csrf
							<input type="hidden" name="id" value="{{ @$fetchedData->id }}">
						<div class="card-body">
							<div class="form-group" style="text-align:right;">
								<a style="margin-right:5px;" href="{{route('admin.email.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>  
								<button type="submit" class="btn btn-primary" onClick="customValidate('edit-holidaytype')"><i class="fa fa-edit"></i> Update Template</button>
							</div>
							<div class="form-group row"> 
								<label for="title" class="col-sm-2 col-form-label">Name <span style="color:#ff0000;">*</span></label>
								<div class="col-sm-10">
								<input type="text" name="title" value="{{ @$fetchedData->title }}" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Name">
								@if ($errors->has('title'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('title') }}</strong>
									</span> 
								@endif
								</div>
						  </div>
						  <div class="form-group row"> 
								<label for="subject" class="col-sm-2 col-form-label">Subject <span style="color:#ff0000;">*</span></label>
								<div class="col-sm-10">
								<input type="text" name="subject" value="{{ @$fetchedData->subject }}" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Subject">
								@if ($errors->has('subject'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('subject') }}</strong>
									</span> 
								@endif
								</div>
						  </div>
						  
						  <div class="form-group row">
								<label for="description" class="col-sm-2 col-form-label">Description <span style="color:#ff0000;">*</span></label>
								<div class="col-sm-10">
									<textarea name="description" data-valid="required" value="" class="textarea" placeholder="Please Add Description Here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{@$fetchedData->description}}</textarea>
									@if ($errors->has('description'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('description') }}</strong>
										</span> 
									@endif
								</div>
						  </div>
						  
						  <div class="form-group float-right">
							<button type="submit" class="btn btn-primary" onClick="customValidate('edit-holidaytype')"><i class="fa fa-edit"></i> Update Template</button>
						  </div> 
						</div> 
						  </form>
					</div>	   
				</div>	
			</div>
		</div>
	</section>
</div>
@endsection