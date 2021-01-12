<?php
    $unread_messages = get_unread_messages();
    $count_unread_messages = count($unread_messages);    
?>

<div class="right">
    <div class="notification d-flex">
    
        <div class="dropdown d-flex">
            <a class="nav-link icon d-none d-md-flex btn btn-default btn-icon ml-1" data-toggle="dropdown"><i class="fa fa-envelope"></i>
                @if($count_unread_messages)
                    <span class="badge badge-success nav-unread"></span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
            <ul class="right_chat list-unstyled w350 p-0">
                @if($count_unread_messages)
                    @foreach ($unread_messages as $item)  
                        <?php
                            $img = 'default.png';
                            if($item->picture){
                                $img = $item->picture;
                            }
                        ?>                          
                        <li class="online">
                            <a href="{{url('mail/view/'.$item->id)}}" class="media">
                                
                                <img class="media-object" src="{{asset('storage/user/'.$img)}}" alt="pix">

                                <div class="media-body">
                                <span class="name">{{$item->surname.' '.$item->first_name}}</span>
                                <div class="message">{{$item->subject}}</div>
                                <small>{{date('h:i a. D d, M, Y', strtotime($item->created_at))}}</small>
                                <span class="badge badge-outline status"></span>
                                </div>
                                
                                
                            </a>
                        </li>
                    @endforeach
                @else
                    <li class="online">
                        <div style="padding-left: 12px;"><i>No new mail</i></div>
                    </li>
                @endif
            </ul>            
                <div class="dropdown-divider"></div>
                <a href="{{url('mail')}}" class="dropdown-item text-center text-muted-dark readall">Open Inbox</a>
            </div>
        </div>
        
        <div class="dropdown d-flex">
            <a href="javascript:void(0)" class="chip ml-3" data-toggle="dropdown">
            <?php 
                $img = '';
                $name = 'Profile';
                $is_signed_in = 0;
                
                if(!Auth::guest()){
                    $img = Auth::user()->picture;
                    $name = Auth::user()->surname;
                    $is_signed_in = 1;
                }

                if(!$img)
                    $img = 'default.png';
                
            ?>
            <span class="avatar" style="background-image: url({{asset('storage/user/'.$img)}})"></span> {{$name}}</a>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">									
                @if($is_signed_in)
                    <a class="dropdown-item" href="{{url('user')}}"><i class="dropdown-icon fe fe-user"></i> My Account</a>
                    <a class="dropdown-item" href="{{url('/mail')}}"><span class="float-right"><span class="badge badge-primary">{{($count_unread_messages)? $count_unread_messages : ''}}</span></span><i class="dropdown-icon fe fe-mail"></i> Inbox</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{url('/logout')}}"><i class="dropdown-icon fe fe-log-out"></i> Sign out</a>
                @else
                    <a class="dropdown-item" href="{{url('/login')}}"><i class="dropdown-icon fe fe-log-in"></i> Sign in</a>
                @endif
            </div>
        </div>
        
    </div>
</div>