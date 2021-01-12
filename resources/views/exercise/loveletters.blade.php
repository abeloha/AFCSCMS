@extends('layout')

<?php 
    $page_title = 'Love Letters';
    $n = 0;

    $terms = get_students_terms_with_exercise_enrolled();
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
			<div class="tab-pane active" id="Library-all">

                <div class="card">

                    <div class="card-body">

                        <div class="table-responsive">
                            
                            <table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5" data-page-length='100'>
                                <thead>                                        
                                    <tr>
                                        <th>#</th>
                                        <th>Exercise</th>
                                        <th>Term</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($terms)

                                        @foreach($terms as $term)
                                            <?php
                                                $exercises = get_exercise_enrolled(0,$term->id);
                                            ?>
                                            
                                            @foreach($exercises as $item) 
                                                <?php if(!$item->love_letter){ continue;} ?>
                                                <tr>
                                                    <td>{{++$n}}</td>                                         
                                                    <td>{{$item->name}}</td>                                         
                                                    <td>{{$item->term}}</td>                                         
                                                    <td><a href="{{asset('storage/love_letter/'.$item->love_letter)}}">Download Love Letter </a></td>
                                                </tr>
                                            @endforeach
                                            
                                        @endforeach

                                    @endif                                     
                                </tbody>
                            </table>
                            
                            @if(!$n)
                                <i>You have no love letter yet.</i>
                            @endif

                        </div>

                    </div>
                </div>       
                
			</div>
		
			
		</div>
	
    </div>
</div>

@endsection