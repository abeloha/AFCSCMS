@extends('layout')

<?php 
    $page_title = 'Events';
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
                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#Library-add">Add New Event</a></li>
            </ul>

		</div>
	</div>
</div>

<div class="section-body mt-4">
    <div class="container-fluid">
	
		<div class="tab-content">
			<div class="tab-pane active" id="Library-all">

                <?php
                    $n = 0;

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
                        <p>New event has been added successfully</p>
                    </div>
                @endif

                @if($deleted)
                    <div class="alert alert-info">
                        <p>Event deleted successfully</p>
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

                        @if(count($events))
                            <div class="table-responsive">
                                <table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5" data-page-length='25'>
                                    <thead>                                        
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Start Title</th>
                                            <th>End Time</th> 
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($events as $item) 
                                            <tr>
                                                <td>{{++$n}}</td>
                                                <td>{{$item->title}}</td>
                                                <td>{{date('h:i a d-m-Y', strtotime($item->start))}}</td>
                                                <td>{{date('h:i a d-m-Y', strtotime($item->end))}}</td>                                                
                                                <td>                                                    
                                                    <a href="{{url('event/'.$item->id.'/delete')}}" onclick="return confirm('Are you sure you want to delete this event?')" class="dropdown-item"><i class="dropdown-icon fa fa-trash-o text-danger"></i> Delete Event</a>
                                                </td>
                                            </tr>
                                        @endforeach                                        
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p>No event added yet</p>
                        @endif                        

					</div>
				</div>
			</div>        
        
			<div class="tab-pane" id="Library-add">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Add Event</h3>
						<div class="card-options ">
							<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
						</div>
					</div>
					<div class="card-body">
                        <form method="POST" action="{{url('event/add')}}">
                            
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
                                            <label>Title of Event</label>
                                            <input type="text" name="title" value="" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label><b>Start of Event</b></label> 
                                            <br>Date: <input type="date" name="start_at_date" id="start_at_date" required>
                                            Time: <input type="time" name="start_at_time" id="start_at_time">
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label><b>End of Event</b></label> 
                                            <br>Date: <input type="date" name="end_at_date" id="end_at_date" required>
                                            Time: <input type="time" name="end_at_time" id="end_at_time">
                                        </div>
                                    </div>

                                    
                                    
                                    <div class="col-sm-12">
                                        <button class="btn btn-primary btn-lg btn-simple">Add Event</button>
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