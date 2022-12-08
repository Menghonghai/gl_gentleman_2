@php
    $hidden = '';
    $id = '';
    
    if ($istamplate) {
        $hidden = 'hide';
        $id = 'template';
    }
    // dd($input);
@endphp
<tr class="{{ $hidden }} numRow" id="{{ $id }}">
    <td>
        <span class="btn btn-md btn-danger" id="remove"><i class="fas fa-minus "></i></span>
    </td>
    <td style="min-width: 150px">
        <span id="unique_id-error" class="error invalid-feedback" style="display: block; font-size:10px"></span>
        <input type="hidden" name="unique_id[]" id="duplicate" value="{{ $input[$i]['unique_id'] ?? 1 }}">
        <input type="hidden" name="main_id" value="{{ $input[$i]['main_id'] ?? '' }}" id=""
            class="form-control">
        <input type="hidden" name="schedule_id[]" value="{{ $input[$i]['schedule_id'] ?? '' }}" id="schedule_id"
            class="form-control">
        <input type="hidden" name="array[]" id="" class="form-control">

        <input type="file" name="product_img[]" id="product_img" class="form-control" value="">

        <span id="product_img-error" class="error invalid-feedback" style="display: block;font-size:13px"></span>
    </td>
    <td style="min-width: 150px">
        <select class="form-control " name="product_color[]" id="product_color">
            <option value="">-- {{ __('dev.noneselected') }} --</option>
            {!! cmb_listing([], [$input[$i]['formuse'] ?? 'routine'], '', '') !!}
        </select>
        <span id="product_color-error" class="error invalid-feedback" style="display: block;"></span>
    </td>
    <td style="min-width: 150px">
        <input type="number" name="product_alert_stock[]" id="product_alert_stock" class="form-control" value="">
        <span id="dis-error" class="error invalid-feedback" style="display: block;font-size:13px"></span>

    </td>
    <td style="min-width: 150px">
        <input type="text" name="product_price[]" id="product_price" class="form-control" value="">
    </td>
    <td style="min-width: 100px">
        <input type="number" name="product_vip_price[]" id="product_vip_price" class="form-control" value="111">
        <span id="product_vip_price-error" class="error invalid-feedback" style="display: block"></span>
    </td>
    <td>
        <input type="number" name="product_whole_sale_price[]" id="product_whole_sale_price" class="form-control "
            value="">
        <span id="product_whole_sale_price-error" class="error invalid-feedback" style="display: block"></span>
    </td>
    <td>
        <i class="fa-solid fa-plus add" style="font-size: 15px; cursor: pointer;"></i>
        <div class="input-group my-group  ind-hide hide" style="width:100%;">
            <input class="form-control" type="number" name="product_discount[]" id="discount" value="20"
                min="0" style="width: 70%;" />
            <select class="form-control form-select input-sm" name="discounttype" id="discounttype" style="width: 30%;">
                {!! cmb_listing(config('me.app.discount'), [$discounttype ?? ''], '', '') !!}
            </select>
            <span id="title-{{ config('me.app.project_lang')['en'][0] }}-error" class="error invalid-feedback"
                style="display: none"></span>
        </div>
        <span id="product_discount-error" class="error invalid-feedback" style="display: block"></span>
    </td>






</tr>
