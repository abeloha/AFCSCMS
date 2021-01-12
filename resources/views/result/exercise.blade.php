@extends('layout')

<?php 

    $page_title = "ARMED FORCES COMMAND AND STAFF COLLEGE PROFICIENCY REPORT | $sub_details_menu";
    $n = 0; 
    $position = 1;
    $previous_student_score = 0;

    $wp_factor = 0;

    $exercise_max_moderation_value = $exercise->weighted_point;

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

                    <?php
                        $msg = '';
                        if(isset($_GET['msg'])){
                            $msg = $_GET['msg'];
                        }
                    ?>
                    @if($msg)
                        <div class="col-md-12">
                            <div class="alert alert-success">
                                <p>{{$msg}}</p>
                            </div>
                        </div>
                    @endif

                    <div class="table-responsive">
                        
                        <div>
                            <br>
                            <h5 class="text-center"><u>ARMED FORCES COMMAND AND STAFF COLLEGE PROFICIENCY REPORT</u></h5>
                            <h6 class="text-center">{!!$sub_details!!}</h6>
                            <br>
                        </div>

                        <form method="POST" onsubmit="return confirm('Are you yure you want to add these values to the students weighted point?')" action="{{url('result/exercise/moderate')}}">
                            
                            @csrf

                            <?php 
                                if($allow_moderation){
                                    $grader_name = get_user_name();
                                    $grader_id = get_user_id();
                            ?>  

                                <input type="hidden" name="grader_name" value="{{$grader_name}}" />
                                <input type="hidden" name="grader_id" value="{{$grader_id}}" />
                                <input type="hidden" name="exercise_id" value="{{$exercise->id}}" />
                                
                                <input type="hidden" name="grade_type" value="{{$moderation_type}}" />
                                
                                <input type="hidden" name="s" value="{{$session->id}}" />
                                <input type="hidden" name="t" value="{{$term->id}}" />
                                <input type="hidden" name="div" value="{{$div_id}}" />
                                <input type="hidden" name="dept" value="{{$dept_id}}" />

                            <?php
                                }
                            ?>

                            <table class="table mb-0 dataTable text-nowrap" data-page-length='100' >
                                
                                <thead>
                                    <tr>
                                        <th>Serial</th>
                                        <th>Rank</th>
                                        <th>{{($show_identity)? 'Name' : 'Code' }}</th>
                                        <th>Score
                                            <br><span style="font-size:10px">(%)</span>
                                        </th>
                                        <th>W/P
                                            <br><span style="font-size:10px">({{$exercise->weighted_point}})</span>
                                        </th>
                                        <th>Syn</th>
                                        <th>Grade</th>
                                        <th>Posn</th>

                                        @if($allow_moderation)
                                            <th> +/- W/P
                                                <br><span style="font-size:10px">(To students)</span>
                                            </th>
                                        @endif

                                        <th>Rmk</th>
                                        @if(is_academic())
                                            <th>Option</th>
                                        @endif

                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    @if($allow_moderation)
                                        <tr>
                                            <td colspan="8" style="text-align: center; border-bottom: 1px, solid black">
                                                <b>Marks across board:</b>
                                            </td>
                                            <td colspan="3" style="text-align: left; border-bottom: 1px, solid black">
                                                <input type="number" name="mark_across" id="mark_across" step="any" /> (The mark is assigned to all)
                                            </td>
                                        </tr>
                                    @endif

                                    @foreach($students as $student)
                                        <?php 

                                            $score = number_format($student->total_wp * $wp_factor, 2);

                                            $syndicate = get_student_syndicate($student->user_id, $term->id, $session->id);
                                            
                                            $syndicate_name = '';
                                            if($syndicate){
                                                $syndicate_name = shorten_syndicate_name($syndicate->name);
                                            }

                                            if($score < $previous_student_score){
                                                $position++;
                                            }

                                            $previous_student_score = $score;
                                            
                                            $student_max_moderation_value = $exercise_max_moderation_value - $student->total_wp;
                                        ?>
                                        <tr>
                                            <td>{{++$n}}</td>
                                            <td>{{($show_identity)? $student->rank : '-' }}</td>

                                            <td>
                                                @if($show_identity)
                                                    <a href="{{url('user/'.$student->user_id)}}" target="_blank">{{$student->surname .' '.$student->first_name}}</a>
                                                @else
                                                    {{user_id_to_code($student->user_id)}}
                                                @endif
                                            </td>

                                            <td>{{$score}}</td>
                                            <td>{{$student->total_wp}}</td>
                                            <td>{{$syndicate_name}}</td>
                                            <td>{{get_grade($score)}}</td>
                                            <td>{{get_position($position)}}</td>

                                            @if($allow_moderation)
                                                <td>
                                                    <input type="hidden" name="student[id][]"  value="{{$student->user_id}}"/>
                                                    <input type="hidden" name="student[enrollment][]"  value="{{$student->enrollment_id}}"/>
                                                    <input type="number" name="student[grade][]" max="{{$student_max_moderation_value}}" step="any"  />
                                                </td>
                                            @endif

                                            <td>
                                                <?php 
                                                    if(strtolower($student->country) != 'nigeria'){
                                                        echo $student->country;
                                                    }
                                                ?>
                                            </td>
                                            
                                            @if(is_academic())
                                                <td>
                                                    <a href="{{url('result/student/'.$student->user_id.'/exercise/'.$exercise->id.'/?s='.$session->id.'&t='.$term->id)}}">View details</a>
                                                </td>
                                            @endif

                                        </tr>
                                    @endforeach 

                                </tbody>
                            </table>
                        
                            @if($n && $allow_moderation)
                                <div class="col-sm-12" style='text-align:right'>
                                    <button class="btn btn-primary btn-lg btn-simple">Save Added Wighted Points</button>
                                </div>
                                <br><br>
                            @endif
                        
                        </form>

                        @if($n && $can_moderate && !$allow_moderation)
                            <div class="col-sm-12">
                                <a href="{{url('result/exercise/'.$exercise->id.'/?s='.$session->id.'&t='.$term->id.'&dept='.$dept_id.'&div='.$div_id.'&moderate=true')}}"><button class="btn btn-primary btn-lg btn-simple">Click Moderate Student Grades</button></a>
                                <span class="text-small">Modrating student grades allows you to ADD to or SUBTRACT from student's weighted points earned in this exercise.</span>
                            </div>
                            <hr><br>
                        @endif

                        @if($n)
                            @if(check_can_manage_exercise_materials($exercise->id))
                                <?php 
                                    $released_exercise_result = get_released_exercise_result($exercise->id, $term->id, $session->id);
                                ?>
                                <div class="card">
                                    <div class="card-body">
                                        <p>                                   
                                        <b>Grade Book Status:</b> 
                                            @if($released_exercise_result)
                                                <span class="tag tag-success">Submitted on {{date('d/m/Y', strtotime($released_exercise_result->created_at))}}</span>
                                            @else
                                                <span class="tag tag-warning">Not Submitted</span>

                                                <br>
                                                    <a href="{{url('result/exercise/'.$exercise->id.'/submitgradebook?s='.$session->id.'&t='.$term->id)}}" onclick="return confirm('Are you sure you want to submit this exercise results?\nEnsure that all scores of students have been entered into the system before you procced.\n\nPlease note that once submitted, the system will NOT ALLOW GRADING of any student for this exercise.\nClick Ok to proccess or Cancel to stop this action')">
                                                        <button type="button" class="btn btn-icon btn-sm" title="View Grade Book"><i class="fa fa-check"></i> Click To Submit Exercise Grade Book</button>
                                                    </a>
                                                <br>
                                                    <span style="font-size: 12px;">You must submit the exercise Grade Book before it becomes available in students results. Ensure that all scores of students have been entered into the system before you procced submit. Please note that once submitted, the system will NOT ALLOW GRADING of any student for this exercise.</span>
                                            @endif

                                        </p>
                                    </div>
                                </div>
                            @endif
                        @endif
                        
                        <?php
                            $depts = '';
                            $divs = '';
                            $is_joint = 0;
                            if($exercise->dept_id && $exercise->course_id){
                                $dept = get_dept($exercise->dept_id);
                                if($dept->is_joint){
                                    $is_joint = 1;
                                    $depts = get_dept();
                                    $divs = get_div_by_course($exercise->course_id);
                                }else{
                                    $divs = get_div_by_dept($dept->id, $exercise->course_id);
                                }
                            }
                        ?>
                        @if($divs)
                            <div class="card">
                                <div class="card-body">

                                    <h5> {{$exercise->name}} PROFICIENCY for <u>Term/Session</u>: {{$term->name}}/{{$session->name}}</h5>

                                    <div class="row">
                                        @if($is_joint)
                                            @if($depts)
                                                <div class="col-sm-6">
                                                    <b>Filter Students by Department:</b> 
                                                    <form action="{{url('result/exercise/'.$exercise->id)}}" method="GET">
                                                        <input type="hidden" name="s" value="{{$session->id}}" />
                                                        <input type="hidden" name="t" value="{{$term->id}}" />
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label>Department</label>
                                                                <select name="dept" class="form-control">
                                                                    <option value="0">All</option>
                                                                    @foreach($depts as $item)
                                                                        <option value="{{$item->id}}" {{($item->id == $dept_id)? 'selected' : ''}}>{{$item->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label>Show Only Organic Students?</label>
                                                                <select name="organic" class="form-control" required>
                                                                    <option value="0">No</option>
                                                                    <option value="1">Yes</option>                                                                    
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <button class="btn btn-primary btn-lg btn-simple">Go</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif
                                        @endif

                                        @if($divs)
                                                <div class="col-sm-6">
                                                    <b>Filter Students by Divisions:</b> 
                                                    <form action="{{url('result/exercise/'.$exercise->id)}}" method="GET">
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

                    </div>

                </div>
            </div>

		</div>
	</div>
</div>

<script>
    $('input[name="mark_across"]').keyup(function(){
        markAcrossBoard($(this).val());
    });

    function markAcrossBoard(mark)
    {  
        console.log('applying marks accross board');
        $('input[name^="student[grade]"]').each(function() {
            //totalAssigned += parseInt(this.value);
            this.value = mark
        });
    }
</script>

@endsection