@php
    $extends = 'app';
    $action_btn = ['save' => true, 'print' => false, 'cancel' => true, 'new' => true];
    foreach (config('me.app.project_lang') as $lang) {
        $langcode[] = $lang[0];
    }
@endphp
@if (is_axios())
    @php
        $extends = 'axios';
        $action_btn = ['save' => true, 'print' => false, 'cancel' => false];
    @endphp
@endif

@extends('layouts.' . $extends)

@section('blade_css')
@endsection

@section('blade_scripts')
    <script>
        $(document).ready(function() {
            $(document).on("change", ".tab_title", function(ev) {
                ///

                var $value = $(this).val();
                helper.enableDisableByLang($(this), {!! json_encode($langcode, true) !!}, 'title-', $value);

                ///
            });


            let hide = "{{ $isupdate ?? '' }}"
            if (hide) {
                $('.create_img').hide();

            } else {
                $('.update_img').hide();
            }


            let route_submit = "{{ $route['submit'] }}";
            let route_cancel = "{{ $route['cancel'] ?? '' }}";

            let frm = "frm-{{ $obj_info['name'] }}";
            let extraFrm;
            let popModal = {
                show: false,
                size: 'modal-lg'
                //modal-sm
                //modal-lg
                //modal-xl
            };
            let container = '';
            let loading_indicator = '';
            let aftersave = (data) => {
                if (data.data.length == 0) {

                    // add new
                    if ("{{ is_axios() }}") {
                        location.reload();
                    }
                    // end add new

                    helper.successAlert("{{ session('message') }}");
                    $('#' + frm)[0].reset();
                }
            };
            let setting = {
                mode: "{{ $extends }}",
                fnSuccess: aftersave,
            };
            $(".btnsave_{{ $obj_info['name'] }}").click(function(e) {
                // alert(1);
                e.preventDefault();
                $("#frm-{{ $obj_info['name'] }} .error").html('').hide();
                helper.silentHandler(route_submit, frm, extraFrm, setting,
                    popModal, container,
                    loading_indicator);

            });

            $(".btncancel_{{ $obj_info['name'] }}").click(function(e) {
                //window.location.replace(route_cancel);
                window.location = route_cancel;
            });


            $(".btnprint_{{ $obj_info['name'] }}").click(function(e) {
                //window.location.replace(route_cancel);
                //window.location = route_print;
                window.open(
                    route_print);
            });
            $('#remove').on('click', function(e) {
                $('.update_img').hide();
                $('.create_img').show();
            });

        });
    </script>
@endsection
@section('content')
    {{-- Header --}}
    <section style="position: sticky;top: 64px; z-index:2"
        class="content-header bg-light ct-bar-action ct-bar-action-shaddow">

        <div class="col-lg-12 col-md-12 sticky">
            <div class="card custom-card" id="right">
                <div class="card-body">
                    <div class="text-wrap">
                        <div class="example">
                            <nav class="breadcrumb-4 d-flex">
                                <div class="flex-grow-1">
                                    <h5 class="mb-2 mg-t-20 mg-l-20">
                                        {!! $obj_info['icon'] !!}
                                        <a href="{{ url_builder($obj_info['routing'], [$obj_info['name']]) }}"
                                            class="ct-title-nav text-md">{{ $obj_info['title'] }}</a>
                                        <small class="text-muted text-sm">
                                            <i class="ace-icon fa fa-angle-double-right text-xs"></i>
                                            {{ $caption ?? '' }}
                                        </small>
                                    </h5>
                                </div>
                                <div class="pd-10 ">
                                    @include('app._include.btn_create', $action_btn)

                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    {{-- end header --}}
    <div class="container-fluid">
        {{-- Start Form --}}

        <form name="frm-{{ $obj_info['name'] }}" id="frm-{{ $obj_info['name'] }}" method="POST"
            action="{{ $route['submit'] }}">
            {{-- please dont delete these default Field --}}
            @CSRF
            <input type="hidden" name="{{ $fprimarykey }}" id="{{ $fprimarykey }}"
                value="{{ $input[$fprimarykey] ?? '' }}">
            <input type="hidden" name="jscallback" value="{{ $jscallback ?? (request()->get('jscallback') ?? '') }}">
            <br>
            <div class="card">
                <div class="card-body">
                    <div class="row row-sm">
                        <div class="col-6">
                            <div class="form-group">
                                <label for=""><b>@lang('dev.name_kh_eng')</b><span class="text-danger">*</span></label>
                                <div class="input-group my-group" style="width:100%;">

                                    <select class="form-control form-select input-sm tab_title" style="width:20%;">
                                        @foreach (config('me.app.project_lang') as $lang)
                                            <option value="@lang($lang[0])">@lang('dev.lang_' . $lang[0])</option>
                                        @endforeach

                                    </select>
                                    @php
                                        $active = '';
                                    @endphp
                                    @foreach (config('me.app.project_lang') as $lang)
                                        @php
                                            // dd($lang);
                                            $title = json_decode($input['title'] ?? '', true);
                                        @endphp
                                        <input type="text" class="form-control input-sm {{ $active }}"
                                            style="width:80%;" name="title-{{ $lang[0] }}"
                                            id="title-{{ $lang[0] }}" placeholder="@lang('dev.lang_' . $lang[0])"
                                            value="{{ $name[$lang[0]] ?? '' }}">
                                        @php
                                            $active = 'hide';
                                        @endphp
                                    @endforeach
                                    <span id="title-{{ config('me.app.project_lang')['en'][0] }}-error"
                                        class="error invalid-feedback" style="display: none"></span>
                                </div>
                                <span id="fullname-error" class="error invalid-feedback" style="display: none"></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for=""><b>@lang('table.code')</b><span class="text-danger">*</span></label>
                                <div class="input-group my-group" style="width:100%;">
                                    <div class="input-group-text">
                                        <i class="fa fa-barcode"></i>
                                    </div>
                                    <input type="text" class="form-control" name="code" id="code"
                                        placeholder="@lang('table.enter') @lang('table.code')"
                                        value="{{ $input['code'] ?? '' }}">
                                    <span id="account-{{ config('me.app.project_lang')['en'][0] }}-error"
                                        class="error invalid-feedback" style="display: none"></span>
                                </div>

                                <span id="code-error" class="error invalid-feedback" style="display: none"></span>
                            </div>
                        </div>

                    </div>


                </div>
            </div>
            <!-- /.card-body -->

            {{--  --}}

        </form>
    </div>
@endsection
