@extends('layout')

<?php 
    $page_title = 'Add Exercise Material';
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
            @if($item)
                <ul class="nav nav-tabs page-header-tab">
                    <li class="nav-item"><a class="nav-link" href="{{url('exercise/'.$item->id)}}">Back to Exercise</a></li>
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
                    $can_add = check_can_manage_exercise_materials($item->id);                    
                ?>
    
                @if($item)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Add New Manterial To Exercise</h3>
                            <div class="card-options ">
                                <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{url('exercise/material')}}" enctype="multipart/form-data">                                
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
                                    <input type="hidden" name="exercise" value="{{$item->id}}">
                                    <div class="col-sm-12">
                                        <div class="form-group">                                            
                                            <p>
                                                <label>Name of Exercise:</label>
                                                <b>{{$item->name}}</b>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Title of Material</label>
                                            <input type="text" name="title" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Content of Material</label>
                                            <textarea id="editor1" name="content" class="form-control" required></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Add Downloadable File (optional)</label>
                                            <input type="file" name="file_1" >
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Add Another Downloadable File (optional)</label>
                                            <input type="file" name="file_2" >
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Add Another Downloadable File (optional)</label>
                                            <input type="file" name="file_3" >
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-12">
                                        <button class="btn btn-primary btn-lg btn-simple">Add Material</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="card">
                        <div class="card-body">
                            <i>Exercise could not be fund</i>
                        </div>
                    </div>
                @endif                
                
			</div>
		
			
		</div>
	
    </div>
</div>
    
<script>
	initSampleEditor1();
</script>

@endsection