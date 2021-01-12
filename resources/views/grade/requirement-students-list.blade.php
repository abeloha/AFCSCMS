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
                    $not_graded = 0; 
                    $per_cent_multiplier = (100/$requirement->marks)
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

                                <table class="table table-hover dataTable js-basic-example table-striped table_custom border-style spacing5" data-page-length='100'>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Pix</th>
                                            <th>Rank</th>
                                            <th>Surname</th>
                                            <th>Firstname</th>
                                            <th>Other name</th>
                                            <th>Score<br>
                                                <span style="font-size:10px;">Out of {{$requirement->marks}}<span>
                                            </th>
                                            <th>%<br>
                                                <span style="font-size:10px;">Out of 100%<span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach($collection as $item)
                                            <?php                                                
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
                                                    <?php
                                                        $score = get_requirement_grade($requirement->id, $item->user_id);
                                                        if($score == '' && $score != '0'){
                                                            echo '-';
                                                            $not_graded ++;

                                                        }else{
                                                            echo $score;
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        if($score || $score == '0'){
                                                            echo number_format(($score * $per_cent_multiplier),2).'%';
                                                        }else{
                                                            echo '-';
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                        @endforeach 
                                                                                
                                    </tbody>
                                </table>
                                @if($not_graded && check_can_manage_exercise_materials($exercise->id) && is_grading_open($exercise->id))
                                    <div class="col-sm-12">
                                        <a href="{{url('exercise/requirement/'.$requirement->id.'/grade/utw?action=grade')}}"><button class="btn btn-primary btn-lg btn-simple">Click To Enter Student Grades</button></a>
                                    </div>
                                @endif
                            </div>
                        @else
                            <p>No students enrolled in this course.</p>
                        @endif                        

					</div>
                </div>
                

                @if(is_student() ||  check_if_exercise_is_enrolled($item->id))
                <!-- is student -->
                @else
                    <div class="card card-collapsed">
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