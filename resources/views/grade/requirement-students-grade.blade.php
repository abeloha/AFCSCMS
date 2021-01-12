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
	
		<div class="tab-content">
			<div class="tab-pane active">

                <?php
                    $n = 0;
                    $user_id = get_user_id();
                    $grader_name = '';
                    $grader_id = 0;
                    if($user_id){
                        $user_data = get_user($user_id);
                        if($user_data){
                            $grader_name = $user_data->rank.' '.$user_data->surname.' '.$user_data->first_name;
                            $grader_id = $user_data->id;
                        }
                    }
                    

                    $grading_open = is_grading_open($exercise->id)
                ?>

				<div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{$header_title}}</h3>
                    </div>

					<div class="card-body">                            
                        <p>
                            <b>Exercise:</b> {{$exercise->name}}
                            <br><b>Requirement:</b> {{$requirement->title}}
                            <br><b>Max-score:</b> {{$requirement->marks}}
                        </p>
                        @if($collection)
                            <div class="table-responsive">
                                <form method="POST" onsubmit="return confirm('Are you yure you want to submit these students grades?\nThe grades entered cannot be edited after you submit!')" action="{{url('exercise/requirement/grade/utw')}}">
												
                                    <input type="hidden" name="requirement_id" value="{{$requirement->id}}" />
                                    <input type="hidden" name="requirement_name" value="{{$requirement->title}}" />
                                    <input type="hidden" name="grader_name" value="{{$grader_name}}" />
                                    <input type="hidden" name="grader_id" value="{{$grader_id}}" />
                                    <input type="hidden" name="exercise_id" value="{{$exercise->id}}" />
                                    
                                    @csrf

                                    <table class="table table-hover js-basic-example table-striped table_custom border-style spacing5" data-page-length='250'>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Pix</th>
                                                <th>Rank</th>
                                                <th>Surname</th>
                                                <th>Firstname</th>
                                                <th>Other name</th>
                                                <th>Score</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach($collection as $item)
                                                <?php
                                                    $score = get_requirement_grade($requirement->id, $item->user_id);
                                                   

                                                    if($score != '' || $score == '0'){
                                                        continue;
                                                    }
                                                    
                                                    

                                                    $img = 'default.png';
                                                    if($item->picture){
                                                        $img = $item->picture;
                                                    }
                                                ?>
                                                <tr>
                                                    <td>{{++$n}}</td>
                                                    <td class="w60">
                                                        <img class="avatar" src="{{asset('storage/user/'.$img)}}" alt="{{$item->surname}}">
                                                    </td>
                                                    <td>{{$item->rank}}</td>
                                                    <td><a href="{{url('user/'.$item->user_id)}}" target="_blank">{{$item->surname}}</a></td>
                                                    <td><a href="{{url('user/'.$item->user_id)}}" target="_blank">{{$item->first_name}}</a></td>                                                    
                                                    <td><a href="{{url('user/'.$item->user_id)}}" target="_blank">{{$item->other_name}}</a></td>
                                                    <td>
                                                        <input type="hidden" name="student[id][]"  value="{{$item->user_id}}"/>
                                                        <input type="hidden" name="student[enrollment][]"  value="{{$item->enrollment_id}}"/>
                                                        <input type="number" name="student[grade][]" max="{{$requirement->marks}}" min="0" step="any" {{($grading_open)? '':'readonly'}} required/> out of {{$requirement->marks}}
                                                    </td>
                                                </tr>
                                            @endforeach 
                                                                                  
                                        </tbody>
                                    </table>
                                    @if($n)
                                        @if($grading_open)
                                            <div class="col-sm-12">
                                                <button class="btn btn-primary btn-lg btn-simple">Save Student Grades</button>
                                            </div>
                                        @else
                                            <b><i>Gradding has been closed as result has been submitted.</b>
                                        @endif
                                    @endif

                                </form>
                            </div>
                        @endif
                        @if(!$n)
                            <p>No student for grading</p>
                        @endif                      

                    </div>

                </div>
                
                @if(is_student() ||  check_if_exercise_is_enrolled($item->id))
                <!-- is student -->
                @else
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Grading Instruction</h3>
                            <div class="card-options ">
                                <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            {!!$requirement->grading_instruction!!} 
                            <br>  
                            @if($requirement->grading_file_1)                                                                  
                                <a href="{{asset('storage/requirement/'.$requirement->grading_file_1)}}" download="AFCSC_grading_instruction_{{$requirement->grading_file_1}}">
                                    <div class="file-name">
                                        <p class="mb-0 text-muted">
                                            <i class="fa fa-file text-success"></i> Green File (Click to download)
                                        </p>
                                    </div>
                                </a>
                            @endif
                            @if($requirement->grading_file_2)                                                                  
                                <a href="{{asset('storage/requirement/'.$requirement->grading_file_2)}}" download="AFCSC_grading_instruction_{{$requirement->grading_file_2}}">
                                    <div class="file-name">
                                        <p class="mb-0 text-muted">
                                            <i class="fa fa-file text-success"></i> Score Sheet (Click to download)
                                        </p>
                                    </div>
                                </a>
                            @endif

                        </div>
                    </div>
                @endif



			</div>
            
		</div>
	
    </div>
</div>

@endsection