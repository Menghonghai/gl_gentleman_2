@if (isset($new) && $new)
    <button id="btnnew_{{ $obj_info['name'] }}" type="button" class="formactionbutton btn btn-outline-success button-icon"><i
            class="fe fe-plus me-2"></i>@lang('dev.new')</button>
@endif

@if (isset($invoice) && $invoice)
    <button id="btninvoice_{{ $obj_info['name'] }}" type="button" class="formactionbutton btn btn-warning button-icon">
        <i class="fa-solid fa-file-invoice"></i> @lang('dev.invoice')
    </button>
@endif
@if (isset($checkout) && $checkout)
    <button id="btncheckout_{{ $obj_info['name'] }}" type="button" class="formactionbutton btn btn-success button-icon">
        <i class="fa-solid fa-right-from-bracket"></i> @lang('dev.checkout')
    </button>
@endif

@if (isset($chash) && $chash)
    <button id="btnchash_{{ $obj_info['name'] }}" type="button" class="formactionbutton btn btn-warning button-icon">
        <i class="fa-solid fa-file-invoice"></i> @lang('dev.invoice')
    </button>
@endif


@if (isset($istrash) && $istrash)
    @if (isset($active) && $active)
        <button id="btnactive_{{ $obj_info['name'] }}" type="button"
            class="formactionbutton btn btn-outline-info button-icon">

            <i class="fe fe-check"></i>@lang('dev.active')
        </button>
    @endif
@else
    @if (isset($trash) && $trash)
        <button id="btntrash_{{ $obj_info['name'] }}" type="button"
            class="formactionbutton btn btn-outline-danger button-icon"><i
                class="fe fe-trash me-2"></i>@lang('btn.btn_trash')</button>
    @endif



    @if (isset($import) && $import)
        <button id="btnimport_{{ $obj_info['name'] }}" type="button"
            class="formactionbutton btn btn-outline-success btn-flat ct-btn-action">
            <i class="fas fa-file-import me-2"></i>@lang('dev.import')
        </button>
    @endif
    @if (isset($import) && $import)
        <button id="btnimport_{{ $obj_info['name'] }}" type="button"
            class="formactionbutton btn btn-outline-success btn-flat ct-btn-action">
            <i class="fas fa-file-import me-2"></i>@lang('dev.import')
        </button>
    @endif


   
    

@endif
