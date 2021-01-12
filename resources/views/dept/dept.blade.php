@extends('layout')

<?php 
	$page_title = 'Department';
?>

@section('title', $page_title)

@section('content')

<div class="section-body">
	<div class="container-fluid">
		<div class="d-flex justify-content-between align-items-center">
			<div class="header-action">
				<h1 class="page-title">{{$page_title}}</h1>
				<ol class="breadcrumb page-breadcrumb">
				<li class="breadcrumb-item"><a href="{{url('/')}}">{{get_app_name()}}</a></li>
				<li class="breadcrumb-item active" aria-current="page">{{$page_title}}</li>
				</ol>
            </div>
            <div class="nav nav-tabs page-header-tab">
                <a href="{{url('depts')}}" class="nav-link active">List of Departments</a>
			</div>
		</div>
	</div>
</div>

<div class="section-body mt-4">
    <div class="container-fluid">
	
		<div class="tab-content">
			<div class="tab-pane active" id="Library-all">

				<?php
                    $sucess = false;
                    if(isset($_GET['sucess'])){
                        $sucess = true;
                    } 
				?>
				
                @if($sucess)
                    <div class="alert alert-info">
                        <p>Edited successfully</p>
                    </div>
				@endif
				
				<div class="card">					
					<div class="card-header">
						<h3 class="card-title">Edit Departments</h3>
						<div class="card-options ">
							<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
						</div>
					</div>
					<div class="card-body">
												
						<form method="POST" action="{{url('dept/edit')}}">
							<div class="row">	
								@if($data)
									@if ($errors->any())
										<div class="alert alert-danger">
											<p>Some errors occured!</p>
											<ul>
												@foreach ($errors->all() as $error)
													<li>{{ $error }}</li>
												@endforeach
											</ul>
										</div>
									@endif

									@csrf
									<input type="hidden" name="id" value="{{$data->id}}" class="form-control">
									<div class="col-sm-12">
										<div class="form-group">
											<label>Name of Department</label>
											<input type="text" name="name" value="{{$data->name}}" class="form-control">
										</div>
									</div>
									
									<?php
										$users = get_user_by_role_name('director');  
										$user_exist = 0;                                      
									?>
									<div class="col-sm-12">
										<div class="form-group">
                                            <label>Select Director Of This Department</label>
											@if($users)
											<?php $user_exist = 1; ?>
                                                <select name = "director" class="form-control" required> 
                                                    <option value="">Select Director</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{$user->id}}" {{($user->id == $data->director_user_id)? 'selected' : ''}}>{{$user->surname.' '.$user->first_name}}</option>
                                                    @endforeach
                                                </select>
											@endif
											@if(!$user_exist)
                                                <i>No Director enrolled in the system</i>
                                            @endif
										</div>
									</div>								
                                    <div class="form-group">
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="is_joint" {{($data->is_joint)? 'checked' : ''}}>
                                            <span class="custom-control-label">This is a joint department</span>
                                        </label>
                                    </div>
									<div class="col-sm-12">
										<button class="btn btn-primary btn-lg btn-simple">Update</button>
									</div>								
								@else
									<p>Department details could not be loaded.</p>
								@endif
							</div>
						</form>
						
					</div>
				</div>
			</div>
		
		</div>
	
    </div>
</div>
    


@endsection