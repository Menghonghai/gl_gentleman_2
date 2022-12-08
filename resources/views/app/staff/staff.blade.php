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
                                        <small class="text-sm">
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
                                        class="btn btn-outline-info button-icon">@lang('btn.btn_back')</a>
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
                                <div class="">
                                    <span class="profile-image pos-relative">

                                        @if (empty($input['image_url']))
                                            <a href="{{ asset('public/images/no_image.png') }}" data-caption="IMAGE-01"
                                                data-id="lion" class="js-img-viewer">
                                                <img src="{{ asset('public/images/no_image.png') }}" width="150px"
                                                    height="100px">
                                            </a>
                                        @else
                                            <a href="{{ asset('storage/app/staff/' . $input['image_url']) }}"
                                                data-caption="IMAGE-01" data-id="lion" class="js-img-viewer">
                                                <img src="{{ asset('storage/app/staff/' . $input['image_url']) }}"
                                                    width="150px" height="100px">
                                            </a>
                                        @endif
                                        {{-- <img class="br-5" alt="" src="{{ asset('storage/app/employee/' . $input['image_url'] ) }}"> --}}
                                        <span class="bg-success text-white wd-1 ht-1 rounded-pill profile-online"></span>
                                    </span>
                                </div>
                                <div class="my-md-auto mt-4 prof-details">

                                    <h4 class="font-weight-semibold ms-md-4 ms-0 mb-1 pb-0">



                                        {{ $name[$dflang[0]] }}

                                    </h4>

                                    <p class="tx-13 text-muted ms-md-4 ms-0 mb-2 pb-2 ">
                                        {{-- <span class="me-3"><i class="fa fa-taxi me-2"></i>Date of Birth: {{$input['dob']}}</span> --}}
                                    </p>

                                    <p class="text-muted ms-md-4 ms-0 mb-2"><span><i
                                                class="fa fa-phone me-2"></i></span><span
                                            class="font-weight-semibold me-2">@lang('dev.phone'):</span>
                                        <span>

                                            @if (empty($input['phone_number']))
                                                (@lang('table.empty'))
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
                                                (@lang('table.empty'))
                                            @else
                                                {!! num_in_khmer($input['salary']) !!}
                                            @endif
                                        </span>
                                    </p>

                                    <p class="text-muted ms-md-4 ms-0 mb-2">
                                        <span><i class="fa-solid fa-calendar-days"></i></i></span>
                                        <span class="font-weight-semibold me-2">@lang('dev.dob'):</span><span>
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
                                        </span>
                                    </p>
                                    <p class="text-muted ms-md-4 ms-0 mb-2">
                                        <span><i class="fa-solid fa-calendar"></i></span>
                                        <span class="font-weight-semibold me-2">@lang('dev.hire_date'):</span><span>
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
                                        </span>
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card-footer py-0">
                    <div class="profile-tab tab-menu-heading border-bottom-0">
                        <nav class="nav main-nav-line p-0 tabs-menu profile-nav-line border-0 br-5 mb-0	">
                            <a class="nav-link  mb-2 mt-2 active" data-bs-toggle="tab" href="#about">@lang('dev.about_staff')</a>
                            <a class="nav-link  mb-2 mt-2" data-bs-toggle="tab" href="#id_card">@lang('dev.id_card')</a>
                            <a class="nav-link mb-2 mt-2" data-bs-toggle="tab" href="#image_cv">@lang('dev.image_cv')</a>
                            <a class="nav-link mb-2 mt-2" data-bs-toggle="tab" href="#description">@lang('dev.job_description')</a>

                            {{-- <a class="nav-link  mb-2 mt-2" data-bs-toggle="tab" href="#friends">Friends</a> --}}
                            {{-- <a class="nav-link  mb-2 mt-2" data-bs-toggle="tab" href="#settings">Account
								Settings</a> --}}
                        </nav>
                    </div>
                </div>
                <!-- Row -->
                <div class="row row-sm">
                    <div class="col-lg-12 col-md-12">
                        <div class="custom-card main-content-body-profile">
                            <div class="tab-content">

                                <div class="main-content-body tab-pane  active" id="about">
                                    <div class="card">
                                        <div class="card-body p-0 border-0 p-0 rounded-10">

                                            <div class="border-top"></div>
                                            <div class="p-4">
                                                <label class="main-content-label tx-13 mg-b-20">@lang('dev.contact')</label>
                                                <div class="d-sm-flex">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary">
                                                                    <i class="icon ion-md-phone-portrait"></i>
                                                                </div>
                                                                <div class="media-body"> <span>@lang('dev.phone')</span>
                                                                    <div> {{ $input['phone_number'] }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div
                                                                    class="media-icon bg-success-transparent text-success">
                                                                    <i class="fa-solid fa-envelope"></i>
                                                                </div>
                                                                <div class="media-body"> <span>
                                                                        @lang('table.email')

                                                                    </span>
                                                                    <div> {{ $input['email'] }} </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-info-transparent text-info">
                                                                    <i class="icon ion-md-locate"></i>
                                                                </div>
                                                                <div class="media-body"> <span>@lang('dev.address')</span>
                                                                    <div>

                                                                        {{ $address[$dflang[0]] }}

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="border-top"></div>
                                            <div class="p-4">
                                                <div class="d-lg-flex">
                                                    <div class="mg-md-r-20 mg-b-10">
                                                        <div class="main-profile-social-list">
                                                            <div class="media">
                                                                <div
                                                                    class="media-icon bg-primary-transparent text-primary">
                                                                    <i class="fa-solid fa-building"></i>
                                                                </div>
                                                                <div class="media-body"> <span>@lang('dev.department')</span>
                                                                    {!! cmb_listing($department, [$input['title'] ?? ''], '', '') !!} </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mg-md-r-20 mg-b-10">
                                                        <div class="main-profile-social-list">
                                                            <div class="media">
                                                                <div
                                                                    class="media-icon bg-success-transparent text-success">
                                                                    <i class="fa-solid fa-person-half-dress"></i>
                                                                </div>
                                                                <div class="media-body"> <span>@lang('dev.gender')</span>

                                                                    @if (empty($input['gender']))
                                                                        (@lang('table.empty'))
                                                                    @elseif ($input['gender'] == 'male')
                                                                        @lang('table.male')
                                                                    @elseif ($input['gender'] == 'female')
                                                                        @lang('table.female')
                                                                    @endif

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mg-md-r-20 mg-b-10">
                                                        <div class="main-profile-social-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-info-transparent text-info">
                                                                    <i class="fa-sharp fa-solid fa-location-dot"></i>
                                                                </div>
                                                                <div class="media-body"> <span>@lang('dev.pob')</span>

                                                                    @if ($dflang[0] == 'en')
                                                                        {{ $pob[$dflang[0]] }}
                                                                    @else
                                                                        {{ $pob[$dflang[0]] }}
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
                                <div class="main-content-body  tab-pane border-top-0" id="id_card">
                                    <div class="border-0">
                                        <div class="main-content-body main-content-body-profile">
                                            <div class="main-profile-body p-0">
                                                <div class="row row-sm">
                                                    <div class="col-10">
                                                        <div class="card mg-b-20 border">
                                                            <div class="card-header p-4">

                                                            </div>
                                                            <div class="card-body">


                                                                <div style="height: 80%">
                                                                    @if (empty($input['image_id_card']))
                                                                        <a href="{{ asset('public/images/no_image.png') }}"
                                                                            data-caption="IMAGE-01" data-id="lion"
                                                                            class="js-img-viewer">
                                                                            <img src="{{ asset('public/images/no_image.png') }}"
                                                                                width="150px" height="100px">
                                                                        </a>
                                                                    @else
                                                                        <a href="{{ asset('storage/app/idCard/' . $input['image_id_card']) }}"
                                                                            data-caption="IMAGE-01" data-id="lion"
                                                                            class="js-img-viewer">
                                                                            <img src="{{ asset('storage/app/idCard/' . $input['image_id_card']) }}"
                                                                                height="600px">
                                                                        </a>
                                                                    @endif



                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <!-- main-profile-body -->
                                        </div>
                                    </div>
                                </div>
                                <div class="main-content-body  tab-pane border-top-0" id="description">
                                    <div class="border-0">
                                        <div class="main-content-body main-content-body-profile">
                                            <div class="main-profile-body p-0">
                                                <div class="row row-sm">
                                                    <div class="col-12">
                                                        <div class="card mg-b-20 border">

                                                            <div class="card-body">

                                                                <div class="p-4">

                                                                    <h4 class="tx-15 text-uppercase mb-3">
                                                                        @lang('dev.job_decription')</h4>
                                                                    {{-- <p class="m-b-5">{{ $job_description[$lang[0]] ?? '' }}</p> --}}
                                                                    <div class="m-t-30">
                                                                        <div class=" p-t-10">
                                                                            {{-- <h5 class="text-primary m-b-5 tx-14">{{ $department_id[$lang[0]] ?? '' }} --}}
                                                                            </h5>

                                                                            <p class="text-muted tx-13 m-b-0">

                                                                                {!! html_entity_decode($job_description[$dflang[0]] ?? '') !!}


                                                                            </p>
                                                                        </div>


                                                                    </div>
                                                                </div>
                                                                <div class="border-top"></div>






                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <!-- main-profile-body -->
                                    </div>
                                </div>
                                <div class="main-content-body  tab-pane border-top-0" id="image_cv">
                                    <div class="border-0">
                                        <div class="main-content-body main-content-body-profile">
                                            <div class="main-profile-body p-0">
                                                <div class="row row-sm">
                                                    <div class="col-8">
                                                        <div class="card mg-b-20 border">

                                                            <div class="card-body">




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
                                        </div>
                                        <!-- main-profile-body -->
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