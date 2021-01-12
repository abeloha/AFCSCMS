@extends('layout')

<?php 
    $page_title = 'Submission Details';
    $can_add = check_has_ability('manage_exercise');
    $user_id = get_user_id();
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

		<div class="row">            

            @if($item)
                
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
                            @if($requirement->req_type == 1)
                                <h5 class="mt-4"><i class="fa fa-list text-danger"></i> Requirement</h5>                                
                                <div>
                                    <b>Requirement Title:</b> {{$requirement->title}}
                                    <br><b>Submission Starts:</b> {{date('h:i a, D d, M, Y', strtotime($requirement->start_at))}} 
                                    <br><b>Submission Stops:</b> {{date('h:i a, D d, M, Y', strtotime($requirement->end_at))}}                                                         
                                    <br>
                                </div>                                

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

                                    @if(is_student() ||  check_if_exercise_is_enrolled($item->id))
                                        <!-- is student -->
                                    @else
                                        <div class="card">
                                            <h5 class="mt-4">Grading Instructions for DS Assessing Student</h5>
                                            {!!$requirement->grading_instruction!!} 
                                            <br>  
                                            @if($requirement->grading_file_1)                                                                  
                                                <a href="{{asset('storage/requirement/'.$requirement->grading_file_1)}}" download="AFCSC_grading_instruction_{{$requirement->grading_file_1}}">
                                                    <div class="file-name">
                                                        <p class="mb-0 text-muted">
                                                            <i class="fa fa-file text-success"></i> Green File (Click to download)
                                                        </p>
                                                    </div>
                                                </a>
                                            @endif
                                            @if($requirement->grading_file_2)                                                                  
                                                <a href="{{asset('storage/requirement/'.$requirement->grading_file_2)}}" download="AFCSC_grading_instruction_{{$requirement->grading_file_2}}">
                                                    <div class="file-name">
                                                        <p class="mb-0 text-muted">
                                                            <i class="fa fa-file text-success"></i> Score Sheet (Click to download)
                                                        </p>
                                                    </div>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                
                                <hr>

                                <h5 class="mt-4"><i class="fa fa-check text-danger"></i> Student Submission</h5>

                                @if($submission)
                                    <?php $student_code = user_id_to_code($submission->user_id); ?>
                                    <b>Student Code:</b> {{$student_code}}<br>
                                    <b>Student Grade:</b> {{($submission->graded_at)? $submission->grade.' out of '.$requirement->marks : 'Not Graded'}}<br>
                                    <b>Submission made at:</b> {{date('h:i a, D d, M, Y', strtotime($submission->created_at))}}<br>
                                    
                                    @if($submission->submitted_file)
                                    <b>Submitted File:</b>
                                        <a href="{{asset('storage/submission/'.$submission->submitted_file)}}" download="{{$student_code}}_Submission_{{$submission->submitted_file}}" >
                                            <div class="file-name">
                                                <p class="mb-0 text-muted">
                                                    <i class="fa fa-file text-success"></i> Click to download the submitted file
                                                </p>
                                            </div>
                                        </a>
                                    @endif

                                    @if($submission->submitted_text)
                                        <b>Submitted Answer:</b><br><br>
                                        {!!$submission->submitted_text!!}
                                    @endif

                                @else
                                    <i>This student has not made any submission yet.</i>
                                @endif

                            @else
                                <i>This requirement does not accept submission and cannot be graded here.</i>
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