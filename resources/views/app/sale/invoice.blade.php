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
@extends('layouts.app')
@section('blade_css')
@endsection
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
                    cancelButtoncategories: 'danger',
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
            
            //card 


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
    {{-- <style>
        
        @page {
            size: 80mm 8.5in;
        }
        
        @page :left {
            margin-left: 3cm;
        }

        @page :right {
            margin-left: 4cm;
        }

    </style> --}}
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
                                            'active' => true,
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
<!-- main-content -->
	<div class="main-content app-content" style="width: 80mm;height:80mm;font-size:.4rem">
		<!-- container -->
		<div class="main-container container-fluid">
			<div class="col-lg-12 col-md-12">
				<div class="card custom-card">
					<div class="card-body">
						<div class="d-lg-flex">
							<h6 class="main-content-label mb-1"><span class="d-flex mb-2"><a href="index.html"><img src="../assets/img/brand/favicon.png" class="sign-favicon ht-40" alt="logo"></a></span></h6>
								<div class="ms-auto">
									<p class="mb-1"><span class="font-weight-bold">Invoice No : #000321</span></p>
								</div>
						</div>
						<div class="row row-sm">
							<div class="col-lg-6" style="font-size: .3rem">
								<p>Invoice Form:</p>
								<address>
									Street Address<br>
									State, City<br>
									Region, Postal Code<br>
									yourdomain@example.com
								</address>
							</div>
							<div class="col-lg-6 text-end" style="font-size: .3rem">
								<p>Invoice To:</p>
								<address>
									Street Address<br>
									State, City<br>
									Region, Postal Code<br>
									ypurdomain@example.com
								</address>
								<div class="">
									<p class="mb-1"><span class="font-weight-bold">Invoice Date :</span></p>
										<address>
											01st November 2020
										</address>
								</div>
							</div>
						</div>
						<div class="table-responsive">
							<table class="table-invoice table-size ">
								<thead>
									<tr>
										<th style="width: 50px">Product</th>
										<th style="width: 50px">Product code</th>
										<th class="tx-center" style="width: 50px">QNTY</th>
										<th class="tx-right">Unit</th>
										<th class="tx-right">Amount</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Logo Creation</td>
										<td class="tx-12">00002</td>
										<td class="tx-center">2</td>
										<td class="tx-right">$60.00</td>
										<td class="tx-right">$120.00</td>
									</tr>
									<tr>
										<td>Online Store Design & Development</td>
										<td class="tx-12">00007</td>
										<td class="tx-center">3</td>
										<td class="tx-right">$80.00</td>
										<td class="tx-right">$240.00</td>
									</tr>
									<tr>
										<td>App Design</td>
										<td class="tx-12">000993</td>
										<td class="tx-center">1</td>
										<td class="tx-right">$40.00</td>
										<td class="tx-right">$40.00</td>
									</tr>
									<tr>
										<td class="valign-middle" colspan="2" rowspan="4">
											<div class="invoice-notes">
												{{-- <label class="main-content-label tx-13">Notes</label> --}}
												<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
											</div><!-- invoice-notes -->
										</td>
										<td class="tx-right">Sub-Total</td>
										<td class="tx-right" colspan="2">$400.00</td>
									</tr>
								
									<tr>
										<td class="tx-right">Discount</td>
										<td class="tx-right" colspan="2">10%</td>
									</tr>
									<tr>
										<td class="tx-right tx-uppercase tx-bold tx-inverse">Total Due</td>
										<td class="tx-right" colspan="2">
											<p class="tx-bold" style="font-size: .3rem">$450.00</p>
										</td>
									</tr>
								</tbody>
							</table>
                            
						</div>
					</div>
                    
					{{-- <div class="card-footer text-end">
                        <button type="button" class="btn ripple btn-primary mb-1"><i class="fe fe-credit-card me-1"></i> Pay Invoice</button>
						<button type="button" class="btn ripple btn-secondary mb-1"><i class="fe fe-send me-1"></i> Send Invoice</button>
						<button type="button" class="btn ripple btn-info mb-1" onclick="javascript:window.print();"><i class="fe fe-printer me-1"></i> Print Invoice</button>
					</div> --}}
                    
				</div>
                <button type="button" class="btn ripple btn-info mb-1" onclick="javascript:window.print();" style="width: 100px; font-size: .6rem"><i class="fe fe-printer me-1"></i> Print</button>
			</div>
				
		</div>
		<!-- Container closed -->
	</div>
<!-- main-content closed -->
       
				

                
@endsection
