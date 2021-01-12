<!doctype html>
<html lang="en" dir="ltr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="icon" href="{{asset('assets/images/favicon.ico')}}" type="image/x-icon" />
	<title>{{get_app_name()}} - @yield('title', get_app_full_name())</title>

	<link rel="stylesheet" href="{{asset('assets/plugins/bootstrap/css/bootstrap.min.css')}}" />
	<link rel="stylesheet" href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')}}">
	<link rel="stylesheet" href="{{asset('assets/css/style.min.css')}}" />
	
	<!-- Data Tables
	<link rel="stylesheet" href="{{asset('assets/datatable/jquery.dataTables.min.css')}}">
	 -->
	<!-- DataTables CSS -->
    <link rel="stylesheet" href="{{asset('assets/datatables/css/dataTables.bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/datatables/css/buttons.bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/datatables/css/responsive.bootstrap.min.css')}}">
	
	
	<script src="{{asset('assets/js/jquery-1.11.0.min.js')}}"></script>

	<!-- WYSIWYG Editor -->
	<script src="{{asset('assets/js/ckeditor/ckeditor.js')}}"></script>
	<script src="{{asset('assets/js/ckeditor/sample.js')}}"></script>

	<!--margic search -->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/js/autocomplete/jquery.magicsearch.css')}}"/>

	<script>
		// Create a countdown"
		function createCountDown(divId, stopDate){
			//console.log('Function Counter started:');
			var countDownDate = new Date(stopDate).getTime();                                                        
			// Update the count down every 1 second
			var x = setInterval(function() {                                                        
			// Get today's date and time
			var now = new Date().getTime();                                                        
			// Find the distance between now and the count down date
			var distance = countDownDate - now;                                                        
			// Time calculations for days, hours, minutes and seconds
			var days = Math.floor(distance / (1000 * 60 * 60 * 24));
			var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			var seconds = Math.floor((distance % (1000 * 60)) / 1000);                                                        
			// Display the result in the element with id="demo"
			document.getElementById(divId).innerHTML = days + "d " + hours + "h "
			+ minutes + "m " + seconds + "s ";                                                        
			// If the count down is finished, write some text
			if (distance < 0) {
				clearInterval(x);
				document.getElementById(divId).innerHTML = "EXPIRED";
			}
			}, 1000);
		} 			
	</script>

</head>
<body class="font-muli theme-cyan gradient">

<?php
	
	$can_view_students = check_has_ability('view_students');
	$can_view_staff = check_has_ability('view_staff');
	$can_manage_exercise = check_has_ability('manage_exercise');
	$is_academic = is_academic();
	$can_manage_syndicates = check_has_ability('manage_syndicate');
	$can_manage_dept_and_div = check_has_ability('manage_dept_and_div');
	$can_manage_term_session_and_course = check_has_ability('manage_term_session_and_course');
	$can_approve_user_profile = check_can_approve_user_profile();

	$current_url = get_current_url();
	$second_menu = array('depts','terms','sessions','courses','syndicates','dept','term','session','course','syndicate','user/deactivated','user/archive','configuration');
	$show_second_link = 0;
	$show_second_menu = 0;

	$can_activate_user = check_has_ability('delete_or_deactivate_user');

	$can_configure = check_has_ability('can_manage_config');

	if($can_manage_term_session_and_course || $can_manage_dept_and_div || $can_manage_syndicates || $can_activate_user || $can_view_students || $can_configure){
		$show_second_link = 1;
		
		if(in_array($current_url, $second_menu)){
			$show_second_menu = 1;
		}
	}

	$approved_active_enabled = 0;	

	if( check_user_approved() && check_user_active() && is_current_student()){
		$approved_active_enabled = 1;
	}

	$count_new_reg_student = count_new_reg('student');
    $count_new_reg_staff = count_new_reg('staff');
?>

<div id="main_content">

	<div id="header_top" class="header_top">
		<div class="container">
			<div class="hleft">
				<a class="header-brand" href="{{url('/')}}"><img src="{{asset('assets/images/logo.png')}}"></a>
				<div class="dropdown">
					<a href="javascript:void(0)" title="Menu" class="nav-link icon menu_toggle"><i class="fe fe-align-center"></i></a>
				</div>	
				@if($approved_active_enabled)	
					<a href="{{url('/')}}" title="Dashboard" class="nav-link icon settingbar"><i class="fa fa-dashboard"></i></i></a>		
					<a href="{{url('user')}}" title="My profile" class="nav-link icon settingbar"><i class="fa fa-user"></i></i></a>
					<a href="{{url('mail')}}" title="Inbox" class="nav-link icon settingbar"><i class="fa fa-envelope"></i></i></a>
				@endif
			</div>
			<div class="hright">
				<a href="{{url('logout')}}" title="Logout" class="nav-link icon settingbar"><i class="fe fe-power"></i></a>
			</div>
		</div>
	</div>

	@if($can_view_students)
		<?php
			$courses = get_course();
			$depts = get_dept();
			$divs = get_div();
		?>
		
		
		<div id="rightsidebar" class="right_sidebar">
			<a href="javascript:void(0)" class="p-3 settingbar float-right"><i class="fa fa-close"></i></a>
			<ul class="nav nav-tabs" role="tablist">
				<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#Settings" aria-expanded="true">Depts</a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#activity1" aria-expanded="false">Div/Course</a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#activity" aria-expanded="false">Syndicate</a></li>
			</ul>			
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane vivify fadeIn active" id="Settings" aria-expanded="true">
				
					<!--
					<div class="mb-4">
						<h6 class="font-14 font-weight-bold text-muted">Theme Color</h6>
						<ul class="choose-skin list-unstyled mb-0">
							<li data-theme="azure"><div class="azure"></div></li>
							<li data-theme="indigo"><div class="indigo"></div></li>
							<li data-theme="purple"><div class="purple"></div></li>
							<li data-theme="orange"><div class="orange"></div></li>
							<li data-theme="green"><div class="green"></div></li>
							<li data-theme="cyan" class="active"><div class="cyan"></div></li>
							<li data-theme="blush"><div class="blush"></div></li>
							<li data-theme="white"><div class="bg-white"></div></li>
						</ul>
					</div>
					-->
					
					<div>
						<h6 class="font-14 font-weight-bold mt-4">Students by Department</h6>
							@if($depts)	
								@foreach ($depts as $dept)
									<h6 class="font-14 font-weight-bold mt-4 text-muted">Students in {{$dept->name}}</h6>
									<ul class="setting-list list-unstyled mt-1 setting_switch">
										<li>
											<a href="{{url('user/student?dept='.$dept->id)}}">Students</a>
										</li>
										@if($courses)	
											@foreach ($courses as $course)
												<li>
													<a href="{{url('user/student?dept='.$dept->id.'&course='.$course->id)}}">{{$course->name}}</a>
												</li>
											@endforeach	
										@endif		
									</ul>	
								@endforeach
							@else
								<li><i>No department added</i></li>
							@endif						
					</div>
					
				</div>
				<div role="tabpanel" class="tab-pane vivify fadeIn" id="activity" aria-expanded="false">
					<div>
						<h6 class="font-14 font-weight-bold mt-4">Students by Syndicate</h6>
						<ul class="setting-list list-unstyled mt-1 setting_switch">
							<li>
								<a href="{{url('syndicates')}}"><b>Open List of Syndicates</b></a>
							</li>
						</ul>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane vivify fadeIn" id="activity1" aria-expanded="false">
					<div>
						<h6 class="font-14 font-weight-bold mt-4">Students by Div</h6>
						<ul class="setting-list list-unstyled mt-1 setting_switch">	
							@if($divs)				
								@foreach ($divs as $div)
									<li>
										<a href="{{url('user/student?div='.$div->id)}}">{{$div->name}}</a>
									</li>
								@endforeach	
							@else
								<li><i>No division added</i></li>
							@endif
						</ul>

						<h6 class="font-14 font-weight-bold mt-4">Students by Course</h6>
						<ul class="setting-list list-unstyled mt-1 setting_switch">	
							@if($courses)				
								@foreach ($courses as $course)
									<li>
										<a href="{{url('user/student?course='.$course->id)}}">{{$course->name}}</a>
									</li>
								@endforeach
							@else
								<li><i>No course added</i></li>
							@endif						
						</ul>
					</div>
				</div>
					
			</div>
		</div>	
			
	@endif

	<div id="left-sidebar" class="sidebar">
		
		<h5 class="brand-name">{{get_app_name()}}<a href="javascript:void(0)" class="menu_option float-right"><i class="icon-grid font-16" data-toggle="tooltip" data-placement="left" title="Grid & List Toggle"></i></a></h5>

		<ul class="nav nav-tabs">
			<li class="nav-item"><a class="nav-link {{($show_second_menu)? '' : 'active' }}" data-toggle="tab" href="#menu-uni">Menu</a></li>
			
			@if($show_second_link)
				<li class="nav-item"><a class="nav-link {{($show_second_menu)? 'active' : '' }}" data-toggle="tab" href="#menu-admin" id="second-menu">Extra</a></li>
			@endif
			
		</ul>
		
		<div class="tab-content mt-3">
			<div class="tab-pane fade {{($show_second_menu)? '' : 'show active' }}" id="menu-uni" role="tabpanel">
				<nav class="sidebar-nav">
					<ul class="metismenu">
						<li class="g_heading">Account</li>

						@if($approved_active_enabled)

							<li class="{{($current_url == 'user/dashboard')? 'active' : ''}}"><a href="{{url('user/dashboard')}}"><i class="fa fa-dashboard"></i><span>Dashboard</span></a></li>

							@if($can_view_students)
								<li class="{{($current_url == 'user/student')? 'active' : ''}}"><a href="{{url('user/student')}}"><i class="fa fa-user-circle"></i><span>All Students</span></a></li>
								<li><a href="javascript:void(0)" class="nav-link icon settingbar"><i class="fa fa-users"></i><span>Students by Category</span></a></li>
							@endif

							@if($can_view_staff)
								<li class="{{($current_url == 'user/staff')? 'active' : ''}}"><a href="{{url('user/staff')}}"><i class="fa fa-black-tie"></i><span>Staff</span></a></li>
							@endif
							
							<li class="g_heading">Academic</li>

							@if(is_student())
								<li><a href="{{url('exercise')}}"><i class="fa fa-book"></i><span>Exercises</span></a></li>
								<?php $show_exercise_reg = is_my_exercise_reg_open(); ?>
								
								@if($show_exercise_reg)
									<li><a href="{{url('exercise/enroll')}}"><i class="fa fa-book"></i><span>Exercises Enrollment</span></a></li>
								@endif

								@if(show_love_letter_to_students())
									<li><a href="{{url('loveletters')}}"><i class="fa fa-commenting-o"></i><span>Love Letters</span></a></li>
								@endif
							@endif	
							
							@if($is_academic || $can_manage_exercise)
								<li class="{{($current_url == 'exercise/list')? 'active' : ''}}"><a href="{{url('exercise/list')}}"><i class="fa fa-book"></i><span>Manage Exercises</span></a></li>
							@endif

							@if($is_academic)
								<li class="{{($current_url == 'result')? 'active' : ''}}"><a href="{{url('result/')}}"><i class="fa fa-bar-chart"></i><span>Results</span></a></li>
								<li class="{{($current_url == 'result/processing')? 'active' : ''}}"><a href="{{url('result/processing')}}"><i class="fa fa-bar-chart-o"></i><span>Results Processing</span></a></li>
							@endif

							@if($can_approve_user_profile)
								<li class="g_heading">New Registrations</li>
								<li class="{{($current_url == 'user/new')? 'active' : ''}}"><a href="{{url('user/new?type=student')}}"><i class="fa fa-user-circle text-warning"></i><span>New Students <span style="background-color:red;border-radius:12px; font-size:10px;">{{$count_new_reg_student}}</span> </span></a></li>
								<li class="{{($current_url == 'user/new')? 'active' : ''}}"><a href="{{url('user/new?type=staff')}}"><i class="fa fa-black-tie text-warning"></i><span>New Staff <span style="background-color:red;border-radius:12px; font-size:10px;">{{$count_new_reg_staff}}</span></span></a></li>
							@endif
									
							<li class="{{($current_url == 'forms')? 'active' : ''}}"><a href="{{url('forms')}}"><i class="fa fa-wpforms"></i><span>Forms</span></a></li>
						@else	
							<li class="{{($current_url == 'user')? 'active' : ''}}"><a href="{{url('user')}}"><i class="fa fa-user"></i><span>My Account</span></a></li>
						@endif
						
					</ul>
				</nav>
			</div>
			<div class="tab-pane fade {{($show_second_menu)? 'show active' : '' }}" id="menu-admin" role="tabpanel">
				<nav class="sidebar-nav">
					<ul class="metismenu">	
						@if($approved_active_enabled)
							<li class="g_heading">Academic</li>
							@if($can_view_students)								
								<li><a href="{{url('user/archive')}}"><i class="fa fa-user-circle"></i><span>Archived Students</span></a></li>
							@endif
							@if($can_manage_dept_and_div)								
								<li class="{{($current_url == 'depts')? 'active' : ''}}"><a href="{{url('depts')}}"><i class="fa fa-building"></i><span>Depts/Divs</span></a></li>
							@endif
							@if($can_manage_syndicates)
								<li class="{{($current_url == 'syndicates')? 'active' : ''}}"><a href="{{url('syndicates')}}"><i class="fa fa-address-book"></i><span>Syndicates</span></a></li>
							@endif
							@if($can_manage_term_session_and_course)	
								<li class="g_heading">Extra</li>
								<li class="{{($current_url == 'terms')? 'active' : ''}}"><a href="{{url('terms')}}"><i class="fa fa-calendar-o"></i><span>Terms</span></a></li>
								<li class="{{($current_url == 'sessions')? 'active' : ''}}"><a href="{{url('sessions')}}"><i class="fa fa-calendar"></i><span>Sessions</span></a></li>
								<li class="{{($current_url == 'courses')? 'active' : ''}}"><a href="{{url('courses')}}"><i class="fa fa-graduation-cap"></i><span>Courses</span></a></li>						
							@endif
							@if($can_configure)
								<li class="{{($current_url == 'configuration')? 'active' : ''}}"><a href="{{url('configuration')}}"><i class="fa fa-gear"></i><span>Configuration</span></a></li>
							@endif							
							@if($can_activate_user)								
								<li style="border-top: 2px solid red"><a href="{{url('user/deactivated?type=student')}}"><i class="fa fa-user-circle text-danger"></i><span>Deactivated Students  </span></a></li>
								<li><a href="{{url('user/deactivated?type=staff')}}"><i class="fa fa-black-tie text-danger"></i><span>Deactivated Staff </span></a></li>
							@endif
						@endif
					</ul>
				</nav>
			</div>
		</div>

	</div>

	<div class="page">

		<div class="section-body" id="page_top">
			<div class="container-fluid">
				<div class="page-header">
					<div class="left">
						<h3>{{get_app_full_name()}}</h3>
					</div>
					@include('include-notification')
				</div>
			</div>
		</div>

		<?php
			$unauthorised = false;
			$unauthorisedMsg = '';
			if(isset($_GET['unauthorised'])){
				$unauthorised = true;
				$unauthorisedMsg = $_GET['unauthorised'];
			}
		?>

		@if($unauthorised)
			<div class="section-body mt-4">
				<div class="container-fluid">
					<div class="tab-content">
					
						<div class="tab-pane active" id="Student-profile">
							<div class="row"> 

								<div class="col-md-12">
									<div class="alert alert-danger">
										<p>
											You do not have authorization for the action.
											@if($unauthorisedMsg)
												(Diagnosis Code: {{$unauthorisedMsg}})
											@endif
										</p>
									</div>
								</div>
							</div>
						</div>
			
					</div>
				</div>
			</div>
		@endif

		@section('content')
			@show
	
		<div class="section-body">
			<footer class="footer">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12 col-sm-12">
							{{get_app_full_name()}}
						</div>
					</div>
				</div>
			</footer>
		</div>
	</div>
</div>

<script src="{{asset('assets/bundles/lib.vendor.bundle.js')}}" type="67e876cca3725cb1e347d08d-text/javascript"></script>
<script src="{{asset('assets/bundles/counterup.bundle.js')}}" type="67e876cca3725cb1e347d08d-text/javascript"></script>

<script src="{{asset('assets/js/core.js')}}" type="67e876cca3725cb1e347d08d-text/javascript"></script>

<script src="{{asset('assets/js/autocomplete/jquery.magicsearch.js')}}"></script>

@section('script-content')
	@show  
	
<script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="1778ab067d66474dd41d31ba-text/javascript"></script>
<script src="{{asset('assets/plugins/sweetalert/sweetalert.min.js')}}" type="1778ab067d66474dd41d31ba-text/javascript"></script>
<script src="{{asset('assets/js/page/dialogs.js')}}" type="1778ab067d66474dd41d31ba-text/javascript"></script>
<script src="{{asset('assets/plugins/cloudflare-static/rocket-loader.min.js')}}" data-cf-settings="67e876cca3725cb1e347d08d-|49" defer=""></script>


<script src="{{asset('assets/datatables/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/datatables/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('assets/datatables/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('assets/datatables/js/buttons.bootstrap.min.js')}}"></script>
<script src="{{asset('assets/datatables/js/buttons.flash.min.js')}}"></script>
<script src="{{asset('assets/datatables/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('assets/datatables/js/buttons.print.min.js')}}"></script>
<script src="{{asset('assets/datatables/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/datatables/js/responsive.bootstrap.min.js')}}"></script>
<script src="{{asset('assets/datatables/js/jszip.min.js')}}"></script>
<script src="{{asset('assets/datatables/js/pdfmake.min.js')}}"></script>
<script src="{{asset('assets/datatables/js/vfs_fonts.js')}}"></script>
	
<script src="{{asset('assets/datatables/advance_table_custom.js')}}"></script>
		
<!--Designed with love by Onuoha Abel (+2347034481876, abonuoha@gmail.com, Suprix Technology) -->
</body>

</html>
