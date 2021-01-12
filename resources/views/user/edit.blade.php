@extends('layout')

<?php 
    $page_title = 'Edit User Account';
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
		</div>
	</div>
</div>

<div class="section-body mt-4">
    <div class="container-fluid">
	
		<div class="tab-content">
			<div class="tab-pane active">

                <?php
                    $n = 1;
                    $success = false;
                    if(isset($_GET['success'])){
                        $success = true;
                    } 
                    
                    $preset = false;
                    if(isset($_GET['preset'])){
                        $preset = true;
                    } 

                    $error = '';
                    if(isset($_GET['error'])){
                        $error = $_GET['error'];
                    }

                    $msg = '';
                    if(isset($_GET['msg'])){
                        $msg = $_GET['msg'];
                    }
                    
                    $can_change_role = check_can_change_user_role();
                    $can_reset_user_password = check_has_ability('reset_user_password'); 
                    $can_delete_or_deactivate_user = check_has_ability('delete_or_deactivate_user'); 
                    $user_id = get_user_id();
                ?>

                @if($success)
                    <div class="alert alert-info">
                        <p>Changes has been saved successfully</p>
                    </div>
                @endif

                @if($preset)
                    <div class="alert alert-info">
                        <p>Password for this account has been reset to surname (lowercase)</p>
                    </div>
                @endif

                @if($error)
                    <div class="alert alert-danger">
                        <p>
                            Sorry, your password change failed.
                            <br><b>{{$error}}</b>
                        </p>
                    </div>
                @endif

                @if($msg)
                    <div class="alert alert-info">
                        <p>{{$msg}}</p>
                    </div>
                @endif

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

				<div class="card">
					<div class="card-body">

                        @if($data)
                            
                            <form method="POST" action="{{url('user/edit')}}">
                           
                                @csrf 
            
                                <input type="hidden" name="user_id" value="{{$data->id}}">                    

                                <div class="row">
                            
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Surname <span class="text-danger">*</span></label>
                                            <input type="text" name="surname" class="form-control" value="{{$data->surname}}" required>
                                            @error('surname')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>First name <span class="text-danger">*</span></label>
                                            <input type="text" name="first_name" class="form-control" value="{{$data->first_name}}" required>
                                            @error('first_name')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Other name</label>
                                            <input type="text" name="other_name" class="form-control" value="{{$data->other_name}}">
                                            @error('other_name')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>  
                                    
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>email</label>
                                            <input type="text" name="email" class="form-control" value="{{$data->email}}" readonly>
                                            @error('email')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>	
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                        <label>Mobile Number <span class="text-danger">*</span></label>
                                        <input name="phone" type="text" class="form-control" value="{{$data->phone}}"  required>
                                        </div>
                                    </div>
                                
                                </div>
                                
                                <div class="row">
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label>Rank <span class="text-danger">*</span></label>
                                            <input type="text" name="rank" class="form-control" value="{{$data->rank}}" required>
                                            @error('rank')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label>Service (SVC) Number <span class="text-danger">*</span></label>
                                            <input type="text" name="svc_no" class="form-control" value="{{$data->svc_no}}" required>
                                            @error('svc_no')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="row">
                                    <div class="col-sm-4 col-md-4">
                                        <div class="form-group">
                                            <label>Service <span class="text-danger">*</span></label>
                                            <input type="text" name="service" class="form-control" value="{{$data->service}}" required>
                                            @error('service')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <?php 
                                        $countries = form_data_country();
                                    ?>
                                    <div class="col-sm-4 col-md-4">
                                        <div class="form-group">
                                            <label>Country <span class="text-danger">*</span></label>
                                            <select class="form-control" name="country" required>
                                                <option value="">-- Select Country --</option>
                                                @if($countries)
                                                    @foreach($countries as $item)
                                                        <option value="{{$item}}" {{($item == $data->country)? 'selected' : ''}}>{{$item}}</option>
                                                    @endforeach                                                                                                
                                                @endif
                                            </select>
                                            @error('country')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4">
                                        <div class="form-group">
                                            <label>Sex <span class="text-danger">*</span></label>
                                            <select class="form-control" name="sex" required>
                                                <option value="">-- Select Sex --</option>
                                                <option value="Male" {{('Male' == $data->sex)? 'selected' : ''}}>Male</option>
                                                <option value="Female" {{('Female' == $data->sex)? 'selected' : ''}}>Female</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                
                                <div class="row">
                                    <div class="col-sm-3 col-md-3">
                                        <div class="form-group">
                                            <label>Corps </label>
                                            <input type="text" name="corps" class="form-control" value="{{$data->corps}}" >
                                            @error('corps')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-3 col-md-3">
                                        <div class="form-group">
                                            <label>Branch </label>
                                            <input type="text" name="branch" class="form-control" value="{{$data->branch}}" >
                                            @error('branch')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-3 col-md-3">
                                        <div class="form-group">
                                            <label>Specialty </label>
                                            <input type="text" name="specialty" class="form-control" value="{{$data->specialty}}" >
                                            @error('specialty')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-3 col-md-3">
                                        <div class="form-group">
                                            <label>Commission </label>
                                            <input type="text" name="commission" class="form-control" value="{{$data->commission}}" >
                                            @error('commission')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>                                    
                                    
                                </div>

                                <div class="row">
                                    <div class="col-sm-12 text-right m-t-20">
                                        <hr>
                                    </div>
                                </div>

                                <div class="row">  

                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label>Bank Name </label>
                                            <input type="text" name="bank" class="form-control" value="{{$data->bank}}" >
                                            @error('bank')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label>Account Number</label>
                                            <input type="text" name="account" class="form-control" value="{{$data->account}}" >
                                            @error('account')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                </div>

                                <div class="row">  

                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label>Immediate Last Unit</label>
                                            <input type="text" name="last_unit_1" class="form-control" value="{{$data->last_unit_1}}" >
                                            @error('last_unit_1')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label>Immediate Last Appointment</label>
                                            <input type="text" name="last_appointment_1" class="form-control" value="{{$data->last_appointment_1}}" >
                                            @error('last_appointment_1')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                </div>

                                <div class="row">  

                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label>Second Last Unit</label>
                                            <input type="text" name="last_unit_2" class="form-control" value="{{$data->last_unit_2}}" >
                                            @error('last_unit_2')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label>Second Last Appointment</label>
                                            <input type="text" name="last_appointment_2" class="form-control" value="{{$data->last_appointment_2}}" >
                                            @error('last_appointment_2')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                </div>

                                @if($can_change_role)
                                    <div class="row">
                                        <div class="col-sm-12 text-right m-t-20">
                                            <hr>
                                        </div>
                                    </div>

                                    <div class="row"> 
                                        <div class="col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label>Change User Department </label>
                                            
                                                <?php $depts = get_dept(); ?>                                                

                                                <select name="dept" class="form-control" required>
                                                    <option value="" >Select Department</option>
                                                    @if($depts)
                                                        @foreach($depts as $dept)
                                                            <option value="{{$dept->id}}" {{($dept->id == $data->dept_id)? 'selected' : ''}}>{{$dept->name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('dept')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row"> 
                                    @if($data->role == 1)
                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label>Change User Course </label>
                                                
                                                    <?php $courses = get_course(); ?>                                                

                                                    <select name="course" class="form-control" required>
                                                        <option value="" >Select Course</option>
                                                        @if($courses)
                                                            @foreach($courses as $course)
                                                                <option value="{{$course->id}}" {{($course->id == $data->course_id)? 'selected' : ''}}>{{$course->name}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('course')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                    @elseif(!$data->locked_account)
                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label>Change User Appointment </label>
                                                
                                                    <?php $roles = get_system_roles('all staff'); ?>                                                

                                                    <select name="role" class="form-control" required>
                                                        <option value="" >Select Appointment</option>
                                                        @if($roles)
                                                            @foreach($roles as $role)
                                                                <?php 
                                                                    $role_code = $role['code'];
                                                                    $role_name = $role['name'];
                                                                ?>
                                                                <option value="{{$role_code}}" {{($role_code == $data->role)? 'selected' : ''}}>{{$role_name}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('role')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                    @endif
                                    </div>

                                @endif

                                <div class="row">
                                    <div class="col-sm-12 m-t-20">
                                        <button type="submit" class="btn btn-primary">SAVE CHANGES</button>
                                    </div>                                    
                                </div>
                                <br>
                                <div class="row"> 

                                    
                                    <div class="col-sm-6 col-md-6">

                                        @if($data->id == $user_id)
                                            <div class="form-group">
                                                <a href="{{url('user/password/edit/'.$data->id)}}"><b>Change Password</b></a>
                                            </div>
                                        @elseif($can_reset_user_password)
                                            <div class="form-group">
                                                <a href="{{url('user/password/reset/'.$data->id)}}" onclick="return confirm('Are you Sure you want to reset this account password?')"><b>Reset this user password to his/her surname</b></a>
                                            </div>
                                        @endif                                       

                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <a href="{{url('user/'.$data->id)}}"> < Cancel and go back to profile</a>
                                        </div>
                                    </div>

                                    @if($can_delete_or_deactivate_user && !$data->locked_account)
                                        <div class="col-sm-12 col-md-12">
                                            <hr>
                                            <div class="form-group">
                                                @if($data->deactivated)
                                                    <h5>Account is deactivated</h5>
                                                    <p><a href="{{url('user/'.$data->id.'/deactivate/?value=activate')}}" onclick="return confirm('Are you Sure you want to activate this user?')" class="dropdown-item"><i class="dropdown-icon fa fa-check text-warning"></i> <span class="text-danger">Activate User Account</span></a></p>
                                                @else
                                                    <h5>Account is Active</h5>
                                                    <p><a href="{{url('user/'.$data->id.'/deactivate/?value=deactivate')}}" onclick="return confirm('Are you Sure you want to deactivate this user?')" class="dropdown-item"><i class="dropdown-icon fa fa-ban text-warning"></i> <span class="text-danger">Deactivate User Account</span></a></p>
                                                @endif
                                                <p>
                                                    When a user account is deactivated, the user will no longer have access to the system but the users records will not be erased. 
                                                </p>
                                            </div>
                                            <hr>
                                            <div class="form-group">
                                                <p><a href="{{url('user/'.$data->id.'/delete')}}" onclick="return confirm('Are you Sure you want to delete this user?\nThis action cannot be undone')" class="dropdown-item"><i class="dropdown-icon fa fa-trash-o text-danger"></i> <span class="text-danger">Delete User Account</span></a></p>

                                                <p>
                                                    If this account is deleted, the records of this user will be removed completely from the system. All records such as results, activities e.t.c will be lost. The account cannot be retrieved again.
                                                    <br>
                                                    If you simply want to prevent this user from gaining access to the software, you should deactivate the account instead.
                                                </p>

                                            </div> 
                                        </div>                                       
                                    @endif

                                </div>
                            </form>	

                        @else
                            <p>Account details could not be loaded now</p>
                        @endif                        

					</div>
                </div>
                
            </div>
            
		</div>
	
    </div>
</div>
    


@endsection