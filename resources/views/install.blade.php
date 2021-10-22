@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="panel panel-default">
					<div class="panel-heading h1">Gludio</div>
					<div class="panel-body">
						@if (session('status'))
							<div class="alert alert-success">
								{{ session('status') }}
							</div>
						@endif
						<div>
							{{ Form::open(["method"=>"POST", "url"=>route("submit")]) }}
								<div class="form-group">
									<label>Your Shopify URL:</label>
									<input class="form-control" type="text" name="shop" value="" placeholder="yourshop.myshopify.com">
								</div>
								<button class="btn btn-outline-success" type="submit">Install</button>
							{{ Form::close() }}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@stop