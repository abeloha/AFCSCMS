@extends('layout')

<?php 

    $page_title = "ARMED FORCES COMMAND AND STAFF COLLEGE | $sub_details_menu";
    $n = 0; 
    $position = 1;
    $previous_student_score = 0;    
    $is_academic = is_academic();

    $wp_total = 0;
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
                            <h5 class="text-center"><u>ARMED FORCES COMMAND AND STAFF COLLEGE JAJI NIGERIA</u></h5>
                            <h6 class="text-center">{!!$sub_details!!}</h6>
                            <br>
                        </div>

                        @if($exercises)

                        <table class="table mb-0 dataTable text-nowrap" data-page-length='100'>
                            
                            <thead>

                                <tr>
                                    <td colspan="4"></td>
                                    @foreach($exercises as $exercise)
                                        <td colspan="3">{{$exercise->name}}</td> 
                                    @endforeach
                                    <td colspan="4" style="text-align: center;"><b>Term Total</b></td>
                                    <td colspan="2" style="text-align: center;"><b>Cumulative</b></td>
                                    <td></td>                                    
                                   
                                </tr>

                                <tr>
                                    <th>Serial</th>
                                    <th>Rank</th>
                                    <th>Name</th>
                                    <th>Syn</th>
                                    @foreach($exercises as $exercise)
                                        <td>Score</td>
                                        <td>W/P
                                            <br><span style="font-size:10px">({{$exercise->weighted_point}})</span>
                                        </td> 
                                        <td>Grade</td>
                                        <?php $wp_total += $exercise->weighted_point; ?>
                                    @endforeach
                                    <td>W/P</td>
                                    <td>Score</td>
                                    <td>Grade</td>
                                    <th>Posn</th>

                                    <td>Score</td>
                                    <td>Grade</td>

                                    <th>Rmk</th>
                                </tr>

                            </thead>
                            <tbody>

                                @foreach($students as $student)
                                    <tr>
                                        <?php                                             
                                            $syndicate = get_student_syndicate($student->id, $term->id, $session->id); 

                                            $syndicate_name = '';
                                            if($syndicate){
                                                $syndicate_name = shorten_syndicate_name($syndicate->name);
                                            }

                                            $total_student_wp = 0;
                                        ?>

                                        <td class="border-right border-left">{{++$n}}</td>
                                        <td>{{$student->rank}}</td>

                                        <td class="border-right">
                                            @if($show_identity)
                                                <a href="{{url('user/'.$student->id)}}" target="_blank">{{$student->surname .' '.$student->first_name}}</a>
                                            @else
                                                {{user_id_to_code($student->id)}}
                                            @endif
                                        </td>

                                        <td class="border-right">{{$syndicate_name}}</td>

                                        @foreach($exercises as $exercise)
                                            
                                            <?php
                                                $wp_factor = 0;
                                                if($exercise->weighted_point){
                                                    $wp_factor = 100/ $exercise->weighted_point;
                                                }

                                                $score = 0; $wp = 0;
                                                $result = get_exercise_enrollment_data($student->id, $exercise->id, $session->id);
                                                if($result){
                                                    $score = number_format($result->total_wp * $wp_factor, 2);
                                                    $wp = $result->total_wp;
                                                    $total_student_wp += $wp;
                                                }
                                            ?>

                                            @if($result)
                                                <td>{{$score}}</td>
                                                <td>{{$wp}}</td> 
                                                <td class="border-right">{{get_grade($score)}}</td>
                                            @else
                                                <td>-</td><td>-</td><td class="border-right">-</td>
                                            @endif
                                            
                                        @endforeach

                                        <?php 
                                            $final_score = $student->term_score;

                                            if($final_score < $previous_student_score){
                                                $position++;
                                            }
                                            $previous_student_score = $final_score; 
                                        ?>

                                        <td class="border-right" style="border-left: 1px solid black;">{{number_format($total_student_wp, 2)}}</td>
                                        <td class="border-right">{{$final_score}}</td>
                                        <td class="border-right">{{get_grade($final_score)}}</td>
                                        <td style="border-right: 1px solid black;">{{get_position($position)}}</td>

                                        <!--cumulative-->
                                        <td class="border-right">{{$student->total_score}}</td>
                                        <td style="border-right: 1px solid black;">{{get_grade($student->total_score)}}</td>
                                        
                                        <td class="border-right">
                                            <?php 
                                                if(strtolower($student->country) != 'nigeria'){
                                                    echo $student->country;
                                                }
                                            ?>
                                        </td>
                                       
                                    <tr>
                                @endforeach

                                @if($n)
                                    <tr>
                                        <td class="border-right border-left">*</td>
                                        <td colspan="3" class="border-right"></td>

                                        @foreach($exercises as $exercise)

                                            <td colspan="3" class="border-right"><a href="{{url('result/exercise/'.$exercise->id.'/?s='.$session->id.'&t='.$term->id)}}" target="_blank">View exercise grade</a></td>
                                            
                                        @endforeach                                        
                                        <td colspan="7" class="border-right"></td>                                       
                                    <tr>
                                @endif

                            </tbody>
                        </table>
                        <hr>

                        @if($n && $can_moderate)
                            <div class="col-sm-12">
                                <a href="{{url('result/show?s='.$session->id.'&t='.$term->id.'&dept='.$dept_id.'&div='.$div_id.'&c='.$course->id.'&moderate=true')}}"><button class="btn btn-primary btn-lg btn-simple">Click To Moderate Student Grades</button></a>
                                <br>
                                <span class="text-small">Modrating student grades allows you to ADD to or SUBTRACT from student's weighted points earned in this term</span>
                            </div>
                            <br><br>
                        @endif

                        <?php
                            $depts = '';
                            $divs = '';
                            $is_joint = $term->is_joint;
                            $depts = get_dept();
                            $divs = get_div_by_course($course->id);
                        ?>
                        @if($divs)
                        
                            <div class="card">
                                <div class="card-body">

                                    <h5>Results for <u>Course</u>: {{$course->name}} | <u>Term/Session</u>: {{$term->name}}/{{$session->name}}</h5>

                                    <div class="row">
                                        @if($depts)
                                            <div class="col-sm-6">
                                                <b>Filter Students by Department:</b> 
                                                <form action="{{url('result/show')}}" method="GET">
                                                    <input type="hidden" name="c" value="{{$course->id}}" />
                                                    <input type="hidden" name="s" value="{{$session->id}}" />
                                                    <input type="hidden" name="t" value="{{$term->id}}" />
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label>Department</label>
                                                            <select name="dept" class="form-control">
                                                                @foreach($depts as $item)
                                                                    <option value="{{$item->id}}" {{($item->id == $dept_id)? 'selected' : ''}}>{{$item->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    @if($is_joint)                                        
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label>Show Only Organic Students?</label>
                                                                <select name="organic" class="form-control" required>
                                                                    <option value="0">No</option>
                                                                    <option value="1">Yes</option>                                                                    
                                                                </select>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <button class="btn btn-primary btn-lg btn-simple">Go</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif

                                        @if($divs)
                                            <div class="col-sm-6">
                                                <b>Filter Students by Divisions:</b> 
                                                <form action="{{url('result/show')}}" method="GET">
                                                    <input type="hidden" name="c" value="{{$course->id}}" />
                                                    <input type="hidden" name="s" value="{{$session->id}}" />
                                                    <input type="hidden" name="t" value="{{$term->id}}" />
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label>Div</label>
                                                            <select name="div" class="form-control">
                                                                <option value="0">All</option>
                                                                @foreach($divs as $item)
                                                                    <option value="{{$item->id}}" {{($item->id == $div_id)? 'selected' : ''}}>{{$item->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    @if($is_joint)
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label>Show Only Organic Students?</label>
                                                                <select name="organic" class="form-control">
                                                                    <option value="0">No</option>
                                                                    <option value="1">Yes</option>                                                                    
                                                                </select>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <button class="btn btn-primary btn-lg btn-simple">Go</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        @endif

                        @else
                        <p><i>No exercise that students can enroll</i></p>
                        @endif

                    </div>

                </div>
            </div>

		</div>
	</div>
</div>


@endsection