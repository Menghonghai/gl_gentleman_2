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
                    var Parent = eThis.parents('tr');
                    var ind = Parent.index() + 1;
                    var url = table.find('tr:eq(' + ind + ') .url').attr('href');
                    window.location.href = url;
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
                    <div class="form-group col-md-2">
                        <label for="">@lang('dev.search')</label>
                        <input type="text" class="form-control input-sm" name="txtproduct" id="txt"
                            value="{{ request()->get('txtproduct') ?? '' }}">
                    </div>
                    <div class="form-group col-md-1.5">
                        <label for="categorie_id">@lang('dev.date')</label>
                        <input type="date" class="form-control input-sm" name="txtdate" id="txt"
                        value="{{ request()->get('txtproduct') ?? '' }}">
                    </div>
                    <div class="form-group col-md-1.5">
                        <label for="categorie_id">@lang('dev.categorie')</label>
                        <select class="form-control input-sm" name="categorie_id" id="categorie">
                            <option value="">-- {{ __('dev.non_select') }}--</option>
                            {!! cmb_listing($categorie, [request()->get('categorie_id') ?? ''], '', '') !!}
                        </select>
                    </div>
                    <div class="form-group col-md-1.5">
                        <label for="color_id">@lang('dev.product_color')</label>
                        <select class="form-control input-sm" name="color_id" id="color_id">
                            <option value="">-- {{ __('dev.non_select') }}--</option>
                            {!! cmb_listing($color, [request()->get('color_id') ?? ''], '', '') !!}
                        </select>
                    </div>
                    <div class="form-group col-md-1.5">
                        <label for="vendor_id">@lang('dev.vendor')</label>
                        <select class="form-control input-sm" name="vendor_id" id="vendor_id">
                            <option value="">-- {{ __('dev.non_select') }}--</option>
                            {!! cmb_listing($vendor, [request()->get('vendor_id') ?? ''], '', '') !!}
                        </select>
                    </div>
                    <div class="form-group col-md-1.5">
                        <label for="year">@lang('dev.status')</label>
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
                        <tr>
                            <th style="width: 10px">@lang('table.id')</th>
                            <th style="width: 150px;">@lang('table.image_logo')</th>
                            <th>@lang('table.name')</th>
                            <th>@lang('table.qty')</th>
                            <th>@lang('table.code_product')</th>
                            <th>@lang('table.cost')</th>
                            <th>@lang('table.selling_price')</th>
                            <th>@lang('table.create_date')</th>
                            <th>@lang('table.create_by')</th>
                            <th style="width: 40px;">@lang('table.status')</th>
                            <th style="width: 40px; text-align: center"><i class="fa fa-ellipsis-h"></i></th>

                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($results as $key => $products)
                                
                                <tr>
                                    <td>

                                        {!! num_in_khmer($key + 1) !!}

                                    </td>
                                    <td>
                                        @if (empty($vendors->image_url))
                                            <a href="{{ asset('public/images/no_image.png') }}"
                                            data-caption="IMAGE-01" data-id="lion" class="js-img-viewer">
                                            <img src="{{ asset('public/images/no_image.png') }}"
                                                width="150px" height="100px">
                                            </a>
                                        @else
                                            <a href="{{ asset('storage/app/product/' . $products['image_url']) }}"
                                                data-caption="IMAGE-01" data-id="lion" class="js-img-viewer">
                                                <img src="{{ asset('storage/app/product/' . $products['image_url'] ) }}"
                                                    width="150px" height="100px">
                                            </a>
                                        @endif

                                    </td>
                                    <td>{{ $products['text'] }}</td>
                                    <td style="width: 5%">
                                        @if (empty($products->qty_stock))
                                                (@lang('table.empty'))
                                            @else
                                                 {!! num_in_khmer($products->qty_stock) !!}
                                            @endif
                                    </td>
                                    <td style="width: 10%">{{ $products->code }}</td>
                                    <td style="width: 10%">
                                        @if (empty($products->cost))
                                            (@lang('table.empty'))
                                         @else
                                            {!! num_in_khmer($products->cost) !!} @lang('table.dolla')
                                        @endif
                                    </td>
                                    <td style="width: 10%">
                                        @if (empty($products->price))
                                            (@lang('table.empty'))
                                         @else
                                            {!! num_in_khmer($products->price) !!} @lang('table.dolla')
                                        @endif
                                    </td>
                                    <td style="width: 10%">
                                        @php
                                            // dd($input);
                                            $date_array = [];
                                            $timestamp = strtotime($products->create_date);
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
                                    {{-- <td style="width: 10%">{{ $products->create_date }}</td> --}}
                                    <td style="width: 10%">{{ $products->username }}</td>

                                    <td>
                                        <div class="form-group">
                                            <label class="custom-switch ps-0">
                                                <input type="checkbox" name="status" class="custom-switch-input status"
                                                    {{ $products->status == 'yes' ? 'checked' : '' }}>
                                                <span class="custom-switch-indicator custom-switch-indicator-md"></span>
                                                        <a class="dropdown-item url"
                                                    href="{{ url_builder($obj_info['routing'], [$obj_info['name'], 'status', $products->product_id], ['status=' . $products->status]) }}">
                                                </a>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $edit = checkpermission('product-edit', $userinfo);
                                            $trash = checkpermission('product-trash', $userinfo);
                                            $detail = checkpermission('product-detail', $userinfo);
                                        @endphp
                                        @include('app._include.btn_record', [
                                            'rowid' => $products->product_id,
                                            'detail' => $detail ? true : false,
                                            'edit' => $edit ? true : false,
                                            'trash' => $trash ? true : false,
                                        ])
                                    </td>
                                </tr>

                            @endforeach
                        </tbody>
                    </table>
                    <!-- Pagination and Record info -->
                    @include('app._include.pagination')
                    <!-- /. end -->

                </div>
            </form>
        </div>
    @endsection
