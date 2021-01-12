@extends('layout')

<?php 
    $page_title = 'Results Proccessing';
    $courses = get_course();
    $depts = get_dept();
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
                    
                    <div class="col-md-12">
                           
                        <!--DS exercises assigned to a ds as sponsor or co-sponsor -->
                        <?php 
                            $exercises = get_ds_exercise(); 
                            $n = 0;
                        ?>
                        @if(count($exercises))
                            <div class="card">
                                <div class="card-body">

                                    <div>
                                        <h4> DS Exercise Grade Book</h4>
                                        <table class="table mb-0 text-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Exercise</th>
                                                    <th>Result Status</th>
                                                    <th>Options</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($exercises as $exercise)
                                                    <?php 
                                                        $term = get_current_term_data_of_exercise($exercise->id);
                                                        $session = get_current_session_data_of_exercise($exercise->id);
                                                    ?>
                                                    <tr>
                                                        <td>{{++$n}}</td>
                                                        <td>{{$exercise->name}}</td>

                                                        @if($term && $session)
                                                            <?php $released_exercise_result = get_released_exercise_result($exercise->id, $term->id, $session->id); ?>
                                                            <td>
                                                                @if($released_exercise_result)
                                                                    <span class="tag tag-success">Submitted on {{date('d/m/Y', strtotime($released_exercise_result->created_at))}}</span>
                                                                @else
                                                                    <span class="tag tag-warning">Not Submitted</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{url('result/exercise/'.$exercise->id.'/?s='.$session->id.'&t='.$term->id)}}"><i class="fa fa-check"></i> View/Submit Grade Book</a>
                                                            </td>
                                                            <td>{{$term->name .'/'.$session->name}}</td>
                                                        @else
                                                            <td>-</td>
                                                            <td>-</td>
                                                            <td><i>Current Term and/or Session has not been set.</i></td>
                                                        @endif



                                                    </tr>

                                                @endforeach                                           
                                            </tbody>
                                        </table>
                                        
                                    </div>

                                </div>                                
                            </div>
                        @endif 

                        <!--TC logic -->
                        <?php 
                            $divs = get_tc_divs(); 
                            $n = 0;
                        ?>
                        @if(count($divs))
                            <h3>TC's Result Reports</h3>
                            <div class="card">
                                <div class="card-body"> 
                                    <h4>Exercise Grade Book Review </h4>                                  
                                    @foreach($divs as $div)
                                        <?php 
                                            $term = get_current_term_data_by_course($div->course_id);
                                            $session = get_current_session_data_by_course($div->course_id);

                                            $not_submitted = 0;
                                            $exercises = get_exercise_available_for_enrollment_by_dept($div->dept_id,$div->course_id, $term->id);
                                            $n = 0;
                                        ?>
                                        @if($term && $session)

                                            @if(count($exercises))
                                                <div>
                                                    <h6><u>Div</u>: {{$div->name}} for <u>term/session</u>: {{$term->name.'/'.$session->name}})</h6>
                                                    <table class="table mb-0 text-nowrap">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Exercise</th>
                                                                <th>Result Status</th>
                                                                <th>Options</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($exercises as $exercise)
                                                                <tr>
                                                                    <td>{{++$n}}</td>
                                                                    <td>{{$exercise->name}}</td>
                                                                    
                                                                    <?php 
                                                                        $released_exercise_result = get_released_exercise_result($exercise->id, $term->id, $session->id); 
                                                                    ?>

                                                                    <td>
                                                                        @if($released_exercise_result)
                                                                            <span class="tag tag-success">Submitted on {{date('d/m/Y', strtotime($released_exercise_result->created_at))}}</span>
                                                                        @else
                                                                            <?php $not_submitted++ ?>
                                                                            <span class="tag tag-warning">Not Submitted</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <a href="{{url('result/exercise/'.$exercise->id.'/?s='.$session->id.'&t='.$term->id)}}"><i class="fa fa-check"></i> View Grade Book</a>
                                                                    </td>                                                                    
                                                                </tr>

                                                            @endforeach                                           
                                                        </tbody>
                                                    </table>
                                                    <div class="card-body">
                                                        <p>                                   
                                                            <b>Div Result Status:</b> 
                                                            
                                                            <?php $released_result = get_division_result_released($div->id, $div->course_id, $term->id, $session->id); ?>
                                                            
                                                            @if($released_result)
                                                                <span class="tag tag-success">Submitted to CI</span>
                                                            @else
                                                                <span class="tag tag-warning">Not Submitted to CI</span>
                                                                <br>                                                                
                                                                
                                                                @if($not_submitted)
                                                                    <b>The remaining ({{$not_submitted}}) ds must submit their exercise grade book before you can submit the div result to CI.</b>
                                                                @else

                                                                    <form action="{{url('result/processing/submit/tc')}}" method="POST" onsubmit="return confirm('Are you sure you want to submit this result to the CI?')" >
                                
                                                                        @csrf
                        
                                                                        <input type="hidden" name="div_id" value="{{$div->id}}" /> 
                                                                        <input type="hidden" name="t" value="{{$term->id}}" /> 
                                                                        <input type="hidden" name="s" value="{{$session->id}}" /> 
                        
                                                                        <div class="col-sm-12">
                                                                            <button class="btn btn-primary btn-lg btn-simple">Submit {{$div->name}} result to CI</button>
                                                                        </div>
                        
                                                                    </form>
                                                                
                                                                @endif
                                                                <br><span style="font-size: 12px;">You must submit the Div results to the CI for proccessing.</span>

                                                            @endif
                
                                                        </p>
                                                        <p><br><h6><a href="{{url('result/show?s='.$session->id.'&t='.$term->id.'&div='.$div->id.'&c='.$div->course_id)}}"><i class="fa fa-check"></i><b> View the result for {{$div->name}}</b></a></h6></p>
                                                        
                                                    </div>

                                                </div>
                                            @endif
                                        
                                        @else
                                            <i>Current Term and/or Session has not been set.</i>
                                        @endif

                                    <hr>
                                    @endforeach

                                </div>                                
                            </div>
                        @endif 

                        <!--CI Logic -->
                        <?php 
                            $divs = get_ci_divs(); 
                            $n = 0;
                        ?>
                        @if(count($divs))
                            <h3>CI's Result Reports</h3>
                            <div class="card">
                                <div class="card-body">
                                    <h4>CI Result Review </h4>
                                    @foreach($divs as $div)
                                        <?php 
                                            $term = get_current_term_data_by_course($div->course_id);
                                            $session = get_current_session_data_by_course($div->course_id);

                                            $not_submitted = 0;
                                            $exercises = get_exercise_available_for_enrollment_by_dept($div->dept_id,$div->course_id, $term->id);
                                            $n = 0;
                                        ?>
                                        @if($term && $session) 
                                            
                                            <h6><u>Div</u>: {{$div->name}} for <u>term/session</u>: {{$term->name.'/'.$session->name}})</h6>
                                            
                                            <div class="card-body">
                                                <p>                                   
                                                <b>Div Result Status:</b> 
                                                    
                                                    <?php $released_result = get_division_result_released($div->id, $div->course_id, $term->id, $session->id); ?>
                                                    
                                                    @if($released_result)

                                                        @if($released_result->approval)                                                            
                                                            <span class="tag tag-success">Submitted to Director</span>
                                                        @else
                                                            <span class="tag tag-warning">Not Submitted to Director</span><br>
                                                            <form action="{{url('result/processing/submit/ci')}}" method="POST" onsubmit="return confirm('Are you sure you want to submit this result to the director?')" >
                        
                                                                @csrf
                
                                                                <input type="hidden" name="released_result_id" value="{{$released_result->id}}" /> 
                                                                <input type="hidden" name="t" value="{{$term->id}}" /> 
                                                                <input type="hidden" name="s" value="{{$session->id}}" /> 
                
                                                                <div class="form-group">
                                                                    <label>CI's Comment</label>
                                                                    <input type="text" name="comment" value="" placeholder="Enter Comment on this result" class="form-control">
                                                                </div>

                                                                <div class="col-sm-12">
                                                                    <button class="btn btn-primary btn-lg btn-simple">Submit {{$div->name}} result to Director</button>
                                                                </div>
                
                                                            </form>                                                         
                                                            <br><span style="font-size: 12px;">You must submit the Div results to the Director for proccessing.</span>
                                                        @endif

                                                        <br><h6><a href="{{url('result/show?s='.$session->id.'&t='.$term->id.'&div='.$div->id.'&c='.$div->course_id)}}"><i class="fa fa-check"></i><b> View the result for {{$div->name}}</b></a></h6>
                                                            

                                                    @else
                                                        <span class="tag tag-danger">TC yet to submit Exercise Grade Books</span>
                                                        <br><b>The TC must submit the grade book from each exercises before you can proccess the result.</b>
                                                    @endif
        
                                                </p>
                                            </div>

                                        @else
                                            <i>Current Term and/or Session has not been set.</i>
                                        @endif                                           
                                        
                                        <hr>  
                                    @endforeach

                                </div>                                
                            </div>
                        @endif 

                        
                        <!--Director logic -->
                        <?php 
                            $depts = get_director_depts(); 
                            $courses = get_course();
                            $n = 0;
                        ?>
                        @if(count($depts) && count($courses))

                            <h3>Director's Result Reports</h3>

                            @foreach($courses as $course)
                                <div class="card">
                                    <div class="card-body">
                                        <h4>{{$course->name}}</h4>

                                        @foreach($depts as $dept)

                                            <?php 
                                                $divs = get_div_by_dept($dept->id, $course->id); 
                                                $term = get_current_term_data_by_course($course->id);
                                                $session = get_current_session_data_by_course($course->id);                                                
                                                $not_submitted = 0;                                               
                                            ?>

                                            @if(count($divs))
                                                <h6><u>Dept</u>: {{$dept->name}} for <u>term/session</u>: {{$term->name.'/'.$session->name}})</h6>
                                                        
                                                <table class="table mb-0 text-nowrap">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Divisions</th>
                                                            <th>Result Status</th>
                                                            <th>Options</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        @foreach($divs as $div)  
                                                       
                                                            <tr>
                                                                <td>{{++$n}}</td>
                                                                <td>{{$div->name}}</td>
                                                                
                                                                <?php $released_result = get_division_result_released($div->id, $course->id, $term->id, $session->id); ?>

                                                                
                                                                @if($released_result)
                                                                    @if($released_result->approval)
                                                                        <td> <span class="tag tag-success">Submitted by CI</span> </td>
                                                                        <td>
                                                                            <a href="{{url('result/show?s='.$session->id.'&t='.$term->id.'&div='.$div->id.'&c='.$course->id)}}"><i class="fa fa-check"></i> View Div Result</a>
                                                                        </td>
                                                                    @else
                                                                        <?php $not_submitted++ ?>
                                                                        <td><span class="tag tag-warning">Not Submitted by CI</span></td>
                                                                        <td>-</td>
                                                                    @endif
                                                                @else
                                                                    <?php $not_submitted++ ?>
                                                                    <td><span class="tag tag-warning">Not Submitted by CI</span></td>
                                                                    <td>-</td>
                                                                @endif
                                                                                                                                    
                                                            </tr>

                                                        @endforeach

                                                    </tbody>
                                                </table>

                                                <div class="card-body">
                                                    <p>                                   
                                                    <b>Department Result Status:</b> 
                                                        
                                                        <?php $released_result = get_department_result_released($dept->id, $course->id, $term->id, $session->id); ?>
                                                        
                                                        @if($released_result)
                                                            <span class="tag tag-success">Submitted to Commandant's Office</span>
                                                        @else
                                                            <span class="tag tag-warning">Not Submitted to Commandant's Office</span>
                                                            <br>
                                                            
                                                            @if($not_submitted)
                                                                <b>The remaining ({{$not_submitted}}) CI must submit their Division's result before you can submit the Department result to Commandant's Office.</b>
                                                            @else
                                                                <form action="{{url('result/processing/submit/dr')}}" method="POST" onsubmit="return confirm('Are you sure you want to submit this result to the Commandant?')" >
                            
                                                                    @csrf
                    
                                                                    <input type="hidden" name="dept_id" value="{{$dept->id}}" /> 
                                                                    <input type="hidden" name="t" value="{{$term->id}}" /> 
                                                                    <input type="hidden" name="s" value="{{$session->id}}" />
                                                                    <input type="hidden" name="c" value="{{$course->id}}" /> 
                    
                                                                    <div class="form-group">
                                                                        <label>Director's Comment</label>
                                                                        <input type="text" name="comment" value="" placeholder="Enter Comment on this result" class="form-control">
                                                                    </div>

                                                                    <div class="col-sm-12">
                                                                        <button class="btn btn-primary btn-lg btn-simple">Submit {{$dept->name}} result</button>
                                                                    </div>
                    
                                                                </form>
                                                            
                                                            @endif

                                                            <span style="font-size: 12px;">You must submit the Department results to the Commandant for proccessing.</span>

                                                        @endif

                                                        @if(!$not_submitted || $released_result)
                                                            <br>
                                                                <a href="{{url('result/show?s='.$session->id.'&t='.$term->id.'&dept='.$dept->id.'&c='.$course->id)}}"><i class="fa fa-check"></i> View Department ({{$dept->name}}) Result</a>
                                                            <br>
                                                        @endif
            
                                                    </p>
                                                </div>

                                                <hr>                                            
                                            @endif

                                        @endforeach

                                    </div>                                
                                </div>
                            @endforeach

                        @endif 


                       
                        <!--Comdt/Deputy Logic -->
                        <?php
                            $is_commandant = is_commandant();
                            $is_deputy_commandant = is_deputy_commandant();
                            $n = 0;
                        ?>
                        
                        @if($is_commandant || $is_deputy_commandant)

                            <?php 
                                $depts = get_dept();
                                $courses = get_course();
                            ?>
                            @if(count($depts) && count($courses))

                                <h3>{{($is_deputy_commandant)? 'Deputy':''}} Commandant's Result Reports</h3>

                                @foreach($courses as $course)
                                    <?php 
                                        $term = get_current_term_data_by_course($course->id);
                                        $session = get_current_session_data_by_course($course->id);
                                    ?>
                                    <div class="card">
                                        <div class="card-body">
                                            <h4>{{$course->name}}</h4>

                                            @if($term && $session)
                                                @foreach($depts as $dept)
                                                        
                                                    <h6><u>Dept</u>: <b>{{$dept->name}}</b> for <u>term/session</u>: {{$term->name.'/'.$session->name}})</h6>
                                                    
                                                    <div class="card-body">
                                                        <p>                                   
                                                        <b>Department Result Status:</b> 
                                                            
                                                            <?php $released_result = get_department_result_released($dept->id, $course->id, $term->id, $session->id); ?>
                                                            
                                                            @if($released_result)
                                                                @if($released_result->approval)                                                            
                                                                    <span class="tag tag-success">Result has been approved</span>
                                                                @else
                                                                    <span class="tag tag-warning">You haven't approve this result</span><br>
                                                                    <form action="{{url('result/processing/submit/comdt')}}" method="POST" onsubmit="return confirm('Are you sure you want to approve this department result?. Once you approve the result, the students grades cannot be edited or modified.')" >
                                
                                                                        @csrf
                        
                                                                        <input type="hidden" name="released_result_id" value="{{$released_result->id}}" /> 
                                                                        <input type="hidden" name="t" value="{{$term->id}}" /> 
                                                                        <input type="hidden" name="s" value="{{$session->id}}" /> 
                                                                        <input type="hidden" name="c" value="{{$course->id}}" /> 

                                                                        <input type="hidden" name="approver_type" value="{{($is_deputy_commandant)? 'deputy':'comdt'}}" /> 

                        
                                                                        <div class="form-group">
                                                                            <label>{{($is_deputy_commandant)? 'Deputy':''}} Commandant's Comment</label>
                                                                            <input type="text" name="comment" value="" placeholder="Enter Comment on this result" class="form-control">
                                                                        </div>

                                                                        <div class="col-sm-12">
                                                                            <button class="btn btn-primary btn-lg btn-simple">Approve {{$dept->name}} result</button>
                                                                            <br><span style="font-size:12px;">Once you approve the result, the students grades cannot be edited or modified</span>
                                                                        </div>
                        
                                                                    </form>
                                                                @endif
                                                                
                                                                <br><h6><a href="{{url('result/show?s='.$session->id.'&t='.$term->id.'&dept='.$dept->id.'&c='.$course->id)}}"><i class="fa fa-check"></i><b> View the result for {{$dept->name}}</b></a></h6>
                                                            @else
                                                                <span class="tag tag-danger">Director yet to submit results</span>
                                                                <br><b>The Director must submit the result for {{$dept->name}} before you can proccess the result.</b>
                                                            @endif
                
                                                        </p>
                                                    </div>                                         
                                                    
                                                    <hr>  
                                                @endforeach
                                            @else
                                                <p><i>Current term and/or session not set.</i></p>
                                            @endif

                                        </div>                                
                                    </div>
                                @endforeach

                            @endif

                        @endif
                    
                        <br>
                        <p>Results needing your attention will be shown here.</p>

                    </div>


                </div>
            </div>

		</div>
	</div>
</div>


@endsection