@extends('layout')

<?php 
    $page_title = 'Enroll Exercise';
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
				$added = false;
				if(isset($_GET['added'])){
					$added = true;
				}				
				$deleted = false;
				if(isset($_GET['removed'])){
					$deleted = true;
				} 
			?>
			@if($added)
				<div class="alert alert-info">
					<p>Exercise(s) has been enrolled successfully</p>
				</div>
			@endif

			@if($deleted)
				<div class="alert alert-info">
					<p>Exercise(s) has been removed from your enrollment successfully</p>
				</div>
			@endif

			@if($user)
				<?php
					$n = 0;
					$enrolled_id = array();
				?>
				<div class="container-fluid">
					<div class="d-flex justify-content-between align-items-center">
						<div class="header-action">
							<h1 class="page-title">Student Name: {{$user->surname.' '.$user->first_name}}</h1>
							<ol class="breadcrumb page-breadcrumb">
							<li class="breadcrumb-item">Course: {{$user->course}} </li>
							<li class="breadcrumb-item"> Department: {{$user->dept}}</li>
							</ol>
						</div>
					</div>
				</div>

				<div class="col-lg-6 col-md-12 seperator-right">				
					<div class="tab-content">
						<div class="card-header">
							<h3 class="card-title">Exercise already enrolled</h3>
						</div>
						
						<div class="tab-pane active" id="Library-all">						

							<div class="card">
								<div class="card-body">

									@if($exercise_enrolled)
										<div class="table-responsive">
											<form method="POST" action="{{url('exercise/enroll/remove')}}">
												<input type="hidden" name="user_id" value="{{$user->id}}"  />
												
												@csrf  

												<table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5" data-page-length='25'>
													<thead>
														<tr>
															<th></th>
															<th>#</th>
															<th>Exercise Name</th>
															<th>Exercise Dept</th>
															<th>Exercise Course</th>													
														</tr>
													</thead>
													<tbody>
														@foreach($exercise_enrolled as $item)
															<?php array_push($enrolled_id, $item->id); ?>													
															<tr>
																<td>
																	<label class="custom-control custom-checkbox">
																		<input type="checkbox" class="custom-control-input" name="enrollments[]" value="{{$item->enrollment_id}}">
																		<span class="custom-control-label">&nbsp;</span>
																	</label>
																</td>
																<td>{{++$n}}</td>
																<td>{{$item->name}}</td>
																<td>{{$item->dept}}</td>
																<td>{{$item->course}}</td>                                                        
															</tr>
														@endforeach                                        
													</tbody>
												</table>

												@if($n)
												<div class="col-sm-12">
													<button class="btn btn-primary btn-lg btn-simple">Remove the selected exercises</button>
												</div>
												@endif

											</form>
										</div>
									@endif 

									@if(!$n)
										<i>No exercise enrolled yet</i>
									@endif
								</div>
							</div>
							
						</div>
						
					</div>
				</div>

				<div class="col-lg-6 col-md-12 seperator-left">				
					<div class="tab-content">
						<div class="card-header">
							<h3 class="card-title">Exercises you can enroll</h3>
						</div>
						
						<div class="tab-pane active">
							
							<?php
								$n = 0;
							?>

							<div class="card">
								<div class="card-body">
									@if($available_exercise)
										<div class="table-responsive">
											<form method="POST" action="{{url('exercise/enroll/add')}}">
												
												<input type="hidden" name="session" value="{{$session}}"  />
												<input type="hidden" name="term" value="{{$term}}"  />
												
												@csrf  

												<table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5" data-page-length='25'>
													<thead>
														<tr>
															<th></th>
															<th>#</th>
															<th>Exercise Name</th>
															<th>Exercise Dept</th>
															<th>Exercise Course</th>													
														</tr>	
													</thead>
													<tbody>
														@foreach($available_exercise as $item)
															<?php 
																if(in_array($item->id,$enrolled_id)){
																	continue;
																}
															?>
															<tr>
																<td>
																	<label class="custom-control custom-checkbox">
																		<input type="checkbox" class="custom-control-input" name="exercises[]" value="{{$item->id}}">
																		<span class="custom-control-label">&nbsp;</span>
																	</label>
																</td>
																<td>{{++$n}}</td>
																<td>{{$item->name}}</td>
																<td>{{$item->dept}}</td>
																<td>{{$item->course}}</td>                                                        
															</tr>
														@endforeach                                        
													</tbody>
												</table>

												@if($n)
												<div class="col-sm-12">
													<button class="btn btn-primary btn-lg btn-simple">Enroll for the selected exercise</button>
												</div>
												@endif

												<div class="col-sm-12" style="font-size: 11px;">
													<p>
														
													</p>
												</div>

											</form>
										</div>
									
									@endif  
									
									@if(!$n)
										<i>No exercise available</i>
									@endif

								</div>
							</div>
						</div>
						
					</div>
				</div>
			@else
				<i>Sorry, user data could not be loaded.</i>
			@endif
		</div>	
    </div>
</div>

    


@endsection