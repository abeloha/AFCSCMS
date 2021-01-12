@extends('layout')

<?php 
    $page_title = "ARMED FORCES COMMAND AND STAFF COLLEGE | $sub_details_menu";
    $n = 0;
?>

@section('title', $page_title)

@section('content')

<div class="section-body">
	<div class="container-fluid">
		<div class="d-flex justify-content-between align-items-center">
			<div class="header-action">
				<h1 class="page-title">ARMED FORCES COMMAND AND STAFF COLLEGE</h1>
				<ol class="breadcrumb page-breadcrumb">
				<li class="breadcrumb-item"><a href="{{url('/')}}">{{get_app_name()}}</a></li>
				<li class="breadcrumb-item active" aria-current="page">Results | {{$sub_details_menu}}</li>
				</ol>
            </div>
		</div>
	</div>
</div>

<div class="section-body mt-4">
	<div class="container-fluid">
		<div class="tab-content">
		 
            <div class="tab-pane active" id="Student-profile">
                <div class="row">

                    <div class="table-responsive">
                        
                        <div>
                            <br>
                            <h5 class="text-center"><u>ARMED FORCES COMMAND AND STAFF COLLEGE JAJI NIGERIA</u></h5>
                            <h6 class="text-center">{!!$sub_details!!}</h6>
                            <br>
                        </div>

                        @if(count($students))

                            <form method="POST" onsubmit="return confirm('Are you yure you want to add these values to the students weighted point for this term?')" action="{{url('result/moderate')}}">
                                
                                @csrf

                                <?php 
                                    $grader_id = get_user_id();
                                ?>

                                <input type="hidden" name="grader_id" value="{{$grader_id}}" />                                
                                <input type="hidden" name="s" value="{{$session->id}}" />
                                <input type="hidden" name="t" value="{{$term->id}}" />
                                <input type="hidden" name="c" value="{{$course->id}}" />
                                <input type="hidden" name="div" value="{{$div_id}}" />
                                <input type="hidden" name="dept" value="{{$dept_id}}" />

                                <table class="table mb-0 dataTable text-nowrap" data-page-length='100'>
                                    
                                    <thead>

                                        <tr>
                                            <th>Serial</th>
                                            <th>Rank</th>
                                            <th>Name</th>
                                            <th>Syn</th>
                                            <th>W/P</th>
                                            <th>Score</th>
                                            <th>Grade</th>
                                            <th>Add Wt Pt
                                                <br><span style="font-size:10px">(To students)</span>
                                            </th>
                                        </tr>

                                    </thead>
                                    <tbody>

                                        <tr>
                                            <td colspan="6" style="text-align: center; border-bottom: 1px, solid black">
                                                <b>Marks across board:</b>
                                            </td>
                                            <td colspan="2" style="text-align: left; border-bottom: 1px, solid black">
                                                <input type="number" name="mark_across" id="mark_across" step="any" /> (The mark is assigned to all)
                                            </td>
                                        </tr>

                                        @foreach($students as $student)
                                            <tr>
                                                <?php                                             
                                                    $syndicate = get_student_syndicate($student->id, $term->id, $session->id); 

                                                    $syndicate_name = '';
                                                    if($syndicate){
                                                        $syndicate_name = shorten_syndicate_name($syndicate->name);
                                                    }

                                                    $student_max_moderation_value = $student->term_exercise_wp - $student->term_wp;
                                                ?>

                                                <td class="border-right border-left">{{++$n}}</td>
                                                <td>{{$student->rank}}</td>

                                                <td class="border-right">
                                                    @if($show_identity)
                                                        <a href="{{url('user/'.$student->id)}}" target="_blank">{{$student->surname .' '.$student->first_name}}</a>
                                                    @else
                                                        {{user_id_to_code($student->id)}}
                                                    @endif
                                                </td>

                                                <td class="border-right">{{$syndicate_name}}</td>

                                                <td>{{$student->term_wp}}</td>
                                                <td>{{$student->term_score}}</td>
                                                <td>{{get_grade($student->term_score)}}</td>

                                                <td>
                                                    <input type="hidden" name="student[id][]"  value="{{$student->id}}"/>
                                                    <input type="number" name="student[grade][]" max="{{$student_max_moderation_value}}" step="any"  />
                                                </td>
                                                                                        
                                            <tr>
                                        @endforeach                                    

                                    </tbody>
                                </table>

                                <hr>

                                <div class="col-sm-12 text-right">
                                    <button class="btn btn-primary btn-lg btn-simple">Save Added Wighted Points</button>
                                    &nbsp &nbsp<a href="{{url('result/show?s='.$session->id.'&t='.$term->id.'&dept='.$dept_id.'&div='.$div_id.'&c='.$course->id)}}">Cancel This Action</a>
                                    <br>
                                </div>
                                <br><br>
                                
                            </form>

                        @else
                            <p><i>No students record</i></p>
                        @endif

                    </div>

                </div>
            </div>

		</div>
	</div>
</div>

<script>
    $('input[name="mark_across"]').keyup(function(){
        markAcrossBoard($(this).val());
    });

    function markAcrossBoard(mark)
    {  
        console.log('applying marks accross board');
        $('input[name^="student[grade]"]').each(function() {
            //totalAssigned += parseInt(this.value);
            this.value = mark
        });
    }
</script>

@endsection