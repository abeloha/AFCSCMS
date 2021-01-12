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
                        <h3 class="text-danger">Account Deactivated<h3>
                    </div>
                </div>
                
                <p>Your account has been deactivated. Could be because you have finished your assignment/study in the college. </p>
                <p>Visit admin office for help on this.</p>
                <div class="text-center">
                    <a href="{{url('/user')}}"><button class="btn btn-primary btn-block">My Account</button></a>
                </div>
                
			</div>
		</div>
	</div>
</div>

@endsection