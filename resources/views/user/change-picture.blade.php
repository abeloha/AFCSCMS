@extends('layout')

<?php 
    $page_title = 'Edit User Account';
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

				<div class="card">
					<div class="card-body">

                        @if($data)

                            <?php
                                $success = false;
                                if(isset($_GET['success'])){
                                    $success = true;
                                }  
                                $failed = '';
                                if(isset($_GET['failed'])){
                                    $failed = $_GET['failed'];
                                }
                            ?>

                            @if($success)
                                <div class="alert alert-info">
                                    <p>Changes has been saved successfully</p>
                                </div>
                            @endif
                            @if($failed)
                                <div class="alert alert-danger">
                                    <p>{{$failed}}</p>
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
                    
                            <form method="POST" action="{{url('user/changepicture')}}" enctype="multipart/form-data">
                           
                                @csrf 
            
                                <input type="hidden" name="user_id" value="{{$data->id}}">

                                <div class="row">  

                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <b>Name: </b> {{$data->surname.' '.$data->first_name.' '.$data->other_name}}
                                            <br><b>Email: </b> {{$data->email}}
                                            <br><b>SVC No. </b> {{$data->svc_no}}
                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label>Change user picture <span class="text-danger">*</span></label>
                                            <br>
                                            <output id="coverFileInfo">
                                                <?php
                                                    $img = 'default.png';
                                                    if($data->picture){
                                                        $img = $data->picture;
                                                    }
                                                ?>
                                                <img src="{{asset('storage/user/'.$img)}}" style="width: auto; max-width: 100%; max-height:300px;" alt="">
                                            </output>                            
                                            <br>
                                            <input type='file' name='picture' id='coverFileToUpload' accept='image/*' />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12 text-right m-t-20">
                                        <button type="submit" class="btn btn-primary">SAVE</button>
                                    </div>
                                    <a href="{{url('user/'.$data->id)}}">Cancel and go back to profile</a>
                                </div>
                                
                            </form>	

                        @else
                            <p>Account details could not be loaded now</p>
                        @endif                        

					</div>
                </div>
                
            </div>
            
		</div>
	
    </div>
</div>
    

<script>
    function coverFileSelect(evt) {
        document.getElementById('coverFileInfo').innerHTML = "";
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            var files = evt.target.files;
                            
            var result = '';
            var file;
            for (var i = 0; file = files[i]; i++) {
                // if the file is not an image, continue
                if (!file.type.match('image.*')) {
                    continue;
                }
        
                reader = new FileReader();
                reader.onload = (function (tFile) {
                    return function (evt) {
                        var div = document.getElementById('coverFileInfo');
                        div.innerHTML = '<img src="' + evt.target.result + '" style="width: auto; max-width: 100%; max-height:300px;" />';                            
                        
                    };
                }(file));
                reader.readAsDataURL(file);
            }
        } else {
            alert('The File APIs are not fully supported in this browser.');
        }
    }

    document.getElementById('coverFileToUpload').addEventListener('change', coverFileSelect, false);
</script>

@endsection