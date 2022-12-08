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

                $("#btninvoice_{{ $obj_info['name'] }}").click(function(e) {

                    let route_create = "{{ $route['invoice'] ?? ''}}";

                    window.location = route_create;
                    //     loading_indicator);
                });
                $("#btncheckout_{{ $obj_info['name'] }}").click(function(e) {

                    let route_create = "{{ $route['checkout'] ?? ''}}";

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

                });

             

                    $(".js-example-responsive").select2({width: 'resolve' // need to override the changed default
                });
                    var $disabledResults = $(".js-example-disabled-results");
                    $disabledResults.select2();


                    var cart = $('.card');
                    var addCart = $('li.add-cart')
                    var countValue = $('#count_value');
                    var img_src = $(this).find('img').attr('src');
                    var title = $(this).find(".title").text();
                    var pro_name = $('.pro_name');

                    var cart_item = `
                    <div class="cart-item card-body p-0 customers mt-1">
                        <div class="list-group list-lg-group list-group-flush ">
                            
                            <a href="javascript:void(0);" class="border-0">
                                <div class="list-group-item list-group-item-action pd-l-0">
                                    <div class="media mt-0">
                                        <a href="profile.html">
                                            <img class="avatar-lg rounded-circle me-3 my-auto" src="${img_src}">
                                        </a>
                                        <div class="media-body">
                                            <div class="d-flex align-items-center">
                                                <div class="mt-1">
                                                    <h5 class="mb-1 tx-13 font-weight-sembold text-dark">TEst</h5>
                                                    <p class="mb-0 tx-13 text-danger">Instock</p>
                                                </div>
                                                <span class="ms-auto wd-55p fs-16">
                                                        <span id="spark2" class="wd-100p align-items-center">
                                                            <a class="btn-add">
                                                                <i class="fa-solid fa-plus"></i>
                                                            </a>
                                                            <span class="counter">
                                                            
                                                                <input type="number" id="count_value" value="1" style="width:40px">
                                               
                                                                </span>
                                                            <a class="btn-minus"><i class="fa-solid fa-minus"></i></a>
                                                            <span class="price">$29.99</span>
                                                            <span class="btn_remove_card" style="color: #fff"><i class="fa-solid fa-trash"></i></span>
                                                        </span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            
                        </div>
                    </div>


                    `

                    $(addCart).on('click', function() {

                        $(cart_item).clone().appendTo("#card-order");
                    })  
                    // $(cart).on('click ', function(e) {
                    // });
                  
                    var btnRemoveCard = $('.btn_remove_card');
                    btnRemoveCard.on('click',function(){
                        alert('ddd')
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
                                   
                                    <div class="form-group col-md-2 mg-t-10">
                                        <select name="beast" id="select-beast" class="form-control  nice-select  custom-select">
												<option value="0">--Select--</option>
												<option value="1">Foot wear</option>
												<option value="2">Top wear</option>
												<option value="3">Bootom wear</option>
												<option value="4">Men's Groming</option>
												<option value="5">Accessories</option>
											</select>
                                    </div>
                                    <div class="form-group col-md-2 mg-t-10">
                                        <select name="beast" id="select-beast" class="form-control  nice-select  custom-select">
												<option value="0">--Select--</option>
												<option value="1">Foot wear</option>
												<option value="2">Top wear</option>
												<option value="3">Bootom wear</option>
												<option value="4">Men's Groming</option>
												<option value="5">Accessories</option>
											</select>
                                    </div>
                                    <div class="form-group col-md-2 mg-t-10">
                                        <select name="beast" id="select-beast" class="form-control  nice-select  custom-select">
												<option value="0">--Select--</option>
												<option value="1">Foot wear</option>
												<option value="2">Top wear</option>
												<option value="3">Bootom wear</option>
												<option value="4">Men's Groming</option>
												<option value="5">Accessories</option>
											</select>
                                    </div>
                                    
        
                                    {{-- <div class="form-group col-md-2 mg-t-10">
                                        <input type="text" class="form-control input-sm" name="txtsale" id="txt"
                                            value="{{ request()->get('txtsale') ?? '' }}">
                                    </div> --}}
                                    <div class="input-group col-md-3 mb-2 mg-t-10">
										<input type="text" class="form-control input-sm" name="txtsale" id="txt"
                                        value="{{ request()->get('txtsale') ?? '' }}" placeholder="Searching.....">
										<span class="input-group-append">
											<button class="btn ripple btn-primary " type="button" style="height: 40px">Search</button>
										</span>
									</div>
                                    
                                    

                                    <div class="pd-10 ">
                                        @include('app._include.btn_index', [
                                            'new' => true,
                                            'trash' => true,
                                            'active' => true,
                                            // 'invoice'=> true,
                                            
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

           

            <div class="main-container container-fluid">
        
                <form style="padding-top: 10px" class="frmsearch ">
                    <div class="form-row justify-content-start">
                        
                        <!-- row -->
                        <div class="row col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 pd-0 mg-0 form-row">
                            <div class="container col-xxl-9 col-xl-9 col-lg-9 col-sm-12">
                                <div class="row row-sm">
                                    
                                    <div class="col-md-4 col-lg-6 col-xl-4 col-xxl-3  col-sm-6">
                                        <div class="card">
                                            <div class="card-body h-100  product-grid6">
                                                <div class="pro-img-box product-image">
                                                    <a href="product-details.html">
                                                        <img class=" pic-1" src="{{ asset('public/assets/img/ecommerce/8.jpg') }}" alt="product-image">
                                                        <img class="pic-2" src="{{ asset('public/assets/img/ecommerce/08.jpg') }}" alt="product-image-1">
                                                    </a>
                                                    <ul class="icons">
                                                        <li><a href="wish-list.html" data-bs-placement="top" data-bs-toggle="tooltip" title="Add to Wishlist" class="primary-gradient me-2"><i class="fa fa-heart"></i></a></li>
                                                        <li class="add-cart"><a data-bs-placement="top" data-bs-toggle="tooltip" title="Add to Cart" class="secondary-gradient me-2 "><i class="fa fa-shopping-cart"></i></a></li>
                                                        <li><a href="product-details.html" data-bs-placement="top" data-bs-toggle="tooltip" title="Quick View" class="info-gradient"><i class="fas fa-eye"></i></a></li>
                                                    </ul>
                                                </div>
                                                <div class="text-left pt-2">
                                                    <h3 class="h6 mb-2 mt-4 font-weight-bold text-uppercase"> <a  class="btn-link title" href="{{url_builder(
                                                        $obj_info['routing'],
                                                        [$obj_info['name'],'detail',],
                                                        []
                                                    )}}" style="font-size: 1.2rem">
                                                    <span>ZARA</span>
                                                    {{-- {!! $sales->text !!} --}}
                                                    </a> </h3>
                                                    <h6> <b> Colors:</b> Block</h6>
                                                <p class="card-text mb-2">Excepteur sint sint occaecat...</p>
                                                    
                                                    <h4 class="h5 mb-0 mt-1 text-center font-weight-bold  tx-22">$40 <span class="text-secondary font-weight-normal tx-13 ms-1 prev-price">$59</span></h4>
        
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-6 col-xl-4 col-xxl-3  col-sm-6">
                                        <div class="card">
                                            <div class="card-body h-100  product-grid6">
                                                <div class="pro-img-box product-image">
                                                    <a href="product-details.html">
                                                        <img class=" pic-1" src="{{ asset('public/assets/img/ecommerce/8.jpg') }}" alt="product-image">
                                                        <img class="pic-2" src="{{ asset('public/assets/img/ecommerce/08.jpg') }}" alt="product-image-1">
                                                    </a>
                                                    <ul class="icons">
                                                        <li><a href="wish-list.html" data-bs-placement="top" data-bs-toggle="tooltip" title="Add to Wishlist" class="primary-gradient me-2"><i class="fa fa-heart"></i></a></li>
                                                        <li class="add-cart"><a data-bs-placement="top" data-bs-toggle="tooltip" title="Add to Cart" class="secondary-gradient me-2 "><i class="fa fa-shopping-cart"></i></a></li>
                                                        <li><a href="product-details.html" data-bs-placement="top" data-bs-toggle="tooltip" title="Quick View" class="info-gradient"><i class="fas fa-eye"></i></a></li>
                                                    </ul>
                                                </div>
                                                <div class="text-left pt-2">
                                                    <h3 class="h6 mb-2 mt-4 font-weight-bold text-uppercase"> <a  class="btn-link title" href="{{url_builder(
                                                        $obj_info['routing'],
                                                        [$obj_info['name'],'detail',],
                                                        []
                                                    )}}" style="font-size: 1.2rem">
                                                    <span>ZARA</span>
                                                    {{-- {!! $sales->text !!} --}}
                                                    </a> </h3>
                                                    <h6> <b> Colors:</b> Block</h6>
                                                <p class="card-text mb-2">Excepteur sint sint occaecat...</p>
                                                    
                                                    <h4 class="h5 mb-0 mt-1 text-center font-weight-bold  tx-22">$40 <span class="text-secondary font-weight-normal tx-13 ms-1 prev-price">$59</span></h4>
        
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-6 col-xl-4 col-xxl-3  col-sm-6">
                                        <div class="card">
                                            <div class="card-body h-100  product-grid6">
                                                <div class="pro-img-box product-image">
                                                    <a href="product-details.html">
                                                        <img class=" pic-1" src="{{ asset('public/assets/img/ecommerce/8.jpg') }}" alt="product-image">
                                                        <img class="pic-2" src="{{ asset('public/assets/img/ecommerce/08.jpg') }}" alt="product-image-1">
                                                    </a>
                                                    <ul class="icons">
                                                        <li><a href="wish-list.html" data-bs-placement="top" data-bs-toggle="tooltip" title="Add to Wishlist" class="primary-gradient me-2"><i class="fa fa-heart"></i></a></li>
                                                        <li class="add-cart"><a data-bs-placement="top" data-bs-toggle="tooltip" title="Add to Cart" class="secondary-gradient me-2 "><i class="fa fa-shopping-cart"></i></a></li>
                                                        <li><a href="product-details.html" data-bs-placement="top" data-bs-toggle="tooltip" title="Quick View" class="info-gradient"><i class="fas fa-eye"></i></a></li>
                                                    </ul>
                                                </div>
                                                <div class="text-left pt-2">
                                                    <h3 class="h6 mb-2 mt-4 font-weight-bold text-uppercase"> <a  class="btn-link title" href="{{url_builder(
                                                        $obj_info['routing'],
                                                        [$obj_info['name'],'detail',],
                                                        []
                                                    )}}" style="font-size: 1.2rem">
                                                    <span>ZARA</span>
                                                    {{-- {!! $sales->text !!} --}}
                                                    </a> </h3>
                                                    <h6> <b> Colors:</b> Block</h6>
                                                <p class="card-text mb-2">Excepteur sint sint occaecat...</p>
                                                    
                                                    <h4 class="h5 mb-0 mt-1 text-center font-weight-bold  tx-22">$40 <span class="text-secondary font-weight-normal tx-13 ms-1 prev-price">$59</span></h4>
        
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-6 col-xl-4 col-xxl-3  col-sm-6">
                                        <div class="card">
                                            <div class="card-body h-100  product-grid6">
                                                <div class="pro-img-box product-image">
                                                    <a href="product-details.html">
                                                        <img class=" pic-1" src="{{ asset('public/assets/img/ecommerce/8.jpg') }}" alt="product-image">
                                                        <img class="pic-2" src="{{ asset('public/assets/img/ecommerce/08.jpg') }}" alt="product-image-1">
                                                    </a>
                                                    <ul class="icons">
                                                        <li><a href="wish-list.html" data-bs-placement="top" data-bs-toggle="tooltip" title="Add to Wishlist" class="primary-gradient me-2"><i class="fa fa-heart"></i></a></li>
                                                        <li class="add-cart"><a data-bs-placement="top" data-bs-toggle="tooltip" title="Add to Cart" class="secondary-gradient me-2 "><i class="fa fa-shopping-cart"></i></a></li>
                                                        <li><a href="product-details.html" data-bs-placement="top" data-bs-toggle="tooltip" title="Quick View" class="info-gradient"><i class="fas fa-eye"></i></a></li>
                                                    </ul>
                                                </div>
                                                <div class="text-left pt-2">
                                                    <h3 class="h6 mb-2 mt-4 font-weight-bold text-uppercase"> <a  class="btn-link title" href="{{url_builder(
                                                        $obj_info['routing'],
                                                        [$obj_info['name'],'detail',],
                                                        []
                                                    )}}" style="font-size: 1.2rem">
                                                    <span>ZARA</span>
                                                    {{-- {!! $sales->text !!} --}}
                                                    </a> </h3>
                                                    <h6> <b> Colors:</b> Block</h6>
                                                <p class="card-text mb-2">Excepteur sint sint occaecat...</p>
                                                    
                                                    <h4 class="h5 mb-0 mt-1 text-center font-weight-bold  tx-22">$40 <span class="text-secondary font-weight-normal tx-13 ms-1 prev-price">$59</span></h4>
        
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                   
                                    
                                    <ul class="pagination product-pagination ms-auto float-end">
                                        <li class="page-item page-prev disabled">
                                            <a class="page-link" href="javascript:void(0);" tabindex="-1">Prev</a>
                                        </li>
                                        <li class="page-item active"><a class="page-link" href="javascript:void(0);">1</a></li>
                                        <li class="page-item"><a class="page-link" href="javascript:void(0);">2</a></li>
                                        <li class="page-item"><a class="page-link" href="javascript:void(0);">3</a></li>
                                        <li class="page-item"><a class="page-link" href="javascript:void(0);">4</a></li>
                                        <li class="page-item page-next">
                                            <a class="page-link" href="javascript:void(0);">Next</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
        
                            <div class="container col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 pd-0 mg-0">
        
                                <div class="col-xxl-12 col-xl-12 col-lg-12 col-sm-6 ">
                                    <div class="sticky">
                                        <aside class="card order-sidebar sidebar-scroll" style="position: fixed; top:100px; width:320px">
                                           
                                            <div class="main-sidemenu" style="margin-top: 50px;">
                                                <div class="overflow-scroll">
                                                    <div id="card-order" class=" card overflow-hidden">
                                                        <div class="card-header pb-1" style="padding-top:0">
                                                            <h3 class="card-title mb-2">Current Order</h3>
                                                        </div>

                                                    

                                                        {{-- <div class="cart_item">
                                                            <div class="cart_row">
                                                                <div class="p-0 customers mt-1">
                                                                        <div class="d-flex align-items-center">
                                                                                <img class="avatar-lg rounded-circle me-3 my-auto" src="${img_src}">
                                                                          
                                                                            <div class="mt-1">
                                                                                <h5 class="mb-1 tx-13 font-weight-sembold text-dark">Zara</h5>
                                                                                <p class="mb-0 tx-13 text-danger">Instock</p>
                                                                            </div>
                                                                            <a class="btn-add mg-l-10">
                                                                                <i class="fa-solid fa-plus"></i>
                                                                            </a>
                                                                        </span>
                                                                        <input class="cart-qty mg-l-10 mg-r-10" type="number" name="" id="" value="1" style="width:40px">
                                                                        <a class="btn-minus"><i class="fa-solid fa-minus"></i></a>
                                                                        <span class="cart-price-total mg-l-10 mg-r-10">19.99</span>
                                                                        <a class="btn_remove_card" style="color: #fff"><i class="fa-solid fa-trash"></i></a>
                                                                           
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                            </div>

                                                        </div> --}}
                                                        
                                                      

                                                        
                                                       
                                                    </div>
                    
                                                </div>
                    
                                                <div class="card card-body col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12" style="padding-top: 0">
                                                   <div class="card-body p-0 customers mt-1">
                                                    <div class="list-group list-lg-group list-group-flush">
                                                            <div class="list-group-item list-group-item-action" style="padding: 0">
                                                                <div class="media mt-0">
                                                                    <div class="media-body">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="mt-1 total-l">
                                                                                <p><b>Discount:</b></p>
                                                                                <p><b>Subtotal:</b></p>
                                                                                <p><b>Total:</b></p>
                                                                            </div>
                                                                            <span class=" ms-auto wd-55p fs-16 total-r">
                                                                                    <span id="spark2" class="wd-100p align-items-center  mg-r-20">
                                                                                        <p>40,00000៛</p>
                                                                                        <p>40,000៛</p>
                                                                                        <p>40,000៛</p>
                                                                                    </span>
                                                                                    <span id="spark2" class="wd-100p align-items-center">
                                                                                        <p>40,000៛</p>
                                                                                        <p>40,000៛</p>
                                                                                        <span class="cart-price-total">hhhh</span>
                                                                                        
                                                                                    </span>
                                                                            </span>
                    
                                                                        </div>
                                                                        <select class="js-example-disabled-results col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                                                            <option value="one">First</option>
                                                                            <option value="two">Second</option>
                                                                            <option value="three">Third</option>
                                                                        </select>
                                                                        {{-- <div class="form-group col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12" style="padding: 0;">
                                                                            <input type="text" class="form-control input-sm" name="txtsale" id="txt"
                                                                                value="{{ request()->get('txtsale') ?? '' }}">
                                                                        </div> --}}
                                                                        <button class="mg-t-10 btn btn-success col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12" style="padding-right: 15px">
                                                                            <span>Pays(<span>$10.00</span> 	=&nbsp;<span>40,000៛</span>)</span>
                                                                        </button>
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
                        
                    </aside>
                    <!-- /row -->
                    {{-- start pagination --}}
                    @include('app._include.pagination')
                    {{-- /pagination --}}
                    
                    <div class="flex-button" style="position: fixed;
                    z-index: 2;
                    bottom: 0;
                    background: #fff;
                    width: 60%;
                    text-align: end;
                    padding: 10px; 
                    ">
                {{-- <select class="js-example-disabled-results col-xxl-6 col-xl-6 col-lg-12 col-md-12 col-sm-12">
                    <option value="one">First</option>
                    <option value="two">Second</option>
                    <option value="three">Third</option>
                </select> --}}
                 @include('app._include.btn_index', [
                       
                        
                        'invoice'=> true,
                        'checkout'=> true,
                        
                        
                    ])

                        {{-- <button class="btn btn-success btn_print" id="btnprint"><i class="fa-solid fa-print"></i>&nbspPrint</button>
                        <button class="btn btn-warning"><i class="fa-solid fa-file-invoice"></i>&nbspInvice</button>--}}
                        <button class="btn btn-info btn_chash" id="btnchash"><i class="fa-brands fa-gg-circle"></i>&nbspChash</button> 
                                
                       

                    </div>
                </form>
               
                        
            <!--div-->           
            </div>
        </div>
    @endsection
