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

			@if($exercise)
				<?php
                    $n = 0;
                    $arry_student = array();
				?>
				<div class="card">
                    <div class="card-body">
                        <h1 class="page-title">Students Assigned for Assessment</h1>
                        <h1 class="page-title">Exercise Name: {{$exercise->name}}</h1>
                        <h1 class="page-title">DS Name: {{$grader_name}}</h1>
					</div>
				</div>

				<div class="col-lg-12 col-md-12">				
					<div class="tab-content">
						<div class="card-header">
							<h3 class="card-title">DS Students' Assessment Summary</h3>
						</div>
						
						<div class="tab-pane active">
							
							<?php
                                $n = 0;
                                $total_req_score = get_req_marks_already_set($exercise->id, 1);
							?>

							<div class="card">
								<div class="card-body">
									@if(count($assigned_students))
										<div class="table-responsive">

                                            <table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5" data-page-length='25'>
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Student</th>
                                                        <th>No. of Submissions</th>
                                                        <th>No. Graded</th>
                                                        <th>
                                                            Score
                                                            <br><span style="font-size:11px">Max: {{$total_req_score}}</span>
                                                        </th>
                                                        <th>
                                                            % Score
                                                            <br><span style="font-size:10px">of submissions</span>
                                                        </th>
                                                        <th>
                                                            % Result
                                                            <br><span style="font-size:10px">of max possible score</span>
                                                        </th>
                                                        <th>Grading Status</th>	
                                                        <th>Action</th>											
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($assigned_students as $item)
                                                        <?php 
                                                            $students_submissions = get_student_exercise_submission($exercise->id,$item->user_id); 
                                                            $total_submission = count($students_submissions);

                                                            $number_graded = 0;
                                                            $total_score = 0;

                                                            $percentage_score = '-';
                                                            $total_marks = 0;

                                                            foreach($students_submissions as $students_submission){

                                                                $total_marks += $students_submission->requirment_marks;

                                                                if($students_submission->grade){ //0.00 returns positive
                                                                    $total_score += $students_submission->grade;
                                                                    if($students_submission->graded_at){
                                                                        $number_graded++;
                                                                    }                                                                    
                                                                }
                                                            }

                                                            if($total_marks){
                                                                $percentage_score = number_format( (100 * $total_score/$total_marks),2 ).'% of '.$total_marks;
                                                            }

                                                            $number_ungraded = $total_submission - $number_graded;

                                                            $student_code = user_id_to_code($item->user_id);

                                                            $percentage_req = '-';
                                                            if($total_req_score){
                                                                $percentage_req = number_format((100 * $total_score/$total_req_score),2).'%';
                                                            }
                                                        ?>
                                                        <tr>
                                                            <td>{{++$n}}</td>
                                                            <td>{{$student_code}}</td>
                                                            <td>{{$total_submission}}</td>
                                                            <td>{{$number_graded}}</td>
                                                            <td>{{$total_score}}</td>
                                                            <td>{{$percentage_score}}</td>
                                                            <td>{{$percentage_req}}</td>
                                                            <td>
                                                                @if($number_ungraded)
                                                                    <span class="text-danger">Unfinished</span>
                                                                @elseif($total_submission)
                                                                    <span>Finished</span>
                                                                @else
                                                                    <span class="text-warning">N/A</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($total_submission)
                                                                    <a href="{{url('exercise/'.$exercise->id.'/submissions/?student='.$student_code)}}" target="_blank">
                                                                        @if($number_ungraded && $i_am_grader)
                                                                            Grade
                                                                        @else
                                                                            View
                                                                        @endif
                                                                        Submissions
                                                                    </a>
                                                                @else
                                                                -
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach                                        
                                                </tbody>
                                            </table>

										</div>
									@else
										<i>No student is assigned to this ds for grading.</i>
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