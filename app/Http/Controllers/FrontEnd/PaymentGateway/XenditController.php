<?php

namespace App\Http\Controllers\FrontEnd\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Event\BookingController;
use App\Http\Helpers\HelperPayment;
use App\Models\BasicSettings\Basic;
use App\Models\Earning;
use App\Models\PaymentGateway\OnlineGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Event\Booking;
use App\Models\PaymentGateway\BookingsPayment;
use App\Models\Transaction;
use App\Models\Event\EventContent;
use App\Models\Event\Ticket;
use Illuminate\Support\Facades\DB;
use App\Models\ParticipantCompetitions;
use App\Models\Disbursement;
use App\Models\DisbursementCallback;

class XenditController extends Controller
{
  public function makePayment(Request $request, $event_id)
  {
    DB::beginTransaction();

    try {
      /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~ Booking Info ~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
      $currencyInfo = $this->getCurrencyInfo();
      $allowed_currency = array('IDR', 'PHP', 'USD', 'SGD', 'MYR');
      if (!in_array($currencyInfo->base_currency_text, $allowed_currency)) {
        return back()->with(['alert-type' => 'error', 'message' => 'Invalid Currency.']);
      }

      if ($request->form_type == "tournament") {
        $cust = Auth::guard('customer')->user();
        $event_content = EventContent::where('event_id', $event_id)->where('language_id', $request->language_id)->first();

        $total = $request->total;
        $quantity = $request->quantity;
        $discount = 0;

        //tax and commission end, handling fee
        $basicSetting = Basic::select('commission', 'percent_handling_fee')->first();

        $tax_amount = Session::get('tax');
        $commission_amount = ($total * $basicSetting->commission) / 100;
        $handling_fee_amount = ($total * $basicSetting->percent_handling_fee) / 100;

        $total_early_bird_dicount = Session::get('total_early_bird_dicount');
        // changing the currency before redirect to PayPal

        $arrData = array(
          'event_id' => $event_id,
          'price' => $total,
          'tax' => $tax_amount,
          'commission' => $commission_amount,
          'percent_handling_fee' => $handling_fee_amount,
          'quantity' => $quantity,
          'discount' => $discount,
          'total_early_bird_dicount' => $total_early_bird_dicount,
          'currencyText' => $currencyInfo->base_currency_text,
          'currencyTextPosition' => $currencyInfo->base_currency_text_position,
          'currencySymbol' => $currencyInfo->base_currency_symbol,
          'currencySymbolPosition' => $currencyInfo->base_currency_symbol_position,
          'fname' => $cust->fname,
          'lname' => empty($cust->lname) ? $cust->fname : $cust->lname,
          'email' => $cust->email,
          'phone' => $cust->phone,
          'country' => $cust->country,
          'state' => $cust->state,
          'city' => empty($cust->city) ? $cust->state : $cust->city,
          'zip_code' => empty($cust->city) ? $cust->state : $cust->city,
          'address' => empty($cust->city) ? $cust->state : $cust->city,
          'paymentMethod' => 'Xendit',
          'gatewayType' => 'online',
          'paymentStatus' => 'pending',
          'paymentStatusBooking' => 'pending',
          'ticketInfos' => json_decode($request->request_ticket_infos),
          'dataOrders' => json_decode($request->request_orders),
          'form_type' => 'tournament',
        );


        //============== create booking and invoice =============================
        $booking = new BookingController();
        // store the course enrolment information in database
        $bookingInfo = $booking->storeData($arrData);

        // generate an invoice in pdf format
        $invoice = $booking->generateInvoice($bookingInfo, $event_id);
        //unlink qr code
        @unlink(public_path('assets/admin/qrcodes/') . $bookingInfo->booking_id . '.svg');
        //end unlink qr code

        // then, update the invoice field info in database
        $bookingInfo->update(['invoice' => $invoice]);

        //add blance to admin revinue
        $earning = Earning::first();
        $earning->total_revenue = $earning->total_revenue + $arrData['price'] + $bookingInfo->tax;
        if ($bookingInfo['organizer_id'] != null) {
          $earning->total_earning = $earning->total_earning + ($bookingInfo->tax + $bookingInfo->commission);
        } else {
          $earning->total_earning = $earning->total_earning + $arrData['price'] + $bookingInfo->tax;
        }
        $earning->save();

        //storeTransaction
        $bookingInfo['paymentStatus'] = 3;
        $bookingInfo['transcation_type'] = 1;

        //store amount to organizer
        $organizerData['organizer_id'] = $bookingInfo['organizer_id'];
        $organizerData['price'] = $arrData['price'];
        $organizerData['tax'] = $bookingInfo->tax;
        $organizerData['commission'] = $bookingInfo->commission;
        storeOrganizer($organizerData);

        $payable_amount = round($total + $tax_amount + $handling_fee_amount, 2);
        /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            ~~~~~~~~~~~~~~~~~ Booking End ~~~~~~~~~~~~~~
            ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

        /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            ~~~~~~~~~~~~~~~~~ Payment Gateway Info ~~~~~~~~~~~~~~
            ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
        $external_id = Str::random(10);
        $secret_key = 'Basic ' . config('xendit.key_auth');

        DB::commit();

        $data_request = Http::withHeaders([
          'Authorization' => $secret_key
        ])->post('https://api.xendit.co/v2/invoices', [
          'external_id' => $external_id,
          'amount' => $payable_amount,
          'description' => $event_content->title . ' (' . $cust->email . ')',
          'currency' => $currencyInfo->base_currency_text,
          'success_redirect_url' => route('event_booking.xindit.notify')
        ]);
        $response = $data_request->object();
        $response = json_decode(json_encode($response), true);

        $arrData['invoice_url_booking'] = $response['invoice_url'];
        $arrData['booking_id'] = $bookingInfo->booking_id;
        $bookingInfo['invoice_url_booking'] = $response['invoice_url'];

        $bookingInfo['payment_url'] = $response['invoice_url'];
        storeTranscation($bookingInfo);

        // Update invoice_url_booking in table bookings
        $updateBooking = Booking::where('booking_id', $bookingInfo->booking_id)->first();
        $updateBooking->invoice_url_booking = $response['invoice_url'];
        $updateBooking->save();

        // send a mail to the customer with the invoice
        $bookingInfo['paymentStatusBooking'] = 'pending';
        $booking->sendMail($bookingInfo);

        if (!empty($response['success_redirect_url'])) {
          $bookings_payment['booking_id'] = $bookingInfo->booking_id;
          $bookings_payment['payment_type'] = 'Xendit';
          $bookings_payment['external_id'] = $external_id;
          $bookingsPayment = BookingsPayment::create($bookings_payment);

          $request->session()->put('booking_id', $bookingInfo->booking_id);
          $request->session()->put('event_id', $event_id);
          $request->session()->put('arrData', $arrData);
          $request->session()->put('xendit_id', $response['id']);
          $request->session()->put('secret_key', config('xendit.key_auth'));
          $request->session()->put('xendit_payment_type', 'tournament');
          return redirect($response['invoice_url']);
        } else {
          return redirect()->route('check-out')->with(['alert-type' => 'error', 'message' => $response['message']]);
        }
      } else {
        $rules = [
          'fname' => 'required',
          'lname' => 'required',
          'email' => 'required',
          'phone' => 'required',
          'country' => 'required',
          'address' => 'required',
          'gateway' => 'required',
        ];

        $message = [];

        $message['fname.required'] = 'The first name feild is required';
        $message['lname.required'] = 'The last name feild is required';
        $message['gateway.required'] = 'The payment gateway feild is required';
        $request->validate($rules, $message);

        $total = Session::get('grand_total');
        $quantity = Session::get('quantity');
        $discount = Session::get('discount');

        //tax and commission end
        $basicSetting = Basic::select('commission')->first();

        $tax_amount = Session::get('tax');
        $commission_amount = ($total * $basicSetting->commission) / 100;

        $total_early_bird_dicount = Session::get('total_early_bird_dicount');
        // changing the currency before redirect to PayPal


        $arrData = array(
          'event_id' => $event_id,
          'price' => $total,
          'tax' => $tax_amount,
          'commission' => $commission_amount,
          'quantity' => $quantity,
          'discount' => $discount,
          'total_early_bird_dicount' => $total_early_bird_dicount,
          'currencyText' => $currencyInfo->base_currency_text,
          'currencyTextPosition' => $currencyInfo->base_currency_text_position,
          'currencySymbol' => $currencyInfo->base_currency_symbol,
          'currencySymbolPosition' => $currencyInfo->base_currency_symbol_position,
          'fname' => $request->fname,
          'lname' => $request->lname,
          'email' => $request->email,
          'phone' => $request->phone,
          'country' => $request->country,
          'state' => $request->state,
          'city' => $request->city,
          'zip_code' => $request->zip_code,
          'address' => $request->address,
          'paymentMethod' => 'Xendit',
          'gatewayType' => 'online',
          'paymentStatus' => 'completed',
        );

        $payable_amount = round($total + $tax_amount, 2);
        /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            ~~~~~~~~~~~~~~~~~ Booking End ~~~~~~~~~~~~~~
            ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

        /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            ~~~~~~~~~~~~~~~~~ Payment Gateway Info ~~~~~~~~~~~~~~
            ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
        $external_id = Str::random(10);
        $secret_key = 'Basic ' . config('xendit.key_auth');

        DB::commit();

        $data_request = Http::withHeaders([
          'Authorization' => $secret_key
        ])->post('https://api.xendit.co/v2/invoices', [
          'external_id' => $external_id,
          'amount' => $payable_amount,
          'currency' => $currencyInfo->base_currency_text,
          'success_redirect_url' => route('event_booking.xindit.notify')
        ]);
        $response = $data_request->object();
        $response = json_decode(json_encode($response), true);

        if (!empty($response['success_redirect_url'])) {
          $request->session()->put('event_id', $event_id);
          $request->session()->put('arrData', $arrData);
          $request->session()->put('xendit_id', $response['id']);
          $request->session()->put('secret_key', config('xendit.key_auth'));
          $request->session()->put('xendit_payment_type', 'event');
          return redirect($response['invoice_url']);
        } else {
          return redirect()->route('check-out')->with(['alert-type' => 'error', 'message' => $response['message']]);
        }
      }
    } catch (\Exception $e) {
      DB::rollback();
    }
  }

  public function callback($request)
  {
    $data = $request->all();
    if ($data['status'] == 'PAID') {
      // get the information from session
      $event_id = Session::get('event_id');
      $arrData = Session::get('arrData');
      $booking = new BookingController();

      // store the course enrolment information in database
      $bookingInfo = $booking->storeData($arrData);
      // generate an invoice in pdf format
      $invoice = $booking->generateInvoice($bookingInfo, $event_id);
      //unlink qr code
      @unlink(public_path('assets/admin/qrcodes/') . $bookingInfo->booking_id . '.svg');
      //end unlink qr code

      // then, update the invoice field info in database
      $bookingInfo->update(['invoice' => $invoice]);

      //add blance to admin revinue
      $earning = Earning::first();
      $earning->total_revenue = $earning->total_revenue + $arrData['price'] + $bookingInfo->tax;
      if ($bookingInfo['organizer_id'] != null) {
        $earning->total_earning = $earning->total_earning + ($bookingInfo->tax + $bookingInfo->commission);
      } else {
        $earning->total_earning = $earning->total_earning + $arrData['price'] + $bookingInfo->tax;
      }
      $earning->save();

      //storeTransaction
      $bookingInfo['paymentStatus'] = 1;
      $bookingInfo['transcation_type'] = 1;

      storeTranscation($bookingInfo);

      //store amount to organizer
      $organizerData['organizer_id'] = $bookingInfo['organizer_id'];
      $organizerData['price'] = $arrData['price'];
      $organizerData['tax'] = $bookingInfo->tax;
      $organizerData['commission'] = $bookingInfo->commission;
      storeOrganizer($organizerData);

      // send a mail to the customer with the invoice
      $booking->sendMail($bookingInfo);

      // remove all session data
      Session::forget('event_id');
      Session::forget('selTickets');
      Session::forget('arrData');
      Session::forget('paymentId');
      Session::forget('discount');
      Session::forget('xendit_id');
      Session::forget('secret_key');
      Session::forget('xendit_payment_type');
      return redirect()->route('event_booking.complete', ['id' => $event_id, 'booking_id' => $bookingInfo->id]);
    } else {
      return redirect()->route('check-out')->with(['alert-type' => 'error', 'message' => 'Payment failed']);
    }
  }

  // return to success page
  public function notify(Request $request)
  {
    try {
      $xendit_id = Session::get('xendit_id');
      $secret_key = Session::get('secret_key');
      if (!is_null($xendit_id) && $secret_key == config('xendit.key_auth')) {
        // get the information from session
        $event_id = Session::get('event_id');
        $arrData = Session::get('arrData');

        // if type tournament
        if ($arrData['form_type'] == "tournament") {
          if (!empty($arrData['booking_id'])) {
            // status update completed
            $booking = Booking::where('booking_id', $arrData['booking_id'])->first();
            $booking->paymentStatus = "completed";
            $booking->save();

            // status update paid
            $updateTransaction = Transaction::where('booking_id', $booking->id)->first();
            $updateTransaction->payment_status = 1;
            $updateTransaction->save();

            // remove all session data
            Session::forget('event_id');
            Session::forget('selTickets');
            Session::forget('arrData');
            Session::forget('paymentId');
            Session::forget('discount');
            Session::forget('xendit_id');
            Session::forget('secret_key');
            Session::forget('xendit_payment_type');
            return redirect()->route('event_booking.complete', ['id' => $arrData['event_id'], 'booking_id' => $booking->id]);
          } else {
            $booking_id = Session::get('booking_id');

            // status update completed
            $booking = Booking::where('booking_id', $booking_id)->first();
            $booking->paymentStatus = "completed";
            $booking->save();

            // status update paid
            $updateTransaction = Transaction::where('booking_id', $arrData['booking_id'])->first();
            $updateTransaction->payment_status = 1;
            $updateTransaction->save();

            // remove all session data
            Session::forget('event_id');
            Session::forget('selTickets');
            Session::forget('arrData');
            Session::forget('paymentId');
            Session::forget('discount');
            Session::forget('xendit_id');
            Session::forget('secret_key');
            Session::forget('xendit_payment_type');
            return redirect()->route('event_booking.complete', ['id' => $event_id, 'booking_id' => $booking_id]);
          }
        } else {
          $booking = new BookingController();
          // store the course enrolment information in database
          $bookingInfo = $booking->storeData($arrData);
          // generate an invoice in pdf format
          $invoice = $booking->generateInvoice($bookingInfo, $event_id);
          //unlink qr code
          @unlink(public_path('assets/admin/qrcodes/') . $bookingInfo->booking_id . '.svg');
          //end unlink qr code

          // then, update the invoice field info in database
          $bookingInfo->update(['invoice' => $invoice]);

          //add blance to admin revinue
          $earning = Earning::first();
          $earning->total_revenue = $earning->total_revenue + $arrData['price'] + $bookingInfo->tax;
          if ($bookingInfo['organizer_id'] != null) {
            $earning->total_earning = $earning->total_earning + ($bookingInfo->tax + $bookingInfo->commission);
          } else {
            $earning->total_earning = $earning->total_earning + $arrData['price'] + $bookingInfo->tax;
          }
          $earning->save();

          //storeTransaction
          $bookingInfo['paymentStatus'] = 1;
          $bookingInfo['transcation_type'] = 1;

          storeTranscation($bookingInfo);

          //store amount to organizer
          $organizerData['organizer_id'] = $bookingInfo['organizer_id'];
          $organizerData['price'] = $arrData['price'];
          $organizerData['tax'] = $bookingInfo->tax;
          $organizerData['commission'] = $bookingInfo->commission;
          storeOrganizer($organizerData);

          // send a mail to the customer with the invoice
          $booking->sendMail($bookingInfo);

          // remove all session data
          Session::forget('event_id');
          Session::forget('selTickets');
          Session::forget('arrData');
          Session::forget('paymentId');
          Session::forget('discount');
          Session::forget('xendit_id');
          Session::forget('secret_key');
          Session::forget('xendit_payment_type');
          return redirect()->route('event_booking.complete', ['id' => $event_id, 'booking_id' => $bookingInfo->id]);
        }
      } else {
        return redirect()->route('check-out')->with(['alert-type' => 'error', 'message' => 'Payment failed']);
      }
    } catch (\Exception $e) {
      return $e;
    }
  }

  // return to success page
  // public function pay_booking(Request $request){
  //     try {
  //         /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  //         ~~~~~~~~~~~~~~~~~ Payment Gateway Info ~~~~~~~~~~~~~~
  //         ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
  //         $booking = Booking::find($request->id);

  //         $external_id = $booking->booking_id;
  //         $secret_key = 'Basic ' . config('xendit.key_auth');

  //         $currencyInfo = $this->getCurrencyInfo();
  //         $allowed_currency = array('IDR', 'PHP', 'USD', 'SGD', 'MYR');
  //         if (!in_array($currencyInfo->base_currency_text, $allowed_currency)) {
  //             return back()->with(['alert-type' => 'error', 'message' => 'Invalid Currency.']);
  //         }

  //         $data_request = Http::withHeaders([
  //             'Authorization' => $secret_key
  //         ])->post('https://api.xendit.co/v2/invoices', [
  //             'external_id' => $external_id,
  //             'amount' => $booking->price,
  //             'currency' => $currencyInfo->base_currency_text,
  //             'success_redirect_url' => route('event_booking.xindit.notify')
  //         ]);
  //         $response = $data_request->object();
  //         $response = json_decode(json_encode($response), true);

  //         if (!empty($response['success_redirect_url'])) {

  //             $arrData = array(
  //                 'event_id' => $booking->event_id,
  //                 'booking_id' => $request->id,
  //                 'form_type' => 'tournament',
  //                 'paymentMethod' => 'Xendit',
  //                 'gatewayType' => 'online',
  //                 'paymentStatus' => 'completed',
  //             );

  //             $request->session()->put('event_id', $booking->event_id);
  //             $request->session()->put('arrData', $arrData);
  //             $request->session()->put('xendit_id', $response['id']);
  //             $request->session()->put('secret_key', config('xendit.key_auth'));
  //             $request->session()->put('xendit_payment_type', 'event');
  //             return redirect($response['invoice_url']);
  //         } else {
  //             return redirect()->route('check-out')->with(['alert-type' => 'error', 'message' => $response['message']]);
  //         }
  //     } catch (\Exception $e) {
  //         return $e;
  //     }
  // }

  public function callback_tournament($request)
  {
    $data = $request->all();
    $req_header = $request->header();

    $callback_token = 'aVFVqPOwHwkQ4S4X8HLzsLaW5W2feaFW3t02cHJLgskwgf1i';
    // if($req_header['X-CALLBACK-TOKEN'] !== $callback_token){
    //   echo 'Invalid Callback Token.'; die;
    // }

    $bookings_payment = BookingsPayment::where('external_id', $data['external_id'])->first();
    // if ($data['payment_method'] == "CREDIT_CARD") {
    //   $payment_channel = "CREDIT_CARD";
    // } elseif ($data['payment_method'] == "QR_CODE") {
    //   $payment_channel = "QR_CODE";
    // } else {
    //   $payment_channel = $data['payment_channel'];
    // }

    if ($data['payment_method'] == "CREDIT_CARD") {
      $payment_channel = "CREDIT_CARD";
    } else {
      $payment_channel = $data['payment_channel'];
    }

    $getPaymentFee['amount'] = $data['amount'];
    $getPaymentFee['payment_method'] = $data['payment_method'];
    $getPaymentFee['payment_channel'] = $payment_channel;
    $fee = HelperPayment::getPaymentFee($getPaymentFee);

    if ($data['status'] == 'PAID') {
      $bookings = Booking::where('booking_id', $bookings_payment->booking_id)->first();
      $transaction = Transaction::where('booking_id', $bookings->id)->first();
      $transaction->payment_fee = $fee;
      $transaction->after_balance = $transaction->after_balance - $fee;
      $transaction->save();

      $bookings_payment->callback = json_encode($data);
      $bookings_payment->req_header = json_encode($req_header);
      $bookings_payment->payment_method = $data['payment_method'];
      $bookings_payment->status = $data['status'];
      $bookings_payment->amount = $data['amount'];
      $bookings_payment->paid_amount = $data['paid_amount'];
      $bookings_payment->bank_code = empty($data['bank_code']) ? null : $data['bank_code'];
      $bookings_payment->paid_at = $data['paid_at'];
      $bookings_payment->payer_email = empty($data['payer_email']) ? null : $data['payer_email'];
      $bookings_payment->description = $data['description'];
      $bookings_payment->adjusted_received_amount = empty($data['adjusted_received_amount']) ? null : $data['adjusted_received_amount'];
      $bookings_payment->fees_paid_amount = empty($data['fees_paid_amount']) ? null : $data['fees_paid_amount'];
      $bookings_payment->currency = empty($data['currency']) ? null : $data['currency'];
      $bookings_payment->payment_channel = empty($data['payment_channel']) ? null : $data['payment_channel'];
      $bookings_payment->payment_destination = empty($data['payment_destination']) ? null : $data['payment_destination'];
      $bookings_payment->payment_fee = $fee;
      $bookings_payment->save();

      // send a mail to the customer with the invoice
      // $booking->sendMail($bookingInfo);
    } elseif ($data['status'] == 'EXPIRED') {
      $bookings = Booking::where('booking_id', $bookings_payment->booking_id)->first();
      $bookings->paymentStatus = 'expired';
      $bookings->paymentStatusBooking = 'expired';
      $bookings->save();

      $transaction = Transaction::where('booking_id', $bookings->id)->first();
      $transaction->payment_status = 4;
      $transaction->save();

      $bookings_payment->callback = json_encode($data);
      $bookings_payment->req_header = json_encode($req_header);
      $bookings_payment->payment_method = $data['payment_method'];
      $bookings_payment->status = $data['status'];
      $bookings_payment->amount = $data['amount'];
      $bookings_payment->paid_amount = $data['paid_amount'];
      $bookings_payment->bank_code = $data['bank_code'];
      $bookings_payment->paid_at = $data['paid_at'];
      $bookings_payment->payer_email = $data['payer_email'];
      $bookings_payment->description = $data['description'];
      $bookings_payment->adjusted_received_amount = $data['adjusted_received_amount'];
      $bookings_payment->fees_paid_amount = $data['fees_paid_amount'];
      $bookings_payment->currency = $data['currency'];
      $bookings_payment->payment_channel = $data['payment_channel'];
      $bookings_payment->payment_destination = $data['payment_destination'];
      $bookings_payment->payment_fee = $fee;
      $bookings_payment->save();

      $participant_competitions = ParticipantCompetitions::where('booking_id', $bookings->id)->get();
      foreach ($participant_competitions as $c) {
        $ticket = Ticket::where('id', $c->ticket_id)->first();
        $ticket->ticket_available = $ticket->ticket_available - 1;
        $ticket->save();
      }

      // send a mail to the customer with the invoice
      // $booking->sendMail($bookingInfo);
    }
  }

  public function callback_disbursement($request)
  {
    $data = $request->all();
    $req_header = $request->header();

    $callback_token = 'aVFVqPOwHwkQ4S4X8HLzsLaW5W2feaFW3t02cHJLgskwgf1i';
    if ($req_header['X-CALLBACK-TOKEN'] !== $callback_token) {
      echo 'Invalid Callback Token.';
      die;
    }

    $callback['payment_type'] = 'Xendit';
    $callback['callback'] = json_encode($data);
    $callback['req_header'] = json_encode($header);
    $callback['callback_id'] = $data['id'];
    $callback['external_id'] = $data['external_id'];
    $callback['amount'] = $data['amount'];
    $callback['bank_code'] = $data['bank_code'];
    $callback['account_holder_name'] = $data['account_holder_name'];
    $callback['disbursement_description'] = $data['disbursement_description'];
    $callback['status'] = $data['status'];
    $callback['currency'] = 'IDR';
    $callback['description'] = null;
    $callback['updated_callback'] = $data['updated'];
    $callback['created_callback'] = $data['created'];
    $callback = DisbursementCallback::create($callback);

    $disb = Disbursement::where('external_id', $data['external_id'])->first();
    $disb->status = $data['status'];
    $disb->save();

    // send a mail to the customer with the invoice
    // $booking->sendMail($bookingInfo);

  }
}
