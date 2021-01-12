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
                    
                    <?php
                        $msg = '';
                        if(isset($_GET['msg'])){
                            $msg = $_GET['msg'];
                        }
                    ?>
                    @if($msg)
                        <div class="col-md-12">
                            <div class="alert alert-success">
                                <p>{{$msg}}</p>
                            </div>
                        </div>
                    @endif
                        
                    <?php
                        $n = 0; 
                        $depts = get_realeased_result_depts_by_session($session->id);

                        $divs = get_realeased_result_divs_by_session($session->id);
                    ?>
                    <div class="col-md-12">
                        
                        <div class="card">
                        
                            <div class="card-header">
                                <h3 class="card-title">{{$course->name}} Results for {{$session->name}}</h3>
                                <div class="card-options">
                                    <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                                    <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
                                </div>
                            </div>

                            <div class="card-body">

                                <a href="{{url('result/overview/?s='.$session->id)}}">Click to get students overview for {{$session->name}}</a>

                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        @if(count($depts))
                                            <h6><b>Search Results By Department </b></h6>                                    

                                                <form action="{{url('result/show')}}" method="GET" )" >   
                                                    <div class="row">   

                                                        <input type="hidden" name="s" value="{{$session->id}}" />
                                                        <input type="hidden" name="c" value="{{$course->id}}" />

                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label>Department</label>
                                                                <select name="dept" id="dept" class="form-control" required>
                                                                    <option value="">Select Department</option>
                                                                    @foreach ($depts as $dept)
                                                                        <option value="{{$dept->id}}">{{$dept->name}}</option>
                                                                    @endforeach                                                   
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label>Term</label>
                                                                <select name="t" id="term" class="form-control" required>
                                                                    <option value="">Select Department First</option>                                                       
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group" style="margin-top: 25px;">
                                                                <button class="btn btn-primary btn-lg btn-simple">Search Dept Result</button>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </form>
                                        @else
                                            <i>No Department with released result</i>
                                        @endif  
                                    </div> 

                                    <div class="col-md-6">
                                        @if(count($depts))
                                            <h6><b>Search Search Results By Division</b></h6>                                    

                                                <form action="{{url('result/show')}}" method="GET" )" >   
                                                    <div class="row">   

                                                        <input type="hidden" name="s" value="{{$session->id}}" />
                                                        <input type="hidden" name="c" value="{{$course->id}}" />

                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label>Division</label>
                                                                <select name="div" id="div" class="form-control" required>
                                                                    <option value="">Select Division</option>
                                                                    @foreach ($divs as $div)
                                                                        <option value="{{$div->id}}">{{$div->name}}</option>
                                                                    @endforeach                                                   
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label>Term</label>
                                                                <select name="t" id="term-div" class="form-control" required>
                                                                    <option value="">Select Division First</option>                                                       
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group" style="margin-top: 25px;">
                                                                <button class="btn btn-primary btn-lg btn-simple">Search Div Result</button>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </form>
                                        @else
                                            <i>No Department with released result</i>
                                        @endif  
                                    </div> 
                                </div>


                            </div>

                        </div>
                    </div>

                </div>
            </div>

		</div>
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function(){

        $('#dept').on('change',function(){
            var dept = $(this).val();
            if(dept){
                $.ajax({
                    url:"{{url('ajax/releasedresult/term')}}/"+dept,
                    type:'get',
                    data:{},
                    success:function(html){
                        $('#term').html(html);
                    }
                }); 
            }else{
                $('#term').html('<option value="">Select Department First</option>');
            }
        }); 

        $('#div').on('change',function(){
            var div = $(this).val();
            if(div){
                $.ajax({
                    url:"{{url('ajax/releasedresultdiv/term')}}/"+div,
                    type:'get',
                    data:{},
                    success:function(html){
                        $('#term-div').html(html);
                    }
                }); 
            }else{
                $('#term-div').html('<option value="">Select Division First</option>');
            }
        });
             
    });  
</script>

@endsection