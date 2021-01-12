@extends('layout')

<?php 
    $page_title = 'Inbox';
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

            <div class="d-flex justify-content-between align-items-center ">

                <div class="row">
                    
                    <div class="col-md-8">
                        <form method="POST" action="{{url('mail/inbox')}}" enctype="multipart/form-data">
                            <div class="input-group" style="padding-top: 12px;">
                                @csrf 
                                <input type="text" name="s" class="form-control" value="{{$search}}" placeholder="What do you want to find in your inbox?" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="submit">Search inbox</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-4">
                        <div class="col-md-12">
                        <ul class="nav nav-tabs">
                            <li class="nav-item"><a class="nav-link active" id="Primary-tab" href="{{url('mail')}}">Inbox</a></li>
                            <li class="nav-item"><a class="nav-link" id="Social-tab" href="{{url('mail/sent')}}">Sent Mails</a></li>
                            <li class="nav-item"><a href="{{url('mail/compose')}}" class="btn btn-primary" title="">Compose</a></li>
                        </ul>
                        </div>
                    </div>

                </div>

            </div>

            <h4>Inbox</h4>
            <div class="tab-pane fade show active" id="Primary">
                <div class="accordion" id="accordionExample">
                
                    @if($search)
                        <p>Search results for <b>{{$search}}</b></p>
                    @endif

                    @if(count($messages))

                        @foreach($messages as $message)
                            <?php 
                                $attachments = get_message_files($message->id); 
                                $count_attachments = count($attachments);
                            ?>
                            <div class="card inbox {{($message->is_read)? '':'unread'}}">                        
                                <div class="card-header" id="headingOne">

                                        <h5 class="mb-0">
                                            <a href="{{url('mail/view/'.$message->id)}}"><button class="btn btn-link" type="button">{{$message->surname.' '.$message->first_name}}</button></a>
                                        </h5>
                                    
                                        <a href="{{url('mail/view/'.$message->id)}}">
                                            <span class="text_ellipsis xs-hide text-body">
                                                {{$message->subject}}
                                                <br>
                                                <p>
                                                    <small class="float-right">
                                                        - {{date('h:i a. D d, M, Y', strtotime($message->created_at))}}.
                                                        @if($count_attachments)
                                                            <i class="fe fe-paperclip mr-1"></i>{{$count_attachments}} attachments
                                                        @endif
                                                    </small>
                                                </p>
                                            </span>
                                        </a>

                                    <div class="card-options">
                                        <a class="text-muted" href="{{url('mail/compose?reply='.$message->id)}}" title="Reply"><i class="fa fa-reply"></i></a>
                                        <a class="text-muted" href="{{url('mail/compose?forward='.$message->id)}}" title="Forward"><i class="fa fa-mail-forward"></i></a>
                                        <a class="text-muted" href="{{url('mail/view/'.$message->id)}}" title="View"><i class="fa fa-eye"></i></a>                                        
                                        <a class="text-muted" href="{{url('mail/delete/'.$message->id)}}" onclick="return confirm('Are you Sure you want to delete your copy of this mail?')" title="Delete"><i class="fa fa-trash"></i></a>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    @else

                        @if($search)
                            <p><i>No result found.</i></p>
                        @else
                            <p><i>Your inbox is empty.</i></p>
                        @endif

                    @endif
                    
                </div>
            </div>

		</div>
	</div>
</div>


@endsection