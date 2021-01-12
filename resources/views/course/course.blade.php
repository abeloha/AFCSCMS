@extends('layout')

<?php 
	$page_title = 'Course';
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
                <a href="{{url('courses')}}" class="nav-link active">List of Courses</a>
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
						<h3 class="card-title">Edit Course</h3>
						<div class="card-options ">
							<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
						</div>
					</div>
					<div class="card-body">
												
						<form method="POST" action="{{url('course/edit')}}">
							<div class="row">	
								@if($course)
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
									<input type="hidden" name="id" value="{{$course->id}}" class="form-control">
									<div class="col-sm-12">
										<div class="form-group">
											<label>Name of Course</label>
											<input type="text" name="name" value="{{$course->name}}" class="form-control">
										</div>
									</div>
									
									<?php
										$terms = get_term_by_course($course->id);
										$sessions = get_session_by_course($course->id)
									?>
									<div class="col-sm-12">
										<div class="form-group">
											<label>Set Current Term</label>
											<select name = "current_term" class="form-control">
												<option value="">Select</option>
                                                @foreach ($terms as $item)
                                                    <option value="{{$item->id}}" {{($item->id == $course->current_term_id)? 'selected' : ''}}>{{$item->name}}</option>
                                                @endforeach
											</select>
										</div>
									</div>

									<div class="col-sm-12">
										<div class="form-group">
											<label>Set Current Session</label>
											<select name = "current_session" class="form-control">
												<option value="">Select</option>
												@foreach ($sessions as $item)
													<option value="{{$item->id}}" {{($item->id == $course->current_session_id)? 'selected' : ''}}>{{$item->name}}</option>
												@endforeach
											</select>
										</div>
									</div>

									<div class="col-sm-12">
										<div class="form-group">
											<label>Enable/Disable Exercises Enrollment for Students</label>
											<select name = "exercise_enrollment" class="form-control">
												<option value="0" {{(0 == $course->exercise_enrollment)? 'selected' : ''}}>Disabled</option>
												<option value="1" {{(1 == $course->exercise_enrollment)? 'selected' : ''}}>Enabled</option>
											</select>
										</div>
									</div>
									
									<div class="col-sm-12">
										<div class="form-group">
											<label>New Student Registration Start Date</label>
											<input type="date" name="reg_start" value="{{$course->reg_start_at}}" class="form-control">
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<label>New Student Registration End Date</label>
											<input type="date" name="reg_end" value="{{$course->reg_end_at}}" class="form-control">
										</div>
									</div>

									<div class="col-sm-12">
										<button class="btn btn-primary btn-lg btn-simple">Update</button>
									</div>								
								@else
									<p>Course details could not be loaded.</p>
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