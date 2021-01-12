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
                
                @if($options)
                    @foreach($options as $option)
                        <?php
                            $t = $option['t'];
                            $c = $option['c'];
                            $name = $option['n'];
                        ?>
                        <div class="text-center" style="margin-bottom: 10px;">
                            <a href="{{url('register?t='.$t.'&c='.$c)}}"><button type="submit" class="btn btn-primary btn-block">{{$name}}</button></a>
                        </div>
                    @endforeach
                    
                    <div class="text-center" style="margin-bottom: 10px;">
                        <i>If you do not see your type of account, it could be that the registration has closed. Please contact the system admin.</i>
                    </div>
                    
                    <div class="text-center">
						<div class="text-muted mt-4">Already have account? <a href="{{url('login')}}">Sign in</a></div>
                    </div>
                @else
                    <div class="alert alert-danger">
                        <p>Registration has closed at this time!</p>
                    </div>
                @endif

			</div>
		</div>
	</div>
</div>

@endsection