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



    });
</script>
@endsection
@section('content')
    {{-- Header --}}
    <section style="position: sticky;top: 64px; z-index:2" class="content-header bg-light ct-bar-action ct-bar-action-shaddow">
            
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
                                    @include('app._include.btn_index', [
                                        'new' => false,
                                        'trash' => false,
                                        // 'active' => true,
                                    ])

                                    <a href="{{ url_builder('admin.controller', [$obj_info['name'], 'index']) }}"
                                        class="btn btn-outline-info button-icon"><i class="fe fe-arrow-left me-2"></i>@lang('btn.btn_back')</a>
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

        <form style="padding-top: 10px" name="frm-{{ $obj_info['name'] }}" id="frm-{{ $obj_info['name'] }}" method="POST"
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
                        <div class="col-xxl-6">
                            <div class="card">
                                <div class="card-body ">
                                    <div class="row row-sm ">
                                        <div class=" col-xxl-6 col-lg-12 col-md-12">
                                            <div class="row">
                                                
                                                <div class="col-xxl-12">
                                                    <div class="product-carousel  border br-5">
                                                        <div id="Slider" class="carousel slide" data-bs-ride="false">
                                                            <div class="carousel-inner">

                                                                <div class="carousel-item active">
                                                                    @if (empty($input['image_url']))
                                                                    <a href="{{ asset('public/images/no_image.png') }}"
                                                                    data-caption="IMAGE-01" data-id="lion" class="js-img-viewer">
                                                                    <img src="{{ asset('public/images/no_image.png') }}"
                                                                    class="img-fluid mx-auto d-block">
                                                                    </a>
                                                                @else
                                                                    <a href="{{ asset('storage/app/equipment/' .$input['image_url']) }}"
                                                                        data-caption="IMAGE-01" data-id="lion" class="js-img-viewer">
                                                                        <img src="{{ asset('storage/app/equipment/'.$input['image_url']) }}"
                                                                        class="img-fluid mx-auto d-block">
                                                                    </a>
                                                                @endif
                                                                </div>
                                                              
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="details col-xxl-6 col-lg-12 col-md-12 mt-4 mt-xl-0">
                                            <h4 class="product-title mb-1">@lang('table.name'): {{ $name[$dflang[0]] }}</h4><br>

                                           
                                                <div style="width: 650px" class="d-md-flex">
                                                    <div style="width: 50%" class="mg-md-r-20 mg-b-10">
                                                        <div class="main-profile-social-list">
                                                            <div class="media">
                                                                <div
                                                                    class="media-icon bg-primary-transparent text-primary">
                                                                    <i class="fas fa-dumpster"></i>
                                                                </div>
                                                                <div class="media-body"><span>@lang('table.category')</span>
                                                                    {!! cmb_listing($inventory, [$input['title'] ?? ''], '', '') !!}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div style="width: 50%" class="mg-md-r-20 mg-b-10">
                                                        <div class="main-profile-social-list">
                                                            <div class="media">
                                                                <div
                                                                    class="media-icon bg-primary-transparent text-primary">
                                                                    <i class="fa fa-barcode"></i>
                                                                </div>
                                                                <div class="media-body"><span>@lang('table.code')</span>
                                                                    @if (empty( $input['seria_number']))
                                                                    <justify style="color: red">(@lang('table.empty'))</justify>
                                                                    @else
                                                                    {{ $input['seria_number'] ?? '' }}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div style="width: 650px" class="d-md-flex">
                                                    <div style="width: 50%" class="mg-md-r-20 mg-b-10">
                                                        <div class="main-profile-social-list">
                                                            <div class="media">
                                                                <div
                                                                    class="media-icon bg-primary-transparent text-primary">
                                                                    <i class="fa-solid fa-location-dot"></i>
                                                                </div>
                                                                <div class="media-body"><span>@lang('table.location')</span>
                                                                    @if (empty($input['location']))
                                                                    <justify style="color: red">(@lang('table.empty'))</justify>
                                                                    @else
                                                                        {{ $location[$dflang[0]] }}
                                                                    @endif</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div style="width: 50%" class="mg-md-r-20 mg-b-10">
                                                        <div class="main-profile-social-list">
                                                            <div class="media">
                                                                <div
                                                                    class="media-icon bg-primary-transparent text-primary">
                                                                    <i class="fas fa-industry"></i>
                                                                </div>
                                                                <div class="media-body"><span>@lang('dev.vendor')</span>
                                                                    {!! cmb_listing($vendor, [$input['title'] ?? ''], '', '') !!}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div style="width: 650px" class="d-lg-flex">
                                                    <div style="width: 50%" class="mg-md-r-20 mg-b-10">
                                                        <div class="main-profile-social-list">
                                                            <div class="media">
                                                                <div
                                                                    class="media-icon bg-primary-transparent text-primary">
                                                                    <i class="fas fa-calendar-alt"></i>
                                                                </div>
                                                                <div class="media-body"><span>@lang('table.create_date')</span>
                                                                    @php
                                                                    // dd($input);
                                                                    $date_array = [];
                                                                    $timestamp = strtotime($input['create_date'] ?? '');
                                                                    $confirmed_date = date('d-m-Y', $timestamp);
                                                                    $date_array = explode('-', $confirmed_date);
                                                                    // dd($date_array);
                                                                @endphp


                                                                    @if ($dflang[0] == 'en')
                                                                        {!! num_in_khmer($date_array[0]) . '-' . $date_array[1] . '-' . num_in_khmer($date_array[2]) !!}
                                                                    @else
                                                                        {!! num_in_khmer($date_array[0]) .
                                                                            '&nbsp;' .
                                                                            month_in_khmer($date_array[1]) .
                                                                            '&nbsp;' .
                                                                            num_in_khmer($date_array[2]) !!}
                                                                    @endif
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
                    <div class="row row-sm">
                        <div class="col-lg-12 col-md-12">
                            <div class="card productdesc">
                                <div class="card-body">
                                    <div class="panel panel-primary">
                                        <div class=" tab-menu-heading">
                                            <div class="tabs-menu1">
                                                <!-- Tabs -->
                                                <ul class="nav panel-tabs">
                                                    <li><a href="#tab5" class="active"
                                                            data-bs-toggle="tab">@lang('table.description')</a></li>
                                                    <li><a href="#tab6" data-bs-toggle="tab">@lang('table.about')@lang('table.materia')</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="panel-body tabs-menu-body">
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab5">
                                                    <p class="mb-3 tx-13">{!! html_entity_decode($description[$dflang[0]] ?? '') !!}</p>
                                                </div>
                                                <div class="tab-pane" id="tab6">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="width: 40%"> @lang('table.model')</td>
                                                                    <td>
                                                                        @if (empty($input['model']))
                                                                        <justify style="color: red">(@lang('table.empty'))</justify>
                                                                        @else
                                                                        {{ $input['model'] ?? '' }}
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> @lang('table.categorie')</td>
                                                                    <td>{!! cmb_listing($inventory, [$input['title'] ?? ''], '', '') !!}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td> @lang('table.cost')</td>
                                                                    <td>
                                                                        @if (empty($input['cost']))
                                                                        <justify style="color: red">(@lang('table.empty'))</justify>
                                                                        @else
                                                                        {{ $input['cost'] ?? '' }}
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> @lang('table.seria_number')</td>
                                                                    <td>
                                                                        @if (empty($input['seria_number']))
                                                                        <justify style="color: red">(@lang('table.empty'))</justify>
                                                                        @else
                                                                        {{ $input['seria_number'] ?? '' }}
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> @lang('dev.vendor')</td>
                                                                    <td>{!! cmb_listing($vendor, [$input['title'] ?? ''], '', '') !!}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td> @lang('table.warranty_date')</td>
                                                                    <td>
                                                                        @if (empty($input['warranty_date']))
                                                                        <justify style="color: red">(@lang('table.empty'))</justify>
                                                                        @else
                                                                        @php
                                                                        // dd($input);
                                                                        $date_array = [];
                                                                        $timestamp = strtotime($input['warranty_date'] ?? '');
                                                                        $confirmed_date = date('d-m-Y', $timestamp);
                                                                        $date_array = explode('-', $confirmed_date);
                                                                        // dd($date_array);
                                                                        @endphp
        
        
                                                                            @if ($dflang[0] == 'en')
                                                                                {!! num_in_khmer($date_array[0]) . '-' . $date_array[1] . '-' . num_in_khmer($date_array[2]) !!}
                                                                            @else
                                                                                {!! num_in_khmer($date_array[0]) .
                                                                                    '&nbsp;' .
                                                                                    month_in_khmer($date_array[1]) .
                                                                                    '&nbsp;' .
                                                                                    num_in_khmer($date_array[2]) !!}
                                                                            @endif
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> @lang('table.create_by')</td>
                                                                    <td>{!! cmb_listing($user, [$input['name'] ?? ''], '', '') !!}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td> @lang('table.last_update_date')</td>
                                                                    <td>
                                                                        @if (empty($input['update_date']))
                                                                        <justify style="color: red">(@lang('table.empty'))</justify>
                                                                        @else
                                                                        @php
                                                                        // dd($input);
                                                                        $date_array = [];
                                                                        $timestamp = strtotime($input['update_date'] ?? '');
                                                                        $confirmed_date = date('d-m-Y', $timestamp);
                                                                        $date_array = explode('-', $confirmed_date);
                                                                        // dd($date_array);
                                                                        @endphp
        
        
                                                                            @if ($dflang[0] == 'en')
                                                                                {!! num_in_khmer($date_array[0]) . '-' . $date_array[1] . '-' . num_in_khmer($date_array[2]) !!}
                                                                            @else
                                                                                {!! num_in_khmer($date_array[0]) .
                                                                                    '&nbsp;' .
                                                                                    month_in_khmer($date_array[1]) .
                                                                                    '&nbsp;' .
                                                                                    num_in_khmer($date_array[2]) !!}
                                                                            @endif
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
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
