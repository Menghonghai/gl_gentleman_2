 <div class=" table-responsive">
     @php
         $use = false;
     @endphp
     <table id="myTable" class=" table order-list table-striped">
         <thead>
             <tr>
                 <td style="width: 40px; text-align: center"><i class="fa fa-ellipsis-h"></i></td>
                 <td>@lang('dev.image')</td>
                 <td>@lang('dev.color')</td>
                 <td>@lang('dev.alert_stock')</td>
                 <td>@lang('dev.price')</td>
                 <td>@lang('dev.vip_price')</td>
                 <td>@lang('dev.wholesale_price')</td>
                 <td>@lang('dev.discount')</td>
             </tr>
         </thead>
         <tbody>
             @if (isset($input))
                 @php
                     $count_row = count($input);
                 @endphp
             @else
                 @php
                     $count_row = 0;
                 @endphp
             @endif
             @for ($i = 0; $i < $count_row; $i++)
                 @include('app.' . $obj_info['name'] . '.template', ['istamplate' => false])
             @endfor
             @include('app.' . $obj_info['name'] . '.template', ['istamplate' => true])
         </tbody>
         <tfoot>
             <tr>
                 <td colspan="5" style="text-align: right; float:left;">
                     <span class="btn btn-primary" id="addrow">
                         <i class="fa-solid fa-plus" style="font-size: 15px"></i>
                         {{-- <i class="fa-solid fa-square-plus" style="font-size: 20px"></i> --}}
                     </span>
                 </td>
             </tr>
         </tfoot>

     </table>

 </div>
 @push('page_scripts')
     <script>
         var table = $('.table');
         $("#addrow").on("click", function() {
             var template = $('#template');
             $clone = template.clone();
             $clone.insertBefore(template);
             $clone.removeClass('hide')
                 .removeAttr('id');
             $clone.find("input").val("").end();
         });
         $("table.order-list").on("click", "#remove", function() {
             var eThis = $(this);
             var Parent = eThis.parents('tr')
             var ind = Parent.index() + 1;
             Parent.hide();
         });
         $('table.order-list').on('click', '.add', function(e) {
             var eThis = $(this);
             var Parent = eThis.parents('tr');
             var ind = Parent.index() + 1;
             var input = table.find('tr:eq(' + ind + ') td:eq(7) .ind-hide').removeClass('.hide');

             //   eThis.hide();

         })
     </script>
 @endpush
