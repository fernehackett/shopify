@if (request()->has('error'))
    <div class="alert alert-danger" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
        </button>
        {{request()->get('error')}}
    </div>
@endif


@if (request()->has('warning'))
    <div class="alert alert-warning" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
        </button>
        {{request()->get('warning')}}
    </div>
@endif


@if (request()->has('info'))
    <div class="alert alert-info" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
        </button>
        {{request()->get('info')}}
    </div>
@endif


@if (request()->has('success'))
    <div class="alert alert-success" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
        </button>
        {{request()->get('success')}}
    </div>
@endif
