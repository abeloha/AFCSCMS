@extends('layout')

<?php 
	$page_title = 'Divisions';
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
            @if($data)
                <div class="nav nav-tabs page-header-tab">
                    <a href="{{url('depts/'.$data->dept_id)}}" class="nav-link active">List of Divisions</a>
                </div>
            @endif
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
						<h3 class="card-title">Edit Divisions</h3>
						<div class="card-options ">
							<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
						</div>
					</div>
					<div class="card-body">
												
						<form method="POST" action="{{url('div/edit')}}">
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
											<label>Name of Division</label>
											<input type="text" name="name" value="{{$data->name}}" class="form-control">
										</div>
									</div>
									
									<?php
										$courses = get_course();
										$depts = get_dept();
										$users = get_user_by_role_name('ci');
										$users_tc = get_user_by_role_name('tc');  
										
										$user_exist = 0;
									?>
									<div class="col-sm-12">
										<div class="form-group">
                                            <label>Select the Course of This Division</label>
                                            @if($courses)
                                                <select name = "course" class="form-control" required> 
                                                    <option value="">Select Course</option>
                                                    @foreach ($courses as $course)
                                                        <option value="{{$course->id}}" {{($course->id == $data->course_id)? 'selected' : ''}}>{{$course->name}}</option>
                                                    @endforeach
                                                </select>
                                            @endif
										</div>
									</div>

									<div class="col-sm-12">
										<div class="form-group">
                                            <label>Select the Department of This Division</label>
                                            @if($depts)
                                                <select name = "dept" class="form-control" required> 
                                                    <option value="">Select Department</option>
                                                    @foreach ($depts as $dept)
                                                        <option value="{{$dept->id}}" {{($dept->id == $data->dept_id)? 'selected' : ''}}>{{$dept->name}}</option>
                                                    @endforeach
                                                </select>
                                            @endif
										</div>
									</div>

									<div class="col-sm-12">
										<div class="form-group">
                                            <label>Selcet CI Of This Division</label>
											@if($users)
												<?php $user_exist = 1; ?>
                                                <select name = "ci" class="form-control" required> 
                                                    <option value="">Select CI</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{$user->id}}" {{($user->id == $data->ci_user_id)? 'selected' : ''}}>{{$user->surname.' '.$user->first_name}}</option>
                                                    @endforeach
                                                </select>
											@endif
											@if(!$user_exist)
                                                <i>No CI staff enrolled in the system</i>
                                            @endif
										</div>
									</div>

									<div class="col-sm-12">
										<div class="form-group">
                                            <label>Selcet TC Of This Division</label>
											@if($users_tc)
												<?php $user_exist = 1; ?>
                                                <select name = "tc" class="form-control" required> 
                                                    <option value="">Select TC</option>
                                                    @foreach ($users_tc as $user)
                                                        <option value="{{$user->id}}" {{($user->id == $data->tc_user_id)? 'selected' : ''}}>{{$user->surname.' '.$user->first_name}}</option>
                                                    @endforeach
                                                </select>
											@endif
											@if(!$user_exist)
                                                <i>No TC staff enrolled in the system</i>
                                            @endif
										</div>
									</div>

									<div class="col-sm-12">
										<button class="btn btn-primary btn-lg btn-simple">Update</button>
										<a href="{{url('depts/'.$data->dept_id)}}" class="nav-link active">Cancel and Go Back to List of Divisions</a>
									</div>								
								@else
									<p>Division details could not be loaded.</p>
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