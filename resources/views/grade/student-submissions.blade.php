@extends('layout')

<?php 
    $page_title = $header_title;
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
		<div class="row clearfix row-deck">			

            <?php
				$success = false;
				if(isset($_GET['success'])){
					$success = true;
				}	
			?>
			@if($success)
				<div class="alert alert-info">
					<p>Student's grade successfully saved.</p>
				</div>
            @endif
            
			@if($exercise)
				<?php
                    $n = 0;
                    $arry_student = array();
                    $student_code = user_id_to_code($student_data->id);
				?>
				<div class="card">
                    <div class="card-body">
                        <h1 class="page-title">Student's Exercise Submissions for Assessment</h1>
                        <h1 class="page-title">Student Code: {{$student_code}}</h1>
                        <h1 class="page-title">Exercise Name: {{$exercise->name}}</h1>
                        <h1 class="page-title">DS Name: {{$grader_name}}</h1>
					</div>
				</div>

				<div class="col-lg-12 col-md-12">				
					<div class="tab-content">
						<div class="card-header">
							<h3 class="card-title">Students' Submission Summary</h3>
						</div>
						
						<div class="tab-pane active">
							
							<?php
                                $n = 0;
                                $total_score = 0;
                                $total_marks = 0;
                                $number_ungraded = 0;
							?>

							<div class="card">
								<div class="card-body">
									@if(count($submissions))
										<div class="table-responsive">
                                            <form method="POST" onsubmit="return confirm('Are you yure you want to submit these grades?\nThe grades entered cannot be edited after you submit!')" action="{{url('exercise/submission/grade')}}" enctype="multipart/form-data">   
												
                                                <input type="hidden" name="student_code" value="{{$student_code}}" />
                                                <input type="hidden" name="grader_name" value="{{$grader_name}}" />
                                                <input type="hidden" name="grader_id" value="{{get_user_id()}}" />
                                                <input type="hidden" name="exercise_id" value="{{$exercise->id}}" />
                                                <input type="hidden" name="enrollment_id" value="{{$enrollment_id}}" />




                                                @csrf

                                                <table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5" data-page-length='25'>
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Exercise</th>
                                                            <th>Req. Title</th>
                                                            <th>Submission</th>
                                                            <th>Submitted At</th>
                                                            <th>Score</th>
                                                            <th>%</th>
                                                            <th>Graded At</th>	
                                                            <th>Action</th>								
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($submissions as $item)
                                                            <?php 
                                                                $percentage_score = '-';
                                                                $graded = 0;

                                                                if($item->graded_at){                                                                    
                                                                    $graded = 1;
                                                                    if($item->requirment_marks){
                                                                        $percentage_score = (100 * $item->grade/$item->requirment_marks).'% of '.$item->requirment_marks;
                                                                    }
                                                                }

                                                                $total_score += $item->grade;
                                                                $total_marks += $item->requirment_marks;
                                                                
                                                            ?>
                                                            <tr>
                                                                <td>{{++$n}}</td>
                                                                <td>{{$item->exercise}}</td>
                                                                <td>{{$item->requirement}}</td>
                                                                <td>
                                                                    @if($item->submitted_file)
                                                                        <a href="{{asset('storage/submission/'.$item->submitted_file)}}" download="{{$student_code}}_Submission_{{$item->submitted_file}}">
                                                                            Download Submitted file
                                                                        </a>
                                                                    @else
                                                                    <a href="{{url('exercise/submission/'.$item->requirement_id)}}" target="_blank">View Submitted Text</a>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($item->submitted_at)
                                                                        {{date('h:i a m/d/Y',strtotime($item->submitted_at))}}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>

                                                                <td>
                                                                    @if ($graded)
                                                                        {{$item->grade}}
                                                                    @else
                                                                        @if($i_am_grader && $grading_open)
                                                                            <?php $number_ungraded++; ?>
                                                                            <input type="hidden" name="student[requirement_name][]"  value="{{$item->requirement}}"/>
                                                                            <input type="hidden" name="student[submission][]"  value="{{$item->submission_id}}"/>
                                                                            <input type="number" name="student[grade][]" max="{{$item->requirment_marks}}" min="0" step="any" required/> 
                                                                            <br> <span style="font-size:12px;">out of {{$item->requirment_marks}}</span>
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    @endif
                                                                </td>

                                                                <td>{{$percentage_score}}</td>

                                                                <td>
                                                                    @if ($item->graded_at)
                                                                        {{date('h:i a m/d/Y',strtotime($item->graded_at))}}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>

                                                                <td>
                                                                    <a href="{{url('exercise/submission/'.$item->requirement_id)}}" target="_blank">View Submission</a>
                                                                </td>
                                                            
                                                            </tr>
                                                        @endforeach 

                                                                                                                                          
                                                    </tbody>
                                                </table>
                                                <?php 
                                                    $percentage_score = '-';
                                                    if($total_marks){
                                                        $percentage_score = (100 * $total_score/$total_marks).'% of '.$total_marks;
                                                    }

                                                    $total_req_score = get_req_marks_already_set($exercise->id, 1);
                                                    $percentage_req = '-';
                                                    if($total_req_score){
                                                        $percentage_req = number_format((100 * $total_score/$total_req_score),2).'%';
                                                    }
                                                ?> 
                                                <table>
                                                    <tbody>
                                                    <tr>
                                                        <td><b>Student Total Submissions Score:</b> {{$total_score}}</td> 
                                                        <td> | <b>Submitted Score Percentage</b> {{$percentage_score}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Max Written Requirement Score:</b> {{$total_req_score}}</td> 
                                                        <td> | <b>Student Score Percentage:</b> {{$percentage_req}}</td>
                                                    </tr>
                                                    <tbody>
                                                </table>
                                                 

                                                @if($number_ungraded)
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label>Upload love letter for the student</label>
                                                            <input type="file" name="love_letter" required>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-12">
                                                        <button class="btn btn-primary btn-lg btn-simple">Save Student Grades</button>
                                                    </div>
                                                @endif

                                            </form>
										</div>
									@else
										<i>No submission made by student in this exercise.</i>
									@endif

								</div>
							</div>
						</div>
						
					</div>
				</div>
			@else
				<i>Sorry, exercise data could not be loaded.</i>
			@endif
		</div>	
    </div>
</div>

    


@endsection