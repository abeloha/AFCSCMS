@extends('layout')

<?php 
    $page_title = 'Add Exercise Material';
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
            @if($item)
                <ul class="nav nav-tabs page-header-tab">
                    <li class="nav-item"><a class="nav-link" href="{{url('exercise/'.$item->id)}}">Back to Exercise</a></li>
                </ul>
            @endif
		</div>
	</div>
</div>

<div class="section-body mt-4">
    <div class="container-fluid">
	
		<div class="tab-content">
			<div class="tab-pane active" id="Library-all">

                <?php
                    $can_add = check_can_manage_exercise_materials($item->id);  
                    $type_name = '';
                    if($type==1){
                        $type_name = 'Written';
                    }elseif($type==2){
                        $type_name = 'UTW';
                    }          
                ?>
    
                @if($item)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Add New {{$type_name}} Requirment To Exercise</h3>
                            <div class="card-options ">
                                <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="addReqForm" onsubmit="return submitProcess();" method="POST" action="{{url('exercise/requirement/add')}}" enctype="multipart/form-data">                                
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

                                    <?php 
                                        $req_marks_already_set = get_req_marks_already_set($item->id);
                                        $max_mark_available = 100 - $req_marks_already_set; 
                                    ?>

                                    <input type="hidden" name="exercise" value="{{$item->id}}">
                                    <input type="hidden" name="type" id="type" value="{{$type}}">
                                    <input type="hidden" id="today_date" name="today_date" value="{{date('d/m/Y H:i')}}">
                                    <input type="hidden" id="max_marks_available" value="{{$max_mark_available}}">
                                    
                                    <div class="col-sm-12">
                                        <div class="form-group">                                            
                                            <p>
                                                <label>Name of Exercise:</label>
                                                <b>{{$item->name}}</b>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Requirement Title or Short Description *</label>
                                            <input type="text" name="title" class="form-control" placeholder="e.g Requirement 1" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <label>Total Marks Obtainable * (Out of {{$max_mark_available}})</label>
                                            <input type="number" name="marks" id="marks" class="form-control" required>
                                        </div>
                                        <label style="font-size: 10px;">Total of {{$req_marks_already_set}} marks has already been set in other Requirements for this exercise.</label>
                                    </div>

                                    @if($type==1)                                        
                                        <div class="col-sm-12">
                                            <h3>For students (This section will be shown to the students)</h3>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Please Select Type of Submission *</label>
                                                <select name="submission_type" class="form-control" required>
                                                    <option value="1" selected>Students will upload a file</option>
                                                    <option value="2">Students will type directly in this software</option>
                                                </select>
                                            </div>
                                        </div>                                    

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Requirement Question (optional)</label>
                                                <textarea id="editor1" name="question" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Aditional Requirement File (optional)</label>
                                                <input type="file" name="question_file" >
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-sm-12">
                                        <hr>
                                        <h3>For the DS grading this Requirement (Students will not have access to this)</h3>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Grading Instruction (optional)</label>
                                            <textarea id="editor3" name="grading_instruction" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Green File (optional)</label>
                                            <input type="file" name="grading_file_1" >
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Score sheet (optional)</label>
                                            <input type="file" name="grading_file_2" >
                                        </div>
                                    </div>

                                    @if($type==1)

                                        <div class="col-sm-12">
                                            <hr>
                                            <h3>Additional Requirement Settings</h3>
                                        </div>
                                        
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label><b>Start Submission Date</b></label> 
                                                <br>Date: <input type="date" name="start_at_date" id="start_at_date" required>
                                                Time: <input type="time" name="start_at_time" id="start_at_time" required>  
                                                <span>Students can start submitting from the time specified</span> <br>                                              
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label><b>Stop Submission Date</b></label>
                                                <br>Date: <input type="date" name="end_at_date" id="end_at_date" required>
                                                Time: <input type="time" name="end_at_time" id="end_at_time" required>
                                                <span>This is the due time after which no more submission is allowed</span><br>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label><b>Show To Student Date (optional)</b></label>
                                                <br>Date: <input type="date" name="show_at_date" id="show_at_date">
                                                Time: <input type="time" name="show_at_time" id="show_at_time">
                                                <span>This is the time students can have see the requirement and its questions. If not provided, Start Submission date will be used.</span>
                                            </div>
                                        </div>
                                    @endif 

                                    <div class="col-sm-12">
                                        <div><b style="color:red;" id="validation_msg"></b></div>
                                        <button class="btn btn-primary btn-lg btn-simple">Add Requirement</button>
                                    </div> 

                                </div>
                            </form>

                            <script>
                                function submitProcess(){

                                    $('#validation_msg').html('processing...');

                                    var max_mark = $('#max_marks_available').val();
                                    var max_marks_available = parseInt(max_mark);

                                    var marks = parseInt($('#marks').val());

                                    if(marks < 0){
                                        $('#validation_msg').html('Total Marks Obtainable cannot be less than 0');
                                        return false;
                                    }else if(marks == 0){
                                        $('#validation_msg').html('Total Marks Obtainable cannot be 0');
                                        return false;
                                    }else if(marks > max_marks_available){
                                        $('#validation_msg').html('Total Marks Obtainable cannot be more than ' + max_mark);
                                        return false;
                                    }

                                    var start_at_date = $('#start_at_date').val();
                                    var start_at_time = $('#start_at_time').val();

                                    var end_at_date = $('#end_at_date').val();
                                    var end_at_time = $('#end_at_time').val();
                                    
                                    var show_at_date = $('#show_at_date').val();
                                    var show_at_time = $('#show_at_time').val();

                                    var start = start_at_date + ' ' +start_at_time;
                                    var end = end_at_date + ' ' +end_at_time;
                                    var show = show_at_date + ' ' +show_at_time;

                                    var today_date = $('#today_date').val();

                                    
                                    if(new Date(today_date) > new Date(start)){
                                        $('#validation_msg').html('Start Submission Date cannot be in the past. Note, server date and time is: ' + new Date(today_date));
                                        return false;
                                    }else if(new Date(end) < new Date(start)){
                                        $('#validation_msg').html('Stop Submission Date cannot be a date before Start Submission Date');
                                        return false;
                                    }else if(new Date(show) > new Date(start)){
                                        $('#validation_msg').html('Show To Student Date cannot be after the Start Submission Date');
                                        return false;
                                    }
                                    
                                    $('#validation_msg').html('');
                                    return true;
                                }

                                function test(){
                                    var marks = parseInt($('#marks').val());
                                    
                                    console.log(marks);

                                    if(marks < 0){
                                        console.log('Total Marks Obtainable cannot be less than 0');
                                    }else if(marks == 0){
                                        console.log('Total Marks Obtainable cannot be 0');
                                    }

                                }
                            </script>

                        </div>
                    </div>
                @else
                    <div class="card">
                        <div class="card-body">
                            <i>Exercise could not be fund</i>
                        </div>
                    </div>
                @endif                
                
			</div>
		
			
		</div>
	
    </div>
</div>
    
<script>
	initSampleEditor1();
</script>

@endsection