@php
    $extends = 'app';
    $action_btn = ['save' => false, 'print' => false, 'cancel' => true, 'new' => true];
    foreach (config('me.app.project_lang') as $lang) {
        $langcode[] = $lang[0];
    }
@endphp
@if (is_axios())
    @php
        $extends = 'axios';
        $action_btn = ['save' => false, 'print' => false, 'cancel' => false];
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

            let hide = "{{ $isupdate ?? '' }}"
            if (hide) {
                $('.create_img').hide();

            } else {
                $('.update_img').hide();
            }


            let route_submit = "{{ $route['submit'] }}";
            let route_cancel = "{{ $route['cancel'] ?? '' }}";
            let route_print = "{{ $route['print'] ?? '' }}";
            let route_new = "{{ $route['new'] ?? '' }}";
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

        });
    </script>
@endsection
@section('content')
    {{-- Header --}}
    {{-- <section style="position: sticky;top: 64px; z-index:2" class="content-header bg-light ct-bar-action ct-bar-action-shaddow">
            
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

    </section> --}}
    {{-- end header --}}
    <div class="container-fluid">
        {{-- Start Form --}}

       <!-- row -->
       <div class="row row-sm">
        <div class="col-xxl-12">
            <div class="card">
                <div class="card-body ">
                    <div class="row row-sm ">
                        <div class=" col-xxl-6 col-lg-12 col-md-12">
                            <div class="row">
                                <div class="col-xxl-2">
                                    <div class="clearfix carousel-slider">
                                        <div id="thumbcarousel" class="carousel slide" data-bs-interval="t">
                                            <div class="carousel-inner">
                                                <ul class="carousel-item active">
                                                    <li data-bs-target="#Slider" data-bs-slide-to="0" class="thumb active my-2"><img src="{{ asset('public/assets/img/ecommerce/shirt-1.png') }}" alt="img"></li>
                                                    <li data-bs-target="#Slider" data-bs-slide-to="1" class="thumb my-2"><img src="{{asset('public/assets/img/ecommerce/shirt-3.png')}}" alt="img"></li>
                                                    <li data-bs-target="#Slider" data-bs-slide-to="2" class="thumb my-2"><img src="{{ asset('public/assets/img/ecommerce/shirt-4.png') }}" alt="img"></li>
                                                    <li data-bs-target="#Slider" data-bs-slide-to="3" class="thumb my-2"><img src="{{ asset('public/assets/img/ecommerce/shirt-2.png') }}" alt="img"></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-10">
                                    <div class="product-carousel  border br-5">
                                        <div id="Slider" class="carousel slide" data-bs-ride="false">
                                            <div class="carousel-inner">
                                                <div class="carousel-item active"><img src="{{ asset('public/assets/img/ecommerce/shirt-1.png') }}" alt="img" class="img-fluid mx-auto d-block">
                                                    <div class="text-center mt-5 mb-5 btn-list">
                                                    </div>
                                                </div>
                                                <div class="carousel-item"> <img src="{{ asset('publicassets/img/ecommerce/shirt-3.png') }}" alt="img" class="img-fluid mx-auto d-block">
                                                    <div class="text-center mb-5 mt-5 btn-list">
                                                    </div>
                                                </div>
                                                <div class="carousel-item"> <img src="{{ asset('public/assets/img/ecommerce/shirt-4.png') }}" alt="img" class="img-fluid mx-auto d-block">
                                                    <div class="text-center  mb-5 mt-5 btn-list">
                                                    </div>
                                                </div>
                                                <div class="carousel-item"> <img src="{{ asset('public/assets/img/ecommerce/shirt-2.png') }}" alt="img" class="img-fluid mx-auto d-block">
                                                    <div class="text-center  mb-5 mt-5 btn-list">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="details col-xxl-6 col-lg-12 col-md-12 mt-4 mt-xl-0">
                            <h4 class="product-title mb-1">Jyothi Fashion Women's Fit & Flare Knee Length Western Frock</h4>
                            <p class="text-muted tx-13 mb-1">women red & Grey Checked Casual frock</p>
                            <div class="rating mb-1">
                                <div class="stars">
                                    <span class="fa fa-star checked"></span>
                                    <span class="fa fa-star checked"></span>
                                    <span class="fa fa-star checked"></span>
                                    <span class="fa fa-star text-muted"></span>
                                    <span class="fa fa-star text-muted"></span>
                                </div>
                                <span class="review-no">41 reviews</span>
                            </div>
                            <h6 class="price">current price: <span class="h3 ms-2">$253</span></h6>
                            <p class="vote"><strong>91%</strong> of buyers enjoyed this product! <strong>(87
                                    votes)</strong></p>
                            <div class="mb-3">
                                <div class="">
                                    <p class="font-weight-normal"><span class="h4">Hurry Up!</span> Sold:
                                        <span class="text-primary h5 ">110/150</span> products in stock.
                                        <p>
                                </div>
                                <div class="progress ht-10  mt-0">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 60%"></div>
                                </div>
                            </div>

                            <div class="sizes d-flex">sizes:
                                <span class="size d-flex"><label class="rdiobox mb-0"><input checked=""
                                            name="rdio" type="radio"> <span>s</span></label>
                                </span>
                                <span class="size d-flex"><label class="rdiobox mb-0"><input name="rdio"
                                            type="radio"> <span>m</span></label>
                                </span>
                                <span class="size d-flex"><label class="rdiobox mb-0"><input name="rdio"
                                            type="radio"> <span>l</span></label>
                                </span>
                                <span class="size d-flex"><label class="rdiobox mb-0"><input name="rdio"
                                            type="radio"> <span>xl</span></label>
                                </span>
                            </div>
                            <div class="d-flex  mt-2">
                                <div class="mt-2 product-title">Quantity:</div>
                                <div class="d-flex ms-2">
                                    <ul class=" mb-0 qunatity-list">
                                        <li>
                                            <div class="form-group">
                                                <select name="quantity" id="select-countries17" class="form-control nice-select wd-50">
                                                    <option value="1" selected="">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                </select>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="colors d-flex me-3 mt-2">
                                <span class="mt-2">colors:</span>
                                <div class="d-sm-flex ms-4">
                                    <div class="me-2">
                                        <label class="colorinput">
                                            <input name="color" type="radio" value="azure"
                                                class="colorinput-input" checked="">
                                            <span class="colorinput-color bg-primary"></span>
                                        </label>
                                    </div>
                                    <div class="me-2">
                                        <label class="colorinput">
                                            <input name="color" type="radio" value="indigo"
                                                class="colorinput-input">
                                            <span class="colorinput-color bg-secondary"></span>
                                        </label>
                                    </div>
                                    <div class="me-2">
                                        <label class="colorinput">
                                            <input name="color" type="radio" value="purple"
                                                class="colorinput-input">
                                            <span class="colorinput-color bg-danger"></span>
                                        </label>
                                    </div>
                                    <div class="me-2">
                                        <label class="colorinput">
                                            <input name="color" type="radio" value="pink"
                                                class="colorinput-input">
                                            <span class="colorinput-color bg-pink"></span>
                                        </label>
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
                                    <li><a href="#tab5" class="active" data-bs-toggle="tab">Specifications</a></li>
                                    <li><a href="#tab6" data-bs-toggle="tab">Dimensions</a></li>
                                    <li><a href="#tab7" data-bs-toggle="tab">Features</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body tabs-menu-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab5">
                                    <h5 class="mb-2 mt-1 fw-semibold">Description :</h5>
                                    <p class="mb-3 tx-13">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident,
                                        similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.</p>
                                    <p class="mb-3 tx-13">odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia.
                                    </p>
                                    <h5 class="mb-2 mt-3 fw-semibold">Specifications :</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tr>
                                                <td class="fw-semibold">Package Dimensions</td>
                                                <td> 33 x 22 x 3 cm; 450 Grams</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">Manufacturer</td>
                                                <td>gownu Production</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">Item part number </td>
                                                <td>BNVRDMRHENFULL-Z14</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">Best Sellers Rank</td>
                                                <td> #141 in Clothing & Accessories (See Top 100 in Clothing & Accessories)
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">Customer Reviews</td>
                                                <td>
                                                    <p class="text-muted float-start me-3">
                                                        <span class="fa fa-star text-warning"></span>
                                                        <span class="fa fa-star text-warning"></span>
                                                        <span class="fa fa-star text-warning"></span>
                                                        <span class="fa fa-star-half-o text-warning"></span>
                                                        <span class="fa fa-star-o text-warning"></span>
                                                        <span class="text-success fw-semibold">(2,076
                                                            ratings)</span>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab6">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td> Care Instructions</td>
                                                    <td>Hand Wash Only</td>
                                                </tr>
                                                <tr>
                                                    <td> Fit Type</td>
                                                    <td>Regular</td>
                                                </tr>
                                                <tr>
                                                    <td> Fabric</td>
                                                    <td>Soft Crepe || full stitched</td>
                                                </tr>
                                                <tr>
                                                    <td> Size</td>
                                                    <td>S(34''), M(36"), L(38"), XL(40"), XXL(42")</td>
                                                </tr>
                                                <tr>
                                                    <td> Length</td>
                                                    <td>Up to 44 inch</td>
                                                </tr>
                                                <tr>
                                                    <td> Manufacturer</td>
                                                    <td>Jyothi fashions</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab7">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td><i class="fa fa-check me-3 text-success"></i>Care Instructions: Hand Wash Only</td>
                                                </tr>
                                                <tr>
                                                    <td><i class="fa fa-check me-3 text-success"></i>Kurta Material:Poly Crepe</td>
                                                </tr>
                                                <tr>
                                                    <td><i class="fa fa-check me-3 text-success"></i>Style: A-line 48" length Kurta with 3/4 Bell Sleeve</td>
                                                </tr>
                                                <tr>
                                                    <td><i class="fa fa-check me-3 text-success"></i>Ocassion:Casual, Formal
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><i class="fa fa-check me-3 text-success"></i>Packet contains: 1 readymade Kurta.</td>
                                                </tr>
                                                <tr>
                                                    <td><i class="fa fa-check me-3 text-success"></i>Size Declaration: Please choose garment size that is two inches more than your body measurement.e.g:-For Bust size -36(S),Select garment size-38''(M).</td>
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
    <!-- /row -->

    <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12">
        <div class="card custom-card">
            <div class="card-body h-100">
                <div id="owl-demo2" class="owl-carousel owl-carousel-icons2">
                    <div class="item">
                        <div class="card">
                            <div class="card custom-card overflow-hidden mb-0 ">
                                <a href="file-details.html"><img class="w-100" src="{{asset('public/assets/img/photos/fileimage4.jpg')}}" alt="img"></a>
                                <div class="card-footer bd-t-0 py-3">
                                    <div class="d-flex">
                                        <div>
                                            <h6 class="mb-0">221.jpg</h6>
                                        </div>
                                        <div class="ms-auto">
                                            <h6 class="text-muted mb-0">120 KB</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="card">
                            <div class="card custom-card overflow-hidden mb-0 ">
                                <a href="file-details.html"><img class="w-100" src="{{asset('public/assets/img/photos/fileimage1.jpg')}}" alt="img"></a>
                                <div class="card-footer bd-t-0 py-3">
                                    <div class="d-flex">
                                        <div>
                                            <h6 class="mb-0">221.jpg</h6>
                                        </div>
                                        <div class="ms-auto">
                                            <h6 class="text-muted mb-0">120 KB</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="card">
                            <div class="card custom-card overflow-hidden mb-0 ">
                                <a href="file-details.html"><img class="w-100" src="{{asset('public/assets/img/photos/fileimage2.jpg')}}" alt="img"></a>
                                <div class="card-footer bd-t-0 py-3">
                                    <div class="d-flex">
                                        <div>
                                            <h6 class="mb-0">221.jpg</h6>
                                        </div>
                                        <div class="ms-auto">
                                            <h6 class="text-muted mb-0">120 KB</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="card">
                            <div class="card custom-card overflow-hidden mb-0 ">
                                <a href="file-details.html"><img class="w-100" src="{{asset('public/assets/img/photos/fileimage3.jpg')}}" alt="img"></a>
                                <div class="card-footer bd-t-0 py-3">
                                    <div class="d-flex">
                                        <div>
                                            <h6 class="mb-0">221.jpg</h6>
                                        </div>
                                        <div class="ms-auto">
                                            <h6 class="text-muted mb-0">120 KB</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="card">
                            <div class="card custom-card overflow-hidden mb-0 ">
                                <a href="file-details.html"><img class="w-100" src="{{asset('public/assets/img/photos/fileimage4.jpg')}}" alt="img"></a>
                                <div class="card-footer bd-t-0 py-3">
                                    <div class="d-flex">
                                        <div>
                                            <h6 class="mb-0">221.jpg</h6>
                                        </div>
                                        <div class="ms-auto">
                                            <h6 class="text-muted mb-0">120 KB</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="card">
                            <div class="card custom-card overflow-hidden mb-0 ">
                                <a href="file-details.html"><img class="w-100" src="{{asset('public/assets/img/photos/fileimage5.jpg')}}" alt="img"></a>
                                <div class="card-footer bd-t-0 py-3">
                                    <div class="d-flex">
                                        <div>
                                            <h6 class="mb-0">221.jpg</h6>
                                        </div>
                                        <div class="ms-auto">
                                            <h6 class="text-muted mb-0">120 KB</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="card">
                            <div class="card custom-card overflow-hidden mb-0 ">
                                <a href="file-details.html"><img class="w-100" src="{{asset('public/assets/img/photos/fileimage1.jpg')}}" alt="img"></a>
                                <div class="card-footer bd-t-0 py-3">
                                    <div class="d-flex">
                                        <div>
                                            <h6 class="mb-0">221.jpg</h6>
                                        </div>
                                        <div class="ms-auto">
                                            <h6 class="text-muted mb-0">120 KB</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="card">
                            <div class="card custom-card overflow-hidden mb-0 ">
                                <a href="file-details.html"><img class="w-100" src="{{asset('public/assets/img/photos/fileimage3.jpg')}}" alt="img"></a>
                                <div class="card-footer bd-t-0 py-3">
                                    <div class="d-flex">
                                        <div>
                                            <h6 class="mb-0">221.jpg</h6>
                                        </div>
                                        <div class="ms-auto">
                                            <h6 class="text-muted mb-0">120 KB</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="card">
                            <div class="card custom-card overflow-hidden mb-0 ">
                                <a href="file-details.html"><img class="w-100" src="{{asset('public/assets/img/photos/fileimage5.jpg')}}" alt="img"></a>
                                <div class="card-footer bd-t-0 py-3">
                                    <div class="d-flex">
                                        <div>
                                            <h6 class="mb-0">221.jpg</h6>
                                        </div>
                                        <div class="ms-auto">
                                            <h6 class="text-muted mb-0">120 KB</h6>
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
@endsection
