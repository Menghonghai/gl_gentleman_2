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
                    helper.errorAlert("{{ session('message') }}");
                @endif

                @if (null !== session('status') && session('status') == true)
                    // location.reload();
                    notif({
                            type: "warning",
                            msg: "{{ session('message') }}",
                            position: "right",
                            fade: true,
                            clickable: true,
                            timeout: 2000,
                    });
        
        
                @endif
                /*please dont delete this above code*/

                // let foo = (bar)=>{
                //     console.log('foo-bar');
                // };

                $('.delete').click(function(e) {
                    e.preventDefault();
                    var link = $(this).attr("href");
                    $('body').removeClass('timer-alert');
                    swal({
                        title: "Are your sure to delete ?",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        setInterval(() => {
                            window.location.href = link;
                            swal("Delete finished!");
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
                                            class="btn btn-outline-warning button-icon">@lang('btn.btn_back')</a>
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
                    <div class="form-group col-md-4">
                        <label for="txt">@lang('dev.search')</label>
                        <input type="text" class="form-control input-sm" name="txtequipment" id="txt"
                            value="{{ request()->get('txtequipment') ?? '' }}">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="year">@lang('dev.date')</label>
                        <div class="input-group">
                            <div class="input-group-text">
                                <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                            </div>
                            <input class="form-control" id="datepickerNoOfMonths" placeholder="MM/DD/YYYY" type="text">
                        </div><!-- input-group -->
                    </div>
                    <div class="form-group col-md-2">
                        <label for="inventory_id">@lang('dev.inventory')</label>
                        <select class="form-control input-sm" name="inventory_id" id="inventory">
                            <option value="">-- {{ __('dev.non_select') }}--</option>
                            {!! cmb_listing($inventory,  [request()->get('inventory_id') ?? ''], '', '') !!}
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="vendor_id">@lang('dev.vendor')</label>
                        <select class="form-control input-sm" name="vendor_id" id="vendor">
                            <option value="">-- {{ __('dev.non_select') }}--</option>
                            {!! cmb_listing($vendor,  [request()->get('vendor_id') ?? ''], '', '') !!}
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

            <div class="card-body p-0 mg-t-20">
                <!-- row -->
                <div class="row row-sh">

                    @foreach ($results as $equipments)
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-body iconfont text-start">
                                <div class="d-flex justify-content-between">
                                    <h4 class="card-title mb-3"> <a  class="btn-link" href="{{url_builder(
                                        $obj_info['routing'],
                                        [$obj_info['name'],'detail',$equipments->equipment_id],
                                        []
                                    )}}">
                                    @lang('table.name'): {!! $equipments->text !!}
                                    </a> </h4>
                                </div>

                                <div style="text-align: right">
                                    
                                    @php
                                        $restore = checkpermission('equipment-restore', $userinfo);
                                    @endphp
                                    @include('app._include.btn_record', [
                                        'rowid' => $equipments->equipment_id,
                                        'edit' => false,
                                        'trash' => false,
                                        'restore' => $restore ? true:false,
                                    ])
                                </div>
                     
                                </span>
                                <div class="d-flex mb-0">
                                    <div class="">
                                        <p class="mb-2 tx-12 text-muted">
                                            @lang('table.seria_number'):
                                            @if (empty($equipments->seria_number))
                                                (@lang('table.empty'))
                                            @else
                                                {{ $equipments->seria_number }}
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <small class="mb-0  text-muted">@lang('table.location'):
                                    @if (empty($equipments->location))
                                        (@lang('table.empty'))
                                    @else
                                        {{ $equipments->location }}
                                    @endif
                                    <span
                                        class="float-end text-muted">
                                        @php
                                        // dd($input);
                                        $date_array = [];
                                        $timestamp = strtotime($equipments->create_date);
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
                                    </span></small>
                            </div>


                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- /row -->
                <!-- Pagination and Record info -->
                @include('app._include.pagination')
                <!-- /. end -->

            </div>

        </div>
    @endsection
