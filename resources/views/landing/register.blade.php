@extends('landing.layout')

@section('title', 'Register')

@section('content')

<div class="auth option2">
	<div class="auth_left">
		<div class="card">
			<div class="card-body">
				<div class="text-center">
					<a class="header-brand" href="{{url('/')}}"> <img src="{{asset('assets/images/logo.png')}}" style="height: 80px; width: 80px;"> </a>
					<div class="card-title">
                        {{get_app_full_name()}}
					</div>
					<h6 class="mt-3 mb-3">{{$type}} Registration</h6>
				</div>
				<?php
					$error_code = '';
					$valid_reg = false;
					$is_staff = false;
					$is_student = false;

					$course_session = 0;
					$course_name = '';
					$course_id = 0;

					if($type == 'Staff'){
						$is_staff = true;
						$valid_reg = true;
					}elseif($type == 'Student'){
						$is_student = true;
						
						$reg_course = get_course($course);
						if($reg_course){
							$course_session = $reg_course->current_session_id;
							$course_name = $reg_course->name;
							$course_id = $reg_course->id;

							if($course_session){
								$valid_reg = true;
							}else{
								$error_code = 'Academic session for course cannot be determined';
							}
							
						}
						
					}

					$depts = get_dept();
					if(!$depts){
						$valid_reg = false;
						$error_code = 'No departments in the system';
					}
				?>
				@if($valid_reg)
					<form method="POST" action="/register">
						@csrf
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
						
						<input type="hidden" name="session" value="{{$course_session}}">						
						<input type="hidden" name="course" value="{{$course_id}}">

						<div class="form-group">
							<label class="form-label">Surname</label>
							<input type="text" name="surname" value="{{ old('surname') }}" class="form-control" required>
							@error('surname')
								<div class="alert alert-danger">{{ $message }}</div>
							@enderror
						</div>                    
						<div class="form-group">
							<label class="form-label">First name</label>
							<input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control" required>
							@error('first_name')
								<div class="alert alert-danger">{{ $message }}</div>
							@enderror
						</div>
						
						<input type="hidden" name="other_name" value="{{ old('other_name') }}" class="form-control" value="">

						<div class="form-group">
							<label class="form-label">Email</label>
							<input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
							@error('email')
								<div class="alert alert-danger">{{ $message }}</div>
							@enderror
						</div>
						
						<div class="form-group">
							<label class="form-label">
								@if($is_staff)
									I Am A Staff Of...
								@elseif($is_student)
									I Am A Student Of...
								@else
									Please Select Your Department
								@endif
							</label>
							<select name="dept" class="form-control" required>
								<option value="">Select Your Department</option>
									@foreach($depts as $dept)
										<option value="{{$dept->id}}">{{$dept->name}}</option>
									@endforeach
							</select>
							@error('dept')
								<div class="alert alert-danger">{{ $message }}</div>
							@enderror
						</div>
						
						@if($is_staff)
							<?php $roles = get_system_roles('staff'); ?>
							<div class="form-group">
								<label class="form-label">
									What Is Your Appointment?
								</label>
								<select name="role" class="form-control" required>
									<option value="">Select Your Appointment</option>
										@if($roles)
											@foreach($roles as $role)
												<?php 
													$role_code = $role['code'];
													$role_name = $role['name'];
												?>
												<option value="{{$role_code}}">{{$role_name}}</option>
											@endforeach
										@endif
								</select>
								@error('role')
									<div class="alert alert-danger">{{ $message }}</div>
								@enderror
							</div>
						@elseif($is_student)
							<input type="hidden" name="role" value="1">
							<div class="form-group">
								<label class="form-label">Course</label>
								<input type="text" name="course_name" value="{{$course_name}}" class="form-control" readonly>
							</div>
						@endif
						
						<div class="form-group">
							<label class="form-label">Password</label>
							<input type="password" name="password" class="form-control" required>
						</div>
						<div class="form-group">
							<label class="form-label">Confirm Password</label>
							<input type="password" name="password_confirmation" class="form-control" required>
							@error('password')
								<div class="alert alert-danger">{{ $message }}</div>
							@enderror
						</div>

						<div class="text-center">
							<button type="submit" class="btn btn-primary btn-block">Create new account</button>
							<div class="text-muted mt-4">Already have account? <a href="{{url('login')}}">Sign in</a></div>
							<div class="text-muted mt-4"><a href="{{url('/')}}">Cancel Registration</a></div>
						</div>
					</form>
				@else
					<div class="text-center" style="margin-bottom: 10px;">
						<i>Sorry, we cannot determine the type of account you want to create. 
							<br><a href="{{url('/')}}">Go back to home page</a>
						@if($error_code)
							<br>
							<br>Additional diagnosis: {{$error_code}}
						@endif
						</i>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>

@endsection