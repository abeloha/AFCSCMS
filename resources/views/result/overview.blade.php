@extends('layout')

<?php 

    $page_title = "ARMED FORCES COMMAND AND STAFF COLLEGE | Result Overview for {{$session->name}}";
    $n = 0; 
    $position = 1;
    $previous_student_score = 0;  
?>

@section('title', $page_title)

@section('content')

<div class="section-body">
	<div class="container-fluid">
		<div class="d-flex justify-content-between align-items-center">
			<div class="header-action">
				<h1 class="page-title">ARMED FORCES COMMAND AND STAFF COLLEGE</h1>
				<ol class="breadcrumb page-breadcrumb">
				<li class="breadcrumb-item"><a href="{{url('/')}}">{{get_app_name()}}</a></li>
				<li class="breadcrumb-item active" aria-current="page">Results | Overview</li>
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
                        
                        <div class="text-center">
                            <br>
                            <h5><u>ARMED FORCES COMMAND AND STAFF COLLEGE JAJI NIGERIA</u></h5>
                            <h6>Result Overview <u>Course</u>: {{$course->name}} <u>Session</u>: {{$session->name}}</u></h6>
                            <br>
                        </div>

                        <table class="table mb-0 dataTable text-nowrap" data-page-length='100'>
                            
                            <thead>
                                <tr>
                                    <th>Serial</th>
                                    <th>Rank</th>
                                    <th>Name</th>
                                    <th>Dept</th>
                                    <th>No. Ex. 
                                        <br><span style="font-size:10px">Enrolled</span>
                                    </th>
                                    <th>W/P Enrolled</th>
                                    <th>W/P Earned</th>
                                    <th>Score</th>
                                    <th>Grade</th>
                                    <th>Posn</th>
                                    <th>Rmk</th>
                                </tr>
                                
                            </thead>
                            <tbody>

                                @foreach($students as $student)
                                    <?php 
                                        if($student->total_score < $previous_student_score){
                                            $position++;
                                        }
                                        $previous_student_score = $student->total_score; 
                                    ?>
                                    <tr>
                                        <td class="border-right border-left">{{++$n}}</td>
                                        <td class="border-right">{{$student->rank}}</td>
                                        <td class="border-right">
                                            <a href="{{url('user/'.$student->id)}}" target="_blank">{{$student->surname .' '.$student->first_name}}</a>
                                        </td> 
                                        <td class="border-right">{{$student->dept}}</td>

                                        <td class="border-right">{{$student->total_no_of_exercise}}</td>
                                        <td class="border-right">{{$student->total_exercise_wp}}</td>
                                        <td class="border-right">{{$student->total_wp}}</td>
                                        <td style="border-right: 1px solid black;">{{$student->total_score}}</td>
                                        <td class="border-right">{{get_grade($student->total_score)}}</td>
                                        <td style="border-right: 1px solid black;">{{get_position($position)}}</td>
                                        <td class="border-right">
                                            <?php 
                                                if(strtolower($student->country) != 'nigeria'){
                                                    echo $student->country;
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                @endforeach
                                
                            </tbody>
                        </table>

                        <hr>

                        <b> <a href="{{url('result/session/'.$session->id)}}"> < Go Back </a></b>

                    </div>

                </div>
            </div>

		</div>
	</div>
</div>


@endsection