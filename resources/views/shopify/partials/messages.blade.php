@if (request()->header('errors'))
    <div class="alert alert-danger" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
        </button>
        {{request()->header('errors')}}
    </div>
@endif


@if (request()->header('warning'))
    <div class="alert alert-warning" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
        </button>
        {{request()->header('warning')}}
    </div>
@endif


@if (request()->header('info'))
    <div class="alert alert-info" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
        </button>
        {{request()->header('info')}}
    </div>
@endif


@if (request()->header('success'))
    <div class="alert alert-success" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
        </button>
        {{request()->header('success')}}
    </div>
@endif
