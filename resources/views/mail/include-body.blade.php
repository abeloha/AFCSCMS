<?php
    $attachments = get_message_files($message->id); 
    $count_attachments = count($attachments);
    $recipients = get_message_recipients($message->id);
    $user_id = get_user_id();
?>

<div class="mail-cnt mt-3">	
    <h5>{{$message->subject}}</h5>
</div>

<div class="detail-header">
    <div class="media">
        <div class="float-left">
            <?php
                $img = 'default.png';
                if($message->picture){
                    $img = $message->picture;
                }
            ?>
            <div class="mr-3">
                <img src="{{asset('storage/user/'.$img)}}" alt="Picture" class="mail-image">
            </div>
        </div>
        <div class="media-body">
            <p class="mb-0"><strong class="text-muted mr-1">From:</strong><a href="{{url('mail/compose?to='.$message->sender_id)}}" title="Send mail to {{$message->surname}}"><span>{{$message->surname.' '.$message->first_name.' ('.$message->email.')'}}</span></a><span class="text-muted text-sm float-right">{{date('h:i a. D d, M, Y', strtotime($message->created_at))}}</span></p>
            <p class="mb-0"><strong class="text-muted mr-1">To:</strong>
                
                @foreach($recipients as $recipient)
                    @if($recipient->recipient_id == $user_id)  
                        <?php mark_message_reciepient_as_read($recipient->message_recipient_id); ?> 
                        <span><b>Me</b></span>;
                    @else                 
                        <a href="{{url('mail/compose?to='.$recipient->recipient_id)}}" title="Send mail to {{$recipient->surname}}"><span>{{$recipient->surname.' '.$recipient->first_name.' ('.$recipient->email.')'}}</span></a>;
                    @endif
                @endforeach                       
                
                @if($count_attachments)
                    <small class="float-right"><i class="fe fe-paperclip mr-1"></i>{{$count_attachments}} attachment(s)</small>
                @endif
            </p>
        </div>
    </div>
</div>

<div class="mail-cnt mt-3">	
    {!!$message->message!!} 

    <br>

    @if($count_attachments)
        <div class="file_folder">

            @foreach($attachments as $attachment)
                <?php 
                    $file_name = $attachment->file;
                    if($attachment->name){
                        $file_name = $attachment->name.'.'.$attachment->type;
                    }
                ?>
                <a href="{{asset('storage/mail/'.$attachment->file)}}" download="{{$file_name}}">
                    <div class="icon">
                        <i class="{{get_file_icon($attachment->type)}} text-success"></i>
                    </div>
                    <div class="file-name">
                        <p class="mb-0 text-muted">{{$file_name}}</p>
                    </div>
                </a>
            @endforeach

        </div>
    @endif
    
</div>

<hr>

@if($message->parent_message_id)

    <?php
        $parent_massage = get_message($message->parent_message_id);
    ?>

    @if($parent_massage)

    <div class="card">        
        <div class="card-header">
            <p class="">
                @if($message->is_forward)
                    <i>..........Forwarded message.........</i>
                @else    
                    <i>..........Reply to..........</i>
                @endif
            </p>
            <div class="card-options ">
                <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
            </div>
        </div>
        <div class="card-body">
            @include('mail.include-body',array('message'=>$parent_massage))
            <br>
        </div>
    </div>
        
    @endif


@endif