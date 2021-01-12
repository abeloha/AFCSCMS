@extends('layout')

<?php 
    $page_title = 'Forms';
    $can_add = check_has_ability('manage_forms');
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
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#Library-all">List Forms</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#Library-add">Add New Form</a></li>
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
                    $n = 0;

                    $added = false;
                    if(isset($_GET['added'])){
                        $added = true;
                    }
                    
                    $deleted = false;
                    if(isset($_GET['deleted'])){
                        $deleted = true;
                    }

                    $err = false;
                    if(isset($_GET['err'])){
                        $err = $_GET['err'];
                    }
                ?>
                @if($added)
                    <div class="alert alert-info">
                        <p>New form has been added successfully</p>
                    </div>
                @endif

                @if($deleted)
                    <div class="alert alert-info">
                        <p>Form deleted successfully</p>
                    </div>
                @endif

                @if($err)
                    <div class="alert alert-danger">
                        <p>{{$err}}</p>
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

                        @if(count($collection))
                            <div class="table-responsive">
                                <table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5" data-page-length='25'>
                                    <thead>                                        
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>File</th>
                                            @if($can_add)
                                                <th>Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($collection as $item)                                            
                                            <tr>
                                                <td>{{++$n}}</td>
                                                <td>{{$item->name}}</td>
                                                <td>{{$item->description}}</td>
                                                <td><a href="{{asset('storage/form/'.$item->file)}}" download="{{$item->file}}">Download form</td>
                                                @if($can_add)
                                                    <td>
                                                        <a href="{{url('forms/'.$item->id.'/delete')}}" onclick="return confirm('Are you sure you want to delete this form?')" class="dropdown-item"><i class="dropdown-icon fa fa-trash-o text-danger"></i> Delete</a>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach                                        
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p>No forms added yet</p>
                        @endif                        

					</div>
				</div>
			</div>
        
            @if($can_add)
                <div class="tab-pane" id="Library-add">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Add Forms</h3>
                            <div class="card-options ">
                                <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{url('forms')}}" enctype="multipart/form-data">                            
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
                                            <label>Title of Form</label>
                                            <input type="text" name="title" value="" placeholder="Enter Form Title" class="form-control" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Short Description of Form (optional)</label>
                                            <textarea name="description" class="form-control" placeholder="Short description of form"></textarea>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Add Form File</label>
                                            <input type="file" name="file" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-12">
                                        <button class="btn btn-primary btn-lg btn-simple">Add</button>
                                    </div>
                                </div>                            
                            </form>
                        </div>
                    </div>
                </div>
            @endif
		
		</div>
	
    </div>
</div>

@endsection