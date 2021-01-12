@extends('landing.layout')

@section('title', 'Approve')

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
                    <div class="card-title">
                        <h3 class="text-danger">Account Disabled<h3>
                    </div>
                </div>
                
                <p>Your account has been disabled because you have graduated or finished your study in the college.</p>
				<p>Visit admin office for help.</p>
				<div class="text-center">
                    <a href="{{url('/user')}}"><button class="btn btn-primary btn-block">My Account</button></a>
                </div>
                
			</div>
		</div>
	</div>
</div>

@endsection