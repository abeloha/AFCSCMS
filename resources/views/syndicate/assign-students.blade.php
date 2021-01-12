@extends('layout')

<?php 
    $page_title = 'Syndicate Students';
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
					<p>Student(s) has been added to this syndicate successfully</p>
				</div>
			@endif

			@if($deleted)
				<div class="alert alert-info">
					<p>Student(s) has been removed from this syndicate successfully</p>
				</div>
			@endif

			@if($syndicate)
					<div class="container-fluid">
						<div class="d-flex justify-content-between align-items-center">
							<div class="header-action">
								<h1 class="page-title">Name of syndicate: {{$syndicate->name}}</h1>
								<ol class="breadcrumb page-breadcrumb">
								<li class="breadcrumb-item">Div: {{$syndicate->div}} </li>
								<li class="breadcrumb-item"> Department: {{$syndicate->dept}}</li>
								</ol>
							</div>
						</div>
					</div>

				<div class="col-lg-6 col-md-12 seperator-right">				
					<div class="tab-content">
						<div class="card-header">
							<h3 class="card-title">Students already assigned to {{$syndicate->name}}</h3>
						</div>
						
						<div class="tab-pane active" id="Library-all">
							
							<?php
								$n = 1;
							?>

							<div class="card">
								<div class="card-body">
									@if($syndicate_students)
										<div class="table-responsive">
											<form method="POST" action="{{url('syndicate/assign/remove')}}">
												<input type="hidden" name="syndicate" value="{{$syndicate->id}}"  />
												<input type="hidden" name="term" value="{{$syndicate->term_id}}"  />
												<input type="hidden" name="session" value="{{$syndicate->session_id}}"  />
												@csrf  

												<table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5" data-page-length='25'>
													<thead>
														<tr>
															<th></th>
															<th>#</th>
															<th>Rank</th>
															<th>Name</th>
															<th>Dept</th>
															<th>Course</th>													
														</tr>
													</thead>
													<tbody>
														@foreach($syndicate_students as $item)													
															<tr>
																<td>
																	<label class="custom-control custom-checkbox">
																		<input type="checkbox" class="custom-control-input" name="students[]" value="{{$item->id}}">
																		<span class="custom-control-label">&nbsp;</span>
																	</label>
																</td>
																<td>{{$n++}}</td>
																<td>{{$item->rank}}</td>
																<td>
																	<a href="{{url('user/'.$item->id)}}" target="_blank">{{$item->surname.' '.$item->first_name}}</a>
																</td>
																<td>{{$item->dept}}</td>
																<td>{{$item->course}}</td>                                                        
															</tr>
														@endforeach                                        
													</tbody>
												</table>

												<div class="col-sm-12">
													<button class="btn btn-primary btn-lg btn-simple">Remove the selected students</button>
													<a href="{{url('syndicates/'.$syndicate->div_id)}}">Back to list of Syndicates</a>
												</div>
											</form>
										</div>
									@else
										<p>No student assigned to this syndicate yet</p>
									@endif                        

								</div>
							</div>
							
						</div>
						
					</div>
				</div>

				<div class="col-lg-6 col-md-12 seperator-left">				
					<div class="tab-content">
						<div class="card-header">
							<h3 class="card-title">Students you can add to syndicate</h3>
						</div>
						
						<div class="tab-pane active">
							
							<?php
								$n = 1;
							?>

							<div class="card">
								<div class="card-body">
									@if($all_students)
										<div class="table-responsive">
											<form method="POST" action="{{url('syndicate/assign/add')}}">
												<input type="hidden" name="syndicate" value="{{$syndicate->id}}"  />
												<input type="hidden" name="term" value="{{$syndicate->term_id}}"  />
												<input type="hidden" name="session" value="{{$syndicate->session_id}}"  />
												@csrf  

												<table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5" data-page-length='25'>
													<thead>
														<tr>
															<th></th>
															<th>#</th>
															<th>Rank</th>
															<th>Name</th>
															<th>Dept</th>
															<th>Course</th>													
														</tr>
													</thead>
													<tbody>
														@foreach($all_students as $item)
															<?php 
																$enrollment = get_student_syndicate($item->id, $syndicate->term_id, $syndicate->session_id);
																if($enrollment){
																	continue;
																}
															?>
															<tr>
																<td>
																	<label class="custom-control custom-checkbox">
																		<input type="checkbox" class="custom-control-input" name="students[]" value="{{$item->id}}">
																		<span class="custom-control-label">&nbsp;</span>
																	</label>
																</td>
																<td>{{$n++}}</td>
																<td>{{$item->rank}}</td>
																<td>
																	<a href="{{url('user/'.$item->id)}}" target="_blank">{{$item->surname.' '.$item->first_name}}</a>
																</td>
																<td>{{$item->dept}}</td>
																<td>{{$item->course}}</td>                                                        
															</tr>
														@endforeach                                        
													</tbody>
												</table>

												<div class="col-sm-12">
													<button class="btn btn-primary btn-lg btn-simple">Add the selected students</button>
													<a href="{{url('syndicates/'.$syndicate->div_id)}}">Back to list of Syndicates</a>
												</div>

												<div class="col-sm-12" style="font-size: 11px;">
													<p>
														<b>1. Do not see the records you are looking for?</b> Could be the student has already been assigned to another syndicate.
														<br><b>2. Want to change a student syndicate?</b> Remove him form the current assigned syndicate.
														<br><b>3. Wrong records or no record showing at all?</b> Ensure the session and term for the course is set.
													</p>
												</div>
											</form>
										</div>
									@else
										<p>No student assigned to this syndicate yet</p>
									@endif                        

								</div>
							</div>
						</div>
						
					</div>
				</div>
			@else
				<i>Sorry, syndicate data could not be loaded.</i>
			@endif
		</div>	
    </div>
</div>

    


@endsection