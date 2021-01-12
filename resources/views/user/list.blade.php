@extends('layout')

<?php 
    $page_title = $header_title;
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
			<div class="tab-pane active">

                <?php
                    $n = 1;
                ?>

				<div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{$header_title}}</h3>
                    </div>

					<div class="card-body">     

                        @if($collection)
                            <div class="table-responsive">
							
                                <table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5" data-page-length='100'>
								
								<!--<table id="button_datatables_example" class="table display table-striped table-bordered">-->
                                    <thead>                                        
                                        <tr>
                                            <th>#</th>
                                            <th>Pix</th>
                                            <th>Rank</th>
                                            <th>Surname</th> 
                                            <th>First Name</th>
                                            <th>Svc No</th> 
                                            <th>Dept</th>
                                            @if($is_student_list)
                                                <th>Course</th>
                                            @else
                                                <th>Appointment</th>
                                            @endif
                                            <th>Service</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Sex</th>
                                            <th>Country</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($collection as $item)  
                                            <?php
                                                $img = 'default.png';
                                                if($item->picture){
                                                    $img = $item->picture;
                                                }
                                            ?>
                                            <tr>
                                                <td>{{$n++}}</td>
                                                <td class="w60">
                                                    <img class="avatar" src="{{asset('storage/user/'.$img)}}" alt="{{$item->surname}}">
                                                </td>
                                                <td>{{$item->rank}}</td>
                                                <td><a href="{{url('user/'.$item->id)}}" target="_blank">{{$item->surname}}</a></td>
                                                <td><a href="{{url('user/'.$item->id)}}" target="_blank">{{$item->first_name}}</a></td>
                                                <td>{{$item->svc_no}}</td>
                                                <td>{{$item->dept}}</td>
                                                
                                                @if($is_student_list)
                                                    <td>{{$item->course}}</td>
                                                @else
                                                    <td>{{role_name($item->role,1)}}</td>
                                                @endif

                                                <td>{{$item->service}}</td>
                                                <td>{{$item->email}}</td>
                                                <td>{{$item->phone}}</td>
                                                <td>{{$item->sex}}</td>
                                                <td>{{$item->country}}</td>
                                                <td>
                                                    {{($item->approved)? 'Approved' : 'Unapproved'}}
                                                    @if($item->deactivated)
                                                        <span class="text-danger">Deactivated</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach                                        
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p>No user yet (if you are searching for student, ensure you set current session for each course)</p>
                        @endif                        

					</div>
				</div>
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