@extends('layout')

<?php 
    $page_title = 'Departments and Divisions';
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

			<div class="col-lg-6 col-md-12 seperator-right">				
				<div class="tab-content">
					<div class="card-header">
						<h3 class="card-title">Departments</h3>
					</div>
					<div>
						<ul class="nav nav-tabs page-header-tab">
							<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#Library-all">List View</a></li>
							<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#Library-add">Add</a></li>
						</ul>
					</div>

					<div class="tab-pane active" id="Library-all">
						
						<?php
							$n = 1;

							$added = false;
							if(isset($_GET['added'])){
								$added = true;
							}
							
							$deleted = false;
							if(isset($_GET['deleted'])){
								$deleted = true;
							} 
						?>
						@if($added)
							<div class="alert alert-info">
								<p>New Department has been added successfully</p>
							</div>
						@endif

						@if($deleted)
							<div class="alert alert-info">
								<p>Department deleted successfully</p>
							</div>
						@endif

						@if ($errors->any())
							<div class="alert alert-danger">
								<p>Some errors occured!</p>
								<ul>
									@foreach ($errors->all() as $error)
										<li>{{ $error }}</li>
									@endforeach
								</ul>
							</div>
						@endif

						<div class="card">
							<div class="card-body">

								@if($depts)
									<div class="table-responsive">
										<table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5">
											<thead>
												<tr>
													<th>#</th>
													<th>Name</th>
													<th>Director</th>
													<th>Is joint?</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												@foreach($depts as $item)
													<?php
														$director = '-';

														if($item->director_user_id){
															$user = get_user($item->director_user_id);
															if($user) $director=$user->surname.' '.$user->first_name;
														}
													?>
													<tr>
														<td>{{$n++}}</td>
														<td><a href="{{url('depts/'.$item->id)}}">{{$item->name}}</a></td>
														<td><a href="{{url('user/'.$item->director_user_id )}}" target="_blank">{{$director}}</a></td>                                                
														<td>{{($item->is_joint)? 'yes' : 'No'}}</td>
														<td>
															<a href="{{url('depts/'.$item->id)}}"><button type="button" class="btn btn-icon btn-sm" title="View"><i class="fa fa-eye"></i></button></a>
															<a href="{{url('dept/'.$item->id)}}"><button type="button" class="btn btn-icon btn-sm" title="Edit"><i class="fa fa-edit"></i></button></a>
															<a href="{{url('dept/'.$item->id.'/delete')}}" onclick="return confirm('Are you Sure you want to delete this record?')"><button type="button" class="btn btn-icon btn-sm" title="Delete"><i class="fa fa-trash-o text-danger"></i></button></a>
														</td>
													</tr>
												@endforeach                                        
											</tbody>
										</table>
									</div>
								@else
									<p>No department added yet</p>
								@endif                        

							</div>
						</div>
					</div>
				
					<div class="tab-pane seperator-left" id="Library-add">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title">Add Department</h3>
								<div class="card-options ">
									<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
								</div>
							</div>
							<div class="card-body">
								<form method="POST" action="{{url('dept')}}">
									<div class="row">                            
										@csrf                                
										@if ($errors->any())
											<div class="alert alert-danger">
												<p>Some errors occured!</p>
												<ul>
													@foreach ($errors->all() as $error)
														<li>{{ $error }}</li>
													@endforeach
												</ul>
											</div>
										@endif
										<div class="col-sm-12">
											<div class="form-group">
												<label>Name of Department</label>
												<input type="text" name="name" value="" placeholder="Enter Name" class="form-control">
											</div>
										</div>
										<div class="form-group">
											<label class="custom-control custom-checkbox">
												<input type="checkbox" class="custom-control-input" name="is_joint">
												<span class="custom-control-label">This is a joint department</span>
											</label>
										</div>
										<div class="col-sm-12">
											<button class="btn btn-primary btn-lg btn-simple">Add</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				
				</div>
			</div>

			<div class="col-lg-6 col-md-12 seperator-left">				
				<div class="tab-content">
					<div class="card-header">
						<h3 class="card-title">Divisions {{($dept_name)? 'in '.$dept_name : ''}}</h3>
					</div>
					<div>
						<ul class="nav nav-tabs page-header-tab">
							<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#Div-all">List View</a></li>
							<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#Div-add">Add</a></li>
						</ul>
					</div>
					<div class="tab-pane active" id="Div-all">						
						<?php
							$n = 1;

							$divadded = false;
							if(isset($_GET['div-added'])){
								$divadded = true;
							}
							
							$divdeleted = false;
							if(isset($_GET['div-deleted'])){
								$divdeleted = true;
							} 
						?>
						@if($divadded)
							<div class="alert alert-info">
								<p>New Division has been added successfully</p>
							</div>
						@endif

						@if($divdeleted)
							<div class="alert alert-info">
								<p>Division deleted successfully</p>
							</div>
						@endif

						

						<div class="card">
							<div class="card-body">
								@if($dept_id)
									<?php
										$divs = get_div_by_dept($dept_id);
									?>
									@if($divs)
										<div class="table-responsive">
											<table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5">
												<thead>
													<tr>
														<th>#</th>
														<th>Name</th>
														<th>Course</th>
														<th>CI</th>
														<th>TC</th>
														<th>Action</th>
													</tr>
												</thead>
												<tbody>
													@foreach($divs as $item)
														<?php
															$ci = '-';
															$tc = '-';

															if($item->ci_user_id){
																$user = get_user($item->ci_user_id);
																if($user) $ci=$user->surname.' '.$user->first_name;
															}
															if($item->tc_user_id){
																$user = get_user($item->tc_user_id);
																if($user) $tc=$user->surname.' '.$user->first_name;
															}
														?>
														<tr>
															<td>{{$n++}}</td>
															<td>{{$item->name}}</td>
															<td>{{$item->course}}</td>
															<td>
																@if($item->ci_user_id)
																	<a href="{{url('user/'.$item->ci_user_id)}}" target="_blank">{{$ci}}</a>
																@else
																	{{$ci}}
																@endif
															</td>
															<td>
																@if($item->tc_user_id)
																	<a href="{{url('user/'.$item->tc_user_id)}}" target="_blank">{{$tc}}</a>
																@else
																	{{$tc}}
																@endif
															</td>  
															<td>
																<a href="{{url('div/'.$item->id)}}"><button type="button" class="btn btn-icon btn-sm" title="Edit"><i class="fa fa-edit"></i></button></a>
																<a href="{{url('div/'.$item->id.'/delete')}}" onclick="return confirm('Are you Sure you want to delete this record?')"><button type="button" class="btn btn-icon btn-sm" title="Delete"><i class="fa fa-trash-o text-danger"></i></button></a>
															</td>
														</tr>
													@endforeach                                        
												</tbody>
											</table>
										</div>
									@else
										<p>No division added yet</p>
									@endif									
								@else
									<p>Select a department to view its divisions</p>
								@endif                        

							</div>
						</div>
					</div>
				
					<div class="tab-pane" id="Div-add">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title">Add Division {{($dept_name)? 'to '.$dept_name : ''}} </h3>
								<div class="card-options ">
									<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
								</div>
							</div>
							<div class="card-body">
								@if($dept_id)
									<?php
										$courses = get_course();
									?>
									<form method="POST" action="{{url('div')}}">
										<div class="row">                            
											@csrf
											<input type="hidden" name="dept" value="{{$dept_id}}">
											<div class="col-sm-12">
												<div class="form-group">
													<label>Name Of Division</label>
													<input type="text" name="name" value="" placeholder="Enter Name Df Division" class="form-control">
												</div>
											</div>

											<div class="col-sm-12">
												<div class="form-group">
													<label>Select the Course of This Division</label>
													@if($courses)
														<select name = "course" class="form-control" required> 
															<option value="">Select Course</option>
															@foreach ($courses as $course)
																<option value="{{$course->id}}"}}>{{$course->name}}</option>
															@endforeach
														</select>
													@endif
												</div>
											</div>

											<div class="col-sm-12">
												<div class="form-group">
													<label>Confirm the Department of This Division</label>
													<input type="text" name="dept_name" value="{{$dept_name}}"  class="form-control" readonly>
												</div>
											</div>											

											<div class="col-sm-12">
												<button class="btn btn-primary btn-lg btn-simple">Add Division</button>
											</div>
										</div>
									</form>
								@else
									<p>Select a department to add division</p>
								@endif 
							</div>
						</div>
					</div>
				
				</div>
			</div>

		</div>	
    </div>
</div>

    


@endsection