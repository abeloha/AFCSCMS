@extends('layout')

<?php 
    $page_title = "ARMED FORCES COMMAND AND STAFF COLLEGE PROFICIENCY REPORT | $sub_details_menu";    
?>
@section('title', $page_title)

@section('content')

<div class="section-body">
	<div class="container-fluid">
		<div class="d-flex justify-content-between align-items-center">
			<div class="header-action">
				<h1 class="page-title">ARMED FORCES COMMAND AND STAFF COLLEGE PROFICIENCY REPORT</h1>
				<ol class="breadcrumb page-breadcrumb">
				<li class="breadcrumb-item"><a href="{{url('/')}}">{{get_app_name()}}</a></li>
				<li class="breadcrumb-item active" aria-current="page">Results | {{$sub_details_menu}}</li>
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

                    <div class="table-responsive">
                        
                        <div>
                            <br>
                            <h5 class="text-center"><u>ARMED FORCES COMMAND AND STAFF COLLEGE PROFICIENCY REPORT</u></h5>
                            <h6 class="text-center">{!!$sub_details!!}</h6>
                            <br>
                        </div>

                        @if($result)

                            <?php
                                $is_academic = is_academic();
                                $score = $result->total_grade;

                                if($exercise->weighted_point){
                                    $wp_factor = 100/ $exercise->weighted_point;
                                    $score = number_format($result->total_wp * $wp_factor, 2);
                                }

                                $student_code = user_id_to_code($student->id);
                            ?>

                            <table class="table mb-0 dataTable text-nowrap" data-page-length='100'>
                                
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>{{($show_identity)? 'Name' : 'Code' }}</th>
                                        <th>UTW (Oral)
                                            <br><span style="font-size:10px">Score</span>
                                        </th>
                                        <th>Written
                                            <br><span style="font-size:10px">Score</span>
                                        </th>
                                        <th>Moderated 
                                            <br><span style="font-size:10px">(W/P added)</span>
                                        </th>
                                        <th>W/P
                                            <br><span style="font-size:10px">({{$exercise->weighted_point}})</span>
                                        </th>
                                        <th>Score
                                            <br><span style="font-size:10px">(%)</span>
                                        </th>
                                        <th>Grade</th>
                                        @if(is_academic())
                                            <th>Option</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                        
                                    <tr>
                                        <td>{{$student->rank}}</td>
                                        <td>
                                            {{$identity}}
                                        </td>

                                        <td>{{$result->oral_grade}}</td>
                                        <td>{{$result->written_grade}}</td>
                                        <td>{{$result->ci_wp_grade + $result->dpty_cmd_wp_grade}}</td>

                                        <td>{{$result->total_wp}}</td>
                                        <td>{{$score}}</td>
                                        <td>{{get_grade($score)}}</td>
                                        
                                        @if(is_academic())
                                            <td>
                                                <a href="{{url('exercise/'.$exercise->id.'/submissions/?student='.$student_code)}}" target="_blank">View Submissions</a>
                                            </td>
                                        @endif

                                    </tr>
                                    
                                </tbody>
                            </table>

                        @else
                            <i>Result not found.</i>
                        @endif
                    </div>

                </div>
            </div>

		</div>
	</div>
</div>


@endsection