@extends('layout')

<?php 
	$page_title = 'Admin';
?>

@section('title', $page_title)

@section('content')

<div class="section-body">
	<div class="container-fluid">
		<div class="d-flex justify-content-between align-items-center">
			<div class="header-action">
				<h1 class="page-title">Dashboard</h1>
				<ol class="breadcrumb page-breadcrumb">
				<li class="breadcrumb-item"><a href="{{url('/')}}">{{get_app_name()}}</a></li>
				<li class="breadcrumb-item active" aria-current="page">Dashboard</li>
				</ol>
			</div>
		</div>
	</div>
</div>
<div class="section-body mt-4">
	<div class="container-fluid">
		
		<div class="row clearfix row-deck">
			
			<div class="col-6 col-md-4 col-xl-2">
				<div class="card">
					<div class="card-body ribbon">
					<div class="ribbon-box green" data-toggle="tooltip" title="Staff">5</div>
					<a href="#" class="my_sort_cut text-muted">
					<i class="fa fa-black-tie"></i>
					<span>Staff</span>
					</a>
					</div>
				</div>
			</div>
		
			<div class="col-6 col-md-4 col-xl-2">
				<div class="card">
					<div class="card-body ribbon">
						<div class="ribbon-box orange" data-toggle="tooltip" title="Student">8</div>
						<a href="#" class="my_sort_cut text-muted">
						<i class="fa fa-user-circle-o"></i>
						<span>Student</span>
						</a>
					</div>
				</div>
			</div>

			<div class="col-6 col-md-4 col-xl-2">
				<div class="card">
					<div class="card-body ribbon">
						<a href="#" class="my_sort_cut text-muted">
						<i class="fa fa-user-circle-o"></i>
						<span>Exercises</span>
						</a>
					</div>
				</div>
			</div>

			<div class="col-6 col-md-4 col-xl-2">
				<div class="card">
					<div class="card-body ribbon">
						<a href="#" class="my_sort_cut text-muted">
						<i class="fa fa-user-circle-o"></i>
						<span>Syndicates</span>
						</a>
					</div>
				</div>
			</div>        
			
			<div class="col-6 col-md-4 col-xl-2">
				<div class="card">
					<div class="card-body ribbon">
						<div class="ribbon-box orange" data-toggle="tooltip" title="Student">8</div>
						<a href="{{url('depts')}}" class="my_sort_cut text-muted">
						<i class="fa fa-user-circle-o"></i>
						<span>Divs</span>
						</a>
					</div>
				</div>
			</div>
			<div class="col-6 col-md-4 col-xl-2">
				<div class="card">
					<div class="card-body ribbon">
						<a href="{{url('depts')}}" class="my_sort_cut text-muted">
						<i class="fa fa-user-circle-o"></i>
						<span>Departments</span>
						</a>
					</div>
				</div>
			</div>
			
		</div>

		
		<div class="row clearfix row-deck">
			<div class="col-lg-4 col-md-12">
			<div class="card">
			<div class="card-body">
			<h3 class="card-title">Events List</h3>
			<div id="event-list" class="fc event_list">
			<div class='fc-event bg-primary' data-class="bg-primary">My Event 1</div>
			<div class='fc-event bg-info' data-class="bg-info">Birthday Party</div>
			<div class='fc-event bg-success' data-class="bg-success">Meeting</div>
			<div class='fc-event bg-warning' data-class="bg-warning">Conference</div>
			<div class='fc-event bg-danger' data-class="bg-danger">My Event 5</div>
			</div>
			<div class="todo_list mt-4">
			<h3 class="card-title">ToDo List <small>This Month task list</small></h3>
			<ul class="list-unstyled mb-0">
			<li>
			<label class="custom-control custom-checkbox">
			<input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1" checked="">
			<span class="custom-control-label">Report Panel Usag</span>
			</label>
			</li>
			<li>
			<label class="custom-control custom-checkbox">
			<input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1">
			<span class="custom-control-label">Report Panel Usag</span>
			</label>
			</li>
			<li>
			<label class="custom-control custom-checkbox">
			<input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1" checked="">
			<span class="custom-control-label">New logo design for Angular Admin</span>
			</label>
			</li>
			<li>
			<label class="custom-control custom-checkbox">
			<input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1">
			<span class="custom-control-label">Design PSD files for Angular Admin</span>
			</label>
			</li>
			</ul>
			</div>
			</div>
			</div>
			</div>
			<div class="col-lg-8 col-md-12">
			<div class="card">
			<div class="card-body">
			<div id="calendar"></div>
			</div>
			</div>
			</div>
		</div>
		
		<div class="tab-content">
		
			<div class="row">
			<div class="col-md-12">
			<div class="card">
			<div class="card-header">
			<h3 class="card-title">New Student List</h3>
			<div class="card-options">
			<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
			<a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
			<a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
			</div>
			 </div>
			<div class="card-body">
			<div class="table-responsive">
			<table class="table table-striped mb-0 text-nowrap">
			<thead>
			<tr>
			<th>No</th>
			<th>Name</th>
			<th>Assigned Professor</th>
			<th>Date Of Admit</th>
			<th>Fees</th>
			<th>Branch</th>
			<th>Edit</th>
			</tr>
			</thead>
			<tbody>
			<tr>
			<td>1</td>
			<td>Jens Brincker</td>
			<td>Kenny Josh</td>
			<td>27/05/2016</td>
			<td>
			<span class="tag tag-success">paid</span>
			</td>
			<td>Mechanical</td>
			<td>
			<a href="javascript:void(0)"><i class="fa fa-check"></i></a>
			<a href="javascript:void(0)"><i class="fa fa-trash"></i></a>
			</td>
			</tr>
			<tr>
			<td>2</td>
			<td>Mark Hay</td>
			<td> Mark</td>
			<td>26/05/2018</td>
			<td>
			<span class="tag tag-warning">unpaid</span>
			</td>
			<td>Science</td>
			<td>
			<a href="javascript:void(0)"><i class="fa fa-check"></i></a>
			<a href="javascript:void(0)"><i class="fa fa-trash"></i></a>
			</td>
			</tr>
			<tr>
			<td>3</td>
			<td>Anthony Davie</td>
			<td>Cinnabar</td>
			<td>21/05/2018</td>
			<td>
			<span class="tag tag-success ">paid</span>
			</td>
			<td>Commerce</td>
			<td>
			<a href="javascript:void(0)"><i class="fa fa-check"></i></a>
			<a href="javascript:void(0)"><i class="fa fa-trash"></i></a>
			</td>
			</tr>
			<tr>
			<td>4</td>
			<td>David Perry</td>
			<td>Felix </td>
			<td>20/04/2019</td>
			<td>
			<span class="tag tag-danger">unpaid</span>
			</td>
			<td>Mechanical</td>
			<td>
			<a href="javascript:void(0)"><i class="fa fa-check"></i></a>
			<a href="javascript:void(0)"><i class="fa fa-trash"></i></a>
			</td>
			</tr>
			<tr>
			<td>5</td>
			<td>Anthony Davie</td>
			<td>Beryl</td>
			<td>24/05/2017</td>
			<td>
			<span class="tag tag-success ">paid</span>
			</td>
			<td>M.B.A.</td>
			<td>
			<a href="javascript:void(0)"><i class="fa fa-check"></i></a>
			<a href="javascript:void(0)"><i class="fa fa-trash"></i></a>
			</td>
			</tr>
			</tbody>
			</table>
			</div>
			</div>
			</div>
			</div>
			</div>

			<div class="row clearfix">

				<div class="col-xl-7 col-lg-6 col-md-12">
					<div class="card">
						<div class="card-header">
						<h3 class="card-title">Quick Mail</h3>
						</div>
						<div class="card-body">
							<div class="input-group">
								<div class="input-group-prepend">
								<span class="input-group-text w80">To:</span>
								</div>
								<input type="text" class="form-control">
							</div>
							<div class="input-group mt-1 mb-3">
								<div class="input-group-prepend">
								<span class="input-group-text w80">Subject:</span>
								</div>
								<input type="text" class="form-control">
							</div>
							<div class="summernote">
								Hi there,
								<br />
								<p>The toolbar can be customized and it also supports various callbacks such as <code>oninit</code>, <code>onfocus</code>, <code>onpaste</code> and many more.</p>
								<br />
								<p>Thank you!</p>
								<h6>Summer Note</h6>
							</div>
							<button class="btn btn-default mt-3">Send</button>
						</div>
					</div>
				</div>

				<div class="col-xl-5 col-lg-6 col-md-12">
					<div class="card">
						<div class="card-header">
							<h3 class="card-title">University Stats</h3>
						</div>
						<div class="card-body">
							<div class="row text-center">
								<div class="col-lg-4 col-4 border-right">
									<label class="mb-0 font-10">Department</label>
									<h4 class="font-20 font-weight-bold">05</h4>
								</div>
								<div class="col-lg-4 col-4 border-right">
									<label class="mb-0 font-10">Total Teacher</label>
									<h4 class="font-20 font-weight-bold">43</h4>
								</div>
								<div class="col-lg-4 col-4">
									<label class="mb-0 font-10">Total Student</label>
									<h4 class="font-20 font-weight-bold">267</h4>
								</div>
							</div>

							<table class="table table-striped mt-4">
								<tbody>
									<tr>
										<td>
											<label class="d-block">Mechanical Engineering<span class="float-right">43%</span></label>
											<div class="progress progress-xs">
											<div class="progress-bar bg-indigo" role="progressbar" aria-valuenow="43" aria-valuemin="0" aria-valuemax="100" style="width: 43%;"></div>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<label class="d-block">Business Analysis - BUS <span class="float-right">27%</span></label>
											<div class="progress progress-xs">
											<div class="progress-bar bg-blue" role="progressbar" aria-valuenow="27" aria-valuemin="0" aria-valuemax="100" style="width: 27%;"></div>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="card-footer">
							<small>Measure How Fast You’re Growing Monthly Recurring Revenue. <a href="#">Learn More</a></small>
						</div>
					</div>
				</div>

			</div>
			
		</div>

	</div>
</div>

@endsection