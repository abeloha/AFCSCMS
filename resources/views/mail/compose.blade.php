@extends('layout')

<?php 
    $page_title = 'Compose';
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
                <li class="breadcrumb-item"><a href="{{url('mail')}}">inbox</a></li>
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
					<div class="card-body mail_compose">

                        <form method="POST" action="{{url('mail/send')}}" onsubmit="return checkSendMail();" enctype="multipart/form-data"> 
                                                      
                            @csrf 
                            
                            <div class="form-group">
								<input type="hidden" name="receivers" id="receivers" >
                                <input type="hidden" name="parent_id" value="{{$parent_id}}">
                                <input type="hidden" name="is_forward" value="{{$is_forward}}">
                            </div>
                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-1">
                                        <label>To: </label>
                                    </div>
                                    <div class="col-md-11">
                                        <input type="text" name="to" id="to" data-id="{{$recipient}}" class="form-control" placeholder="Recipients name or email">
                                        <div class="text-right"> <span class="text-danger" id="to-err-msg"></span> </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-1">
                                        <label>Subject: </label>
                                    </div>
                                    <div class="col-md-11">
                                        <input type="text" name="subject" class="form-control" value="{{$subject}}" placeholder="Subject" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
								<textarea id="editor1" name="message" class="form-control" required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <span>Attachments. <span style="font-size: 11px;">You can select multiple files by holding CTRL or SHIFT</span> </span>
								<input type="file" name="files[]" class="form-control" multiple>
                            </div>

                            @if($is_forward)
                                <p><i>...Forwarded message will be quoted here.</i></p>
                            @endif

                            @if($is_reply)
                                <p><i>...Original message will be quoted in this reply</i></p>
                            @endif
                            
                            <div class="form-group text-right"> <span class="text-danger" id="form-err-msg"></span> </div>

                            <div class="mt-4 text-right">
                                <input type="submit" class="btn btn-success" value="Send Message" >
                                <a href="{{url('mail')}}" class="btn btn-outline-secondary">Cancel</a>
                            </div>

                        </form>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
    $(function() {

        $('#to').magicsearch({
           
            dataSource: '/ajax/users',
            type: 'ajax',
            ajaxOptions: {
                success: function(data) {

                }
            },

            fields: ['surname', 'first_name', 'email'],
            id: 'id',
            noResult: 'Not found...',
            format: '%surname% · %first_name% · %email%',
            multiple: true,
            multiField: 'surname% %first_name',
            multiStyle: {
                space: 5,
                width: 120
            }
        });

    }); 
</script>

<script>
    function checkSendMail()
    {
        $('#receivers').value = '';

        $('#form-err-msg').html('');
        $('#to-err-msg').html('');        

        var receivers = $('#to').attr('data-id');
        $('#receivers').val(receivers);
        
        var to = $('#receivers').val();

        if(to.length > 0){
            return true;
        };

        $('#form-err-msg').html('Recipients cannot be empty. Search for Recipients by name or email select from the dropdown');
        $('#to-err-msg').html('Search for Recipients by name or email select from the dropdown');
        return false;
    }

	initSampleEditor1();
</script>



@endsection