@extends('layout')

<?php 
    $page_title = 'Exercises Details';
    $can_add = check_has_ability('manage_exercise');
    $user_id = get_user_id();
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
        <?php                    
            $success = false;
            if(isset($_GET['success'])){
                $success = true;
            }
            $deleted = false;
            if(isset($_GET['deleted'])){
                $deleted = true;
            } 
            $addedreq = false;
            if(isset($_GET['addedreq'])){
                $addedreq = true;
            }                
        ?>
        @if($success)
            <div class="alert alert-info">
                <p>New exercise material has been added successfully</p>
            </div>
        @endif
        @if($deleted)
            <div class="alert alert-danger">
                <p>The exercise material has been deleted successfully</p>
            </div>
        @endif
        @if($addedreq)
            <div class="alert alert-info">
                <p>New exercise requirement has been added successfully</p>
            </div>
        @endif

		<div class="row">            

            @if($item)
                <?php
                    $can_manage_materials =  check_can_manage_exercise_materials($item->id);
                ?>

                <div class="col-xl-4 col-lg-5 col-md-12">
                
                    @include('exercise.exercise-card',['item'=>$item,'full_description' => 1])
                    
                </div>
                
                <div class="col-xl-8 col-lg-7 col-md-12">
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Exercise Materials</h3> 

                            <div class="card-options ">
                                <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            
                            @if($can_manage_materials)
                                <a href="{{url('exercise/'.$item->id.'/material')}}"><button type="button" class="btn btn-icon btn-sm" title="Add"><i class="fa fa-plus"></i> Click To Add New Material To This Exercise</button></a>
                            @endif 

                            <?php
                                $materials = get_exercise_materials($item->id);
                                $material_count = count($materials);
                            ?>

                            @if($material_count)                                
                                @foreach ($materials as $material)
                                    <hr>
                                    {!!$material->content!!} 
                                    <br>

                                    <?php
                                        $file_Number = 0;
                                    ?>

                                    <div class="file_folder">
                                        @if($material->file_1)
                                            <?php $file_Number++ ;?>
                                            <a href="{{asset('storage/material/'.$material->file_1)}}" download="AFCSC_Exercise_Material_{{$material->file_1}}">
                                                <div class="icon">
                                                    <i class="fa fa-folder text-success"></i>
                                                </div>
                                                <div class="file-name">
                                                    <p class="mb-0 text-muted">Download</p>
                                                    <small>Material {{($file_Number > 1)? $file_Number : ''}}</small>
                                                </div>
                                            </a>
                                        @endif 
                                        @if($material->file_2)
                                            <?php $file_Number++ ;?>
                                            <a href="{{asset('storage/material/'.$material->file_2)}}" download="AFCSC_Exercise_Material_{{$material->file_2}}">
                                                <div class="icon">
                                                    <i class="fa fa-folder text-success"></i>
                                                </div>
                                                <div class="file-name">
                                                    <p class="mb-0 text-muted">Download</p>
                                                    <small>Material {{($file_Number > 1)? $file_Number : ''}}</small>
                                                </div>
                                            </a>
                                        @endif                                        
                                        @if($material->file_3)
                                            <?php $file_Number++ ;?>
                                            <a href="{{asset('storage/material/'.$material->file_3)}}" download="AFCSC_Exercise_Material_{{$material->file_3}}">
                                                <div class="icon">
                                                    <i class="fa fa-folder text-success"></i>
                                                </div>
                                                <div class="file-name">
                                                    <p class="mb-0 text-muted">Download</p>
                                                    <small>Material {{($file_Number > 1)? $file_Number : ''}}</small>
                                                </div>
                                            </a>
                                        @endif
                                    </div>
                                    @if($can_manage_materials)
                                        <br>
                                        <a href="{{url('exercise/material/'.$material->id.'/delete')}}" onclick="return confirm('Are you sure you want to delete this material?')"><button type="button" class="btn btn-icon btn-sm" title="Delete"><i class="fa fa-trash-o text-danger"></i> Delete this material</button></a>
                                    @endif

                                @endforeach 
                            @else
                                <p><i>No exercise material added yet</i></p>
                            @endif
                            
                            
                        </div>
                    </div>


                    @if($can_manage_materials)
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Add New Exercise Requirements</h3>
                                <div class="card-options ">
                                    <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p>
                                    <a href="{{url('exercise/'.$item->id.'/requirment/add?type=1')}}"><button type="button" class="btn btn-icon btn-sm" title="Add"><i class="fa fa-plus"></i> Click To Add New Written Requirment To This Exercise</button></a>
                                    <br>
                                    <span style="font-size: 11px;"><b>Written Requirements:</b> This requirment is shown to students and accepts submission from student</span>
                                </p>
                                <p>
                                    <a href="{{url('exercise/'.$item->id.'/requirment/add?type=2')}}"><button type="button" class="btn btn-icon btn-sm" title="Add"><i class="fa fa-plus"></i> Click To Add New UTW Requirment To This Exercise</button></a>
                                    <br>
                                    <span style="font-size: 11px;"><b>UTW Requirements:</b> This requirment is hidden from student and does not accept submissions. It can be used for Oral assessments</span>
                                </p>
                            </div>
                        </div>
                    @endif


                </div>  

            @else
                <div class="col-md-12">
                    <i>Exercise details could not be loaded now or exercise has been removed.</i>
                </div>
            @endif
		</div>
	
    </div>
</div>
   

@endsection