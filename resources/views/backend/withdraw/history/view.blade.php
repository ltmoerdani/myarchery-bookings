@foreach ($collection as $item)
  <div class="modal fade" id="withdrawModal{{ $item->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalLongTitle">{{ __('Withdraw Information') }}</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          @php
            $d_feilds = json_decode($item->feilds, true);
          @endphp
          <div class="">
            <p>{{ __('Total Payable Amount') }} :
              {{ $currencyInfo->base_currency_symbol_position == 'left' ? $currencyInfo->base_currency_symbol : '' }}
              {{ $item->payable_amount }}
              {{ $currencyInfo->base_currency_symbol_position == 'right' ? $currencyInfo->base_currency_symbol : '' }}
            </p>
            @if ($item->feilds != '')
            @foreach ($d_feilds as $key => $d_feild)
              @if($key == 'Bank_Account')
                @php
                  $method_input = App\Models\WithdrawMethodInput::where('withdraw_payment_method_id', $item->method_id)->where('name', $key)->first();
                  $method_option = App\Models\WithdrawMethodOption::where('withdraw_method_input_id', $method_input->id)->where('id', $d_feild)->first();
                @endphp
                <p><strong>{{ str_replace('_', ' ', $key) }} : {{ $method_option->name }}</strong></p>
              @else
                <p><strong>{{ str_replace('_', ' ', $key) }} : {{ $d_feild }}</strong></p>
              @endif
            @endforeach
            @endif
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
            {{ __('Close') }}
          </button>
        </div>
      </div>
    </div>
  </div>
@endforeach
