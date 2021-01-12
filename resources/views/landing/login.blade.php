@extends('landing.layout')

@section('content')

<div class="auth option2">

    <div class="auth_left">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <a class="header-brand" href="{{url('/')}}"> <img src="{{asset('assets/images/logo.png')}}" style="height: 80px; width: 80px;"> </a>
                    <div class="card-title">
                        {{get_app_full_name()}}
                    </div>
                </div>
                <form method="POST" action="/login">
                                        
                    @csrf
                    
                    <?php
                        $show_reg_link = show_registration_link();
                        $new = false;
                        if(isset($_GET['new'])){
                            $new = true;
                        } 
                        
                        $failed = false;
                        if(isset($_GET['failed'])){
                            $failed = true;
                        } 

                        $role = false;
                        if(isset($_GET['role'])){
                            $role = true;
                        } 

                        $type = '';
                        if(isset($_GET['type'])){
                            $type = $_GET['type'];
                        } 
                    ?>
                    @if($new)
                        <div class="alert alert-info">
                            <p>Your new account has been created. Login to begin!</p>                            
                        </div>
                    @endif

                    @if($failed)
                        <div class="alert alert-danger">
                            <p>Your email and password combination is incorrect.</p>                            
                        </div>
                    @endif

                    @if($type)
                        <div class="alert alert-danger">
                            <p>You must login as "{{strtoupper($type)}}" user to continue</p>                            
                        </div>
                    @endif

                    @if($role)
                        <div class="alert alert-danger">
                            <p>You must login with an account that has a role assigned to it</p>                            
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
                    
                    
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                        @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                        @error('password')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                        @if($show_reg_link)
                            <div class="text-muted mt-4">Don't have account yet? <a href="{{url('register')}}">Sign up</a></div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</div>

@endsection