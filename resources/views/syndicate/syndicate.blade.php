@extends('layout')

<?php 
	$page_title = 'Syndicate';
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
            @if($data)
                <div class="nav nav-tabs page-header-tab">
                    <a href="{{url('syndicates/'.$data->div_id)}}" class="nav-link active">List of Syndicates</a>
                </div>
            @endif
		</div>
	</div>
</div>

<div class="section-body mt-4">
    <div class="container-fluid">
	
		<div class="tab-content">
			<div class="tab-pane active" id="Library-all">

				<?php
                    $sucess = false;
                    if(isset($_GET['sucess'])){
                        $sucess = true;
                    } 
				?>
				
                @if($sucess)
                    <div class="alert alert-info">
                        <p>Edited successfully</p>
                    </div>
				@endif
				
				<div class="card">					
					<div class="card-header">
						<h3 class="card-title">Edit Syndicate</h3>
						<div class="card-options ">
							<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
						</div>
					</div>
					<div class="card-body">
												
						<form method="POST" action="{{url('syndicate/edit')}}">
							<div class="row">	
								@if($data)
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

									@csrf
									<input type="hidden" name="id" value="{{$data->id}}" class="form-control">
									<div class="col-sm-12">
										<div class="form-group">
											<label>Name of Syndicate</label>
											<input type="text" name="name" value="{{$data->name}}" class="form-control">
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
                                                <select name = "ds" class="form-control" required> 
                                                    <option value="">Select DS</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{$user->id}}" {{($data->ds_user_id == $user->id)? 'selected' : ''}}>{{$user->surname.' '.$user->first_name}}</option>
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
                                            <input type="text" name="div_name" value="{{$data->div}}" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Department of Syndicate</label>
                                            <input type="text" name="dept_name" value="{{$data->dept}}" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Course of Syndicate</label>
                                            <input type="text" name="course_name" value="{{$data->course}}" class="form-control" readonly>
                                        </div>
                                    </div>
                                    
									<div class="col-sm-12">
										<button class="btn btn-primary btn-lg btn-simple">Update</button>
										<a href="{{url('syndicates/'.$data->div_id)}}" class="nav-link active">Cancel and Go Back to List of Syndicates</a>
									</div>								
								@else
									<p>Syndicates details could not be loaded.</p>
								@endif
							</div>
						</form>
						
					</div>
				</div>
			</div>
		
		</div>
	
    </div>
</div>
    


@endsection