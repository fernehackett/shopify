@extends('shopify.default')

@section('page-header')
    Hello, {{ $store->name }}
@stop
@section('content')
    <div class="bgc-white bd bdrs-3 p-20 mB-20">
        {{ Form::open([
            "url" => route("shopify.anti-theft"),
            "method" => "POST"
        ]) }}
            <div class="form-check">
                <label class="form-label form-check-label">
                    @isset($antiTheft)
                        <input type="hidden" name="script_id" value="{{ $antiTheft->script_id }}">
                    @endisset
                    <input onchange="this.form.submit()" class="form-check-input" name="anti-theft" value="on" type="checkbox" @isset($antiTheft) checked @endisset> Anti Theft
                </label>
            </div>
        {{ Form::close() }}
    </div>
@stop