@extends('frame')

@section('menu-content')

	<li class="active"><a href="{{url('/')}}"><i class="fa fa-dashboard"></i><span>Dashboard</span></a></li>
	
    <li><a href="{{url('user')}}"><i class="fa fa-user"></i><span>My account</span></a></li>
    <li><a href="{{url('user/edit')}}"><i class="fa fa-pencil"></i><span>Edit account</span></a></li>

@endsection
