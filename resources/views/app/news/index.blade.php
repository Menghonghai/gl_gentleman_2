@php
    //dd(request()->session()->all());
@endphp
@extends('layouts.app')
@section('blade_css')
@endsection
@push('page_css')
    @section('blade_scripts')
        <script>
            $(document).ready(function() {

                /*Please dont delete this code*/
                @if (null !== session('status') && session('status') == false)
                    helper.successAlert("{{ session('message') }}");
                @endif
                @if (null !== session('status') && session('status') == true)
                    helper.successAlert("{{ session('message') }}");
                @endif
                /*please dont delete this above code*/


                // let foo = (bar)=>{
                //     console.log('foo-bar');
                // };
                $("#save_img").click(function(e) {
                    // alert(1);
                    let route_submit = "{{ $route['submit'] }}";
                    // alert(route_submit);
                    // e.preventDefault();
                    // let route_import = "{{ $route['create'] }}";
                    let extraFrm = {}; //{jscallback:'test'};
                    let setting = {}; //{fnSuccess:foo};
                    let container = '';
                    let loading_indicator = '';
                    let popModal = {
                        show: false,
                        size: 'modal-xl'
                        //modal-sm, modal-lg, modal-xl
                    };
                    helper.silentHandler(route_submit, "frm-2{{ $obj_info['name'] }}",
                        extraFrm,
                        setting,
                        popModal, container,
                        loading_indicator);

                });
                $('.delete').click(function(e) {
                    e.preventDefault();
                    var link = $(this).attr("href");
                    $('body').removeClass('timer-alert');
                    swal({
                        title: "{{ __('table.are_your_sure_delete') }}",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        cancelButtonText: "{{ __('table.cancel') }}",
                        cancelButtonColor: 'danger',
                        closeOnConfirm: false,
                        confirmButtonText: "{{ __('btn.btn_OK') }}",
                        showLoaderOnConfirm: true
                    }, function() {
                        setInterval(() => {
                            window.location.href = link;
                            // swal("Delete finished!");
                        }, 1000);
                    });
                });
                var table = $('.table');
                $('.status').on('change', function(e) {
                    var eThis = $(this);
                    var Parent = eThis.parents('.card');
                    // var ind = Parent.index() + 1;
                    
                    var url = Parent.find(".url");
                    var link = url.attr('href');
                    // alert(link);
                    window.location.href = link;
                });


                $("#btnnew_{{ $obj_info['name'] }}").click(function(e) {

                    let route_create = "{{ $route['create'] }}";

                    window.location = route_create;
                    //     loading_indicator);
                });

                $("#btntrash_{{ $obj_info['name'] }}").click(function(e) {
                    let route_create = "{{ $route['trash'] ?? '' }}";
                    window.location = route_create;

                });


                $('.btn_remove').on('click', function() {
                    var eThis = $(this);
                    var p = eThis.parents('#photo');
                    var id = p.find('#id').val();
                    p.find('#img_id').val(id * -1);

                    // alert(id);
                    // p.hide();
                    // alert(id * -1);

                })
                $('#datepicker-date').bootstrapdatepicker({
                    format: "dd-mm-yyyy",
                    viewMode: "date",
                    multidate: true,
                    multidateSeparator: "|",
                })


            });

            function updateDistrict(jsondata) {

                let dropdown = $('#district');
                let data = jsondata.data;
                helper.makeDropdownByJson(dropdown, data, -1, 'please select');
            }

            function updateCommune(jsondata) {
                let dropdown = $('#commune');
                let data = jsondata.data;
                helper.makeDropdownByJson(dropdown, data, -1, 'please select');
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
                                             <!--<small class="text-sm">
                                                <i class="ace-icon fa fa-angle-double-right text-xs"></i>
                                              {{--  {{$caption ?? '' }} --}}
                                            </small>-->
                                        </h5>
                                    </div>
                                    <div class="pd-10 ">
                                        @include('app._include.btn_index', [
                                            'new' => true,
                                            'trash' => true,
                                            'active' => true,
                                        ])
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

            <form style="padding-top: 10px" class="frmsearch-{{ $obj_info['name'] }}">
                <div class="form-row justify-content-end" style="font-size: 11px;">
                    <div class="form-group col-md-3">
                        <label for="txt">@lang('dev.search')</label>
                        <div class="input-group">
                            <div class="input-group-text">
                                <i class="fa fa-search"></i>
                            </div>
                            <input type="text" class="form-control input-sm" name="txtnews" id="txt" placeholder="@lang('table.search_here')"
                                value="{{ request()->get('txtnews') ?? '' }}">
                        </div><!-- input-group -->
                    </div>
                    <div class="form-group col-md-3">
                        <label for="year">@lang('table.article')</label>
                        <div class="input-group">
                            <div class="input-group-text">
                                <i class="fas fa-newspaper"></i>
                            </div>
                            <select class="form-control input-sm" name="article" id="article">
                                <option value="">--{{ __('dev.non_select') }} --</option>
                                {!! cmb_listing(
                                    ['news' => __('table.news'), 'promotion' => __('table.promotion')],
                                    [request()->get('article') ?? ''],
                                    '',
                                    '',
                                    '',
                                ) !!}
                            </select>
                        </div><!-- input-group -->
                    </div>
                    <div class="form-group col-md-2">
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
                    <div class="form-group col-md-2">
                        <label for="year">@lang('dev.status')</label>
                        <div class="input-group">
                            <div class="input-group-text">
                                <i class="fa fa-tasks"></i>
                            </div>
                            <select class="form-control input-sm" name="status" id="status">
                                <option value="">--{{ __('dev.non_select') }} --</option>
                                {!! cmb_listing(
                                    ['yes' => __('table.enable'), 'no' => __('table.disable')],
                                    [request()->get('status') ?? ''],
                                    '',
                                    '',
                                    '',
                                ) !!}
                            </select>
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


                <div class="card-body p-0">
                    <!-- row -->
                    <div class="row row-sh">

                        <div style="padding-top: 0px" class="card-body">
                            @foreach ($results as $news)
                            <div class="d-sm-flex p-3 mt-4 sub-review-section border subsection-color br-tl-0 br-tr-0">
                                <div class="d-flex me-4">
                                    
                                    @if (empty($news->image_url))
                                            <a href="{{ asset('public/images/no_image.png') }}"
                                                    data-caption="IMAGE-01" data-id="lion" class="js-img-viewer">
                                                    <img style="width: 100px" class="media-object brround avatar-xxl" src="{{ asset('public/images/no_image.png') }}">
                                            </a>
                                        @else
                                            <a href="{{ asset('storage/app/news/' . $news['image_url']) }}"
                                                    data-caption="IMAGE-01" data-id="lion" class="js-img-viewer">
                                                    <img style="width: 200px" class="media-object brround avatar-xxl"  src="{{ asset('storage/app/news/' . $news['image_url']) }}">
                                            </a>
                                        @endif
                                    
                                </div>
                                <div class="media-body">
                                    <a  class="btn-link" href="{{url_builder($obj_info['routing'],[$obj_info['name'],'detail',$news->news_id],[]
                                    )}}">
                                        <h5 class="mt-0 mb-1 font-weight-semibold">@lang('table.title'): {{ $news['text'] }}</a>
                                            @if ($news->status=='yes')
                                                <span class="tx-14 ms-0" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="verified"><i class="fas fa-eye"></i></span>
                                            @else
                                                <span class="tx-14 ms-0" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="verified"><i class="fas fa-eye-slash"></i></span>
                                            @endif
                                        </h5>
                                    <p class="text-muted ms-md-1 ms-0 mb-1"><span><i 
                                        class="fas fa-newspaper me-2"></i></span><span
                                        class="font-weight-semibold me-2">@lang('table.article'):</span><span>
                                                                                                                                            @if ($news->article_tag=='news')
                                                                                                                                                @lang('table.news')
                                                                                                                                            @elseif ($news->article_tag=='promotion')
                                                                                                                                                @lang('table.promotion')
                                                                                                                                            @else
                                                                                                                                                <justify style="color: red">(@lang('table.empty'))</justify>
                                                                                                                                            @endif
                                                                                                                                            </span> | <i 
                                                                                                                                            class="fas fa-clock me-2"></i>@lang('table.create_date'):
                                                                                                                                            @php
                                                                                                                                                // dd($input);
                                                                                                                                                $date_array = [];
                                                                                                                                                $timestamp = strtotime($news->create_date);
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
                                    </p>
                                    <p class="text-muted ms-md-1 ms-0 mb-1"><span>
                                        <i class="fas fa-calendar-check me-2"></i></span><span
                                        class="font-weight-semibold me-2">@lang('table.start_date'):</span><span> 
                                            @if (empty($news->start_date))
                                                <justify style="color: red">(@lang('table.not_set'))</justify>
                                            @else
                                                    @php
                                                        // dd($input);
                                                        $date_array = [];
                                                        $timestamp = strtotime($news->start_date);
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
                                        - @lang('table.end'): 
                                        @if (empty($news->start_date))
                                            <justify style="color: red">(@lang('table.not_set'))</justify>
                                        @else
                                            @php
                                                // dd($input);
                                                $date_array = [];
                                                $timestamp = strtotime($news->end_date);
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
                                    <p class="text-muted ms-md-1 ms-0 mb-1"><span><i 
                                        class="fas fa-user-edit me-2"></i></span><span
                                    class="font-weight-semibold me-2">@lang('table.create_by'):</span><span>{{ $news->username }}</span>
                                    </p>
                                    <label class="custom-switch ps-0">
                                        <input type="checkbox" name="status" class="custom-switch-input status"
                                            {{ $news->status == 'yes' ? 'checked' : '' }}>
                                            <a class="dropdown-item url"
                                                href="{{ url_builder($obj_info['routing'], [$obj_info['name'], 'status', $news->news_id], ['status=' . $news->status]) }}">
                                            </a>
                                        <span class="custom-switch-indicator custom-switch-indicator-md"></span>
                                    </label>
                                    <div class="btn-group btn-group-sm mb-1 ms-auto float-sm-right mt-1">
                                        @php
                                                $edit = checkpermission('news-edit', $userinfo);
                                                $trash = checkpermission('news-trash', $userinfo);
                                            @endphp
                                            @include('app._include.btn_record', [
                                                        'rowid' => $news->news_id,
                                                        'edit' => $edit ? true : false,
                                                        'trash' => $trash ? true : false,
                                                    
                                                    ])
                                    </div>
                                </div>
                            </div>
                            @endforeach
    
                        </div>

                    </div>

                    <!-- /row -->
                    <!-- Pagination and Record info -->
                    @include('app._include.pagination')
                    <!-- /. end -->

                </div>
            </form>
        </div>
    @endsection
