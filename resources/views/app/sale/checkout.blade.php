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

        <!-- Row -->

        <div class="card-body">
            <div class="row">
                <div class="col-xl-6 mx-auto">
                    <div class="checkout-steps wrapper">
                        <div id="checkoutsteps">
                            <!-- SECTION 1 -->
                            <h4>Signin</h4>
                            <section>
                                <form>
                                    <h5 class="text-start mb-2">Signin to Your Account</h5>
                                    <p class="mb-4 text-muted tx-13 ms-0 text-start">Signin to create, discover and connect with the global community</p>
                                    <div class="form-group text-start">
                                        <label>Email</label>
                                        <input class="form-control" placeholder="Enter your email" type="text">
                                    </div>
                                    <div class="form-group text-start">
                                        <label>Password</label>
                                        <input class="form-control" placeholder="Enter your password" type="password">
                                    </div>
                                    <button class="btn ripple btn-primary btn-block">Sign In</button>
                                </form>
                            </section>
                            <!-- SECTION 2 -->
                            <h4>Billing</h4>
                            <section>
                                <form class="needs-validation" novalidate="">
                                    <h5 class="text-start mb-2">Billing Information</h5>
                                    <p class="mb-4 text-muted tx-13 ms-0 text-start">Lorem Ipsum has been the industry's standard dummy text ever since</p>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="firstName">First name</label>
                                            <input type="text" class="form-control" id="firstName" placeholder="" value="" required="">
                                            <div class="invalid-feedback">Valid first name is required.</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="lastName">Last name</label>
                                            <input type="text" class="form-control" id="lastName" placeholder="" value="" required="">
                                            <div class="invalid-feedback">Valid last name is required.</div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="address">Address</label>
                                        <input type="text" class="form-control" id="address" placeholder="1234 Main St" required="">
                                        <div class="invalid-feedback">Please enter your shipping address.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="address2">Address 2 <span class="text-muted">(Optional)</span>
                                                </label>
                                        <input type="text" class="form-control" id="address2" placeholder="Apartment or suite">
                                    </div>
                                    <div class="mb-3">
                                        <label for="mobile">Mobile Number</label>
                                        <input type="text" class="form-control" id="mobile">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5 mb-3">
                                            <label for="country">Country</label>
                                            <select class="custom-select d-block w-100" id="country" required="">
                                                        <option value="">Choose...</option>
                                                        <option>United States</option>
                                                    </select>
                                            <div class="invalid-feedback">Please select a valid country.</div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="state">State</label>
                                            <select class="custom-select d-block w-100" id="state" required="">
                                                        <option value="">Choose...</option>
                                                        <option>California</option>
                                                    </select>
                                            <div class="invalid-feedback">Please provide a valid state.</div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="zip">Zip</label>
                                            <input type="text" class="form-control" id="zip" placeholder="" required="">
                                            <div class="invalid-feedback">Zip code required.</div>
                                        </div>
                                    </div>
                                    <hr class="mb-4">
                                    <button class="btn btn-primary btn-lg btn-block" type="submit">Continue to checkout</button>
                                </form>
                            </section>
                            <!-- SECTION 3 -->
                            <h4>Order</h4>
                            <section>
                                <h5 class="text-start mb-2">Your Order</h5>
                                <p class="mb-4 text-muted tx-13 ms-0 text-start">Lorem Ipsum has been the industry's standard dummy text ever since</p>
                                <div class="product">
                                    <div class="item flex-wrap">
                                        <div class="left">
                                            <a href="javascript:void(0);" class="thumb radius"> <img src="../assets/img/ecommerce/09.jpg" alt="" class="radius"> </a>
                                            <div class="purchase">
                                                <h6> <a href="javascript:void(0);">Flowerpot</a> </h6>
                                                <div class="d-flex flex-wrap  mt-2">
                                                    <div class="mt-2 product-title tx-12 me-2">Quantity:</div>
                                                    <div class="handle-counter" id="handleCounter1">
                                                        <button class="counter-minus btn btn-outline-light border"><i class="fe fe-minus"></i></button>
                                                        <input type="text" value="2" class="qty">
                                                        <button class="counter-plus btn btn-outline-light border"><i class="fe fe-plus"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> <span class="price tx-20">$290</span>
                                    </div>
                                    <div class="item flex-wrap">
                                        <div class="left">
                                            <a href="javascript:void(0);" class="thumb radius"> <img src="../assets/img/ecommerce/03.jpg" alt="" class="radius"> </a>
                                            <div class="purchase">
                                                <h6> <a href="javascript:void(0);">white chair</a> </h6>
                                                <div class="d-flex flex-wrap mt-2">
                                                    <div class="mt-2 product-title tx-12 me-2">Quantity:</div>
                                                    <div class="handle-counter" id="handleCounter2">
                                                        <button class="counter-minus btn btn-outline-light border"><i class="fe fe-minus"></i></button>
                                                        <input type="text" value="2" class="qty">
                                                        <button class="counter-plus btn btn-outline-light border"><i class="fe fe-plus"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> <span class="price tx-20">$124</span>
                                    </div>
                                </div>
                                <div class="checkout">
                                    <div class="subtotal"> <span class="heading">Subtotal</span> <span class="total tx-20 font-weight-bold">$364</span> </div>
                                </div>
                            </section>
                            <!-- SECTION 4 -->
                            <h4>Payments</h4>
                            <section>
                                <div class="">
                                    <h5 class="text-start mb-2">Payments</h5>
                                    <p class="mb-4 text-muted tx-13 ms-0 text-start">Lorem Ipsum has been the industry's standard dummy text ever since</p>
                                </div>
                                <div class="card-pay">
                                    <ul class="tabs-menu nav">
                                        <li class=""><a href="#tab20" class="active" data-bs-toggle="tab"><i class="fa fa-credit-card"></i> Credit Card</a></li>
                                        <li><a href="#tab21" data-bs-toggle="tab" class=""><i class="fab fa-paypal"></i>  Paypal</a></li>
                                        <li><a href="#tab22" data-bs-toggle="tab" class=""><i class="fa fa-university"></i>  Bank Transfer</a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active show" id="tab20">
                                            <div class="bg-danger-transparent-2 text-danger py-3 br-3 mb-4" role="alert">Please Enter Valid Details</div>
                                            <div class="form-group">
                                                <label class="form-label">CardHolder Name</label>
                                                <input type="text" class="form-control" placeholder="First Name">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Card number</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Search for...">
                                                    <span class="input-group-append">
                                                                <button class="btn btn-primary box-shadow-0" type="button"><i class="fab fa-cc-visa"></i> &nbsp; <i class="fab fa-cc-amex"></i> &nbsp;
                                                                <i class="fab fa-cc-mastercard"></i></button>
                                                            </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <div class="form-group">
                                                        <label class="form-label">Expiration</label>
                                                        <div class="input-group">
                                                            <input type="number" class="form-control" placeholder="MM" name="Month">
                                                            <input type="number" class="form-control" placeholder="YY" name="Year">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="form-label">CVV <i class="fa fa-question-circle"></i></label>
                                                        <input type="number" class="form-control" required="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab21">
                                            <p class="mt-4">Paypal is easiest way to pay online</p>
                                            <p><a href="javascript:void(0);" class="btn btn-primary"><i class="fab fa-paypal"></i> Log in my Paypal</a></p>
                                            <p class="mb-0"><strong>Note:</strong> Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. </p>
                                        </div>
                                        <div class="tab-pane" id="tab22">
                                            <p class="mt-4">Bank account details</p>
                                            <dl class="card-text">
                                                <dt>BANK: </dt>
                                                <dd> THE UNION BANK 0456</dd>
                                            </dl>
                                            <dl class="card-text">
                                                <dt>Account number: </dt>
                                                <dd> 67542897653214</dd>
                                            </dl>
                                            <dl class="card-text">
                                                <dt>IBAN: </dt>
                                                <dd>543218769</dd>
                                            </dl>
                                            <p class="mb-0"><strong>Note:</strong> Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. </p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <h4>Finished</h4>
                            <section class="text-center">
                                <div class="">
                                    <h5 class="text-center mb-4">Your order Confirmed!</h5>
                                </div>
                                <svg class="wd-100 ht-100 mx-auto justify-content-center mb-3 text-center" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                                            <circle class="path circle" fill="none" stroke="#22c03c" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1" />
                                            <polyline class="path check" fill="none" stroke="#22c03c" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 " />
                                        </svg>
                                <p class="success pl-5 pr-5">Order placed successfully. Your order will be dispacted soon. meanwhile you can track your order in my order section.</p>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Row -->
    </div>
@endsection
