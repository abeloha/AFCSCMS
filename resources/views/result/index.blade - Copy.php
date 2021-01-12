@extends('layout')

<?php 
    $page_title = 'Results';
    $courses = get_course();
    $depts = get_dept();
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
		 
            <div class="tab-pane active" id="Student-profile">
                <div class="row">

                    @foreach($courses as $course)
                        <?php 
                            $n = 0; 
                            $session = '';
                            $session_id = 0;
                            if($course->current_session_id){
                                $session_data = get_session($course->current_session_id);
                                $session_id = $session_data->id;
                                $session = $session_data->name;
                            }

                            $term = '';
                            $term_id = 0;
                            if($course->current_term_id){
                                $term_data = get_term($course->current_term_id);
                                $term_id = $term_data->id;
                                $term = $term_data->name;
                            }
                        ?>
                        <div class="col-md-12">
                            <div class="card">
                            
                                <div class="card-header">
                                    <h3 class="card-title">{{$course->name}} Results</h3>
                                    <div class="card-options">
                                        <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                                        <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
                                        <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
                                    </div>
                                </div>

                                <div class="card-body">
                                    
                                    @if($term_id && $session_id)                                
                                        <div class="table-responsive">
                                            <p>
                                                <b>Session: {{$session}}</b>
                                                <br><b>Term:  {{$term}}</b>
                                            </p>
                                            <table class="table mb-0 text-nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Department</th>
                                                        <th>Result Status</th>
                                                        <th>Options</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($depts as $dept)
                                                        <?php 
                                                            if($dept->is_joint)
                                                                continue;

                                                            $divs = get_div_by_dept($dept->id, $course->id);
                                                            $p = 0;
                                                        ?>
                                                        <tr>
                                                            <td>{{++$n}}</td>
                                                            <td>{{$dept->name}}</td>
                                                            <td>
                                                                <span class="tag tag-success">paid</span>
                                                            </td>
                                                            <td>
                                                                <a href="#"><i class="fa fa-building"></i> View Department Result</a>
                                                            </td>
                                                        </tr>

                                                        @if(count($divs))
                                                            <tr>
                                                                <td></td>
                                                                <td colspan="3" class="text-left"> <b> Divisions in {{$dept->name}}</b></td>
                                                            </tr>
                                                            @foreach ($divs as $div)
                                                                <tr>
                                                                    <td class="text-right">{{++$p}}</td>
                                                                    <td>{{$div->name}}</td>
                                                                    <td>
                                                                        <span class="tag tag-success">paid</span>
                                                                    </td>
                                                                    <td>
                                                                        <a href="#"><i class="fa fa-check"></i> View Division Result</a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            <tr>
                                                                <td colspan="4" class="text-center"> </b></td>
                                                            </tr>
                                                        @endif

                                                    @endforeach                                           
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                            
                                        <p><i>Current academic session and/or term could not be determined</i></p>
                                            
                                    @endif

                                    <div>
                                        <h4>View Older Results</h4>

                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label>Department</label>
                                                    <select name="session" class="form-control" required>
                                                        <option value="">Select</option>
                                                       
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label>Session</label>
                                                    <select name="session" class="form-control" required>
                                                        <option value="">Select</option>
                                                       
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label>Term</label>
                                                    <select name="session" class="form-control" required>
                                                        <option value="">Select</option>                                                       
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group" style="margin-top: 25px;">
                                                    <button class="btn btn-primary btn-lg btn-simple">Search Result</button>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                    
                                </div>

                            </div>
                        </div>
                    @endforeach


                </div>
            </div>

		</div>
	</div>
</div>


@endsection