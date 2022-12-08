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

        <section class="sticky-section content-header bg-light ct-bar-action ct-bar-action-shaddow">

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
                                            class="btn btn-outline-warning button-icon"><i class="fe fe-arrow-left me-2"></i>@lang('btn.btn_back')</a>
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
                <div class="form-row d-flex justify-content-end" style="font-size: 11px">
                    <div class="form-group col-md-6">
                        <label for="txt">@lang('dev.search')</label>
                        <div class="input-group">
                            <div class="input-group-text">
                                <i class="fa fa-search"></i>
                            </div>
                            <input type="text" class="form-control input-sm" name="txtcategorie" id="txt" placeholder="@lang('table.search_here')"
                                value="{{ request()->get('txtcategorie') ?? '' }}">
                        </div><!-- input-group -->
                    </div>
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


            <div class="card-body table-responsive p-0 mg-t-20">
                <table class="table  table-striped table-hover text-nowrap table-bordered">
                    @if (isset($istrash) && $istrash)
                        <thead style="color: var(--warning)">
                        @else
                            <thead style="color: var(--info)">
                    @endif
                    <tr style="text-align: center">
                        <th style="width: 10px">@lang('table.id')</th>
                        <th>@lang('table.code')</th>
                        <th>@lang('table.name')</th>
                        <th>@lang('table.create_date')</th>
                        <th>@lang('table.create_by')</th>
                        <th style="width: 40px; text-align: center"><i class="fa fa-ellipsis-h"></i></th>

                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($results as $key => $categories)
                            <tr>
                                <td>

                                    {!! num_in_khmer($key + 1) !!}

                                </td>
                                <td style="width: 15%">
                                    @if (empty($categories->code))
                                        <justify style="color: red">(@lang('table.empty'))</justify>
                                    @else
                                        {{ $categories->code }}
                                    @endif</td>
                                <td>{{ $categories['text'] }}</td>
                                <td style="width: 10%"> @php
                                    // dd($input);
                                    $date_array = [];
                                    $timestamp = strtotime($categories->create_date);
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
                                <td style="width: 10%">{{ $categories->username }}</td>
                                <td>
                                    @php
                                            $restore = checkpermission('categorie-restore', $userinfo);
                                    @endphp
                                    @include('app._include.btn_record', [
                                        'rowid' => $categories->categorie_id,
                                        'edit' => false,
                                        'trash' => false,
                                        'restore' => $restore ? true:false,
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

        </div>
    @endsection
