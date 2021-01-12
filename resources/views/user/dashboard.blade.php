@extends('layout')

<script src='{{asset("assets/fullcalendar/lib/main.js")}}'></script>
<link href='{{asset("assets/fullcalendar/lib/main.css")}}' rel='stylesheet' />
<script>

    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');
  
      var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        initialDate: '{{date("Y-m-d")}}',
        editable: true,
        navLinks: true, // can click day/week names to navigate views
        dayMaxEvents: true, // allow "more" link when too many events
        
        eventClick: function(arg) {
            alert(arg.event.title);
        },
      

        events: {
          url: '{{url("ajax/events")}}',
        },
        loading: function(bool) {
          document.getElementById('loading').style.display =
            bool ? 'block' : 'none';
        }
      });
  
      calendar.render();
    });
  
</script>

<?php 
    $page_title = 'Dashboard';
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
            <div class="tab-pane active" id="Holiday-Calendar">
                <div class="card">
                    <div class="card-body">                        
                        <div id='loading'>loading calendar...</div>
                        <div id='calendar'></div>

                        @if(check_has_ability('manage_event'))
                            <br>
                            <a href="{{url('events')}}">Edit Events/Add new events</a>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        
		<div class="tab-content">
		 
            <div class="tab-pane active">
                <div class="row">

                    <?php 
                        //sponsor ds
                        $exercises = get_ds_exercise(); 
                        $n = 0;
                    ?>
                    @if(count($exercises))
                        <div class="card">
                            <div class="card-body">
                                <h4>Exercise(s) you sponsor</h4>
                                @foreach($exercises as $exercise)
                                    <li><a href="{{url('exercise/'.$exercise->id)}}">{{$exercise->name}}</a></li>
                                @endforeach
                                <div class="">
                                    <div class="card">
                                        <a href="{{url('result/processing')}}" class="my_sort_cut text-muted">
                                            <i class="fa fa-bar-chart-o"></i>
                                            <span>Click here for Result Processing</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <?php 
                        //exercises enrolled
                        $exercises = get_exercise_enrolled(); 
                        $n = 0;
                    ?>                    
                    @if(count($exercises))
                        <div class="card">
                            <div class="card-body">
                                <h4>Exercise(s) you enroll</h4>
                                @foreach($exercises as $exercise)
                                    <li><a href="{{url('exercise/'.$exercise->id)}}">{{$exercise->name}}</a></li>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <?php 
                    //I am to grade
                        $exercises = get_grader_assigned_exercises(); 
                        $n = 0;
                    ?>                    
                    @if(count($exercises))
                        <div class="card">
                            <div class="card-body">
                                <h4>Exercise(s) you are to grade students</h4>                                    
                                    <table class="table table-hover js-basic-example table-striped table_custom border-style spacing5">
                                        <thead></thead>

                                        <tbody>
                                            @foreach($exercises as $exercise)
                                                <tr>
                                                    <td>{{++$n.'. '.$exercise->name}}</td>
                                                    <td><a href="{{url('exercise/'.$exercise->id)}}">View Exercise</a></td>
                                                    <td><a href="{{url('exercise/'.$exercise->id.'/grade')}}">View Submissions</a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>

                                    </table>                                    
                            </div>
                        </div>
                    @endif

                </div>
            </div>

        </div>
        
	</div>
</div>

<script>
    function doPrint(){        
        $("#options_link").hide();
        $("#quickmail").hide();
        $("#change_picture_link").hide();
        window.print();
        $("#change_picture_link").show();
        $("#quickmail").show();
        $("#options_link").show();
    }
</script>

@endsection