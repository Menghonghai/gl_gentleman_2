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
                                        <small class="text-sm text-muted">
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

           <!-- container -->
				<div style="padding: 0px" class="main-container container-fluid">
					<!--Row-->
					<div class="row">
						<div class="col-xxl-7 col-xl-12 col-lg-12 col-md-12">
							<div class="card overflow-hidden">
								<div class="item7-card-img px-4 pt-4">
									<a href="javascript:void(0);"></a>
                                    @if (empty($input['image_url']))
                                        <a href="{{ asset('public/images/no_image.png') }}"
                                                data-caption="IMAGE-01" data-id="lion" class="js-img-viewer">
                                                <img style="height: 400px" src="{{ asset('public/images/no_image.png') }}" alt="img" class="cover-image br-7 w-100">
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/app/news/' . $input['image_url']) }}"
                                                data-caption="IMAGE-01" data-id="lion" class="js-img-viewer">
                                                <img style="height: 400px" src="{{ asset('storage/app/news/' . $input['image_url']) }}" alt="img" class="cover-image br-7 w-100">
                                        </a>
                                    @endif
                                    <div class="card-footer pb-2 pt-2">
                                        <div class="item7-card-desc d-md-flex">
                                            <div class="d-flex align-items-center mt-0">
                                                @if ($input['status']=='yes')
                                                    <span class="tx-14 ms-0" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="verified"><i class="fas fa-eye"></i> @lang('table.enable')</span>
                                                @else
                                                    <span class="tx-14 ms-0" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="verified"><i class="fas fa-eye-slash"></i> @lang('table.disable')</span>
                                                @endif
                                               
                                            </div>
                                            <div class="ms-auto mb-2 d-flex mt-3">
                                            <span class="fe fe-calendar text-muted tx-17"></span><div class="mt-0 mt-0 text-dark">&nbsp;@lang('table.create_date'): {{ $input['create_date'] }}</div>
                                            </div>
                                        </div>
                                    </div>
								</div>
								<div class="card-body">
									<a href="javascript:void(0);" class="mt-4"><h3 class="font-weight-semibold">@lang('table.title'): {{ $name[$dflang[0]] }}</h3></a>
									<p class="text-muted mb-0">{!! html_entity_decode($content[$dflang[0]] ?? '') !!}</p>
								</div>
							</div>
						</div>
						<div class="col-xxl-5 col-xl-12 col-lg-12 col-md-12">
                            <div class="card custom-card">
                                <div class=" card-body ">
                                    <h5 class="mb-3">@lang('table.detail') :</h5>
                                    <div class="">
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <div class="table-responsive">
                                                    <table class="table mb-0 border-top table-bordered text-nowrap">
                                                        <tbody>
                                                            <tr>
                                                                <th scope="row">@lang('table.article')</th>
                                                                <td>
                                                                    @if ($input['article_tag']=='news')
                                                                            @lang('table.news')
                                                                    @elseif ($input['article_tag']=='promotion')
                                                                            @lang('table.promotion')
                                                                    @else
                                                                            <justify style="color: red">(@lang('table.empty'))</justify>
                                                                     @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">@lang('table.start_date')</th>
                                                                <td>
                                                                    @if (empty($input['start_date']))
                                                                        <justify style="color: red">(@lang('table.not_set'))</justify>
                                                                    @else
                                                                        @php
                                                                        // dd($input);
                                                                        $date_array = [];
                                                                        $timestamp = strtotime($input['start_date']);
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
                                                                <th scope="row">@lang('table.end')</th>
                                                                <td>
                                                                    @if (empty($input['end_date']))
                                                                        <justify style="color: red">(@lang('table.not_set'))</justify>
                                                                    @else
                                                                        @php
                                                                        // dd($input);
                                                                        $date_array = [];
                                                                        $timestamp = strtotime($input['end_date']);
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
                                                                <th scope="row">@lang('table.create_by')</th>
                                                                <td>{!! cmb_listing($user, [$input['name'] ?? ''], '', '') !!}</td>
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
					<!--End Row-->

				</div>
				<!-- Container closed -->
            
            </div>
            <!-- /.card-body -->

            {{--  --}}

        </form>
    </div>
@endsection
