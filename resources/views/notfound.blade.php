@php ($extends = 'app')
@if(is_axios())
@php ($extends = 'app_silent')
@endif

@extends('layouts.'.$extends)

@section('content')
     <!-- main-content -->


    <!-- container -->


    <!-- row -->
    <div class="row">
        <!-- Main-error-wrapper -->
        <div class="main-error-wrapper wrapper-1 page page-h">
            <h1 class=""> {!! num_in_khmer("404") !!}<span class="tx-20">@lang('dev.error')</span></h1>
            <h2 class="">@lang('dev.page_not_found')</h2>
            <h6 class="">@lang('dev.the_page_you_are_looking_for_doesnt_exist')</h6><a class="btn btn-primary"
                href="{{ url_builder('admin.controller', ['home']) }}">@lang('btn.btn_back_to_home')</a>
        </div>
        <!-- /Main-error-wrapper -->

    </div>
    <!-- row closed -->

    <!-- Container closed -->

    <!-- main-content closed -->
@endsection
