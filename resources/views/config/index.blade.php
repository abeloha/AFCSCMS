@extends('layout')

<link  rel="stylesheet" href="{{asset('assets/multiselect/styles/multiselect.css')}}"/>
<script src="{{asset('assets/multiselect/multiselect.min.js')}}"></script>

<?php 
    $page_title = 'Configurations';
    $success = false;
    if(isset($_GET['success'])){
        $success = true;
    }
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
                @if($success)
                    <div class="alert alert-info">
                        <p>Configurations has been saved successfully</p>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">System Configuration</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{url('configuration')}}" enctype="multipart/form-data">                            
                            <div class="row">                            
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

                                <h2>General Settings</h2>

                                <!--app_name-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                App Short Name
                                            </div>
                                            <div class="col-sm-10">
                                                <input type="text" name="app_name" value="{{get_app_name()}}" class="form-control" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--app_name-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                App Short Name
                                            </div>
                                            <div class="col-sm-10">
                                                <input type="text" name="app_full_name" value="{{get_app_full_name()}}" class="form-control" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--show_love_letter_to_students-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                Show Love Letter To Students?
                                            </div>
                                            <div class="col-sm-10">
                                                <select name="show_love_letter_to_students">
                                                    <option value="1" {{(show_love_letter_to_students())? 'selected':''}}>Yes</option>
                                                    <option value="0" {{(show_love_letter_to_students())? '':'selected'}}>No</option>
                                                </select>
                                                <span>If yes, students can view/download love letters uploaded by ds while gradding the student.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--open_staff_registration-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                Allow new staff to register?
                                            </div>
                                            <div class="col-sm-10">
                                                <select name="open_staff_registration">
                                                    <option value="1" {{(check_reg_open_for_staff())? 'selected':''}}>Yes</option>
                                                    <option value="0" {{(check_reg_open_for_staff())? '':'selected'}}>No</option>
                                                </select>
                                                <span>If yes, New staff can create account on the system. (Note: You can set the date for start/end of student registration from course menu)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               

                                <h2>Permissions</h2>
                                <?php  $staff_roles = get_system_roles('all staff',1); ?> 
                                
                                <div class="col-12">
                                    <b><u>Exercises</u></b>
                                </div>                                
                                <!--view_all_exercise-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                View All Exercise
                                            </div>
                                            <div class="col-sm-10">
                                                <select name="view_all_exercise[]" multiple class="selector">
                                                    @if($staff_roles)
                                                        @foreach($staff_roles as $role)
                                                            <?php 
                                                                $role_code = $role['code'];
                                                                $role_name = $role['name'];
                                                                $selected = '';                                                                
                                                                if(check_has_ability('view_all_exercise',$role_code)){
                                                                    $selected = 'selected';
                                                                }
                                                            ?>
                                                            <option value="{{$role_code}}" {{$selected}}>{{$role_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <span>Selected staff can view list of exercises but cannot edit or delete.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--manage_exercise-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                Manage Exercise
                                            </div>
                                            <div class="col-sm-10">
                                                <select name="manage_exercise[]" multiple class="selector">
                                                    @if($staff_roles)
                                                        @foreach($staff_roles as $role)
                                                            <?php 
                                                                $role_code = $role['code'];
                                                                $role_name = $role['name'];
                                                                $selected = '';
                                                                if(check_has_ability('manage_exercise', $role_code)){
                                                                    $selected = 'selected';
                                                                }
                                                            ?>
                                                            <option value="{{$role_code}}" {{$selected}}>{{$role_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <span>Selected staff can add, edit, and delete exercises.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <hr>
                                </div>

                                <div class="col-12">
                                    <b>Users</b>
                                </div> 
                                <!--approve_user-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                Approve New User
                                            </div>
                                            <div class="col-sm-10">
                                                <select name="approve_user[]" multiple class="selector">
                                                    @if($staff_roles)
                                                        @foreach($staff_roles as $role)
                                                            <?php 
                                                                $role_code = $role['code'];
                                                                $role_name = $role['name'];
                                                                $selected = '';
                                                                if(check_has_ability('approve_user', $role_code)){
                                                                    $selected = 'selected';
                                                                }
                                                            ?>
                                                            <option value="{{$role_code}}" {{$selected}}>{{$role_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <span>Selected staff can approve a registration.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--view_students-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                View Students
                                            </div>
                                            <div class="col-sm-10">
                                                <select name="view_students[]" multiple class="selector">
                                                    @if($staff_roles)
                                                        @foreach($staff_roles as $role)
                                                            <?php 
                                                                $role_code = $role['code'];
                                                                $role_name = $role['name'];
                                                                $selected = '';
                                                                if(check_has_ability('view_students', $role_code)){
                                                                    $selected = 'selected';
                                                                }
                                                            ?>
                                                            <option value="{{$role_code}}" {{$selected}}>{{$role_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <span>Selected staff can view list of students on the system.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                                <!--view_staff-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                View Staff
                                            </div>
                                            <div class="col-sm-10">
                                                <select name="view_staff[]" multiple class="selector">
                                                    @if($staff_roles)
                                                        @foreach($staff_roles as $role)
                                                            <?php 
                                                                $role_code = $role['code'];
                                                                $role_name = $role['name'];
                                                                $selected = '';
                                                                if(check_has_ability('view_staff', $role_code)){
                                                                    $selected = 'selected';
                                                                }
                                                            ?>
                                                            <option value="{{$role_code}}" {{$selected}}>{{$role_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <span>Selected staff can view list of staff on the system.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--always_see_identity-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                Always See Student Identity
                                            </div>
                                            <div class="col-sm-10">
                                                <select name="always_see_identity[]" multiple class="selector">
                                                    @if($staff_roles)
                                                            @foreach($staff_roles as $role)
                                                            <?php 
                                                                $role_code = $role['code'];
                                                                $role_name = $role['name'];
                                                                $selected = '';
                                                                if(check_has_ability('always_see_identity', $role_code)){
                                                                    $selected = 'selected';
                                                                }
                                                            ?>
                                                            <option value="{{$role_code}}" {{$selected}}>{{$role_name}}</option>
                                                            @endforeach
                                                    @endif
                                                </select>
                                                <span>Selected staff can always see identity of students while grading or processing results. <span class="text-warning">(Please note, selected staff cannot do blind grading!).</span></span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!--can_view_user_detailed_profile-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                View User Detailed Profile
                                            </div>
                                            <div class="col-sm-10">
                                                <select name="can_view_user_detailed_profile[]" multiple class="selector">
                                                    @if($staff_roles)
                                                        @foreach($staff_roles as $role)
                                                            <?php 
                                                                $role_code = $role['code'];
                                                                $role_name = $role['name'];
                                                                $selected = '';
                                                                if(check_has_ability('can_view_user_detailed_profile', $role_code)){
                                                                    $selected = 'selected';
                                                                }
                                                            ?>
                                                            <option value="{{$role_code}}" {{$selected}}>{{$role_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <span>Selected staff can view all the information in a user profile. Staff not selected can only view the basic data in user profile.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--can_edit_user-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                Can Edit User Profile
                                            </div>
                                            <div class="col-sm-10">
                                                <select name="can_edit_user[]" multiple class="selector">
                                                    @if($staff_roles)
                                                            @foreach($staff_roles as $role)
                                                            <?php 
                                                                $role_code = $role['code'];
                                                                $role_name = $role['name'];
                                                                $selected = '';
                                                                if(check_has_ability('can_edit_user', $role_code)){
                                                                    $selected = 'selected';
                                                                }
                                                            ?>
                                                            <option value="{{$role_code}}" {{$selected}}>{{$role_name}}</option>
                                                            @endforeach
                                                    @endif
                                                </select>
                                                <span>Selected staff can edit user profile but cannot change profile picture, appointment, department or course</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!--can_change_user_picture-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                Can Change User Picture
                                            </div>
                                            <div class="col-sm-10">
                                                <select name="can_change_user_picture[]" multiple class="selector">
                                                    @if($staff_roles)
                                                            @foreach($staff_roles as $role)
                                                            <?php 
                                                                $role_code = $role['code'];
                                                                $role_name = $role['name'];
                                                                $selected = '';
                                                                if(check_has_ability('can_change_user_picture', $role_code)){
                                                                    $selected = 'selected';
                                                                }
                                                            ?>
                                                            <option value="{{$role_code}}" {{$selected}}>{{$role_name}}</option>
                                                            @endforeach
                                                    @endif
                                                </select>
                                                <span>Selected staff can upload/change profile pictures of users</span>
                                            </div>
                                        </div>

                                    </div>
                                </div> 
                                <!--can_change_user_role-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                Can Change User Appointment
                                            </div>
                                            <div class="col-sm-10">
                                                <select name="can_change_user_role[]" multiple class="selector">
                                                    @if($staff_roles)
                                                            @foreach($staff_roles as $role)
                                                            <?php 
                                                                $role_code = $role['code'];
                                                                $role_name = $role['name'];
                                                                $selected = '';
                                                                if(check_has_ability('can_change_user_role', $role_code)){
                                                                    $selected = 'selected';
                                                                }
                                                            ?>
                                                            <option value="{{$role_code}}" {{$selected}}>{{$role_name}}</option>
                                                            @endforeach
                                                    @endif
                                                </select>
                                                <span>Selected staff can change user appointment, department and course</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <!--reset_user_password-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                Reset User Password
                                            </div>
                                            <div class="col-sm-10">
                                                <select name="reset_user_password[]" multiple class="selector">
                                                    @if($staff_roles)
                                                        @foreach($staff_roles as $role)
                                                            <?php 
                                                                $role_code = $role['code'];
                                                                $role_name = $role['name'];
                                                                $selected = '';
                                                                if(check_has_ability('reset_user_password', $role_code)){
                                                                    $selected = 'selected';
                                                                }
                                                            ?>
                                                            <option value="{{$role_code}}" {{$selected}}>{{$role_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <span>Selected staff can reset password of a user account.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                                <!--delete_or_deactivate_user-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                Delete or Deactivate User
                                            </div>
                                            <div class="col-sm-10">
                                                <select name="delete_or_deactivate_user[]" multiple class="selector">
                                                    @if($staff_roles)
                                                        @foreach($staff_roles as $role)
                                                            <?php 
                                                                $role_code = $role['code'];
                                                                $role_name = $role['name'];
                                                                $selected = '';
                                                                if(check_has_ability('delete_or_deactivate_user', $role_code)){
                                                                    $selected = 'selected';
                                                                }
                                                            ?>
                                                            <option value="{{$role_code}}" {{$selected}}>{{$role_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <span>Selected staff can delete or deactivate a user account.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div> 


                                <div class="col-12">
                                    <hr>
                                </div>

                                <div class="col-12">
                                    <b>Academic Features</b>
                                </div> 
                                <!--manage_syndicate-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                Manage Syndicate
                                            </div>
                                            <div class="col-sm-10">
                                                <select name="manage_syndicate[]" multiple class="selector">
                                                    @if($staff_roles)
                                                        @foreach($staff_roles as $role)
                                                            <?php 
                                                                $role_code = $role['code'];
                                                                $role_name = $role['name'];
                                                                $selected = '';
                                                                if(check_has_ability('manage_syndicate', $role_code)){
                                                                    $selected = 'selected';
                                                                }
                                                            ?>
                                                            <option value="{{$role_code}}" {{$selected}}>{{$role_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <span>Selected staff can add, edit, delete syndicates and students in the syndicates.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--manage_dept_and_div-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                Manage Dept & Div
                                            </div>
                                            <div class="col-sm-10">
                                                <select name="manage_dept_and_div[]" multiple class="selector">
                                                    @if($staff_roles)
                                                        @foreach($staff_roles as $role)
                                                            <?php 
                                                                $role_code = $role['code'];
                                                                $role_name = $role['name'];
                                                                $selected = '';
                                                                if(check_has_ability('manage_dept_and_div', $role_code)){
                                                                    $selected = 'selected';
                                                                }
                                                            ?>
                                                            <option value="{{$role_code}}" {{$selected}}>{{$role_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <span>Selected staff can add, edit and delete departments and divisions in the system.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>  
                                <!--manage_term_session_and_course-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                Manage Term, Session & Course
                                            </div>
                                            <div class="col-sm-10">
                                                <select name="manage_term_session_and_course[]" multiple class="selector">
                                                    @if($staff_roles)
                                                        @foreach($staff_roles as $role)
                                                            <?php 
                                                                $role_code = $role['code'];
                                                                $role_name = $role['name'];
                                                                $selected = '';
                                                                if(check_has_ability('manage_term_session_and_course', $role_code)){
                                                                    $selected = 'selected';
                                                                }
                                                            ?>
                                                            <option value="{{$role_code}}" {{$selected}}>{{$role_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <span>Selected staff can add, edit and delete Term, Session & Course in the system.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>                                 

                                <div class="col-12">
                                    <hr>
                                </div>
                                <div class="col-12">
                                    <b>Others</b>
                                </div>  
                                <!--manage_event-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                Manage Calendar Events
                                            </div>
                                            <div class="col-sm-10">
                                                <select name="manage_event[]" multiple class="selector">
                                                    @if($staff_roles)
                                                        @foreach($staff_roles as $role)
                                                            <?php 
                                                                $role_code = $role['code'];
                                                                $role_name = $role['name'];
                                                                $selected = '';
                                                                if(check_has_ability('manage_event', $role_code)){
                                                                    $selected = 'selected';
                                                                }
                                                            ?>
                                                            <option value="{{$role_code}}" {{$selected}}>{{$role_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <span>Selected staff can add, edit, and delete events in calendar.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--manage_forms-->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">  
                                            <div class="col-sm-2">
                                                Manage Forms
                                            </div>
                                            <div class="col-sm-10">
                                                <select name="manage_forms[]" multiple class="selector">
                                                    @if($staff_roles)
                                                        @foreach($staff_roles as $role)
                                                            <?php 
                                                                $role_code = $role['code'];
                                                                $role_name = $role['name'];
                                                                $selected = '';
                                                                if(check_has_ability('manage_forms', $role_code)){
                                                                    $selected = 'selected';
                                                                }
                                                            ?>
                                                            <option value="{{$role_code}}" {{$selected}}>{{$role_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <span>Selected staff can add, edit, and delete forms.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <button class="btn btn-primary btn-lg btn-simple">Save Configurations</button>
                                </div>
                            </div>                            
                        </form>
                    </div>
                </div>
            </div>
		</div>
	
    </div>
</div>

<script>
	document.multiselect('.selector');
</script>

@endsection