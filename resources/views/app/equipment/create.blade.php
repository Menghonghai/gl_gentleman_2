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
<!-- include summernote css/js -->
<link href="{{ asset('public/assets/editor/summernote.min.css') }}" rel="stylesheet">
@endsection
@section('blade_scripts')
<!-- include summernote css/js -->
<script src="{{ asset('public/assets/editor/summernote.min.js') }}"></script>
<!--Internal  jquery.maskedinput js -->
<script src="{{ asset('public/assets/plugins/jquery.maskedinput/jquery.maskedinput.js') }}"></script>
    <script>
        $(document).ready(function() {

            $('.summernote').summernote();

            $(document).on("change", ".tab_title", function(ev) {
                var $value = $(this).val();
                helper.enableDisableByLang($(this), {!! json_encode($langcode, true) !!}, 'title-', $value);

                ///
            });

            let hide = "{{ $isupdate ?? '' }}"
            if (hide) {
                $('.create_img').hide();
                $('.create_img1').hide();
                $('.create_img2').hide();

            } else {
                $('.update_img').hide();
                $('.update_img1').hide();
                $('.update_img2').hide();
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
                // console.log(data);

                if (data.data.length == 0) {
                    $('#' + frm)[0].reset();
                    $('textarea').text('');
                    $('.dropify-preview').css({
                        "display": "none"
                    });
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
            $('#remove1').on('click', function(e) {

                $('.update_img1').hide();
                $('.create_img1').show();
            });
            $('#remove2').on('click', function(e) {
                $('.update_img2').hide();
                $('.create_img2').show();
            });
            $('#datepicker-date-single').bootstrapdatepicker({
                    format: "dd-mm-yyyy",
                    viewMode: "date",
                    multidate: false,
                    multidateSeparator: "|",
                })


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
                        <div class="col-4">
                                <div class="col-12">
                                    <div class="form-group create_img">
                                        <div class="input-group my-group" style="width:100%;">
                                            <input type="file" class="dropify" data-height="350"
                                                accept="image/png, image/jpeg,image/PNG, image/JPEG,image/jpg,image/JPG"
                                                name="images" value="" />
                                            <span id="image-{{ config('me.app.project_lang')['en'][0] }}-error"
                                                class="error invalid-feedback" style="display: none"></span>
                                        </div>
                                        <h4 style="padding: 7px;text-align: center" class="tx-15 mb-3"><i class="fas fa-image"></i> @lang('table.image_logo')</h4>
                                        <span id="type-error" class="error invalid-feedback"
                                            style="display: none"></span>
                                    </div>
                                    @if (isset($input))
                                        <div class="input-group my-group update_img" style="width:100%;">
                                            <div class="dropify-wrapper has-preview" style="height: 350px;">
                                                <div class="dropify-message"><span class="file-icon">
                                                    </span>
                                                    <p class="dropify-error">Ooops, something wrong appended.</p>
                                                </div>
                                                <div class="dropify-loader" style="display: none;"></div>
                                                <div class="dropify-errors-container">
                                                    <ul></ul>
                                                </div><input type="file" class="dropify" data-height="350"
                                                    accept="image/png, image/jpeg,image/PNG, image/JPEG,image/jpg,image/JPG"
                                                    name="" value=""
                                                    data-date="3331-09-10T00:00:00+07:00"><button type="button"
                                                    id="remove"
                                                    class="dropify-clear remove_img">@lang('table.remove')</button>
                                                <div class="dropify-preview" style="display: block;"><span
                                                        class="dropify-render"><img
                                                            src="{{ asset('storage/app/equipment/' . $input['image_url'] ?? '') }}"
                                                            style="max-height: 350px;"></span>
                                                    <div class="dropify-infos">
                                                        <div class="dropify-infos-inner">
                                                            <p class="dropify-filename"><span
                                                                    class="dropify-filename-inner"></span>
                                                            </p>
                                                            <p class="dropify-infos-message">@lang('table.drag_and_drop_click_replace')</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <span id="image-error" class="error invalid-feedback"
                                                style="display: none"></span>
                                        </div>
                                        <input type="hidden" name="old_image"
                                            value="{{ $input['image_url'] ?? '' }}">
                                    @endif
                                </div>
                        </div>
                        <div class="col-8">
                            <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for=""><b>@lang('dev.name_kh_eng')</b><span class="text-danger">*</span></label>
                                            <div class="input-group my-group" style="width:100%;">
            
                                                <select class="form-control form-select input-sm tab_title" style="width:25%;">
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
                                                        style="width:75%;" name="title-{{ $lang[0] }}"
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
                                            <label for=""><b>@lang('table.code')</b></label>
                                            <div class="input-group my-group" style="width:100%;">
                                                <div class="input-group-text">
                                                    <i class="fa fa-barcode"></i>
                                                </div>
                                                <input type="text" class="form-control" name="seria_number" id="seria_number"
                                                    placeholder="@lang('table.enter') @lang('table.code')"
                                                    value="{{ $input['seria_number'] ?? '' }}">
                                                <span id="title-{{ config('me.app.project_lang')['en'][0] }}-error"
                                                    class="error invalid-feedback" style="display: none"></span>
                                            </div>
            
                                            <span id="fullname-error" class="error invalid-feedback" style="display: none"></span>
                                        </div>
                                    </div>
                            </div>
                            <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for=""><b>@lang('table.location')</b></label>
                                            <div class="input-group my-group" style="width:100%;">
            
                                                <select class="form-control form-select input-sm tab_title" style="width:25%;">
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
                                                        style="width:75%;" name="location-{{ $lang[0] }}"
                                                        id="title-{{ $lang[0] }}" placeholder="@lang('dev.lang_' . $lang[0])"
                                                        value="{{ $location[$lang[0]] ?? '' }}">
                                                    @php
                                                        $active = 'hide';
                                                    @endphp
                                                @endforeach
                                                <span id="location-{{ config('me.app.project_lang')['en'][0] }}-error"
                                                    class="error invalid-feedback" style="display: none"></span>
                                            </div>
                                            <span id="location-error" class="error invalid-feedback" style="display: none"></span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for=""><b>@lang('table.model')</b></label>
                                            <div class="input-group my-group" style="width:100%;">
                                                <div class="input-group-text">
                                                    <i class="fa fa-star"></i>
                                                </div>
                                                <input type="text" class="form-control" name="model" id="model"
                                                placeholder="@lang('table.enter') @lang('table.model')"
                                                value="{{ $input['model'] ?? '' }}">
                                                <span id="title-{{ config('me.app.project_lang')['en'][0] }}-error"
                                                    class="error invalid-feedback" style="display: none"></span>
                                            </div>
            
                                            <span id="fullname-error" class="error invalid-feedback" style="display: none"></span>
                                        </div>
                                    </div>
                            </div>
                            <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for=""><b>@lang('table.cost')</b></label>
                                            <div class="input-group my-group" style="width:100%;">
                                                <div class="input-group-text">
                                                    <i class="fa fa-dollar"></i>
                                                </div>
                                                <input type="number" class="form-control" name="cost" id="cost"
                                                    step="0.01" min="0" placeholder="@lang('table.enter') @lang('table.cost')"
                                                    value="{{ $input['cost'] ?? '' }}">
                                                <span class="input-group-text">.00 @lang('table.dolla')</span>
                                                <span id="title-{{ config('me.app.project_lang')['en'][0] }}-error"
                                                    class="error invalid-feedback" style="display: none"></span>
                                            </div>
            
                                            <span id="fullname-error" class="error invalid-feedback" style="display: none"></span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for=""><b>@lang('table.warranty_date')</b></label>
                                            <div class="input-group my-group" style="width:100%;">
                                                <div class="input-group-text">
                                                    <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                                                </div>
                                                <input class="form-control" id="datepicker-date-single" placeholder="@lang('table.date_placeholder')" type="text"
                                                name="warranty_date" value="{{ $input['warranty_date'] ?? '' }}">
                                                <span id="title-{{ config('me.app.project_lang')['en'][0] }}-error"
                                                    class="error invalid-feedback" style="display: none"></span>
                                            </div>
            
                                            <span id="fullname-error" class="error invalid-feedback" style="display: none"></span>
                                        </div>
                                    </div>
                            </div>
                            <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for=""><b>@lang('dev.inventory')</b></label>
                                            <div class="input-group my-group" style="width:100%;">
                                                <div class="input-group-text">
                                                    <i class="fas fa-dumpster"></i>
                                                </div>
                                                <select class="form-control input-sm" name="inventory_id" id="inventory_id">
                                                    <option value="">-- {{ __('dev.non_select') }}--</option>
                                                    {!! cmb_listing($inventory, [$input['inventory_id'] ?? ''], '', '') !!}
            
                                                </select>
                                                <span id="title-{{ config('me.app.project_lang')['en'][0] }}-error"
                                                    class="error invalid-feedback" style="display: none"></span>
                                            </div>
            
                                            <span id="fullname-error" class="error invalid-feedback" style="display: none"></span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for=""><b>@lang('dev.vendor')</b></label>
                                            <div class="input-group my-group" style="width:100%;">
                                                <div class="input-group-text">
                                                    <i class="fas fa-industry"></i>
                                                </div>
                                                <select class="form-control input-sm" name="vendor_id" id="vendor_id">
                                                    <option value="">-- {{ __('dev.non_select') }}--</option>
                                                    {!! cmb_listing($vendor, [$input['vendor_id'] ?? ''], '', '') !!}
            
            
                                                </select>
                                                <span id="title-{{ config('me.app.project_lang')['en'][0] }}-error"
                                                    class="error invalid-feedback" style="display: none"></span>
                                            </div>
            
                                            <span id="fullname-error" class="error invalid-feedback" style="display: none"></span>
                                        </div>
                                    </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for=""><i class="fas fa-info-circle"></i> <b>@lang('table.description')</b></label>
                                    <div class="main-content-body  tab-pane border-top-0" id="description">
                                        <div class="card">
                                            <div class="card-body p-0 border-0 p-0 rounded-10">

                                                <div class="text-wrap">
                                                    <div class="example">
                                                        <div class="panel panel-primary tabs-style-2">
                                                            <div class=" tab-menu-heading">
                                                                <div class="tabs-menu1">
                                                                    <!-- Tabs -->
                                                                    <ul class="nav panel-tabs main-nav-line">
                                                                        <li><a href="#taben" class="nav-link active"
                                                                                data-bs-toggle="tab">@lang('dev.lang_en')</a></li>
                                                                        <li><a href="#tabkh" class="nav-link"
                                                                                data-bs-toggle="tab">@lang('dev.lang_kh')</a></li>

                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="panel-body tabs-menu-body main-content-body-right border">
                                                                <div class="tab-content">
                                                                    @foreach (config('me.app.project_lang') as $tab_ind => $lang)
                                                                        <div class="tab-pane {{ $tab_ind == 'en' ? 'active' : '' }}"
                                                                            id="tab{{ $lang[0] }}">
                                                                            <textarea class="summernote" id="description-{{ $lang[0] }}" name="description-{{ $lang[0] }}">

                                                                                {!! html_entity_decode($description[$lang[0]] ?? '') !!}
                                                                            </textarea>
                                                                        </div>
                                                                    @endforeach


                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
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
