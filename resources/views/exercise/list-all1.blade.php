@extends('layout')

<?php 
    $page_title = 'Exercises';
    $can_add = check_has_ability('manage_exercise');
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
            @if($can_add)
                <ul class="nav nav-tabs page-header-tab">
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#Library-all">List View</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#Library-add">Add</a></li>
                </ul>
            @endif
		</div>
	</div>
</div>

<div class="section-body mt-4">
    <div class="container-fluid">
	
		<div class="tab-content">
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
                        <p>New exercise has been added successfully</p>
                    </div>
                @endif

                @if($deleted)
                    <div class="alert alert-info">
                        <p>Exercise deleted successfully</p>
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

                        @if($collection)
                            <div class="table-responsive">
                                <table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Dept</th> 
                                            <th>Course</th> 
                                            <th>Term</th>
                                            <th>Sponsor</th>
                                            <th>Co-sponsor</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($collection as $item)  
                                            <?php 
                                                $sponsor = '-';
                                                $cosponsor = '-';
                                                
                                                if($item->sponsor_user_id){
                                                    $data_fxn = get_user($item->sponsor_user_id);
                                                    if($data_fxn){
                                                        $sponsor = $data_fxn->surname.' '.$data_fxn->first_name;
                                                    }
                                                }
                                                
                                                if($item->cosponsor_user_id){
                                                    $data_fxn = get_user($item->cosponsor_user_id);
                                                    if($data_fxn){
                                                        $cosponsor = $data_fxn->surname.' '.$data_fxn->first_name;
                                                    }
                                                }
                                            ?>
                                            <tr>
                                                <td>{{$n++}}</td>
                                                <td>{{$item->name}}</td>
                                                <td>{{$item->dept}}</td>
                                                <td>{{$item->course}}</td>
                                                <td>{{$item->term}}</td>
                                                <td>{{$sponsor}}</td>
                                                <td>{{$cosponsor}}</td>
                                                <td>
                                                    <a href="{{url('exercise/'.$item->id.'/edit')}}"><button type="button" class="btn btn-icon btn-sm" title="Edit"><i class="fa fa-edit"></i></button></a>
                                                    <a href="{{url('exercise/'.$item->id.'/delete')}}" onclick="return confirm('Are you sure you want to delete this record?')"><button type="button" class="btn btn-icon btn-sm" title="Delete"><i class="fa fa-trash-o text-danger"></i></button></a>
                                                </td>
                                            </tr>
                                        @endforeach                                        
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p>No exercise added yet</p>
                        @endif                        

					</div>
				</div>
			</div>
        
            @if($can_add)
			<div class="tab-pane" id="Library-add">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Add Exercise</h3>
						<div class="card-options ">
							<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
						</div>
					</div>
					<div class="card-body">
                        <form method="POST" action="{{url('exercise/add')}}">
                            <?php
                                $courses = get_course();
                                $depts = get_dept();
                                $ds_staff = get_user_by_role_name('ds');
                            ?>
                            @if($courses && $depts)
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
                                            <label>Name of Exercise</label>
                                            <input type="text" name="name" value="" placeholder="Enter Exercise Name" class="form-control" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Short Description of Exercise</label>
                                            <textarea name="description" class="form-control" placeholder="Short description of exercise" required> </textarea>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Department this exercise belongs to</label>
                                            <select name="dept" id="dept" class="form-control" required>
                                                <option value="">Select</option>
                                                @foreach ($depts as $dept)
                                                    <option value="{{$dept->id}}">{{$dept->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Course this exercise belongs to</label>
                                            <select name="course" id="course" class="form-control" required>
                                                <option value="">Select</option>
                                                @foreach ($courses as $course)
                                                    <option value="{{$course->id}}">{{$course->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Term this exercise will be taken</label>
                                            <select name="term" id="term" class="form-control" required>
                                                <option value="">Select Course First</option>                                                
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Sponsor DS for this exercise</label>
                                            <select name="sponsor" class="form-control">
                                                <option value="">Select</option>
                                                @foreach ($ds_staff as $ds)
                                                    <option value="{{$ds->id}}">{{$ds->surname.' '.$ds->first_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Co-sponsor DS for this exercise</label>
                                            <select name="cosponsor" class="form-control">
                                                <option value="">Select</option>
                                                @foreach ($ds_staff as $ds)
                                                    <option value="{{$ds->id}}">{{$ds->surname.' '.$ds->first_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-12">
                                        <button class="btn btn-primary btn-lg btn-simple">Add</button>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <p>Add courses and departments first before you can add exercise!</p>                            
                                </div> 
                            @endif
                        </form>
					</div>
				</div>
            </div>
            @endif
		
		</div>
	
    </div>
</div>
    
<script type="text/javascript">
        $(document).ready(function(){
            $('#course').on('change',function(){
                var course = $(this).val();
                if(course){
                    $.ajax({
                        url:"{{url('ajax/term')}}/"+course,
                        type:'get',
                        data:{},
                        success:function(html){
                            $('#term').html(html);
                        }
                    }); 
                }else{
                    $('#term').html('<option value="">Select course first</option>');
                }
            });        
        });  
</script>

@endsection