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

			<?php
				$success = false;
				if(isset($_GET['success'])){
					$success = true;
				}	
			?>
			@if($success)
				<div class="alert alert-info">
					<p>Students assigned successfully to DS for assessment</p>
				</div>
			@endif


			@if($exercise)
				<?php
                    $n = 0;
                    $arry_student = array();
				?>
				<div class="card">
                    <div class="card-body">
						<h1 class="page-title">Assign Students To DS for Assessment of Written Submissions</h1>
                        <h1 class="page-title">Exercise Name: {{$exercise->name}}</h1>
                        <h1 class="page-title">Div Name: {{$div->name}}</h1>
					</div>
				</div>

				<div class="col-lg-4 col-md-12 seperator-right">				
					<div class="tab-content">
						<div class="card-header">
							<h3 class="card-title">Students yet to be assigned to DS for assessment of written submissions</h3>
						</div>
						
						<div class="tab-pane active" id="Library-all">						

							<div class="card">
								<div class="card-body">

									@if(count($enrolled_students))
										<div class="table-responsive">
											
                                            <table class="table table-hover js-basic-example table-striped table_custom border-style spacing5" data-page-length='25'>
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Code</th>													
                                                    </tr>
                                                </thead>
                                                <tbody>
													@foreach($enrolled_students as $item)
													
                                                        <?php 
                                                            $assigned = get_grader_assigned_to_student($exercise->id,$item->user_id);
                                                            if($assigned){ continue; }
                                                            array_push($arry_student, $item->user_id);
															$code = user_id_to_code($item->user_id);
                                                        ?>													
                                                        <tr>															
                                                            <td>{{++$n}}</td>
                                                            <td>{{$code}}</td>                                                   
														</tr>
														
                                                    @endforeach                                        
                                                </tbody>
                                            </table>
												
										</div>
									@endif
									@if(!$n)
										<i>No unassigned students for assessment in this exerise.</i>
									@endif
								</div>
							</div>
							
						</div>
						
					</div>
				</div>

				<div class="col-lg-8 col-md-12 seperator-left">				
					<div class="tab-content">
						<div class="card-header">
							<h3 class="card-title">DS for assigning students</h3>
						</div>
						
						<div class="tab-pane active">
							
							<?php
                                $n = 0;
								$no_of_students = count($arry_student);
								$students_ids = implode(',',$arry_student);
							?>

							<div class="card">
								<div class="card-body">
									@if(count($div_ds))
										<div class="table-responsive">

											<form method="POST" onsubmit="return checkSubmission()" action="{{url('exercise/assigngrader')}}">
												
												<input type="hidden" name="div" value="{{$div->id}}" />
												<input type="hidden" name="exercise_id" value="{{$exercise->id}}" />
												<input type="hidden" name="student_ids" value="{{$students_ids}}" />
												
												@csrf
											
												<table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5" data-page-length='25'>
													<thead>
														<tr>
															<th>#</th>
															<th>Name</th>
															<th>Already Assigned</th>
															<th>Action</th>
															<th>
																Number To Assign
																<br><span style="font-size:10px;">Total Assignable: {{$no_of_students}}</span>
															</th>												
														</tr>
													</thead>
													<tbody>
														@foreach($div_ds as $item)
															<?php $students_assigned_to_grader = count(get_students_assigned_to_grader($exercise->id,$item->user_id)); ?>													
															<tr>
																<td>{{++$n}}</td>
																<td><a href="{{url('user/'.$item->user_id)}}" target="_blank">{{$item->surname.' '.$item->first_name}}</a></td>
																<td>{{$students_assigned_to_grader}}</td>

																@if($students_assigned_to_grader)
																	<td>
																		<a href="{{url('exercise/'.$exercise->id.'/grade/?grader='.$item->user_id)}}" target="_blank">View Progress</a>
																	</td>  
																@else
																	<td>-</td>
																@endif

																@if($no_of_students)
																	<td>
																		<input type="hidden" name="ds[id][]"  value="{{$item->user_id}}"/>																	
																		<input type="number" name="ds[no_of_students][]" value="0" min="0" required/>
																	</td>  
																@else
																	<td>-</td>
																@endif

															</tr>
														@endforeach                                        
													</tbody>
												</table>

												@if($no_of_students)
													<div class="col-sm-12 text-danger" id="error_msg"></div>
													<div class="col-sm-12">
														<button class="btn btn-primary btn-lg btn-simple">Save Students Assigned To DS for Assessment</button>
													</div>
												@endif

												<hr>
												<a href="{{url('exercise/'.$exercise->id.'/assigngraderlist?div='.$div->id)}}"><button type="button" class="btn btn-icon btn-sm" title="View"><i class="fa fa-eyes"></i> Click To view list of students and DS assigned</button></a>

											</form>
												
											<script>
												function checkSubmission()
												{
													$('#error_msg').html('');

													var noStudents = '{{$no_of_students}}';
													var totalStudents = parseInt(noStudents); 

													var totalAssigned = 0;

													$('input[name^="ds[no_of_students]"]').each(function() {
														totalAssigned += parseInt(this.value);
													});

													if(totalAssigned == totalStudents){
														return true
													}

													$msg = '';
													if(totalAssigned > totalStudents){
														$msg = 'Total number of students assigned to the DS cannot be greater than the total number of available students\n\nTotal available students = ' + totalStudents + '\nTotal students assigned = ' + totalAssigned;
														$('#error_msg').html('Total number of students assigned to the DS cannot be greater than the total number of available students');
													}

													if(totalAssigned < totalStudents){
														$msg = 'Total number of students assigned to the DS cannot be less than the total number of available students\n\nTotal available students = ' + totalStudents + '\nTotal students assigned = ' + totalAssigned;
														$('#error_msg').html('Total number of students assigned to the DS cannot be less than the total number of available students');
													}

													alert($msg);

													return false;
												}
											</script>
										</div>
									@else
										<i>No ds available.</i>
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