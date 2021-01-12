@extends('layout')

<?php 

    $page_title = "ARMED FORCES COMMAND AND STAFF COLLEGE | $sub_details_menu";
    $n = 0; 
    $position = 1;
    $previous_student_score = 0;    
    $is_academic = is_academic();

    $wp_total = 0;

    $terms = get_realeased_result_terms_by_dept($dept_id, 1);
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
                            <h5><u>ARMED FORCES COMMAND AND STAFF COLLEGE JAJI NIGERIA</u></h5>
                            <h6>{!!$sub_details!!}</h6>
                            <br>
                        </div>

                        @if(count($terms))
                            
                            <table class="table mb-0 dataTable text-nowrap" data-page-length='100'>
                                
                                <thead>

                                    <tr>
                                        <td colspan="3"></td>

                                        @foreach($terms as $term)
                                            <td colspan="2">{{$term->name}}</td> 
                                        @endforeach
                                        <td colspan="3" style="text-align: center;"><b>Cumulative</b></td>

                                        <td></td>                                    
                                    
                                    </tr>

                                    <tr>
                                        <th>Serial</th>
                                        <th>Rank</th>
                                        <th>Name</th>

                                        @foreach($terms as $term)
                                            <th>Score</th>                                        
                                            <th>Grade</th>
                                        @endforeach

                                        <th>Score</th>
                                        <th>Grade</th>
                                        <th>Posn</th>

                                        <th>Rmk</th>
                                    </tr>

                                </thead>
                                <tbody>

                                    <tr class="tr-print">
                                        
                                        <td colspan="3" class="border-right border-left"></td>
                                        <td style="display:none"></td>
                                        <td style="display:none"></td>

                                        @foreach($terms as $term)
                                            <td colspan="2">{{$term->name}}</td>
                                            <td style="display:none"></td>
                                        @endforeach

                                        <!--cumulative-->
                                        <td colspan="4" class="border-right">Cumulative</td>
                                        <td style="display:none"></td>
                                        <td style="display:none"></td>
                                        <td style="display:none"></td>
                                    
                                    </tr>

                                    @foreach($students as $student)
                                        <tr>
                                            <?php                                             
                                                
                                                $total_student_wp = 0;
                                            ?>

                                            <td class="border-right border-left">{{++$n}}</td>                                            
                                            
                                            <td>{{$student->rank}}</td>
                                            <td class="border-right">
                                                <a href="{{url('user/'.$student->id)}}" target="_blank">{{$student->surname .' '.$student->first_name}}</a>
                                            </td> 

                                            @foreach($terms as $term)
                                                
                                                <?php
                                                    $result = get_student_result_statistics_in_term($student->id,$session->id, $term->id);
                                                    $score = 0;
                                                    if($result){
                                                        $score = $result['term_score'];
                                                    }
                                                    
                                                ?>

                                                @if($result)
                                                    <td>{{$score}}</td>
                                                    <td class="border-right">{{get_grade($score)}}</td>
                                                @else
                                                    <td>-</td><td class="border-right">-</td>
                                                @endif
                                                
                                            @endforeach

                                            <?php 
                                               if($student->total_score < $previous_student_score){
                                                    $position++;
                                                }
                                                $previous_student_score = $student->total_score; 
                                            ?>

                                            <!--cumulative-->
                                            <td class="border-right">{{$student->total_score}}</td>
                                            <td style="border-right: 1px solid black;">{{get_grade($student->total_score)}}</td>
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

                                    @if($n && $is_academic)
                                        <tr>
                                            <td style="display:none">{{$n+5}}</td>
                                            <td  colspan="3" class="border-right border-left"></td>
                                            <td style="display:none"></td>                                            

                                            @foreach($terms as $term)
                                                <td colspan="2" class="border-right"><a href="{{url('result/show?s='.$session->id.'&t='.$term->id.'&div='.$div_id.'&dept='.$dept_id.'&c='.$course->id)}}" target="_blank">View Term Result</a></td>
                                                <td style="display:none"></td>
                                            @endforeach  

                                            <td colspan="4" class="border-right"></td> 
                                            <td style="display:none"></td>
                                            <td style="display:none"></td>
                                            <td style="display:none"></td>                                      
                                        </tr>                                   
                                    @endif

                                </tbody>
                            </table>
                            <hr>

                            <?php
                                $depts = '';
                                $divs = '';
                                //$depts = get_dept();
                                //$divs = get_div_by_course($course->id);
                                $depts = get_realeased_result_depts_by_session($session->id);
                                //$divs = get_realeased_result_divs_by_session($session->id);
                            ?>
                            @if($depts)
                            
                                <div class="card">
                                    <div class="card-body">

                                        <h5>Results for <u>Course</u>: {{$course->name}} | <u>Session</u>: {{$session->name}}</h5>

                                        <div class="row">
                                            @if($depts)
                                                <div class="col-sm-12">
                                                    <b>Filter Students by Department:</b> 
                                                    <form action="{{url('result/show')}}" method="GET">
                                                        <input type="hidden" name="c" value="{{$course->id}}" />
                                                        <input type="hidden" name="s" value="{{$session->id}}" />
                                                        <input type="hidden" name="t" value="0" />
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
                            <p><i>No term with released results</i></p>
                        @endif

                    </div>

                </div>
            </div>

		</div>
	</div>
</div>


@endsection