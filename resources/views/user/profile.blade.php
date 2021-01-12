@extends('layout')

<?php 
    $page_title = 'User Account';
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
		 
            <div class="tab-pane active" id="Student-profile">
                <div class="row">                    

                    @if($data)

                        <?php
							$appointment = role_name($data->role);
                            $can_edit = 1; $can_view = 1;
                            $can_approve = check_can_approve_user_profile();
                            $my_account = 1;

                            $user_id = get_user_id();

                            $can_mail = 0;
	
                            if($data->id != $user_id){
                                $can_edit = check_can_edit_user_profile(); 
                                $can_view = check_can_view_user_profile();
                                $my_account = 0;

                                if( $data->approved && !$data->deactivated && is_current_student($data->id)){
                                        $can_mail = 1;
                                }    
                            }

                            $success = false;
                            if(isset($_GET['success'])){
                                $success = true;
                            }
                        ?>
                        
                        @if($success)
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <p>Action is successful</p>
                                </div>
                            </div>
                        @endif
                        
                        <div class="col-xl-4 col-md-12">
                            <div class="card">
                                <div class="card-body w_user">
                                    <div class="user_avtar">
                                        <?php
                                            $img = 'default.png';
                                            if($data->picture){
                                                $img = $data->picture;
                                            }
                                        ?>
                                        <img class="rounded-circle" src="{{asset('storage/user/'.$img)}}" alt="pix">
                                        <span id="change_picture_link">
                                        @if(check_can_change_user_picture())
                                        <br>
                                            <a href="{{url('user/changepicture/'.$data->id)}}">
                                                <small>Change Picture</small>
                                            </a>
                                        @endif
                                        </span>
                                    </div>
                                    <div class="wid-u-info">

                                        <h5>{{$data->rank}} {{$data->surname.' '.$data->first_name.' '.$data->other_name}}</h5>
                                        <p class="text-muted m-b-0">
                                            {{$appointment}}                                        
                                        </p>

                                        <ul class="list-unstyled" id="options_link">
                                            <li onclick="doPrint()">
                                                <h5 class="mb-0"><i class="dropdown-icon fa fa-print"></i></h5>
                                                <small>Print</small>
                                            </li>

                                            @if($can_edit)
                                                <li>
                                                    <a href="{{url('user/edit/'.$data->id)}}">
                                                        <h5 class="mb-0"><i class="dropdown-icon fa fa-edit"></i></h5>
                                                        <small>Edit</small>
                                                    </a>
                                                </li>                                                
                                            @endif

                                            @if($data->approved)
                                                <li>
                                                    <h5 class="mb-0"><i class="dropdown-icon fa fa-check"></i></h5>
                                                    <small>Approved</small>
                                                </li>
                                            @else
                                                <li>
                                                    <h5 class="mb-0"><i class="dropdown-icon fa fa-check" style="color:#b5b5b6;"></i></h5>
                                                    <small>Not approved</small>
                                                </li> 
                                                @if($can_approve)                                                                                                    
                                                    <li>
                                                        <a href="{{url('user/approve/'.$data->id)}}">
                                                            <h5 class="mb-0"><i class="dropdown-icon fa fa-check" style="color:red;"></i></h5>
                                                            <small>Click to Approved</small>
                                                        </a>
                                                    </li>
                                                @endif
                                            @endif
                                            
                                        </ul>

                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Basic Data</h3>
                                    <div class="card-options ">
                                    <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                                    <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <b>Rank </b>
                                            <div class="pull-right">{{$data->rank}}</div>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Service Number </b>
                                            <div class="pull-right">{{$data->svc_no}}</div>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Surname </b>
                                            <div class="pull-right">{{$data->surname}}</div>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Other Name </b>
                                            <div class="pull-right">{{$data->first_name}} {{$data->other_name}}</div>
                                        </li> 
                                        <li class="list-group-item">
                                            <?php $dept = get_dept($data->dept_id); ?>
                                            <b>Department</b>
                                            <div class="pull-right">{{($dept)? $dept->name : '-'}}</div>
                                        </li>                                       						
                                        <li class="list-group-item">
                                            <b>Appointment </b>
                                            <div class="pull-right">{{$appointment}} </div>
                                        </li>
                                        @if ($data->role == 1)
                                            <?php 
                                                $course = get_course($data->course_id); 
                                                $session = get_session($data->session_id); 
                                                $syndicate = get_student_syndicate($data->id);
                                            ?>
                                            <li class="list-group-item">
                                                <b>Course </b>                                            
                                                <div class="pull-right">{{($course)? $course->name : '-'}} </div>
                                            </li>

                                            <li class="list-group-item">
                                                <b>Session </b>                                            
                                                <div class="pull-right">{{($session)? $session->name : '-'}} </div>
                                            </li>

                                            <li class="list-group-item">
                                                <b>Syndicate </b>                                            
                                                <div class="pull-right">{{($syndicate)? $syndicate->name : '-'}} </div>
                                            </li>
                                        @endif
                                        <li class="list-group-item">
                                            <b>Service </b>
                                            <div class="pull-right">{{$data->service}}</div>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Email </b>
                                            <div class="pull-right">{{$data->email}}</div>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Phone </b>
                                            <div class="pull-right">{{$data->phone}}</div>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Sex </b>
                                            <div class="pull-right">{{$data->sex}}</div>
                                        </li> 
                                        <li class="list-group-item">
                                            <b>Country </b>
                                            <div class="pull-right">{{$data->country}}</div>
                                        </li> 
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-8 col-md-12">                            
                            @if($can_view)

                                @if($my_account && !$data->approved)
                                    <div style="background-color:red; color:white; text-align:center; padding:12px; margin:5px;">Complete your profile and go to admin office for approval.</div> 
                                @endif

                                @if($data->deactivated)
                                    <div style="background-color:red; color:white; text-align:center; padding:12px; margin:5px;">This account has been deactivated.</div> 
                                @endif

                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Other Details</h3>
                                        <div class="card-options ">
                                            <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                                            <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
                                        </div>		
                                    </div>
                                    <div class="card-body"> 
                                        <b>Corps:</b> {{$data->corps}}
                                        <br><b>Branch:</b> {{$data->branch}}
                                        <br><b>Branch:</b> {{$data->branch}}
                                        <br><b>Specialty:</b> {{$data->specialty}}
                                        <br><b>Commission:</b> {{$data->commission}}

                                        <hr>
                                        <b>Bank Name:</b> {{$data->bank}}
                                        <br><b>Bank Account:</b> {{$data->account}}

                                        <hr>
                                        <b>Last Unit:</b> {{$data->last_unit_1}}
                                        <br><b>Last Appointment:</b> {{$data->last_appointment_1}}
                                        <br><b>Second Last Unit:</b> {{$data->last_unit_2}}
                                        <br><b>Second Last Appointment:</b> {{$data->last_appointment_2}}

                                    </div>
                                </div>
                            @endif

                            
                            @if($can_mail)
                                <div class="card" id="quickmail">
                                    <div class="card-header">
                                        <h3 class="card-title">Quick Mail</h3>
                                    </div>                                
                                    <div class="card-body">                                    
                                        <a href="{{url('mail/compose?to='.$data->id)}}"><button class="btn btn-default mt-3">Send Mail To {{$data->surname}}</button></a>
                                    </div>
                                </div>
                            @endif
                                    
                        </div>

                    @else
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <p>Account details could not be loaded now</p>
                                </div>
                            </div>
                        </div>
                    @endif
                
                </div>
            </div>

		</div>
	</div>
</div>

<script>
    function doPrint(){        
        $("#options_link").hide();
        $("#quickmail").hide();
        $("#change_picture_link").hide();
        window.print();
        $("#change_picture_link").show();
        $("#quickmail").show();
        $("#options_link").show();
    }
</script>

@endsection