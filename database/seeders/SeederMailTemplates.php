<?php

namespace Database\Seeders;

use App\Models\BasicSettings\MailTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeederMailTemplates extends Seeder
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
              'mail_type' => 'event_booking_pending',
              'mail_subject' => 'Payment Pending for Your Event Order',
              'mail_body' => '<p>Hi <span style="font-weight:600;">{customer_name}</span>,</p><p>Thank you for enrolling in the following event.</p><p>Booking Id: #{order_id}<br />Event: {title} </p><p>Please note that your payment is still pending. To complete your enrollment, kindly proceed with the payment using the attached invoice or by clicking the link below :</p>{complete_your_payment}<p>If you have any questions or need assistance, feel free to contact us.</p><p>Best regards.<br />{website_title}</p>',
            ],
          ];
          foreach ($data as $valueData) {
            MailTemplate::create($valueData);
          }
    }
}
