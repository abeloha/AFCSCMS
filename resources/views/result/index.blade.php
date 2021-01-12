@extends('layout')

<?php 
    $page_title = 'Results';
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
                            $n = 0; 
                            $sessions = get_realeased_result_sessions($course->id);
                        ?>
                        <div class="col-md-6">
                            
                            <div class="card">
                            
                                <div class="card-header">
                                    <h3 class="card-title">{{$course->name}} Results</h3>
                                    <div class="card-options">
                                        <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                                        <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
                                        <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
                                    </div>
                                </div>

                                <div class="card-body">
                                   <h4>Select Session</h4>
                                   @if(count($sessions))
                                        <ol>
                                            @foreach ($sessions as $session)
                                                <li><a href="{{url('result/session/'.$session->id)}}">{{$session->name}}</a></li>
                                            @endforeach
                                        </ol>
                                    @else
                                        <i>No session with released results</i>
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