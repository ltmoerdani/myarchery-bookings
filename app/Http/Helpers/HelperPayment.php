<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\GatewaySettings;

class HelperPayment{

    public static function getPaymentFee($data){
        try {
            $ppn = 11;
            $amount = $data['amount'];
            $fixed_amount = 0;
            $percentage_amount = 0;

            $gateway = GatewaySettings::where('payment_method', $data['payment_method'])->where('payment_channel', $data['payment_channel'])->first();
            
            if($gateway->fixed_amount){
                $fixed_amount = $gateway->fixed_amount;
            }
            
            if($gateway->percentage_amount){
                $percentage_amount = ($amount * ($gateway->percentage_amount/100));
            }

            $subtotal = ($fixed_amount + $percentage_amount);
            $total = $subtotal + ($subtotal*($ppn/100));
            
            return round($total, 0);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
