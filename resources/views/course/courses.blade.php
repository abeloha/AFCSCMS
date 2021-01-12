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
            <ul class="nav nav-tabs page-header-tab">
                <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#Library-all">List View</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#Library-add">Add</a></li>
            </ul>
		</div>
	</div>
</div>

<div class="section-body mt-4">
    <div class="container-fluid">
	
		<div class="tab-content">
			<div class="tab-pane active" id="Library-all">

                <?php
                    $n = 1;

                    $added = false;
                    if(isset($_GET['added'])){
                        $added = true;
                    }
                    
                    $deleted = false;
                    if(isset($_GET['deleted'])){
                        $deleted = true;
                    } 
                ?>
                @if($added)
                    <div class="alert alert-info">
                        <p>New course has been added successfully</p>
                    </div>
                @endif

                @if($deleted)
                    <div class="alert alert-info">
                        <p>Course deleted successfully</p>
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

                        @if($courses)
                            <div class="table-responsive">
                                <table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Current Term</th>
                                            <th>Current Session</th>
                                            <th>Ex Enrollment</th>
                                            <th>Reg Start</th>
                                            <th>Reg End</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($courses as $course)
                                            <?php
                                                $term = '-';
                                                $session = '-';

                                                if($course->current_term_id){
                                                    $terms = get_term($course->current_term_id);
                                                    if($terms) $term=$terms->name;
                                                }
                                                if($course->current_session_id){
                                                    $sessions = get_session($course->current_session_id);
                                                    if($sessions) $session=$sessions->name;
                                                }
                                            ?>
                                            <tr>
                                                <td>{{$n++}}</td>
                                                <td>{{$course->name}}</td>
                                                <td>{{$term}}</td>
                                                <td>{{$session}}</td>
                                                <td>{{($course->exercise_enrollment)? 'Enabled' : 'Disabled'}}</td>

                                                <td>
                                                    @if($course->reg_start_at)
                                                        {{date( 'd M, Y',strtotime($course->reg_start_at))}}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($course->reg_end_at) 
                                                        {{date( 'd M, Y',strtotime($course->reg_end_at))}}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{url('course/'.$course->id)}}"><button type="button" class="btn btn-icon btn-sm" title="Edit"><i class="fa fa-edit"></i></button></a>
                                                    <a href="{{url('course/'.$course->id.'/delete')}}" onclick="return confirm('Are you Sure you want to delete this record?')"><button type="button" class="btn btn-icon btn-sm" title="Delete"><i class="fa fa-trash-o text-danger"></i></button></a>
                                                </td>
                                            </tr>
                                        @endforeach                                        
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p>No course added yet</p>
                        @endif                        

					</div>
				</div>
			</div>
		
			<div class="tab-pane" id="Library-add">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Add Course</h3>
						<div class="card-options ">
							<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
						</div>
					</div>
					<div class="card-body">
                        <form method="POST" action="{{url('course')}}">
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
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Name of Course</label>
                                        <input type="text" name="name" value="" placeholder="Enter Name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <button class="btn btn-primary btn-lg btn-simple">Add</button>
                                </div>
                            </div>
                        </form>
					</div>
				</div>
			</div>
		
		</div>
	
    </div>
</div>
    


@endsection