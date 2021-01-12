@extends('layout')

<?php 
    $page_title = 'Mail Details';
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
				<li class="breadcrumb-item"><a href="{{url('mail')}}">Mail</a></li>
				<li class="breadcrumb-item active" aria-current="page">{{$page_title}}</li>
				</ol>
            </div>
		</div>
	</div>
</div>

<div class="section-body mt-4">
	<div class="container-fluid">
		<div class="row row-deck">
			<div class="col-12">
				<div class="card">

                    @if($message)
                    
                        <div class="card-header">
                            <h3 class="card-title"><a href="{{url('mail')}}"><i class="fa fa-arrow-left"></i> Back</h3></a>
                            <div class="card-options">

                                <a href="{{url('mail/compose?reply='.$message->id)}}"><i class="fa fa-reply"></i> Reply </a>
                                <a href="{{url('mail/compose?forward='.$message->id)}}"><i class="fa fa-mail-forward"></i> Forward </a>                                        
                                <a href="#"  onclick="javascript:document.getElementById('mail-max').click(); window.print();document.getElementById('mail-max').click();"><i class="fa fa-print"></i> Print </a>
                                <a href="{{url('mail/delete/'.$message->id)}}" onclick="return confirm('Are you Sure you want to delete your copy of this mail?')"><i class= "fa fa-trash"></i> Delete </a>
                                <a href="#" data-toggle="card-fullscreen" id="mail-max"></a>

                                
                            </div>
                        </div>
                        
                        <div class="card-body detail">

                            
                            @include('mail.include-body',array('message'=>$message))

                            <div class="mail-cnt mt-3">
                                <strong>Click here to</strong>
                                <a href="{{url('mail/compose?reply='.$message->id)}}">Reply all</a> or
                                <a href="{{url('mail/compose?forward='.$message->id)}}">Forward all</a>
                            </div>
                        
                        </div>                        

                    @else
                        <div class="card-body detail"><i>Mail not found.</i></div>
                    @endif                    
				
				</div>
			</div>
		</div>
	</div>
</div>


@endsection