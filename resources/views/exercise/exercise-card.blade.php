<div class="card">
	<div class="card-body d-flex flex-column">
        <h5><a href="{{url('exercise/'.$item->id)}}">{{$item->name}}</a></h5>
        <div class="text-muted">
            {{my_substring($item->description, 77)}}
        </div>
	</div>
	<div class="table-responsive">
		<table class="table table-striped table-vcenter mb-0">
			<tbody>
                <tr>
					<td><i class="fa fa-building text-danger"></i></td>
					<td class="tx-medium">Department</td>
					<td class="text-right">{{$item->dept}}</td>
				</tr>
				<tr>
					<td><i class="fa fa-graduation-cap text-warning"></i></td>
					<td class="tx-medium">Course</td>
					<td class="text-right">{{$item->course}}</td>
				</tr>				
				<tr>
					<td class="w20"><i class="fa fa-calendar-o text-blue"></i></td>
					<td class="tx-medium">Term</td>
					<td class="text-right">{{$item->term}}</td>
				</tr>    
			 </tbody>
		</table>
	</div>
	<div class="card-footer">
		<div class="d-flex align-items-center mt-auto">
            <?php
                $sponsor = '';
                if($item->sponsor_user_id){
                    $sponsor = get_user($item->sponsor_user_id);                    
                }             
            ?>
            @if($sponsor)
                <?php
                    $img = 'default.png';
                    if($sponsor->picture){
                        $img = $sponsor->picture;
                    }
                ?>
                <img class="avatar avatar-md mr-3" src="{{asset('storage/user/'.$img)}}" alt="avatar">
                <div>
                    <a href="{{url('user/'.$sponsor->id)}}">{{$sponsor->rank.' '.$sponsor->surname.' '.$sponsor->first_name}}</a>
                    <small class="d-block text-muted">Sponsor DS</small>
                </div>
            @else
                <img class="avatar avatar-md mr-3" src="{{asset('storage/user/default.png')}}" alt="avatar">
                <div>
                    <a href="#">-</a>
                    <small class="d-block text-muted">Sponsor DS</small>
                </div>
            @endif
		</div>
    </div>
    <div class="card-footer">
		<div class="d-flex align-items-center mt-auto">
            <?php
                $cosponsor = '';
                if($item->cosponsor_user_id){
                    $cosponsor = get_user($item->cosponsor_user_id);                    
                }                
            ?>
            @if($cosponsor)
                <?php
                    $img = 'default.png';
                    if($cosponsor->picture){
                        $img = $cosponsor->picture;
                    }
                ?>
                <img class="avatar avatar-md mr-3" src="{{asset('storage/user/'.$img)}}" alt="avatar">
                <div>
                    <a href="{{url('user/'.$cosponsor->id)}}">{{$cosponsor->rank.' '.$cosponsor->surname.' '.$cosponsor->first_name}}</a>
                    <small class="d-block text-muted">Co-sponsor DS</small>
                </div>
            @else
                <img class="avatar avatar-md mr-3" src="{{asset('storage/user/default.png')}}" alt="avatar">
                <div>
                    <a href="#">-</a>
                    <small class="d-block text-muted">Co-sponsor DS</small>
                </div>
            @endif

		</div>
    </div>
    <div class="card-footer">
		<div class="d-flex align-items-center">
            <a href="{{url('exercise/'.$item->id)}}">Click to View Exercise</a>
		</div>
	</div>
</div>