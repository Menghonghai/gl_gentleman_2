@php
    $extends = 'app';
    $action_btn = ['save' => true, 'print' => false, 'cancel' => true, 'new' => true];
    foreach (config('me.app.project_lang') as $lang) {
        $langcode[] = $lang[0];
    }
    
    foreach (config('me.app.discount') as $lang) {
        $langcodes[] = $lang[0];
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


    <script>
        $(document).ready(function() {
            $('.summernote').summernote();
            $(document).on("change", ".tab_title", function(ev) {
                ///
                var $value = $(this).val();
                helper.enableDisableByLang($(this), {!! json_encode($langcode, true) !!}, 'title-', $value);
                ///
            });
            $(document).on("change", ".tab_discount", function(ev) {
                ///
                var $value = $(this).val();
                alert($value);
                helper.enableDisableByLang($(this), {!! json_encode($langcodes, true) !!}, 'dicount-', $value);
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
            let route_print = "{{ $route['print'] ?? '' }}";

            let frm, extraFrm;
            let popModal = {
                show: false,
                size: 'modal-lg'
                //modal-sm
                //modal-lg
                //modal-xl
            };
            let container = '';
            let loading_indicator = '';
            let setting = {
                mode: "{{ $extends }}"
            };
            $(".btnsave_{{ $obj_info['name'] }}").click(function(e) {
                // alert(1);
                e.preventDefault();
                $("#frm-{{ $obj_info['name'] }} .error").html('').hide();
                helper.silentHandler(route_submit, "frm-{{ $obj_info['name'] }}", extraFrm, setting,
                    popModal, container,
                    loading_indicator);

            });

            $(".btncancel_{{ $obj_info['name'] }}").click(function(e) {
                //window.location.replace(route_cancel);
                window.location = route_cancel;
            });
            $("#btnnew_{{ $obj_info['name'] }}").click(function(e) {


                window.location = route_new;
                //     loading_indicator);
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
            $("#productaddunit").click(function() {
                //const table = $("table-form-{{ $obj_info['name'] }}");
                add_item();
            });
            $(document).on("click", ".btn_remove_subunit", function(ev) {
                ///
                var $row = $(this).parents('.table_row');
                $row.remove();
                ///
            });

        });

        function add_item() {

            var template = $('#record-unit-template');
            var clone = template
                .clone()
                .removeClass('hide')
                .removeAttr('id')
                .insertBefore("#table-form-{{ $obj_info['name'] }} tr:last");

            var numItems = $("#table-form-{{ $obj_info['name'] }} tr").length;

            let ch = clone.find("#unit_id-n-error");
            let baseunitnum = clone.find("#baseunitnum-n-error");
            ch.attr('id', "unit_id-" + (numItems - 2) + "-error");
            baseunitnum.attr('id', "baseunitnum-" + (numItems - 2) + "-error");

        }
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
                                        <small class="text-sm">
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
                        <div class="col-xl-4 col-lg-6 col-md-5 col-sm-12">
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
                        <div class="col-xl-4 col-lg-6 col-md-5 col-sm-12">
                            <div class="form-group">
                                <label for=""><b>@lang('table.code')</b></label>
                                <input type="number" name="product_code" id="product_code" class="form-control">
                                <span id="product_code-error" class="error invalid-feedback" style="display: none"></span>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-5 col-sm-12">
                            <div class="form-group">
                                <label for=""><b>@lang('dev.category')</b></label>
                                <select class="form-control form-select select2" name="category" id="category">
                                    <option value="0">{{ __('dev.noneselected') }}</option>
                                    {!! cmb_listing($categorie, [$input['category_id'] ?? 0], '', '') !!}
                                </select>
                                <span id="category-error" class="error invalid-feedback" style="display: none"></span>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-5 col-sm-12">
                            <div class="form-group">
                                <label for=""><b>@lang('dev.brand')</b></label>
                                <select class="form-control form-select input-sm sumoselect" name="brand" id="brand">
                                    <option value="0">{{ __('dev.noneselected') }}</option>
                                    {!! cmb_listing($categorie, [$input['category_id'] ?? 0], '', '') !!}
                                </select>
                                <span id="brand-error" class="error invalid-feedback" style="display: none"></span>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-5 col-sm-12">
                            <div class="form-group">
                                <label for=""><b>@lang('dev.property')</b></label>
                                <select class="form-control form-select input-sm sumoselect" name="property" id="property">
                                    <option value="0">{{ __('dev.noneselected') }}</option>
                                    {!! cmb_listing($categorie, [$input['category_id'] ?? 0], '', '') !!}
                                </select>
                                <span id="property-error" class="error invalid-feedback" style="display: none"></span>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-5 col-sm-12">
                            <div class="form-group">
                                <label for=""><b>@lang('dev.discount')</b></label>
                                <div class="input-group my-group" style="width:100%;">


                                    <input class="form-control" type="number" name="product_discount[]" id="discount"
                                        value="" min="0" style="width: 80%;" />
                                    <select class="form-control form-select input-sm" name="discounttype"
                                        id="discounttype" style="width: 20%;">
                                        {!! cmb_listing(config('me.app.discount'), [$discounttype ?? ''], '', '') !!}
                                    </select>
                                    <span id="title-{{ config('me.app.project_lang')['en'][0] }}-error"
                                        class="error invalid-feedback" style="display: none"></span>
                                </div>
                                <span id="fullname-error" class="error invalid-feedback" style="display: none"></span>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-5 col-sm-12">
                            <div class="form-group">
                                <label for=""><b>@lang('dev.cost')</b></label>
                                <input type="number" name="cost" id="cost" class="form-control">
                                <span id="cost-error" class="error invalid-feedback" style="display: none"></span>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-5 col-sm-12">
                            <div class="form-group">
                                <label for=""><b>@lang('dev.price')</b></label>
                                <input type="number" name="price" id="price" class="form-control">
                                <span id="price-error" class="error invalid-feedback" style="display: none"></span>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-5 col-sm-12">
                            <div class="form-group">
                                <label for=""><b>@lang('dev.vip_price')</b></label>
                                <input type="number" name="vip_price" id="vip_price" class="form-control">
                                <span id="vip_price-error" class="error invalid-feedback" style="display: none"></span>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-5 col-sm-12">
                            <div class="form-group">
                                <label for=""><b>@lang('dev.wholesale_price')</b></label>
                                <input type="number" name="wholesale_price" id="wholesale_price" class="form-control">
                                <span id="wholesale_price-error" class="error invalid-feedback"
                                    style="display: none"></span>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-5 col-sm-12">
                            <div class="form-group">
                                <label for=""><b>@lang('dev.alert_stock')</b></label>
                                <input type="number" name="alert_stock" id="alert_stock" class="form-control">
                                <span id="alert_stock-error" class="error invalid-feedback" style="display: none"></span>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-5 col-sm-12">
                            <div class="form-group">
                                <label for=""><b>@lang('dev.order_index')</b></label>
                                <input type="number" name="order_index" id="order_index" class="form-control">
                                <span id="order_index-error" class="error invalid-feedback" style="display: none"></span>
                            </div>
                        </div>


                    </div>


                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card custom-card">
                            <div class="card-footer py-0">
                                <div class="profile-tab tab-menu-heading border-bottom-0">
                                    <nav class="nav main-nav-line p-0 tabs-menu profile-nav-line border-0 br-5 mb-0	">
                                        <a class="nav-link  mb-2 mt-2 active" data-bs-toggle="tab"
                                            href="#image_profile">@lang('dev.extra')</a>
                                        <a class="nav-link mb-2 mt-2" data-bs-toggle="tab"
                                            href="#id_card">@lang('dev.single')</a>
                                        <a class="nav-link  mb-2 mt-2" data-bs-toggle="tab"
                                            href="#image_cv">@lang('dev.content')</a>

                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-sm">
                    <div class="col-lg-12 col-md-12">
                        <div class="custom-card main-content-body-profile">
                            <div class="tab-content">
                                <div class="main-content-body tab-pane  active" id="image_profile">
                                    <div class="card">
                                        <div class="card-body p-0 border-0 p-0 rounded-10">
                                            <div class="tab-pane active" id="tab4">
                                                <div class="row">
                                                    @include('app.products.create_element')
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="main-content-body tab-pane border-top-0" id="id_card">
                                    <div class="card">
                                        <div class="card-body p-0 border-0 p-0 rounded-10">
                                            <div class="p-4">
                                                <h4 class="tx-15 text-uppercase mb-3">@lang('dev.color')</h4>
                                                <div class="col-md-4 col-xl-4 col-lg-4">
                                                    <select class="form-control " name="formuse[]" id="formuse">
                                                        <option value="">-- {{ __('dev.noneselected') }} --</option>
                                                        {!! cmb_listing(config('me.kobo.formuse'), [$input['formuse'] ?? 'routine'], '', '') !!}
                                                    </select>
                                                </div>
                                                <br>
                                                <h4 class="tx-15 text-uppercase mb-3">@lang('dev.image')</h4>
                                                <div class="form-group create_img1">
                                                    <div class="input-group my-group" style="width:60%;">
                                                        <input type="file" class="dropify" data-height="600"
                                                            accept="image/png, image/jpeg,image/PNG, image/JPEG,image/jpg,image/JPG"
                                                            name="single_image" value="" />
                                                        <span
                                                            id="title-{{ config('me.app.project_lang')['en'][0] }}-error"
                                                            class="error invalid-feedback" style="display: none"></span>
                                                    </div>
                                                    <span id="type-error" class="error invalid-feedback"
                                                        style="display: none"></span>
                                                </div>
                                                @if (isset($input))
                                                    <div class="input-group my-group update_img1" style="width:60%;">
                                                        <div class="dropify-wrapper has-preview" style="height: 600px;">
                                                            <div class="dropify-message"><span class="file-icon">
                                                                </span>
                                                                <p class="dropify-error">Ooops, something wrong appended.
                                                                </p>
                                                            </div>
                                                            <div class="dropify-loader" style="display: none;"></div>
                                                            <div class="dropify-errors-container">
                                                                <ul></ul>
                                                            </div><input type="file" class="dropify" data-height="400"
                                                                accept="image/png, image/jpeg,image/PNG, image/JPEG,image/jpg,image/JPG"
                                                                name="" value=""
                                                                data-date="3331-09-10T00:00:00+07:00"><button
                                                                type="button" id="remove1"
                                                                class="dropify-clear remove_img">@lang('table.remove')</button>
                                                            <div class="dropify-preview" style="display: block;"><span
                                                                    class="dropify-render"><img
                                                                        src="{{ asset('storage/app/idCard/' . $input['image_id_card'] ?? '') }}"
                                                                        style="max-height: 600px;"></span>
                                                                <div class="dropify-infos">
                                                                    <div class="dropify-infos-inner">
                                                                        <p class="dropify-filename"><span
                                                                                class="dropify-filename-inner"></span>
                                                                        </p>
                                                                        <p class="dropify-infos-message">@lang('table.drag_and_drop_click_replace')
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <span id="title-en-error" class="error invalid-feedback"
                                                            style="display: none"></span>
                                                    </div>
                                                    <input type="hidden" name="old_image1"
                                                        value="{{ $input['image_id_card'] ?? '' }}">
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="main-content-body  tab-pane border-top-0" id="image_cv">
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
                                                                            data-bs-toggle="tab">@lang('dev.lang_en')</a>
                                                                    </li>
                                                                    <li><a href="#tabkh" class="nav-link"
                                                                            data-bs-toggle="tab">@lang('dev.lang_kh')</a>
                                                                    </li>

                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="panel-body tabs-menu-body main-content-body-right border">
                                                            <div class="tab-content">
                                                                @foreach (config('me.app.project_lang') as $tab_ind => $lang)
                                                                    <div class="tab-pane {{ $tab_ind == 'en' ? 'active' : '' }}"
                                                                        id="tab{{ $lang[0] }}">
                                                                        <textarea class="summernote" id="description-{{ $lang[0] }}" name="description-{{ $lang[0] }}">

                                                                        {!! html_entity_decode($job_description[$lang[0]] ?? '') !!}
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
                                                                            data-bs-toggle="tab">@lang('dev.lang_en')</a>
                                                                    </li>
                                                                    <li><a href="#tabkh" class="nav-link"
                                                                            data-bs-toggle="tab">@lang('dev.lang_kh')</a>
                                                                    </li>

                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="panel-body tabs-menu-body main-content-body-right border">
                                                            <div class="tab-content">
                                                                @foreach (config('me.app.project_lang') as $tab_ind => $lang)
                                                                    <div class="tab-pane {{ $tab_ind == 'en' ? 'active' : '' }}"
                                                                        id="tab{{ $lang[0] }}">
                                                                        <textarea class="summernote" id="description-{{ $lang[0] }}" name="description-{{ $lang[0] }}">

                                                                        {!! html_entity_decode($job_description[$lang[0]] ?? '') !!}
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

                                <div class="main-content-body tab-pane  border-0" id="theme">
                                    <div class="card">
                                        <div class="card-body border-0" data-select2-id="12">

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
