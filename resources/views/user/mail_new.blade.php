@extends('layout')

@section('title', 'New Mail')

@section('content')

<div class="section-body">
	<div class="container-fluid">
		<div class="d-flex justify-content-between align-items-center">
			<div class="header-action">
				<h1 class="page-title">Mail</h1>
				<ol class="breadcrumb page-breadcrumb">
				<li class="breadcrumb-item"><a href="{{url('/')}}">{{get_app_name()}}</a></li>
				<li class="breadcrumb-item active" aria-current="page">New Mail</li>
				</ol>
			</div>
		</div>
	</div>
</div>
<div class="section-body mt-4">
	<div class="container-fluid">

		<div class="tab-content">
			<div class="tab-pane fade show active" id="admin-Dashboard" role="tabpanel">
				<div class="row clearfix row-deck">

					<div class="col-md-12">
						<div class="card">
							<div class="card-header">
							<h3 class="card-title">Quick Mail</h3>
							</div>
							<div class="card-body">
								<div class="input-group">
									<div class="input-group-prepend">
									<span class="input-group-text w80">To:</span>
									</div>
									<input type="text" class="form-control">
								</div>
								<div class="input-group mt-1 mb-3">
									<div class="input-group-prepend">
									<span class="input-group-text w80">Subject:</span>
									</div>
									<input type="text" class="form-control">
								</div>
								<div class="summernote">
									Hi there,
									<br />
									<p>The toolbar can be customized and it also supports various callbacks such as <code>oninit</code>, <code>onfocus</code>, <code>onpaste</code> and many more.</p>
									<br />
									<p>Thank you!</p>
									<h6>Summer Note</h6>
								</div>
								<button class="btn btn-default mt-3">Send</button>
							</div>
						</div>
					</div>

				</div>
			</div>

		</div>
	</div>
</div>

@endsection