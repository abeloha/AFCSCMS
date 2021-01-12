@extends('layout')

<?php 
    $page_title = 'Exercises Details';
    $can_add = check_has_ability('manage_exercise');
    $user_id = get_user_id();
?>

@section('title', $page_title)

@section('content')

<script>
    // Create a countdown"
    function createCountDownForm(divId, stopDate, clrDivId){
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
            document.getElementById(clrDivId).innerHTML = "<p><i>Time up, no more submissions allowed.</i></p>";
        }
        }, 1000);
    } 			
</script>

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
        <?php                    
            $success = false;
            if(isset($_GET['success'])){
                $success = true;
            }             
        ?>
        @if($success)
            <div class="alert alert-info">
                <p>Your submission has been saved successfully</p>
            </div>
        @endif

		<div class="row">            

            @if($item)
                <?php
                    $can_manage_materials =  check_can_manage_exercise_materials($item->id);
                    $can_view_req = check_can_view_exercise_requirements($item->id);                                                 
                    $iam_enrolled = check_if_exercise_is_enrolled($item->id);
                ?>

                <div class="col-xl-4 col-lg-5 col-md-12">
                
                    @include('exercise.exercise-card',['item'=>$item])
                    
                </div>
                
                <div class="col-xl-8 col-lg-7 col-md-12">
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{$requirement->title}}</h3> 

                            <div class="card-options ">
                                <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($can_manage_materials || $can_view_req || $iam_enrolled)
                                
                                @if($requirement->req_type == 1)
                                    <h5 class="mt-4"><i class="fa fa-list text-danger"></i> {{$requirement->title}}</h5>                                
                                    <div>
                                        <span id="count_down_{{$requirement->id}}" class="tag tag-danger">0.00</span>
                                        <br><b>Submission Starts:</b> {{date('h:i a, D d, M, Y', strtotime($requirement->start_at))}} 
                                        <br><b>Submission Stops:</b> {{date('h:i a, D d, M, Y', strtotime($requirement->end_at))}}                                                         
                                        <br>
                                    </div>
                                    <script>
                                        // Set the date we're counting down"
                                        var stopDate = "{{date('M d, Y H:i', strtotime($requirement->end_at))}}";
                                        var divId = "count_down_{{$requirement->id}}";                                                     
                                        createCountDown(divId, stopDate);
                                    </script>                                

                                    <h5 class="mt-4">Question: </h5>
                                        {!!$requirement->question!!}                             

                                        @if($requirement->question_file)
                                            <br>                                                                    
                                            <a href="{{asset('storage/requirement/'.$requirement->question_file)}}" download="AFCSC_Exercise_Requirement_{{$requirement->question_file}}">
                                                <div class="file-name">
                                                    <p class="mb-0 text-muted">
                                                        <i class="fa fa-file text-success"></i> Question File (Click to download)
                                                    </p>
                                                </div>
                                            </a>
                                        @endif  
                                    
                                    <hr>

                                    @if($iam_enrolled)

                                        <h5 class="mt-4"><i class="fa fa-check text-danger"></i> Your submission</h5>

                                        <?php $submission = get_my_requirement_submission($requirement->id); ?>

                                        @if($submission)

                                            <b>Submission made at:</b> {{date('h:i a, D d, M, Y', strtotime($submission->created_at))}}<br>
                                            
                                            @if($submission->submitted_file)
                                            <b>You Submitted A File:</b>
                                                <a href="{{asset('storage/submission/'.$submission->submitted_file)}}" download="AFCSC_Requirement_Submission_{{$submission->submitted_file}}">
                                                    <div class="file-name">
                                                        <p class="mb-0 text-muted">
                                                            <i class="fa fa-file text-success"></i> Click to download your submitted file
                                                        </p>
                                                    </div>
                                                </a>
                                            @endif

                                            @if($submission->submitted_text)
                                                <b>Submitted Answer:</b><br><br>
                                                {!!$submission->submitted_text!!}
                                            @endif

                                        @else
                                            <?php
                                                $allow_submission = 0;

                                                $time_now = strtotime(date('Y-m-d H:i'));                                                    
                                                $end_time = strtotime($requirement->end_at);
                                                
                                                if($time_now <= $end_time){
                                                    $allow_submission = 1;
                                                }
                                            ?>

                                            @if($allow_submission)
                                                <div id="submission_form">
                                                    <form method="POST" action="{{url('exercise/requirement/submission')}}" enctype="multipart/form-data">                                
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

                                                            <input type="hidden" name="requirement" value="{{$requirement->id}}">
                                                            
                                                            @if($requirement->submission_type == 1)
                                                                <div class="col-sm-12">
                                                                    <div class="form-group">
                                                                        <label>Upload File *</label>
                                                                        <input type="file" name="submission_file" required>
                                                                    </div>
                                                                </div>
                                                            @elseif($requirement->submission_type == 2)
                                                                <div class="col-sm-12">
                                                                    <div class="form-group">
                                                                        <label>Type Your Answer</label>
                                                                        <textarea id="editor1" name="submission_text" class="form-control" required></textarea>
                                                                    </div>
                                                                </div>
                                                                <script>
                                                                    initSampleEditor1();
                                                                </script>
                                                            @endif

                                                            <div class="col-sm-12">
                                                                <div><b style="color:red;" id="validation_msg"></b></div>
                                                                <button class="btn btn-primary btn-lg btn-simple">Submit</button> 
                                                                <span class="tag tag-danger">Time left: <span id="count_down_submit">0.00</span></span>
                                                            </div>
                                                        </div>
                                                                
                                                    </form>
                                                </div>
                                                <script>
                                                    var stopDate = "{{date('M d, Y H:i', strtotime($requirement->end_at))}}";
                                                    var divId = "count_down_submit";                                                     
                                                    createCountDownForm(divId, stopDate, 'submission_form');
                                                </script>
                                            @else
                                                <p><i>No more submissions allowed.</i></p>
                                            @endif

                                        @endif

                                    @endif

                                @endif

                            @else
                                <p><i>You are not enrolled for this exercise</i></p>
                            @endif

                        </div>
                    </div>

                </div>  

            @else
                <div class="col-md-12">
                    <i>Exercise details could not be loaded now or exercise has been removed.</i>
                </div>
            @endif
		</div>
	
    </div>
</div>

@endsection