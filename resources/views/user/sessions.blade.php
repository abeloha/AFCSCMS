@extends('layout')

<?php 
    $page_title = 'Session';
    $courses = get_course();
    $depts = get_dept();
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
                    
                    <?php
                        $msg = '';
                        if(isset($_GET['msg'])){
                            $msg = $_GET['msg'];
                        }
                    ?>
                    @if($msg)
                        <div class="col-md-12">
                            <div class="alert alert-success">
                                <p>{{$msg}}</p>
                            </div>
                        </div>
                    @endif

                    @foreach($courses as $course)

                        <?php 
                            $sessions = get_session_by_course($course->id);
                        ?>
                        <div class="col-md-6">
                            
                            <div class="card">
                            
                                <div class="card-header">
                                    <h3 class="card-title">{{$course->name}} Students</h3>
                                    <div class="card-options">
                                        <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                                        <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
                                    </div>
                                </div>

                                <div class="card-body">
                                   <h4>Select Session</h4>
                                   @if(count($sessions))

                                        <form action="{{url('user/archive')}}" method="GET" )" >   
                                            <div class="row">

                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label>Select Session</label>
                                                        <select name="session" id="session" class="form-control" required>
                                                            <option value="">Select Session</option>
                                                            @foreach ($sessions as $session)
                                                                <option value="{{$session->id}}">{{$session->name}}</option>
                                                            @endforeach                                                   
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label>Select Department</label>
                                                        <select name="dept" id="dept" class="form-control">
                                                            <option value="">All Department</option> 
                                                            @if($depts)                                                            
                                                                @foreach ($depts as $dept)
                                                                    <option value="{{$dept->id}}">{{$dept->name}}</option>
                                                                @endforeach 
                                                            @endif                                                                                                             
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12">
                                                    <div class="form-group" style="margin-top: 25px;">
                                                        <button class="btn btn-primary btn-lg btn-simple">Search Students Records</button>
                                                    </div>
                                                </div>

                                            </div>
                                        </form>

                                    @else
                                        <i>No academic session</i>
                                   @endif                                    
                                </div>

                            </div>
                        </div>
                    @endforeach


                </div>
            </div>

		</div>
	</div>
</div>


@endsection