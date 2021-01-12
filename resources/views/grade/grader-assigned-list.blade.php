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
		<div class="row clearfix row-deck">

			@if($exercise)
				<?php
                    $n = 0;
				?>
				<div class="card">
                    <div class="card-body">
						<h1 class="page-title">Students Assigned To DS for Assessment of Written Submissions</h1>
                        <h1 class="page-title">Exercise Name: {{$exercise->name}}</h1>
                        <h1 class="page-title">Div Name: {{$div->name}}</h1>
					</div>
				</div>

				<div class="col-lg-12 col-md-12">				
					<div class="tab-content">
						<div class="card-header">
							<h3 class="card-title">Students and DS grading them</h3>
						</div>
						
						<div class="tab-pane active" id="Library-all">						

							<div class="card">
								<div class="card-body">

									@if(count($enrolled_students))
										<div class="table-responsive">
											
                                            <table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5" data-page-length='25'>
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Code</th>	
                                                        <th>Assigned To</th>
                                                        <th>Grading Status</th>
                                                        <th>Action</th>												
                                                    </tr>
                                                </thead>
                                                <tbody>
													@foreach($enrolled_students as $item)
													
                                                        <?php 
                                                            $code = user_id_to_code($item->user_id);
                                                            $grader_data = get_grader_assigned_to_student($exercise->id,$item->user_id);
                                                            $ungraded_submissions = get_student_exercise_submission_ungraded($exercise->id,$item->user_id); 
                                                        ?>													
                                                        <tr>															
                                                            <td>{{++$n}}</td>
                                                            <td>{{$code}}</td>
                                                            <td>
                                                                @if($grader_data)
                                                                    <a href="{{url('user/'.$grader_data->assigned_user_id)}}" target="_blank">{{$grader_data->rank.' '.$grader_data->surname.' '.$grader_data->first_name}}</a>
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if(count($ungraded_submissions))
                                                                    <span class="text-danger">Unfinished</span>
                                                                @else
                                                                    <span>Finished</span>
                                                                @endif
                                                            </td>
                                                            <td>

                                                                <a href="{{url('exercise/'.$exercise->id.'/grade/?grader='.$grader_data->assigned_user_id)}}" target="_blank"><button type="button" class="btn btn-icon btn-sm" title="View Grader progress"><i class="fa fa-search"></i></button></a>
                                                                <a href="{{url('exercise/'.$exercise->id.'/submissions/?student='.$code)}}" target="_blank"><button type="button" class="btn btn-icon btn-sm" title="View Students Submissions"><i class="fa fa-eye"></i></button></a>

                                                                <div class="item-action dropdown ml-2">
                                                                    <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false"><i class="fe fe-more-vertical"></i> </a>
                                                                    <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(18px, 25px, 0px);">
                                                                        @if($grader_data)
                                                                            <a href="{{url('exercise/'.$exercise->id.'/grade/?grader='.$grader_data->assigned_user_id)}}" target="_blank" class="dropdown-item"><i class="dropdown-icon fa fa-search"></i>View Grader progress</a>
                                                                        @endif
                                                                        <a href="{{url('exercise/'.$exercise->id.'/submissions/?student='.$code)}}" target="_blank" class="dropdown-item"><i class="dropdown-icon fa fa-eye"></i>View Students Submissions</a>
                                                                    
                                                                    </div>
                                                                </div>
                                                                
                                                            </td>
														</tr>
														
                                                    @endforeach                                        
                                                </tbody>
                                            </table>
                                            
                                            <hr>
                                            <a href="{{url('exercise/'.$exercise->id.'/assigngrader?div='.$div->id)}}"><button type="button" class="btn btn-icon btn-sm" title="View"><i class="fa fa-eyes"></i> Click To Assign DS to Grade Students Submissions in <b>{{$div->name}}</b></button></a>

										</div>
									@endif
									@if(!$n)
										<i>No students for assessment in this exerise.</i>
									@endif
								</div>
							</div>
							
						</div>
						
					</div>
                </div>
                
			@else
				<i>Sorry, exercise data could not be loaded.</i>
			@endif
		</div>	
    </div>
</div>

    


@endsection