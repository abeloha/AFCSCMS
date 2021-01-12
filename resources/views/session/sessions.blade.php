@extends('layout')

<?php 
    $page_title = 'Sessions';
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
                        <p>New Session has been added successfully</p>
                    </div>
                @endif

                @if($deleted)
                    <div class="alert alert-info">
                        <p>Session deleted successfully</p>
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

                        @if($sessions)
                            <div class="table-responsive">
                                <table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Course</th> 
                                            <th>Created at</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sessions as $session)
                                            <?php
                                                $course = '-';
                                                
                                                $courses = get_course($session->course_id);
                                                if($courses) $course=$courses->name;
                                                
                                            ?>
                                            <tr>
                                                <td>{{$n++}}</td>
                                                <td>{{$session->name}}</td>
                                                <td>{{$course}}</td>
                                                <td>{{$session->created_at}}</td>
                                                <td>
                                                    <a href="{{url('session/'.$session->id.'/delete')}}" onclick="return confirm('Are you Sure you want to delete this record?')"><button type="button" class="btn btn-icon btn-sm" title="Delete"><i class="fa fa-trash-o text-danger"></i></button></a>
                                                </td>
                                            </tr>
                                        @endforeach                                        
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p>No session added yet</p>
                        @endif                        

					</div>
				</div>
			</div>
		
			<div class="tab-pane" id="Library-add">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Add Session</h3>
						<div class="card-options ">
							<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
						</div>
					</div>
					<div class="card-body">
                        <form method="POST" action="{{url('session')}}">
                            <?php
                                $courses = get_course()
                            ?>
                            @if($courses)
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
                                            <label>Name</label>
                                            <input type="text" name="name" value="" placeholder="Enter Name" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Course</label>
                                            <select name="course" class="form-control" required>
                                                <option value="">Select</option>
                                                @foreach ($courses as $course)
                                                    <option value="{{$course->id}}">{{$course->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-12">
                                        <button class="btn btn-primary btn-lg btn-simple">Add</button>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <p>Add courses first before you can add session!</p>                            
                                </div> 
                            @endif
                        </form>
					</div>
				</div>
			</div>
		
		</div>
	
    </div>
</div>
    


@endsection