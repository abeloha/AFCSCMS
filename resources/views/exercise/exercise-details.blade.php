@extends('layout')

<?php 
    $page_title = 'Exercises Details';
    $can_add = check_has_ability('manage_exercise');
    $user_id = get_user_id();
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
        <?php                    
            $success = false;
            if(isset($_GET['success'])){
                $success = true;
            }
            $deleted = false;
            if(isset($_GET['deleted'])){
                $deleted = true;
            } 
            $addedreq = false;
            if(isset($_GET['addedreq'])){
                $addedreq = true;
            }                
        ?>
        @if($success)
            <div class="alert alert-info">
                <p>New exercise material has been added successfully</p>
            </div>
        @endif
        @if($deleted)
            <div class="alert alert-danger">
                <p>The exercise material has been deleted successfully</p>
            </div>
        @endif
        @if($addedreq)
            <div class="alert alert-info">
                <p>New exercise requirement has been added successfully</p>
            </div>
        @endif

		<div class="row">            

            @if($item)

                <div class="col-xl-4 col-lg-5 col-md-12">
                
                    @include('exercise.exercise-card',['item'=>$item])
                    
                </div>

                <?php
                    $can_manage_materials = check_can_manage_exercise_materials($item->id);
                    $is_exercise_grader = is_exercise_grader($item->id, $user_id);

                ?>
                <div class="col-xl-8 col-lg-7 col-md-12">
                    
                    @if($can_manage_materials)
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Exercise Grade Book</h3>
                                <div class="card-options ">
                                    <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p>

                                    <?php
                                        $session = get_current_session($item->course_id);
                                        $term = get_current_term($item->course_id);

                                        if($session && $term){
                                            $released_exercise_result = get_released_exercise_result($item->id, $term, $session);
                                    ?>
                                        <b>Grade Book Status:</b> 
                                            @if($released_exercise_result)
                                                <span class="tag tag-success">Submitted on {{date('d/m/Y', strtotime($released_exercise_result->created_at))}}</span>
                                            @else
                                                <span class="tag tag-warning">Not Submitted</span>
                                            @endif

                                        <br>
                                            <a href="{{url('result/exercise/'.$item->id.'/?s='.$session.'&t='.$term)}}" target="_blank"><button type="button" class="btn btn-icon btn-sm" title="View Grade Book"><i class="fa fa-check"></i> Click To View Exercise Grade Book</button></a>
                                        <br>
                                        <span style="font-size: 11px;">You must submit the exercise Grade Book before it becomes available in students results.</span>

                                    <?php
                                        }else{
                                            echo '<i>Current session and/or term could not be determined. Contact system administrator</i>';
                                        }
                                    ?>

                                </p>
                            </div>
                        </div>
                    @endif
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{$item->name}}</h3> 
                            <div class="card-options ">
                                <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                            </div>
                        </div>
                        <div class="card-body">

                            {{$item->description}}
                            
                            <?php
                                $materials = get_exercise_materials($item->id);
                            ?>

                            <h5 class="mt-4"><i class="fa fa-file-text text-danger"></i> Study Matterials</h5>

                            @if($can_manage_materials)
                                <a href="{{url('exercise/'.$item->id.'/material')}}"><button type="button" class="btn btn-icon btn-sm" title="Add"><i class="fa fa-plus"></i> Click To Add New Study Material To This Exercise</button></a>
                            @endif

                            @if(count($materials)) 
                                <ul class="list-group"> 
                                    @foreach ($materials as $material)
                                        <?php
                                            $file_number = 0;
                                            if($material->file_1){$file_number++;}
                                            if($material->file_2){$file_number++;}
                                            if($material->file_3){$file_number++;}
                                        ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-left">
                                            <a href="{{url('exercise/material/'.$material->id)}}">{{$material->title}}</a>
                                            @if($file_number)
                                                <span class="badge badge-primary badge-pill">{{$file_number}} Attachment(s)</span></li>
                                            @endif
                                        </li>
                                    @endforeach 
                                </ul>
                            @else
                                <p><i>No study material added yet</i></p>
                            @endif                 
                            
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list text-danger"></i> Exercise Requirement</h3> 
                            <div class="card-options ">
                                <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                            </div>
                        </div>
                        <div class="card-body">  

                            <?php
                                $requirements = array();
                                $req_count = 0; 
                                $can_view_req = check_can_view_exercise_requirements($item->id);                                                 
                                $iam_enrolled = check_if_exercise_is_enrolled($item->id);

                                $admin_view = 0; $show_req = 0;
                                if($can_view_req || $can_manage_materials || $is_exercise_grader){
                                    $admin_view = 1;
                                }

                                if($admin_view || $iam_enrolled){
                                    $show_req = 1;
                                    $requirements = get_exercise_requirement($item->id);
                                }
                            ?> 

                            @if(count($requirements) && $show_req)
                                <ul>
                                        
                                    @foreach($requirements as $requirement)
                                        <?php                                             
                                            $show_this = 0;

                                            if($admin_view){
                                                $show_this = 1;
                                            }else{                                                

                                                if($requirement->req_type == 1){
                                                    $time_now = strtotime(date('Y-m-d H:i'));
                                                    
                                                    if($requirement->show_at){
                                                        $show_time = strtotime($requirement->show_at);
                                                    }else{
                                                        $show_time = strtotime($requirement->start_at);
                                                    }
                                                    
                                                    if($time_now >= $show_time){
                                                        $show_this = 1;
                                                    }
                                                }

                                            }     
                                            //h:i a, D d, M, Y                                   
                                        ?>
                                        @if($show_this)
                                            <?php $req_count++; ?>                                        
                                            <li>
                                                <h4><a href="{{url('exercise/requirement/'.$requirement->id)}}">{{$requirement->title}}</a></h4>
                                                @if($requirement->req_type == 1)
                                                    <div>
                                                        <span id="count_down_{{$requirement->id}}" class="tag tag-danger">0.00</span>
                                                        <br><b>Submission Starts:</b> {{date('h:i a, D d, M, Y', strtotime($requirement->start_at))}} 
                                                        <br><b>Submission Stops:</b> {{date('h:i a, D d, M, Y', strtotime($requirement->end_at))}}                                                         
                                                        <br>

                                                        

                                                        @if($admin_view)
                                                            <b>Shown To students By:</b> {{date('h:i a, D d, M, Y', strtotime($requirement->show_at))}} 
                                                            <br><b>Marks: </b>{{$requirement->marks}}
                                                            <br>
                                                            <a href="{{url('exercise/requirement/'.$requirement->id)}}">Click to View requirement details</a>
                                                        @elseif($iam_enrolled)
                                                            <?php $submission = get_my_requirement_submission($requirement->id); ?>
                                                            @if($submission)
                                                            <a href="{{url('exercise/requirement/'.$requirement->id)}}">Click to view your submission</a> - <span class="text-green">You made submission at {{date('h:i a, D d, M, Y', strtotime($submission->created_at))}}.</span>
                                                            @else
                                                                <h3><a href="{{url('exercise/requirement/'.$requirement->id)}}">Click to make submission</a> - <span class="text-red">You have not made any submission yet.</span></h3>
                                                            @endif                                                            
                                                        @endif
                                                        @if($is_exercise_grader)
                                                            <br>
                                                            <h3><a href="{{url('exercise/'.$item->id.'/grade')}}">Click to grade students' submissions</a></h3>
                                                        @endif
                                                    </div>

                                                    <script>
                                                        // Set the date we're counting down"
                                                        var stopDate = "{{date('M d, Y H:i', strtotime($requirement->end_at))}}";
                                                        var divId = "count_down_{{$requirement->id}}";                                                     
                                                        createCountDown(divId, stopDate);
                                                    </script>


                                                @elseif($admin_view)
                                                    <b>Marks: </b>{{$requirement->marks}}
                                                    <br><a href="{{url('exercise/requirement/'.$requirement->id.'/grade')}}">Student grades</a>                                            
                                                @endif

                                            </li>
                                            <hr>
                                        @endif
                                    @endforeach

                                </ul>
                            @endif

                            @if(!$req_count)
                                @if($show_req)
                                    <p><i>No requirement yet.</i></p>
                                @else
                                    <p><i>You are not enrolled for this exercise.</i></p>
                                @endif
                            @endif                         
                            
                        </div>
                    </div>

                    @if($can_manage_materials)
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Add New Exercise Requirements</h3>
                                <div class="card-options ">
                                    <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p>
                                    <a href="{{url('exercise/'.$item->id.'/requirement/add?type=1')}}"><button type="button" class="btn btn-icon btn-sm" title="Add"><i class="fa fa-plus"></i> Click To Add New Written Requirement To This Exercise</button></a>
                                    <br>
                                    <span style="font-size: 11px;"><b>Written Requirements:</b> This requirement is shown to students and accepts submission from student</span>
                                </p>
                                <p>
                                    <a href="{{url('exercise/'.$item->id.'/requirement/add?type=2')}}"><button type="button" class="btn btn-icon btn-sm" title="Add"><i class="fa fa-plus"></i> Click To Add New UTW Requirement To This Exercise</button></a>
                                    <br>
                                    <span style="font-size: 11px;"><b>UTW Requirements:</b> This requirement is hidden from student and does not accept submissions. It can be used for Oral assessments</span>
                                </p>
                            </div>
                        </div>
                    @endif 

                    <?php
                        $my_divs = get_tc_divs();

                        $qualified_divs = array();

                        $show_assign_students_grader_link = 0;
                        if(count($my_divs))
                        {

                            $arr_depts = get_dept_joint_ids();  

                            foreach ($my_divs as $my_div) {
                                array_push($arr_depts, $my_div->dept_id);
                                if(in_array($item->dept_id,$arr_depts) && $item->course_id == $my_div->course_id){
                                    $show_assign_students_grader_link = 1;
                                    array_push($qualified_divs, $my_div);
                                }
                            }

                        }
                    ?>

                    @if($qualified_divs)
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Assign DS for Grading </h3>
                                <div class="card-options ">
                                    <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p>
                                    @foreach ($qualified_divs as $qualified_div)                                    
                                        <a href="{{url('exercise/'.$item->id.'/assigngrader?div='.$qualified_div->id)}}"><button type="button" class="btn btn-icon btn-sm" title="Add"><i class="fa fa-plus"></i> Click To Assign DS to Grade Students Submissions in <b>{{$qualified_div->name}}</b></button></a>
                                        <br>
                                    @endforeach
                                    <hr>
                                    <span style="font-size: 14px;"><b>You can assign a given number of solutions submitted by students in this exercise to DS for assessment</span>
                                </p> 
                                
                            </div>
                        </div>
                    @endif

                </div>  

            @else
                <div class="col-md-12">
                    <i>Exercise details could not be loaded now or exercise has been removed.</i>
                </div>
            @endif
		</div>
	
    </div>
</div>
   

@endsection