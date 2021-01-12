@extends('layout')

<?php 
    $page_title = 'Syndicates';
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
						<h3 class="card-title">Divisions</h3>
                    </div>
                    
					<div class="tab-pane active" id="Library-all">
						
						<?php
							$n = 1;
						?>

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

								@if($divs)
									<div class="table-responsive">
										<table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5" data-page-length='25'>
											<thead>
												<tr>
													<th>#</th>
                                                    <th>Name</th>
                                                    <th>Dept</th>
                                                    <th>Course</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												@foreach($divs as $item)													
													<tr>
														<td>{{$n++}}</td>
                                                        <td><a href="{{url('syndicates/'.$item->id)}}">{{$item->name}}</a></td>
                                                        <td>{{$item->dept}}</td>
                                                        <td>{{$item->course}}</td>
														<td>
															<a href="{{url('syndicates/'.$item->id)}}"><button type="button" class="btn btn-icon btn-sm" title="View"><i class="fa fa-eye"></i></button></a>
														</td>
													</tr>
												@endforeach                                        
											</tbody>
										</table>
									</div>
								@else
									<p>No course added yet</p>
								@endif                        

							</div>
						</div>
                    </div>
                    
				</div>
			</div>

			<div class="col-lg-6 col-md-12 seperator-left">				
				<div class="tab-content">
					<div class="card-header">
						<h3 class="card-title">Syndicates {{($div_name)? 'in '.$div_name : ''}}</h3>
					</div>
					<div>
						<ul class="nav nav-tabs page-header-tab">
							<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#syn-all">List View</a></li>
							<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#syn-add">Add</a></li>
						</ul>
					</div>
					<div class="tab-pane active" id="syn-all">						
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
								<p>New Syndicate has been added successfully</p>
							</div>
						@endif

						@if($deleted)
							<div class="alert alert-info">
								<p>Syndicate deleted successfully</p>
							</div>
						@endif

						

						<div class="card">
							<div class="card-body">
								@if($div_id)
									<?php
										$syndicates = get_syndicate_by_div($div_id);
                                        $can_manage_syndicates = check_has_ability('manage_syndicate');
	                                    $can_view_students = check_has_ability('view_students');
									?>
									@if($syndicates)
										<div class="table-responsive">
											<table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5" data-page-length='25'>
												<thead>
													<tr>
														<th>#</th>
														<th>Name</th>
                                                        <th>Div</th>
														<th>DS</th>
														<th>Action</th>
													</tr>
												</thead>
												<tbody>
													@foreach($syndicates as $item)
														<?php
															$ds = '-';

															if($item->ds_user_id){
																$user = get_user($item->ds_user_id);
																if($user) $ds = $user->surname.' '.$user->first_name;
															}
														?>
														<tr>
															<td>{{$n++}}</td>
															<td>{{$item->name}}</td>
                                                            <td>{{$item->div}}</td>                                                          
															<td>
																@if($item->ds_user_id)
																	<a href="{{url('user/'.$item->ds_user_id)}}" target="_blank">{{$ds}}</a>
																@else
																	{{$ds}}
																@endif
															</td> 

															<td>
																<div class="item-action dropdown ml-2">
																	<a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false"><i class="fe fe-more-vertical"></i> </a>
																	<div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(18px, 25px, 0px);">
																		
																		@if($can_view_students)
																			<a href="{{url('user/student?syndicate='.$item->id)}}" class="dropdown-item"><i class="dropdown-icon fa fa-user-circle"></i> View Students in Syndicate</a>
																		@endif
																		@if($can_manage_syndicates)
																			<a href="{{url('syndicate/'.$item->id.'/assign')}}" class="dropdown-item"><i class="dropdown-icon fa fa-plus"></i>Assign Students to Syndicate</a>
																			<a href="{{url('syndicate/'.$item->id)}}" class="dropdown-item"><i class="dropdown-icon fa fa-edit"></i>Edit Syndicate</a>
																			<a href="{{url('syndicate/'.$item->id.'/delete')}}" onclick="return confirm('Are you Sure you want to delete this record?')" class="dropdown-item"><i class="dropdown-icon fa fa-trash-o text-danger"></i>Delete Syndicate</a>
																		@endif

																	</div>
																</div>
															</td>
															
														</tr>
													@endforeach                                        
												</tbody>
											</table>
										</div>
									@else
										<p>No syndicated added yet</p>
									@endif									
								@else
									<p>Select a course to view syndicates</p>
								@endif                        

							</div>
						</div>
					</div>
                
                    @if($div_id)
					<div class="tab-pane" id="syn-add">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title">Add Syndicate</h3>
								<div class="card-options ">
									<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
								</div>
							</div>
							<div class="card-body">
								<form method="POST" action="{{url('syndicate')}}">
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
                                    
                                        @if($session)
                                            <input type="hidden" name="div" value="{{$div_id}}" class="form-control">
                                            <input type="hidden" name="session" value="{{$session}}" class="form-control">
   
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Name of Syndicate</label>
                                                    <input type="text" name="name" value="" placeholder="Enter Name" class="form-control" required>
                                                </div>
                                            </div>
                                            <?php
                                                $users = get_user_by_role_name('ds');  
                                                $user_exist = 0;                                      
                                            ?>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Select Directing Staff Of This Syndicate</label>
                                                    @if($users)
                                                    <?php $user_exist = 1; ?>
                                                        <select name = "ds" class="form-control"> 
                                                            <option value="">Select DS</option>
                                                            @foreach ($users as $user)
                                                                <option value="{{$user->id}}">{{$user->surname.' '.$user->first_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                    @if(!$user_exist)
                                                        <i>No Directing Staff enrolled in the system</i>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Div of Syndicate</label>
                                                    <input type="text" name="div_name" value="{{$div_name}}" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Course of Syndicate</label>
                                                    <input type="text" name="course_name" value="{{$course_name}}" class="form-control" readonly>
                                                </div>
                                            </div>

                                            <div class="col-sm-12">
                                                <button class="btn btn-primary btn-lg btn-simple">Add</button>
                                            </div>
                                        @else
                                            <div class="col-sm-12">
                                                <i>Course Current Session must be set from the manage course menu.</i>
                                            </div>
                                        @endif
									</div>
								</form>
							</div>
						</div>
					</div>
                    @endif

				</div>
			</div>

		</div>	
    </div>
</div>

    


@endsection