@extends('layout')

<?php 
    $page_title = 'Exercises';
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
	
		<?php
			$msg = '';
			if(isset($_GET['msg'])){
				$msg = $_GET['msg'];
			}
		?>
		@if($msg)
			<div class="alert alert-info">
				<p>{{$msg}}</p>
			</div>
		@endif

		<div class="tab-content">
			<div class="tab-pane active">                

				<div class="row row-deck">    
					<?php
						$n = 0;
					?>
					@if($collection)
						@foreach($collection as $item)
							<?php $n++; ?>
							<div class="col-xl-4 col-lg-4 col-md-6">
								@include('exercise.exercise-card',['item'=>$item,'full_description' => 0])
							</div>
						@endforeach
					@else
						<p>No exercise to display</p>
					@endif                        

					
					@if(!$n)
						<?php $is_student=is_student(); ?>
						<div class="card">
							<div class="card-body"> 							
								<div class="col-12">
									@if($is_student)
										<p><i>You have not registered for any exercise</i></p>
									@else
										<p><i>No exercise to display</i></p>
									@endif
								</div>
							</div>
						</div>					
					@endif

				</div>
				
			</div>
		
		</div>
	
    </div>
</div>

@endsection