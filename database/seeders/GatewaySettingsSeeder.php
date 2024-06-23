<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GatewaySettings;

class GatewaySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'payment_method' => 'BANK_TRANSFER',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'BCA',
                'percentage_amount' => null,
                'fixed_amount' => '4000',
                'fee' => 0,
                'min_limit' => '10000',
                'max_limit' => '999999999999',
                'is_active' => 1
            ],
            [
                'payment_method' => 'BANK_TRANSFER',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'BNI',
                'percentage_amount' => null,
                'fixed_amount' => '4000',
                'fee' => 0,
                'min_limit' => '1',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'BANK_TRANSFER',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'BRI',
                'percentage_amount' => null,
                'fixed_amount' => '4000',
                'fee' => 0,
                'min_limit' => '1',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'BANK_TRANSFER',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'BJB',
                'percentage_amount' => null,
                'fixed_amount' => '4000',
                'fee' => 0,
                'min_limit' => '1',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'BANK_TRANSFER',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'BSI',
                'percentage_amount' => null,
                'fixed_amount' => '4000',
                'fee' => 0,
                'min_limit' => '1',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'BANK_TRANSFER',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'BNC',
                'percentage_amount' => null,
                'fixed_amount' => '4000',
                'fee' => 0,
                'min_limit' => '1',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'BANK_TRANSFER',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'CIMB',
                'percentage_amount' => null,
                'fixed_amount' => '4000',
                'fee' => 0,
                'min_limit' => '1000',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'BANK_TRANSFER',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'DBS',
                'percentage_amount' => null,
                'fixed_amount' => '4000',
                'fee' => 0,
                'min_limit' => '1',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'BANK_TRANSFER',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'MANDIRI',
                'percentage_amount' => null,
                'fixed_amount' => '4000',
                'fee' => 0,
                'min_limit' => '1',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'BANK_TRANSFER',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'PERMATA',
                'percentage_amount' => null,
                'fixed_amount' => '4000',
                'fee' => 0,
                'min_limit' => '10000',
                'max_limit' => '999999999999',
                'is_active' => 1
            ],
            [
                'payment_method' => 'BANK_TRANSFER',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'SAHABAT_SAMPOERNA',
                'percentage_amount' => null,
                'fixed_amount' => '4000',
                'fee' => 0,
                'min_limit' => '1',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'CREDIT_CARD',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'Master Card, Visa, JCB, AE',
                'percentage_amount' => '2.90',
                'fixed_amount' => '2000',
                'fee' => 0,
                'min_limit' => '10000',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'QR_CODE',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'QR_CODE',
                'percentage_amount' => '0.70',
                'fixed_amount' => null,
                'fee' => 0,
                'min_limit' => '10000',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'RETAIL_OUTLET',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'ALFAMART',
                'percentage_amount' => null,
                'fixed_amount' => '5000',
                'fee' => 0,
                'min_limit' => '10000',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'RETAIL_OUTLET',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'INDOMARET',
                'percentage_amount' => null,
                'fixed_amount' => '7000',
                'fee' => 0,
                'min_limit' => '10000',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'EWALLET',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'ID_OVO',
                'percentage_amount' => '3',
                'fixed_amount' => null,
                'fee' => 0,
                'min_limit' => '10000',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'EWALLET',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'ID_DANA',
                'percentage_amount' => '1.50',
                'fixed_amount' => null,
                'fee' => 0,
                'min_limit' => '10000',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'EWALLET',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'ID_LINKAJA',
                'percentage_amount' => '1.50',
                'fixed_amount' => null,
                'fee' => 0,
                'min_limit' => '10000',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'EWALLET',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'ID_SHOPEEPAY',
                'percentage_amount' => '2',
                'fixed_amount' => null,
                'fee' => 0,
                'min_limit' => '10000',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'EWALLET',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'ID_ASTRAPAY',
                'percentage_amount' => '1.50',
                'fixed_amount' => null,
                'fee' => 0,
                'min_limit' => '10000',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'EWALLET',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'ID_JENIUSPAY',
                'percentage_amount' => '2',
                'fixed_amount' => null,
                'fee' => 0,
                'min_limit' => '10000',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'DIRECT_DEBIT',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'DC_BRI',
                'percentage_amount' => '1.90',
                'fixed_amount' => null,
                'fee' => 0,
                'min_limit' => '10000',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'DIRECT_DEBIT',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'BCA_ONEKLIK',
                'percentage_amount' => null,
                'fixed_amount' => '2500',
                'fee' => 0,
                'min_limit' => '10000',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'DIRECT_DEBIT',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'MANDIRI',
                'percentage_amount' => null,
                'fixed_amount' => '4500',
                'fee' => 0,
                'min_limit' => '10000',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'PAYLATER',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'ID_KREDIVO',
                'percentage_amount' => '2.30',
                'fixed_amount' => null,
                'fee' => 0,
                'min_limit' => '10000',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'PAYLATER',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'ID_AKULAKU',
                'percentage_amount' => '1.70',
                'fixed_amount' => null,
                'fee' => 0,
                'min_limit' => '10000',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'PAYLATER',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'ID_UANGME',
                'percentage_amount' => '1.80',
                'fixed_amount' => null,
                'fee' => 0,
                'min_limit' => '10000',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'PAYLATER',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'ID_INDODANA',
                'percentage_amount' => '1.75',
                'fixed_amount' => null,
                'fee' => 0,
                'min_limit' => '10000',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
            [
                'payment_method' => 'PAYLATER',
                'gateway_type' => 'Xendit',
                'currency' => 'IDR',
                'payment_channel' => 'ID_ATOME',
                'percentage_amount' => '5',
                'fixed_amount' => null,
                'fee' => 0,
                'min_limit' => '10000',
                'max_limit' => '50000000000',
                'is_active' => 1
            ],
        ];
        foreach ($data as $valueData) {
            GatewaySettings::create($valueData);
        }
    }
}