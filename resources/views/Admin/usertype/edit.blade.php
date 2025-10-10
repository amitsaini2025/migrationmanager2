@extends('layouts.admin')
@section('title', 'User Type')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body"> 
			<form action="{{ url('admin/usertype/edit') }}" method="POST" name="edit-usertype" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="id" value="{{ @$fetchedData->id }}">
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Edit User Type</h4>
								<div class="card-header-action">
									<a href="{{route('admin.usertype.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>	
					<div class="col-12 col-md-6 col-lg-6">
						<div class="card">
							<div class="card-body">
								<div class="form-group"> 
									<label for="name">User Type Name</label>
									<input type="text" name="name" value="{{ @$fetchedData->name }}" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter User Type">
									@if ($errors->has('name'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('name') }}</strong>
										</span> 
									@endif
								</div>	
								<div class="form-group float-right">
									<button type="submit" class="btn btn-primary">Update</button>
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