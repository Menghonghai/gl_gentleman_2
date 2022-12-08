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
                $('.dropify-preview').css({
                    "display": "none"
                });
                $('#' + frm)[0].reset();
                $('textarea').text('');
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
            $('#datepicker-date').bootstrapdatepicker({
                    format: "dd-mm-yyyy",
                    viewMode: "date",
                    multidate: true,
                    multidateSeparator: "|",
                })
        });
    </script>
@endsection
@section('content')
    <section class="ticky-section content-header bg-light ct-bar-action ct-bar-action-shaddow">

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

        <form name="frm-{{ $obj_info['name'] }}" id="frm-{{ $obj_info['name'] }}" method="POST"
            action="{{ $route['submit'] }}">
            {{-- please dont delete these default Field --}}
            @CSRF
            <input type="hidden" name="{{ $fprimarykey }}" id="{{ $fprimarykey }}"
                value="{{ $input[$fprimarykey] ?? '' }}">
            <input type="hidden" name="jscallback" value="{{ $jscallback ?? (request()->get('jscallback') ?? '') }}">
            <!-- container -->
            <div class="main-container container-fluid">

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card custom-card">
                            <div class="card-body d-md-flex">
                                <div style="width: 160px" class="">
                                    <span class=" pos-relative">

                                        @if (empty($input['image_url']))
                                            <a href="{{ asset('public/images/no_image.png') }}" data-caption="IMAGE-01"
                                                data-id="lion" class="js-img-viewer">
                                                <img src="{{ asset('public/images/no_image.png') }}" width="200px"
                                                    height="160px">
                                            </a>
                                        @else
                                            <a href="{{ asset('storage/app/staff/' . $input['image_url']) }}"
                                                data-caption="IMAGE-01" data-id="lion" class="js-img-viewer">
                                                <img src="{{ asset('storage/app/staff/' . $input['image_url']) }}"
                                                    width="200px" height="160px">
                                            </a>
                                        @endif
                                        {{-- <img class="br-5" alt="" src="{{ asset('storage/app/employee/' . $input['image_url'] ) }}"> --}}
                                        <span class="bg-success text-white wd-1 ht-1 rounded-pill profile-online"></span>
                                    </span>
                                </div>
                                <div class="my-md-auto mt-4 prof-details">

                                    <h4 class="font-weight-semibold ms-md-4 ms-0 mb-1 pb-0">
                                       @lang('table.name'): {{ $name[$dflang[0]] }}
                                    </h4>

                                    <p class="tx-13 text-muted ms-md-4 ms-0 mb-2 pb-2 ">
                                        {{-- <span class="me-3"><i class="fa fa-taxi me-2"></i>Date of Birth: {{$input['dob']}}</span> --}}
                                    </p>

                                    <p class="text-muted ms-md-4 ms-0 mb-2"><span><i
                                                class="fa fa-phone me-2"></i></span><span
                                            class="font-weight-semibold me-2">@lang('dev.phone'):</span>
                                        <span>

                                            @if (empty($input['phone_number']))
                                                <justify style="color: red">(@lang('table.empty'))</justify>
                                            @else
                                                {!! num_in_khmer($input['phone_number']) !!}
                                            @endif
                                        </span>
                                    </p>
                                    <p class="text-muted ms-md-4 ms-0 mb-2"><span><i
                                                class="fa-solid fa-dollar-sign mg-r-10"></i></span><span
                                            class="font-weight-semibold me-2">@lang('dev.salary'):</span>
                                        <span>
                                            @if (empty($input['salary']))
                                                <justify style="color: red">(@lang('table.empty'))</justify>
                                            @else
                                                {!! num_in_khmer($input['salary']) !!} @lang('table.dolla')
                                            @endif
                                        </span>
                                    </p>
                                    <p class="text-muted ms-md-4 ms-0 mb-2">
                                        <span><i class="fa-solid fa-calendar-days"></i></i></span>
                                        <span class="font-weight-semibold me-2">@lang('dev.dob'):</span><span>
                                            @if (empty($input['dob']))
                                                <justify style="color: red">(@lang('table.not_set'))</justify>
                                            @else
                                                    @php
                                                        // dd($input);
                                                        $date_array = [];
                                                        $timestamp = strtotime($input['dob']);
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
                                        </span>
                                    </p>
                                    <p class="text-muted ms-md-4 ms-0 mb-2">
                                        <span><i class="fa-solid fa-calendar"></i></span>
                                        <span class="font-weight-semibold me-2">@lang('dev.hire_date'):</span><span>
                                            @if (empty($input['hire_date']))
                                                <justify style="color: red">(@lang('table.not_set'))</justify>
                                            @else
                                                    @php
                                                        // dd($input);
                                                        $date_array = [];
                                                        $timestamp = strtotime($input['dob']);
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
                                        </span>
                                    </p>
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
                                                <li><a href="#job_description" class="active"
                                                        data-bs-toggle="tab"><i class="fas fa-info-circle"></i> @lang('dev.job_description')</a></li>
                                                <li><a href="#about_staff" data-bs-toggle="tab"><i class="fa fa-address-book"></i> @lang('dev.about_staff')</a></li>
                                                <li><a href="#id_card" data-bs-toggle="tab"><i class="fas fa-id-card"></i> @lang('dev.id_card')</a>
                                                <li><a href="#image_cv" data-bs-toggle="tab"><i class="fas fa-file-alt"></i> @lang('dev.image_cv')</a>
                                                <li><a href="#address" data-bs-toggle="tab"><i class="fas fa-map-marker-alt"></i> @lang('dev.address')</a>
                                                <li><a href="#pob" data-bs-toggle="tab"><i class="fas fa-map-marker-alt"></i> @lang('dev.pob')</a>
                                                <li><a href="#salary_list" data-bs-toggle="tab"><i class="fas fa-file-invoice-dollar"></i> @lang('dev.salary_list')</a>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="panel-body tabs-menu-body">
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="job_description">
                                                <p class="mb-3 tx-13"> {!! html_entity_decode($job_description[$dflang[0]] ?? '') !!}</p>
                                            </div>
                                            <div class="tab-pane" id="about_staff">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <tbody>
                                                            <tr>
                                                                <td style="width: 25%"> @lang('dev.department')</td>
                                                                <td>
                                                                    @if (empty($input['department_id']))
                                                                        <justify style="color: red">(@lang('table.empty'))</justify>
                                                                    @else
                                                                        {!! cmb_listing($department, [$input['title'] ?? ''], '', '') !!}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td> @lang('table.position')</td>
                                                                <td>
                                                                    @if (empty($input['position']))
                                                                        <justify style="color: red">(@lang('table.empty'))</justify>
                                                                    @else
                                                                        {{ $position[$dflang[0]] }}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td> @lang('table.phone_number')</td>
                                                                <td>
                                                                    @if (empty($input['phone_number']))
                                                                        <justify style="color: red">(@lang('table.empty'))</justify>
                                                                    @else
                                                                    {!! num_in_khmer($input['phone_number']) !!}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td> @lang('table.email')</td>
                                                                <td>
                                                                    @if (empty($input['email']))
                                                                    <justify style="color: red">(@lang('table.empty'))</justify>
                                                                @else
                                                                    {{ $input['email'] }}
                                                                @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td> @lang('table.hire_date')</td>
                                                                <td>
                                                                    @if (empty($input['hire_date']))
                                                                        <justify style="color: red">(@lang('table.not_set'))</justify>
                                                                    @else
                                                                                @php
                                                                                // dd($input);
                                                                                $date_array = [];
                                                                                $timestamp = strtotime($input['hire_date']);
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
                                                                <td> @lang('table.create_date')</td>
                                                                <td>
                                                                    @php
                                                                                // dd($input);
                                                                                $date_array = [];
                                                                                $timestamp = strtotime($input['create_date']);
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
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td> @lang('table.last_update_date')</td>
                                                                <td>
                                                                    @if (empty($input['update_date']))
                                                                        <justify style="color: red">(@lang('table.not_set'))</justify>
                                                                    @else
                                                                                @php
                                                                                // dd($input);
                                                                                $date_array = [];
                                                                                $timestamp = strtotime($input['update_date']);
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
                                                                <td>
                                                                    {!! cmb_listing($user, [$input['name'] ?? ''], '', '') !!}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="id_card">
                                                <div style="margin: 0 auto" class="col-7">
                                                    <div class="card mg-b-20 border">
                                                        
                                                        <div class="card-body">
                                                            <div>
                                                                @if (empty($input['image_id_card']))
                                                                    <a href="{{ asset('public/images/no_image.png') }}"
                                                                        data-caption="IMAGE-01" data-id="lion"
                                                                        class="js-img-viewer">
                                                                        <img src="{{ asset('public/images/no_image.png') }}"
                                                                            width="150px" height="700px">
                                                                    </a>
                                                                @else
                                                                    <a href="{{ asset('storage/app/cv/' . $input['image_id_card']) }}"
                                                                        data-caption="IMAGE-01" data-id="lion"
                                                                        class="js-img-viewer">
                                                                        <img src="{{ asset('storage/app/cv/' . $input['image_id_card']) }}"
                                                                            width="100px" height="900px">
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
            
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="image_cv">
                                                <div style="margin: 0 auto" class="col-7">
                                                    <div class="card mg-b-20 border">
                                                        
                                                        <div class="card-body">
                                                            <div>
                                                                @if (empty($input['cv_document']))
                                                                    <a href="{{ asset('public/images/no_image.png') }}"
                                                                        data-caption="IMAGE-01" data-id="lion"
                                                                        class="js-img-viewer">
                                                                        <img src="{{ asset('public/images/no_image.png') }}"
                                                                            width="150px" height="700px">
                                                                    </a>
                                                                @else
                                                                    <a href="{{ asset('storage/app/cv/' . $input['cv_document']) }}"
                                                                        data-caption="IMAGE-01" data-id="lion"
                                                                        class="js-img-viewer">
                                                                        <img src="{{ asset('storage/app/cv/' . $input['cv_document']) }}"
                                                                            width="100px" height="900px">
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
            
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="address">
                                                <p class="mb-3 tx-13"> {!! html_entity_decode($address[$dflang[0]] ?? '') !!}</p>
                                            </div>
                                            <div class="tab-pane" id="pob">
                                                <p class="mb-3 tx-13"> {!! html_entity_decode($pob[$dflang[0]] ?? '') !!}</p>
                                            </div>
                                            <div class="tab-pane" id="salary_list">
                                                                <form style="padding-top: 10px" class="frmsearch-{{ $obj_info['name'] }}">
                                                                    <div class="form-row justify-content-end" style="font-size: 11px;">
                                                                        <div class="form-group col-md-4">
                                                                            <label for="year">@lang('dev.date_to')</label>
                                                                            <div class="input-group">
                                                                                <div class="input-group">
                                                                                    <div class="input-group-text">
                                                                                        <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                                                                                    </div>
                                                                                    <input class="form-control" id="datepicker-date" placeholder="@lang('table.date_placeholder')" type="text"
                                                                                        name="create_date" value="{{ request()->get('create_date') ?? '' }}" autocomplete="off">
                                                                                </div>
                                                                            </div><!-- input-group -->
                                                                        </div>
                                                                        <div class="form-group col-md-1">
                                                                            <label>&nbsp;</label>
                                                                            <button type="submit" value="filter" class="btn btn-outline-primary btn-block formactionbutton"><i
                                                                                    class="fa fa-search"></i></button>
                                                                        </div>
                                                                        <div class="form-group col-md-1">
                                                                            <label>&nbsp;</label>
                                                                            <button type="button" class="btn btn-outline-primary btn-block formactionbutton "
                                                                                onclick="location.href='{{ url()->current() }}'"><i class="fa fa-refresh" aria-hidden="true"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                <form name="frm-2{{ $obj_info['name'] }}" id="frm-2{{ $obj_info['name'] }}" method="POST"
                                                                action="{{ $route['submit'] }}" enctype="multipart/form-data">
                                                                {{-- please dont delete these default Field --}}
                                                                @CSRF
                                                                <input type="hidden" name="{{ $fprimarykey }}" id="{{ $fprimarykey }}"
                                                                    value="{{ $input[$fprimarykey] ?? '' }}">
                                                                <input type="hidden" name="jscallback" value="{{ $jscallback ?? (request()->get('jscallback') ?? '') }}">

                                                                <div class="card-body table-responsive p-0 mg-t-20">
                                                                    <table class="table  table-striped table-hover text-nowrap table-bordered">
                                                                        @if (isset($istrash) && $istrash)
                                                                            <thead style="color: var(--warning)">
                                                                            @else
                                                                                <thead style="color: var(--info)">
                                                                        @endif
                                                                        <tr style="text-align: center">
                                                                            <th style="width: 10%">@lang('table.id')</th>
                                                                            <th>@lang('table.create_date')</th>
                                                                            <th>@lang('table.salary')</th>

                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            
                                                                                <tr>
                                                                                    <td>
                                                                                        {!! num_in_khmer("1") !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        11-10-2020
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! num_in_khmer("220") !!} @lang('table.dolla')
                                                                                    </td>
                                                                                </tr>
                                                                            
                                                                        </tbody>
                                                                    </table>
                                                                    

                                                                </div>
                                                            </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- row closed -->
    </div>
    <!-- Container closed -->

    </form>
    </div>
@endsection