@extends('layout')

<?php 
    $page_title = 'Change Password';
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

                    $success = false;
                    if(isset($_GET['success'])){
                        $success = true;
                    }

                    $error = '';
                    if(isset($_GET['error'])){
                        $error = $_GET['error'];
                    }
                ?>

                @if($success)
                    <div class="alert alert-info">
                        <p>
                            Your password has been changed successfully.
                            <br><b>You will need your new password next time you login into the system.</b>
                        </p>
                    </div>
                
                @else
                    
                    @if($error)
                        <div class="alert alert-danger">
                            <p>
                                Sorry, your password change failed.
                                <br><b>{{$error}}</b>
                            </p>
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

                            @if($data)
                                
                                <form method="POST" action="{{url('user/password/change')}}">
                            
                                    @csrf             
                                    <input type="hidden" name="user_id" value="{{$data->id}}">                   

                                    <div class="row">
                                
                                        <div class="col-md-12 col-sm-12">
                                            <div class="form-group">
                                                <label>New Password <span class="text-danger">*</span></label>
                                                <input type="password" name="password" class="form-control"  required>
                                                @error('password')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-sm-12">
                                            <div class="form-group">
                                                <label>Confirm New Password <span class="text-danger">*</span></label>
                                                <input type="password" name="password_confirmation" class="form-control" required>
                                                @error('password_confirmation')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-sm-12">
                                            <div class="form-group">
                                            <hr>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-sm-12">
                                            <div class="form-group">
                                                <label>Current Password <span class="text-danger">*</span></label>
                                                <input type="password" name="current_password" class="form-control"  required>
                                                @error('current_password')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    
                                    
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12 m-t-20">
                                            <button type="submit" class="btn btn-primary">Change Password</button>
                                            <br>
                                            <br>
                                        </div>
                                        
                                        <a href="{{url('user/edit/'.$data->id)}}">Cancel and go back to edit profile</a>
                                    </div>
                                    
                                </form>	

                            @else
                                <p>Account details could not be loaded now</p>
                            @endif                        

                        </div>
                    </div>

                @endif
            </div>
            
		</div>
	
    </div>
</div>
    


@endsection