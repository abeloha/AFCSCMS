@extends('layout')

<?php 

    $page_title = "ARMED FORCES COMMAND AND STAFF COLLEGE | $sub_details_menu";
    $n = 0; 
    $position = 1;
    $previous_student_score = 0;    
    $is_academic = is_academic();

    $wp_total = 0;


    $release_type = 'div';
    $released_result_data = '';
    $approved = '';

    if($div_id){
        $release_type = 'div';
        $released_result_data = get_division_result_released($div_id, $course->id, $term->id, $session->id);
    }elseif($dept_id){
        $release_type = 'dept';
        $released_result_data = get_department_result_released($dept_id, $course->id, $term->id, $session->id);
    }

    if($released_result_data){
        if($released_result_data->approval){
            $approved = 1;
        }
    }
    
    $count_exercises = count($exercises);
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
                        
                        <div class="text-center">
                            <br>
                            <h5><u>ARMED FORCES COMMAND AND STAFF COLLEGE NIGERIA</u></h5>
                            <h6>{!!$sub_details!!}</h6>
                            
                            @if(!$approved)
                                @if($release_type == 'dept')
                                    <br><span class="text-warning">Result not yet approved by Commandant's office.</span>
                                @elseif($release_type == 'div')
                                    <br><span class="text-warning">Result not yet approved by CI.</span>
                                @endif
                            @endif 

                            <br>
                        </div>

                        @if($count_exercises)

                            <table class="table mb-0 dataTable text-nowrap" data-page-length='100'>
                                
                                <thead>

                                    <tr>
                                        <td colspan="4"> </td>
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

                                        <th>{{($show_identity)? 'Name' : 'Code' }}</th>

                                        <th>Syn</th>
                                        @foreach($exercises as $exercise)
                                            <th>Score</th>
                                            <th>W/P
                                                <br><span style="font-size:10px">({{$exercise->weighted_point}})</span>
                                            </th> 
                                            <th>Grade</th>
                                            <?php $wp_total += $exercise->weighted_point; ?>
                                        @endforeach
                                        <th>W/P</th>
                                        <th>Score</th>
                                        <th>Grade</th>
                                        <th>Posn</th>

                                        <th>Score</th>
                                        <th>Grade</th>

                                        <th>Rmk</th>
                                    </tr>

                                </thead>
                                <tbody>

                                    <tr class="tr-print">
                                        <td colspan="4" class="header-print"> </td>
                                        <td style="display: none;"></td>
                                        <td style="display: none;"></td>
                                        <td style="display: none;"></td>

                                        @foreach($exercises as $exercise)
                                            <td colspan="3" class="header-print">{{$exercise->name}}</td>                                            
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                        @endforeach
                                        <td colspan="4" class="header-print"><b>Term Total</b></td>                                        
                                        <td style="display: none;"></td>
                                        <td style="display: none;"></td>
                                        <td style="display: none;"></td>


                                        <td colspan="2" class="header-print"><b>Cumulative</b></td>
                                        <td style="display: none;"></td>

                                        <td></td>
                                    </tr>

                                    @foreach($students as $student)
                                    <?php                                             
                                        $syndicate = get_student_syndicate($student->id, $term->id, $session->id); 

                                        $syndicate_name = '';
                                        if($syndicate){
                                            $syndicate_name = shorten_syndicate_name($syndicate->name);
                                        }

                                        $total_student_wp = 0;
                                    ?>

                                    <tr>
                                        <td class="border-right border-left">{{++$n}}</td>
                                        
                                        @if($show_identity)
                                            <td>{{$student->rank}}</td>
                                            <td class="border-right">
                                                <a href="{{url('user/'.$student->id)}}" target="_blank">{{$student->surname .' '.$student->first_name}}</a>
                                            </td>
                                            <td class="border-right">{{$syndicate_name}}</td>
                                        @else
                                            <td>-</td>
                                            <td class="border-right">
                                                {{user_id_to_code($student->id)}}
                                            </td>
                                            <td class="border-right">-</td>
                                        @endif

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

                                                $term_wp =number_format($student->term_wp,2);
                                            ?>

                                            <td class="border-right" style="border-left: 1px solid black;">{{$term_wp}}</td>
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
                                    </tr>
                                    @endforeach

                                    
                                    @if($n && $is_academic)
                                        <tr>
                                            <td style="display: none">{{($n + 5)}}</td> <!--DataTable sorts by serial, let this come last -->
                                            <td colspan="4" class="border-left border-right"></td>
                                            <td style="display: none"></td> <!--DataTable Hack as it does not support colspan -->
                                            <td style="display: none"></td>
                                            

                                            @foreach($exercises as $exercise)
                                                <td colspan="3" class="border-right"><a href="{{url('result/exercise/'.$exercise->id.'/?s='.$session->id.'&t='.$term->id.'&div='.$div_id.'&dept='.$dept_id)}}" target="_blank">View exercise grade</a></td>
                                                <td style="display:none"></td>
                                                <td style="display:none"></td>                                                
                                            @endforeach   
                                                                                
                                            <td colspan="7" class="border-right"></td>                                             
                                            <td style="display:none"></td>                                      
                                            <td style="display:none"></td>                                      
                                            <td style="display:none"></td>                                      
                                            <td style="display:none"></td>                                      
                                            <td style="display:none"></td>                                      
                                            <td style="display:none"></td>
                                        </tr>                                   
                                    @endif

                                    
                                    @if($released_result_data)
                                        
                                        <?php
                                            $no_exercise_td = $count_exercises * 3 + 11;
                                            $no_hidden_td = $no_exercise_td - 2
                                        ?>

                                        
                                        @if($released_result_data->ci_comment)
                                            <tr> 
                                                <td style="display:none">{{($n + 5)}}</td>                                                                            
                                                <td colspan="{{$no_exercise_td}}" class="border-right"><b>CI's Comment</b>: {{$released_result_data->ci_comment}}</td>                                       
                                                @for($i = 0; $i < $no_hidden_td; $i++)
                                                    <td style="display:none"> - </td> 
                                                @endfor
                                            </tr> 
                                        @endif

                                        @if($released_result_data->director_comment)
                                            <tr>   
                                                <td style="display:none">{{($n + 6)}}</td>                                                                            
                                                <td colspan="{{$no_exercise_td}}" class="border-right"><b>Director's Comment</b>: {{$released_result_data->director_comment}}</td>                                       
                                                @for($i = 0; $i < $no_hidden_td; $i++)
                                                    <td style="display:none"> - </td> 
                                                @endfor
                                            </tr> 
                                        @endif

                                        
                                        @if($released_result_data->depty_cmd_comment)
                                            <tr>    
                                                <td style="display:none">{{($n + 7)}}</td>                                                                             
                                                <td colspan="{{$no_exercise_td}}" class="border-right"><b>Deputy Cmdt's Comment</b>: {{$released_result_data->depty_cmd_comment}}</td>                                       
                                                @for($i = 0; $i < $no_hidden_td; $i++)
                                                    <td style="display:none"> - </td> 
                                                @endfor
                                            </tr> 
                                        @endif

                                        @if($released_result_data->cmd_comment)
                                            <tr>           
                                                <td style="display:none">{{($n + 8)}}</td>                                                                      
                                                <td colspan="{{$no_exercise_td}}" class="border-right"><b>Cmdt's Comment</b>: {{$released_result_data->cmd_comment}}</td>                                       
                                                @for($i = 0; $i < $no_hidden_td; $i++)
                                                    <td style="display:none"> - </td> 
                                                @endfor
                                            </tr> 
                                        @endif

                                    
                                                                        
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
                                <hr>
                            @endif

                            <?php
                                $depts = '';
                                $divs = '';
                                $is_joint = $term->is_joint;
                                //$depts = get_dept();
                                //$divs = get_div_by_course($course->id);
                                $depts = get_realeased_result_depts_by_session($session->id);
                                $divs = get_realeased_result_divs_by_session($session->id);
                            ?>
                            @if($divs || $depts)
                            
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
                                                                        <option value="0" {{($organic)? '':'selected'}}>No</option>
                                                                        <option value="1" {{($organic)? 'selected':''}}>Yes</option>
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
                                                                        <option value="0" {{($organic)? '':'selected'}}>No</option>
                                                                        <option value="1" {{($organic)? 'selected':''}}>Yes</option>
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