@extends('layout')

<?php 

    $page_title = "ARMED FORCES COMMAND AND STAFF COLLEGE PROFICIENCY REPORT | $sub_details_menu";
    $n = 0; 
    $position = 1;
    $previous_student_score = 0;

    $wp_factor = 0;

    if($exercise->weighted_point){
        $wp_factor = 100/ $exercise->weighted_point;
    }
    
    $is_academic = is_academic();
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

                        <table class="table mb-0 dataTable text-nowrap" data-page-length='100'>
                            
                            <thead>
                                <tr>
                                    <th>Serial</th>
                                    <th>Rank</th>
                                    <th>{{($show_identity)? 'Name' : 'Code' }}</th>
                                    <th>Score
                                        <br><span style="font-size:10px">(%)</span>
                                    </th>
                                    <th>Wp pt
                                        <br><span style="font-size:10px">({{$exercise->weighted_point}})</span>
                                    </th>
                                    <th>Syn</th>
                                    <th>Grade</th>
                                    <th>Position</th>
                                    @if(is_academic())
                                        <th>Option</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                    <?php 
                                        $score = number_format($student->total_wp * $wp_factor, 2);
                                        $syndicate = get_student_syndicate($student->user_id, $term->id, $session->id);

                                        if($score < $previous_student_score){
                                            $position++;
                                        }

                                        $previous_student_score = $score;                                        
                                    ?>
                                    <tr>
                                        <td>{{++$n}}</td>
                                        <td>{{$student->rank}}</td>

                                        <td>
                                            @if($show_identity)
                                                {{$student->surname .' '.$student->first_name}}
                                            @else
                                                {{user_id_to_code($student->user_id)}}
                                            @endif
                                        </td>

                                        <td>{{$score}}</td>
                                        <td>{{$student->total_wp}}</td>
                                        <td>{{($syndicate)? $syndicate->name : ''}}</td>
                                        <td>{{get_grade($score)}}</td>
                                        <td>{{get_position($position)}}</td>
                                        
                                        @if(is_academic())
                                            <td>
                                                <a href="{{url('result/student/'.$student->user_id.'/exercise/'.$exercise->id.'/?s='.$session->id.'&t='.$term->id)}}">View details</a>
                                            </td>
                                        @endif

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>

                </div>
            </div>

		</div>
	</div>
</div>


@endsection