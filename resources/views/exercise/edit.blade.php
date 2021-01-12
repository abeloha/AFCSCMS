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
            <ul class="nav nav-tabs page-header-tab">
                <li class="nav-item"><a class="nav-link" href="{{url('exercise/list')}}">List of Exercises</a></li>
            </ul>
		</div>
	</div>
</div>

<div class="section-body mt-4">
    <div class="container-fluid">
	
		<div class="tab-content">
			<div class="tab-pane active" id="Library-all">

                <?php
                    
                    $success = false;
                    if(isset($_GET['success'])){
                        $success = true;
                    }
                    
                ?>
                @if($success)
                    <div class="alert alert-info">
                        <p>Exercise has been edited successfully</p>
                    </div>
                @endif
    
                @if($item)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Edit Exercise</h3>
                            <div class="card-options ">
                                <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{url('exercise/edit')}}">
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
                                        <input type="hidden" name="id" value="{{$item->id}}">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Name of Exercise</label>
                                                <input type="text" name="name" value="{{$item->name}}" placeholder="Enter Exercise Name" class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Weighted Point of Exercise</label>
                                                <input type="number" name="weighted_point" value="{{$item->weighted_point}}"  class="form-control" step="any" required>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Short Description of Exercise</label>
                                                <textarea name="description" class="form-control" placeholder="Short description of exercise" required>{{$item->description}}</textarea>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Department this exercise belongs to</label>
                                                <select name="dept" id="dept" class="form-control" required>
                                                    <option value="">Select</option>
                                                    @foreach ($depts as $dept)
                                                        <option value="{{$dept->id}}" {{($item->dept_id == $dept->id)? 'selected' : ''}}>{{$dept->name}}</option>
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
                                                        <option value="{{$course->id}}" {{($item->course_id == $course->id)? 'selected' : ''}}>{{$course->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Term this exercise will be taken</label>
                                                <select name="term" id="term" class="form-control" required>
                                                    <option value="{{$item->term_id}}" selected>{{$item->term}}</option>                                                
                                                </select>
                                                <span style="font-size: 11px;">Change selected Course to view other terms<span>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Sponsor DS for this exercise</label>
                                                <select name="sponsor" class="form-control">
                                                    <option value="">Select</option>
                                                    @foreach ($ds_staff as $ds)
                                                        <option value="{{$ds->id}}" {{($item->sponsor_user_id == $ds->id)? 'selected' : ''}}>{{$ds->surname.' '.$ds->first_name}}</option>
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
                                                    <option value="{{$ds->id}}" {{($item->cosponsor_user_id == $ds->id)? 'selected' : ''}}>{{$ds->surname.' '.$ds->first_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-12">
                                            <button class="btn btn-primary btn-lg btn-simple">Save</button>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <p>Add courses and departments first before you can edit exercise!</p>                            
                                    </div> 
                                @endif
                            </form>
                        </div>
                    </div>
                @else
                    <div class="card">
                        <div class="card-body">
                            <i>Details could not be loaded</i>
                        </div>
                    </div>
                @endif                
                
			</div>
		
			
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