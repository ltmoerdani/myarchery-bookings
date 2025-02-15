<?php

namespace App\Http\Controllers\FrontEnd\Event;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\PaymentGateway\FlutterwaveController;
use App\Http\Controllers\FrontEnd\PaymentGateway\InstamojoController;
use App\Http\Controllers\FrontEnd\PaymentGateway\IyzipayController;
use App\Http\Controllers\FrontEnd\PaymentGateway\MercadoPagoController;
use App\Http\Controllers\FrontEnd\PaymentGateway\MidtransController;
use App\Http\Controllers\FrontEnd\PaymentGateway\MollieController;
use App\Http\Controllers\FrontEnd\PaymentGateway\MyFatoorahController;
use App\Http\Controllers\FrontEnd\PaymentGateway\OfflineController;
use App\Http\Controllers\FrontEnd\PaymentGateway\PayPalController;
use App\Http\Controllers\FrontEnd\PaymentGateway\PaystackController;
use App\Http\Controllers\FrontEnd\PaymentGateway\PaytabsController;
use App\Http\Controllers\FrontEnd\PaymentGateway\PaytmController;
use App\Http\Controllers\FrontEnd\PaymentGateway\PerfectMoneyController;
use App\Http\Controllers\FrontEnd\PaymentGateway\PhonepeController;
use App\Http\Controllers\FrontEnd\PaymentGateway\RazorpayController;
use App\Http\Controllers\FrontEnd\PaymentGateway\StripeController;
use App\Http\Controllers\FrontEnd\PaymentGateway\ToyyibpayController;
use App\Http\Controllers\FrontEnd\PaymentGateway\XenditController;
use App\Http\Controllers\FrontEnd\PaymentGateway\YocoController;
use App\Models\BasicSettings\Basic;
use App\Models\BasicSettings\MailTemplate;
use App\Models\Event;
use App\Models\Event\Booking;
use App\Models\Event\EventContent;
use App\Models\Event\EventDates;
use App\Models\Event\EventImage;
use App\Models\Event\Ticket;
use App\Models\Organizer;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PHPMailer\PHPMailer\PHPMailer;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Participant;
use App\Models\ParticipantCompetitions;
use App\Models\IndonesianSubdistrict;
use App\Models\IndonesianCities;
use App\Models\InternationalCities;
use App\Http\Helpers\HelperUser;
use App\Models\Clubs;
use App\Models\Organization;
use App\Models\ParticipantByCustomer;
use App\Models\School;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
  public function index(Request $request, $id)
  {
    $basic = Basic::select('event_guest_checkout_status')->first();
    if ($basic->event_guest_checkout_status == 0 && $request->type != 'guest') {
      // check whether user is logged in or not
      if (Auth::guard('customer')->check() == false) {
        return redirect()->route('customer.login', ['redirectPath' => 'course_details']);
      }
    }

    // payment
    if ($request->total != 0 || Session::get('sub_total') != 0) {
      if (!$request->exists('gateway')) {
        Session::flash('error', 'Please select a payment method.');

        return redirect()->back();
      } else if ($request['gateway'] == 'paypal') {
        $paypal = new PayPalController();

        return $paypal->bookingProcess($request, $id);
      } else if ($request['gateway'] == 'razorpay') {
        $razorpay = new RazorpayController();

        return $razorpay->bookingProcess($request, $id);
      } else if ($request['gateway'] == 'instamojo') {
        $instamojo = new InstamojoController();

        return $instamojo->bookingProcess($request, $id);
      } else if ($request['gateway'] == 'paystack') {
        $paystack = new PaystackController();

        return $paystack->bookingProcess($request, $id);
      } else if ($request['gateway'] == 'flutterwave') {
        $flutterwave = new FlutterwaveController();

        return $flutterwave->bookingProcess($request, $id);
      } else if ($request['gateway'] == 'mercadopago') {
        $mercadopago = new MercadoPagoController();

        return $mercadopago->bookingProcess($request, $id);
      } else if ($request['gateway'] == 'mollie') {
        $mollie = new MollieController();

        return $mollie->bookingProcess($request, $id);
      } else if ($request['gateway'] == 'stripe') {
        $stripe = new StripeController();

        return $stripe->bookingProcess($request, $id);
      } else if ($request['gateway'] == 'paytm') {
        $paytm = new PaytmController();

        return $paytm->bookingProcess($request, $id);
      } else if ($request['gateway'] == 'midtrans') {
        Session::put('midtrans_payment_type', 'event');
        $paytm = new MidtransController();

        return $paytm->makePayment($request, $id);
      } else if ($request['gateway'] == 'iyzico') {
        $paytm = new IyzipayController();

        return $paytm->makePayment($request, $id);
      } else if ($request['gateway'] == 'paytabs') {
        $paytabs = new PaytabsController();

        return $paytabs->makePayment($request, $id);
      } else if ($request['gateway'] == 'toyyibpay') {
        $toyyibpay = new ToyyibpayController();

        return $toyyibpay->makePayment($request, $id);
      } else if ($request['gateway'] == 'phonepe') {
        $phonepe = new PhonepeController();

        return $phonepe->makePayment($request, $id);
      } else if ($request['gateway'] == 'yoco') {
        $yoco = new YocoController();

        return $yoco->makePayment($request, $id);
      } else if ($request['gateway'] == 'xendit') {
        $xendit = new XenditController();

        return $xendit->makePayment($request, $id);
      } else if ($request['gateway'] == 'myfatoorah') {
        $xendit = new MyFatoorahController();

        return $xendit->makePayment($request, $id);
      } else if ($request['gateway'] == 'perfect_money') {
        $perfect_money = new PerfectMoneyController();

        return $perfect_money->makePayment($request, $id);
      } else {
        $offline = new OfflineController();
        return $offline->bookingProcess($request, $id);
      }
    } else {
      try {
        $total = $request->total;
        //tax and commission end, handling fee
        $basicSetting = Basic::select('commission', 'percent_handling_fee')->first();

        $handling_fee_amount = ($total * $basicSetting->percent_handling_fee) / 100;

        $event = json_decode($request->event, true);
        $arrData = array(
          'event_id' => $event['id'],
          'price' => 0,
          'tax' => 0,
          'commission' => 0,
          'percent_handling_fee' => $handling_fee_amount,
          'quantity' => $request->quantity,
          'discount' => 0,
          'total_early_bird_dicount' => 0,
          'currencyText' => null,
          'currencyTextPosition' => null,
          'currencySymbol' => null,
          'currencySymbolPosition' => null,
          'fname' => $request->fname,
          'lname' => $request->lname,
          'email' => $request->email,
          'phone' => $request->phone,
          'country' => $request->country,
          'state' => $request->state,
          'city' => $request->city,
          'zip_code' => $request->city,
          'address' => $request->address,
          'paymentMethod' => null,
          'gatewayType' => null,
          'paymentStatus' => 'free',
          'paymentStatusBooking' => 'free',
          'event_date' => Session::get('event_date'),
          'form_type' => $request->form_type,
        );
        $bookingInfo = $this->storeData($arrData);
        // generate an invoice in pdf format
        $invoice = $this->generateInvoice($bookingInfo, $event['id']);
        //unlink qr code
        @mkdir(public_path('assets/admin/qrcodes/'), 0775, true);
        @unlink(public_path('assets/admin/qrcodes/') . $bookingInfo->booking_id . '.svg');
        //end unlink qr code

        // then, update the invoice field info in database
        $bookingInfo->update(['invoice' => $invoice]);

        // send a mail to the customer with the invoice
        $this->sendMail($bookingInfo);

        $request->session()->forget('event_id');
        $request->session()->forget('selTickets');
        $request->session()->forget('arrData');
        $request->session()->forget('discount');
        Session::flash('success', 'Booking successfully');
        return redirect()->route('customer.booking_details', $bookingInfo->id);
      } catch (\Throwable $th) {
        Log::build([
          'driver' => 'single',
          'path' => storage_path('logs/booking-out-event-tournament-' . time() . '.log'),
        ])->error($th->getMessage());
        return view('errors.404');
      }
    }
  }

  public function booking_tournament(Request $request, $id)
  {
    // dd($request->all());
    $basic = Basic::select('event_guest_checkout_status')->first();
    if ($basic->event_guest_checkout_status == 0 && $request->type != 'guest') {
      // check whether user is logged in or not
      if (Auth::guard('customer')->check() == false) {
        return redirect()->route('customer.login', ['redirectPath' => 'course_details']);
      }
    }

    // payment
    if ($request->total != 0 || Session::get('sub_total') != 0) {

      if (!$request->exists('gateway')) {
        Session::flash('error', 'Please select a payment method.');

        return redirect()->back();
      } else if ($request['gateway'] == 'paypal') {
        $paypal = new PayPalController();

        return $paypal->bookingProcess($request, $id);
      } else if ($request['gateway'] == 'razorpay') {
        $razorpay = new RazorpayController();

        return $razorpay->bookingProcess($request, $id);
      } else if ($request['gateway'] == 'instamojo') {
        $instamojo = new InstamojoController();

        return $instamojo->bookingProcess($request, $id);
      } else if ($request['gateway'] == 'paystack') {
        $paystack = new PaystackController();

        return $paystack->bookingProcess($request, $id);
      } else if ($request['gateway'] == 'flutterwave') {
        $flutterwave = new FlutterwaveController();

        return $flutterwave->bookingProcess($request, $id);
      } else if ($request['gateway'] == 'mercadopago') {
        $mercadopago = new MercadoPagoController();

        return $mercadopago->bookingProcess($request, $id);
      } else if ($request['gateway'] == 'mollie') {
        $mollie = new MollieController();

        return $mollie->bookingProcess($request, $id);
      } else if ($request['gateway'] == 'stripe') {
        $stripe = new StripeController();

        return $stripe->bookingProcess($request, $id);
      } else if ($request['gateway'] == 'paytm') {
        $paytm = new PaytmController();

        return $paytm->bookingProcess($request, $id);
      } else if ($request['gateway'] == 'midtrans') {
        Session::put('midtrans_payment_type', 'event');
        $paytm = new MidtransController();

        return $paytm->makePayment($request, $id);
      } else if ($request['gateway'] == 'iyzico') {
        $paytm = new IyzipayController();

        return $paytm->makePayment($request, $id);
      } else if ($request['gateway'] == 'paytabs') {
        $paytabs = new PaytabsController();

        return $paytabs->makePayment($request, $id);
      } else if ($request['gateway'] == 'toyyibpay') {
        $toyyibpay = new ToyyibpayController();

        return $toyyibpay->makePayment($request, $id);
      } else if ($request['gateway'] == 'phonepe') {
        $phonepe = new PhonepeController();

        return $phonepe->makePayment($request, $id);
      } else if ($request['gateway'] == 'yoco') {
        $yoco = new YocoController();

        return $yoco->makePayment($request, $id);
      } else if ($request['gateway'] == 'xendit') {
        $xendit = new XenditController();

        return $xendit->makePayment($request, $id);
      } else if ($request['gateway'] == 'myfatoorah') {
        $xendit = new MyFatoorahController();

        return $xendit->makePayment($request, $id);
      } else if ($request['gateway'] == 'perfect_money') {
        $perfect_money = new PerfectMoneyController();

        return $perfect_money->makePayment($request, $id);
      } else {
        $offline = new OfflineController();
        return $offline->bookingProcess($request, $id);
      }
    } else {
      try {
        $event = json_decode($request->event, true);
        $cust = Auth::guard('customer')->user();
        $arrData = array(
          'event_id' => $id,
          'price' => 0,
          'tax' => 0,
          'commission' => 0,
          'percent_handling_fee' => 0,
          'quantity' => $request->quantity,
          'discount' => 0,
          'total_early_bird_dicount' => 0,
          'currencyText' => null,
          'currencyTextPosition' => null,
          'currencySymbol' => null,
          'currencySymbolPosition' => null,
          'fname' => $cust->fname,
          'lname' => empty($cust->lname) ? $cust->fname : $cust->lname,
          'email' => $cust->email,
          'phone' => $cust->phone,
          'country' => $cust->country,
          'state' => $cust->state,
          'city' => empty($cust->city) ? $cust->state : $cust->city,
          'zip_code' => empty($cust->city) ? $cust->state : $cust->city,
          'address' => empty($cust->city) ? $cust->state : $cust->city,
          // 'paymentMethod' => 'Xendit',
          // 'gatewayType' => 'online',
          'paymentMethod' => null,
          'gatewayType' => null,
          'paymentStatus' => 'free',
          'paymentStatusBooking' => 'free',
          'ticketInfos' => json_decode($request->request_ticket_infos),
          'dataOrders' => json_decode($request->request_orders),
          'form_type' => 'tournament',
          'event_date' => Session::get('event_date')
        );

        $bookingInfo = $this->storeData($arrData);
        // generate an invoice in pdf format
        $invoice = $this->generateInvoice($bookingInfo, $id);
        //unlink qr code
        @mkdir(public_path('assets/admin/qrcodes/'), 0775, true);
        @unlink(public_path('assets/admin/qrcodes/') . $bookingInfo->booking_id . '.svg');
        //end unlink qr code

        // then, update the invoice field info in database
        $bookingInfo->update(['invoice' => $invoice]);

        // send a mail to the customer with the invoice
        $this->sendMail($bookingInfo);

        $request->session()->forget('event_id');
        $request->session()->forget('selTickets');
        $request->session()->forget('arrData');
        $request->session()->forget('discount');

        return redirect()->route('event_booking.complete', ['id' => $id, 'booking_id' => $bookingInfo->id, 'via' => 'offline']); //code...
      } catch (\Throwable $th) {
        Log::build([
          'driver' => 'single',
          'path' => storage_path('logs/booking-tournament-' . time() . '.log'),
        ])->error($th->getMessage());
        return view('errors.404');
      }
    }
  }

  public function booking_tournament_pending(Request $request, $id)
  {
    $basic = Basic::select('event_guest_checkout_status')->first();
    if ($basic->event_guest_checkout_status == 0 && $request->type != 'guest') {
      // check whether user is logged in or not
      if (Auth::guard('customer')->check() == false) {
        return redirect()->route('customer.login', ['redirectPath' => 'course_details']);
      }
    }

    // payment
    if ($request->total != 0 || Session::get('sub_total') != 0) {

      $xendit = new XenditController();
      return $xendit->makePayment($request, $id);
      $offline = new OfflineController();
      return $offline->bookingProcess($request, $id);
    } else {
      try {
        $event = json_decode($request->event, true);
        $arrData = array(
          'event_id' => $event['id'],
          'price' => 0,
          'tax' => 0,
          'commission' => 0,
          'quantity' => $request->quantity,
          'discount' => 0,
          'total_early_bird_dicount' => 0,
          'currencyText' => null,
          'currencyTextPosition' => null,
          'currencySymbol' => null,
          'currencySymbolPosition' => null,
          'fname' => $request->fname,
          'lname' => $request->lname,
          'email' => $request->email,
          'phone' => $request->phone,
          'country' => $request->country,
          'state' => $request->state,
          'city' => $request->city,
          'zip_code' => $request->city,
          'address' => $request->address,
          'paymentMethod' => null,
          'gatewayType' => null,
          'paymentStatus' => 'free',
          'event_date' => Session::get('event_date')
        );

        $bookingInfo = $this->storeData($arrData);

        // generate an invoice in pdf format
        $invoice = $this->generateInvoice($bookingInfo, $event['id']);
        //unlink qr code
        @mkdir(public_path('assets/admin/qrcodes/'), 0775, true);
        @unlink(public_path('assets/admin/qrcodes/') . $bookingInfo->booking_id . '.svg');
        //end unlink qr code

        // then, update the invoice field info in database
        $bookingInfo->update(['invoice' => $invoice]);

        // send a mail to the customer with the invoice
        $this->sendMail($bookingInfo);

        $request->session()->forget('event_id');
        $request->session()->forget('selTickets');
        $request->session()->forget('arrData');
        $request->session()->forget('discount');

        return redirect()->route('event_booking.complete', ['id' => $event['id'], 'booking_id' => $bookingInfo->id, 'via' => 'offline']); //code...
      } catch (\Throwable $th) {
        Log::build([
          'driver' => 'single',
          'path' => storage_path('logs/booking-tournament-pending-' . time() . '.log'),
        ])->error($th->getMessage());
        return view('errors.404');
      }
    }
  }

  public function storeData($info)
  {
    try {
      $event = Event::where('id', $info['event_id'])->first();

      if ($event) {
        if ($event->organizer_id) {
          $organizer_id = $event->organizer_id;
        } else {
          $organizer_id = null;
        }
      }

      if ($info['form_type'] == "tournament" || $info['form_type'] == "turnamen") {
        $variations = $info['ticketInfos'];
      } else {
        $variations = Session::get('selTickets');
      }
      // dd($info, $variations);

      if ($variations) {

        foreach ($variations as $variation) {
          if ($info['form_type'] == "tournament" || $info['form_type'] == "turnamen") {
            $variation = (array) $variation;
          }

          $ticket = Ticket::where('id', $variation['ticket_id'])->first();

          if ($ticket->pricing_type == 'normal' && $ticket->ticket_available_type == 'limited') {

            if ($info['form_type'] == "tournament" || $info['form_type'] == "turnamen") {
              if ($ticket->ticket_available - $variation['quantity'] >= 0) {
                $ticket->ticket_available = $ticket->ticket_available - $variation['quantity'];
                $ticket->save();
              }
            } else {
              if ($ticket->ticket_available - $variation['qty'] >= 0) {
                $ticket->ticket_available = $ticket->ticket_available - $variation['qty'];
                $ticket->save();
              }
            }
          } elseif ($ticket->pricing_type == 'variation') {
            $ticket_variations =  json_decode($ticket->variations, true);
            $update_variation = [];
            foreach ($ticket_variations as $ticket_variation) {
              if ($ticket_variation['name']  == $variation['name']) {

                if ($ticket_variation['ticket_available_type'] == 'limited') {
                  $ticket_available = intval($ticket_variation['ticket_available']) - intval($variation['qty']);
                } else {
                  $ticket_available = $ticket_variation['ticket_available'];
                }

                $update_variation[] = [
                  'name' => $ticket_variation['name'],
                  'price' => round($ticket_variation['price'], 2),
                  'ticket_available_type' => $ticket_variation['ticket_available_type'],
                  'ticket_available' => $ticket_available,
                  'max_ticket_buy_type' => $ticket_variation['max_ticket_buy_type'],
                  'v_max_ticket_buy' => $ticket_variation['v_max_ticket_buy'],
                ];
              } else {
                $update_variation[] = [
                  'name' => $ticket_variation['name'],
                  'price' => round($ticket_variation['price'], 2),
                  'ticket_available_type' => $ticket_variation['ticket_available_type'],
                  'ticket_available' => $ticket_variation['ticket_available'],
                  'max_ticket_buy_type' => $ticket_variation['max_ticket_buy_type'],
                  'v_max_ticket_buy' => $ticket_variation['v_max_ticket_buy'],
                ];
              }
            }
            $ticket->variations = json_encode($update_variation, true);
            $ticket->save();
          } elseif ($ticket->pricing_type == 'free' && $ticket->ticket_available_type == 'limited') {

            if ($info['form_type'] == "tournament" || $info['form_type'] == "turnamen") {
              if ($ticket->ticket_available - $variation['quantity'] >= 0) {
                $ticket->ticket_available = $ticket->ticket_available - $variation['quantity'];
                $ticket->save();
              }
            } else {
              if ($ticket->ticket_available - $variation['qty'] >= 0) {
                $ticket->ticket_available = $ticket->ticket_available - $variation['qty'];
                $ticket->save();
              }
            }
          }

          if ($info['form_type'] == "tournament" || $info['form_type'] == "turnamen") {
            $variations_ticket[] = [
              "ticket_id" => $variation['ticket_id'],
              "early_bird_dicount" => 0,
              "name" => $variation['title'],
              "qty" => $variation['quantity'],
              "price" => $variation['price'],
            ];
          }
        }

        if ($info['form_type'] == "tournament" || $info['form_type'] == "turnamen") {
          $variations = json_encode($variations_ticket, true);
        } else {
          $variations = json_encode(Session::get('selTickets'), true);
        }
      } else {
        $ticket = $event->ticket()->first();
        $ticket->ticket_available = $ticket->ticket_available - (int)$info['quantity'];
        $ticket->save();
      }

      $basic  = Basic::where('uniqid', 12345)->select('tax', 'commission')->first();
      $booking_unique_id = uniqid() . time();

      $booking = Booking::create([
        'customer_id' => Auth::guard('customer')->user() ? Auth::guard('customer')->user()->id : null,
        'booking_id' => $booking_unique_id,
        'fname' => $info['fname'],
        'lname' => $info['lname'],
        'email' => $info['email'],
        'phone' => $info['phone'],
        'country' => $info['country'],
        'state' => $info['state'],
        'city' => $info['city'],
        'zip_code' => $info['zip_code'],
        'address' => $info['address'],
        'event_id' => $info['event_id'],
        'organizer_id' => $organizer_id,
        'variation' => $variations,
        'price' => round($info['price'], 2),
        'tax' => round($info['tax'], 2),
        'commission' => round($info['commission'], 2),
        'handling_fee_amount' => round($info['percent_handling_fee'], 2),
        'tax_percentage' => $basic->tax,
        'commission_percentage' => $basic->commission,
        'quantity' => $info['quantity'],
        'discount' => round($info['discount'], 2),
        'early_bird_discount' => round($info['total_early_bird_dicount'], 2),
        'currencyText' => $info['currencyText'],
        'currencyTextPosition' => $info['currencyTextPosition'],
        'currencySymbol' => $info['currencySymbol'],
        'currencySymbolPosition' => $info['currencySymbolPosition'],
        'paymentMethod' => $info['paymentMethod'],
        'gatewayType' => $info['gatewayType'],
        'paymentStatus' => $info['paymentStatus'],
        'paymentStatusBooking' => $info['paymentStatusBooking'],
        'invoice' => array_key_exists('attachmentFile', $info) ? $info['attachmentFile'] : null,
        'attachmentFile' => array_key_exists('attachmentFile', $info) ? $info['attachmentFile'] : null,
        'event_date' => Session::get('event_date'),
        'conversation_id' => array_key_exists('conversation_id', $info) ? $info['conversation_id'] : null,
      ]);

      if ($info['form_type'] == "tournament" || $info['form_type'] == "turnamen") {
        $dataOrders = $info['dataOrders'];
        foreach ($dataOrders as $d) {
          $ticket_detail_individu_order = $d->ticket_detail_individu_order;
          $ticket_detail_official_order = $d->ticket_detail_official_order;

          // return ['ticket_detail_individu_order' => $ticket_detail_individu_order, 'ticket_detail_official_order' => $ticket_detail_official_order];

          if ($ticket_detail_individu_order) {
            foreach ($ticket_detail_individu_order as $valueDataOrderIndividu) {
              if ($valueDataOrderIndividu->county_id == "102") { //Indonesia
                if ($valueDataOrderIndividu->city_id) {
                  $city_name = IndonesianCities::select('id', 'name')->where('id', $valueDataOrderIndividu->city_id)->first();
                }
              } else {
                if ($valueDataOrderIndividu->city_id) {
                  $city_name = InternationalCities::select('id', 'name')->where('country_id', $valueDataOrderIndividu->county_id)->where('id', $valueDataOrderIndividu->city_id)->first();
                }
              }

              $data_participant['fname'] = $valueDataOrderIndividu->user_full_name;
              $data_participant['lname'] = null;
              // $data_participant['gender'] = ($valueDataOrderIndividu->user_gender == 'male') ? 'M' : 'F';
              $data_participant['gender'] = $valueDataOrderIndividu->user_gender;
              $data_participant['birthdate'] = $valueDataOrderIndividu->birthdate;
              $username = HelperUser::AutoGenerateUsernameParticipant($data_participant);

              $checkParticipant = Participant::where('fname', $data_participant['fname'])
                ->where('lname', $data_participant['lname'])
                ->where('gender', $data_participant['gender'])
                ->where('birthdate', $data_participant['birthdate'])
                ->first();

              if (!$checkParticipant) {
                // Save to table participant
                $input['fname'] = $valueDataOrderIndividu->user_full_name;
                $input['lname'] = null;
                $input['gender'] = $valueDataOrderIndividu->user_gender;
                $input['birthdate'] = $valueDataOrderIndividu->birthdate;
                $input['county_id'] = $valueDataOrderIndividu->county_id;
                $input['country'] = $valueDataOrderIndividu->country_name;
                $input['city_id'] = $valueDataOrderIndividu->city_id;
                $input['city'] = empty($city_name->name) ? null : $city_name->name;
                $input['username'] = $username;
                $peserta = Participant::create($input);
                $participant_id = $peserta->id;
              } else {
                $participant_id = $checkParticipant->id;
              }

              ParticipantByCustomer::create(['participant_id' => $participant_id, 'customer_id' => $info['customer_id']]);

              $delegation_id = null;
              $country_id = null;
              $province_id = null;

              switch (strtolower($valueDataOrderIndividu->delegation_type)) {
                case 'country':
                  $delegation_id = empty($valueDataOrderIndividu->country_delegation) ? null : $valueDataOrderIndividu->country_delegation;
                  break;
                case 'province':
                  $delegation_id = empty($valueDataOrderIndividu->province_delegation) ? null : $valueDataOrderIndividu->province_delegation;
                  $country_id = empty($valueDataOrderIndividu->country_delegation) ? null : $valueDataOrderIndividu->country_delegation;
                  break;
                case 'city/district':
                  $country_id = empty($valueDataOrderIndividu->country_delegation) ? null : $valueDataOrderIndividu->country_delegation;
                  $province_id = empty($valueDataOrderIndividu->province_delegation) ? null : $valueDataOrderIndividu->province_delegation;
                  $delegation_id = empty($valueDataOrderIndividu->city_delegation) ? null : $valueDataOrderIndividu->city_delegation;
                  break;
                case 'school/universities':
                  $schooldId = null;
                  $checkSchool = School::where('id', $valueDataOrderIndividu->school_id)->first();

                  if ($checkSchool) {
                    $schooldId = $valueDataOrderIndividu->school_id;
                  } else {
                    $newSchool = School::create(['name' => $valueDataOrderIndividu->school_name]);
                    $schooldId = $newSchool['id'];
                  }

                  $delegation_id = $schooldId;
                  break;
                case 'organization':
                  $organization_id = null;
                  $checkOrganization = Organization::where('id', $valueDataOrderIndividu->organization_id)->first();

                  if ($checkOrganization) {
                    $organization_id = $valueDataOrderIndividu->organization_id;
                  } else {
                    $newOrganization = Organization::create(['name' => $valueDataOrderIndividu->organization_name]);
                    $organization_id = $newOrganization['id'];
                  }
                  $delegation_id = $organization_id;
                  break;
                default:
                  $clubId = null;
                  $checkClub = Clubs::where('id', $valueDataOrderIndividu->club_id)->first();

                  if ($checkClub) {
                    $clubId = $valueDataOrderIndividu->club_id;
                  } else {
                    $newClub = Clubs::create(['name' => $valueDataOrderIndividu->club_name]);
                    $clubId = $newClub['id'];
                  }
                  $delegation_id = $clubId;
                  // $delegation_id = empty($valueDataOrderIndividu->club_id) ? null : $valueDataOrderIndividu->club_id;
                  break;
              }

              $p['competition_name'] = $valueDataOrderIndividu->sub_category_ticket;
              $p['event_id'] = $info['event_id'];
              $p['participant_id'] = $participant_id;
              $p['ticket_id'] = $valueDataOrderIndividu->ticket_id;
              $p['booking_id'] = $booking->id;
              $p['category'] = $valueDataOrderIndividu->delegation_type;
              $p['delegation_id'] = $delegation_id;
              $p['customer_id'] = Auth::guard('customer')->user()->id;
              $p['description'] = null;
              $p['country_id'] = $country_id;
              $p['province_id'] = $province_id;
              ParticipantCompetitions::create($p);
            }
          }

          if ($ticket_detail_official_order) {
            foreach ($ticket_detail_official_order as $valueDataOrderOfficial) {
              if ($valueDataOrderOfficial->county_id == "102") { //Indonesia
                if ($valueDataOrderOfficial->city_id) {
                  $city_name = IndonesianCities::select('id', 'name')->where('id', $valueDataOrderOfficial->city_id)->first();
                }
              } else {

                if ($valueDataOrderOfficial->city_id) {
                  $city_name = InternationalCities::select('id', 'name')->where('country_id', $valueDataOrderOfficial->county_id)->where('id', $valueDataOrderOfficial->city_id)->first();
                }
              }

              $data_participant['fname'] = $valueDataOrderOfficial->user_full_name;
              $data_participant['lname'] = null;
              // $data_participant['gender'] = ($valueDataOrderOfficial->user_gender == 'male') ? 'M' : 'F';
              $data_participant['gender'] = $valueDataOrderOfficial->user_gender;
              $data_participant['birthdate'] = $valueDataOrderOfficial->birthdate;
              $username = HelperUser::AutoGenerateUsernameParticipant($data_participant);

              $checkParticipant = Participant::where('fname', $data_participant['fname'])
                ->where('lname', $data_participant['lname'])
                ->where('gender', $data_participant['gender'])
                ->where('birthdate', $data_participant['birthdate'])
                ->first();

              if (!$checkParticipant) {
                // Save to table participant
                $input['fname'] = $valueDataOrderOfficial->user_full_name;
                $input['lname'] = null;
                // $input['gender'] = ($valueDataOrderOfficial->user_gender == 'male') ? 'M' : 'F';
                $input['gender'] = $valueDataOrderOfficial->user_gender;
                $input['birthdate'] = $valueDataOrderOfficial->birthdate;
                $input['county_id'] = $valueDataOrderOfficial->county_id;
                $input['country'] = $valueDataOrderOfficial->country_name;
                $input['city_id'] = $valueDataOrderOfficial->city_id;
                $input['city'] = empty($city_name->name) ? null : $city_name->name;
                $input['username'] = $username;
                $peserta = Participant::create($input);
                $participant_id = $peserta->id;
              } else {
                $participant_id = $checkParticipant->id;
              }

              ParticipantByCustomer::create(['participant_id' => $participant_id, 'customer_id' => $info['customer_id']]);

              $delegation_id = null;
              $country_id = null;
              $province_id = null;

              switch (strtolower($valueDataOrderOfficial->delegation_type)) {
                case 'country':
                  $delegation_id = empty($valueDataOrderOfficial->country_delegation) ? null : $valueDataOrderOfficial->country_delegation;
                  break;
                case 'province':
                  $delegation_id = empty($valueDataOrderOfficial->province_delegation) ? null : $valueDataOrderOfficial->province_delegation;
                  $country_id = empty($valueDataOrderOfficial->country_delegation) ? null : $valueDataOrderOfficial->country_delegation;
                  break;
                case 'city/district':
                  $country_id = empty($valueDataOrderOfficial->country_delegation) ? null : $valueDataOrderOfficial->country_delegation;
                  $province_id = empty($valueDataOrderOfficial->province_delegation) ? null : $valueDataOrderOfficial->province_delegation;
                  $delegation_id = empty($valueDataOrderOfficial->city_delegation) ? null : $valueDataOrderOfficial->city_delegation;
                  break;
                case 'school/universities':
                  $schooldId = null;
                  $checkSchool = School::where('id', $valueDataOrderOfficial->school_id)->first();

                  if ($checkSchool) {
                    $schooldId = $valueDataOrderOfficial->school_id;
                  } else {
                    $newSchool = School::create(['name' => $valueDataOrderOfficial->school_name]);
                    $schooldId = $newSchool['id'];
                  }

                  $delegation_id = $schooldId;
                  break;
                case 'organization':
                  $organization_id = null;
                  $checkOrganization = Organization::where('id', $valueDataOrderOfficial->organization_id)->first();

                  if ($checkOrganization) {
                    $organization_id = $valueDataOrderOfficial->organization_id;
                  } else {
                    $newOrganization = Organization::create(['name' => $valueDataOrderOfficial->organization_name]);
                    $organization_id = $newOrganization['id'];
                  }
                  $delegation_id = $organization_id;
                  break;
                default:
                  $clubId = null;
                  $checkClub = Clubs::where('id', $valueDataOrderOfficial->club_id)->first();

                  if ($checkClub) {
                    $clubId = $valueDataOrderOfficial->club_id;
                  } else {
                    $newClub = Clubs::create(['name' => $valueDataOrderOfficial->club_name]);
                    $clubId = $newClub['id'];
                  }
                  $delegation_id = $clubId;
                  break;
              }

              $p['competition_name'] = 'Official';
              $p['event_id'] = $info['event_id'];
              $p['participant_id'] = $participant_id;
              $p['ticket_id'] = $valueDataOrderOfficial->ticket_id;
              $p['booking_id'] = $booking->id;
              $p['category'] = $valueDataOrderOfficial->delegation_type;
              $p['delegation_id'] = $delegation_id;
              $p['customer_id'] = Auth::guard('customer')->user()->id;
              $p['description'] = null;
              $p['country_id'] = $country_id;
              $p['province_id'] = $province_id;
              ParticipantCompetitions::create($p);
            }
          }
        }
      }

      return $booking;
    } catch (\Exception $th) {
      Log::build([
        'driver' => 'single',
        'path' => storage_path('logs/booking-generate-payment-' . time() . '.log'),
      ])->error($th->getMessage());
    }
  }

  public function complete(Request $request)
  {
    $language = $this->getLanguage();

    Session::forget('selTickets');
    Session::forget('total');
    Session::forget('quantity');
    Session::forget('total_early_bird_dicount');
    Session::forget('event');

    $id = $request->id;
    $booking_id = $request->booking_id;

    $booking = Booking::where('id', $booking_id)->first();
    $information['booking'] = $booking;
    $event = Event::where('id', $id)->with([
      'information' => function ($query) use ($language) {
        return $query->where('language_id', $language->id)->first();
      }
    ])->first();
    $information['event'] = $event;
    if ($event->date_type == 'multiple') {
      $start_date_time = strtotime($booking->event_date);
      $start_date_time = date('Y-m-d H:i:s', $start_date_time);

      $event_date = EventDates::where('start_date_time', $start_date_time)->where('event_id', $id)->first();

      $information['event_date'] = $event_date;
    }
    return view('frontend.payment.success', $information);
  }

  public function cancel($id, Request $request)
  {
    return redirect()->route('check-out');
  }

  public function sendMail($bookingInfo)
  {
    if ($bookingInfo->paymentStatusBooking == 'pending') {
      // first get the mail template info from db
      $mailTemplate = MailTemplate::where('mail_type', 'event_booking_pending')->first();
      $mailSubject = $mailTemplate->mail_subject;
      $mailBody = $mailTemplate->mail_body;
    } else {
      // first get the mail template info from db
      $mailTemplate = MailTemplate::where('mail_type', 'event_booking')->first();
      $mailSubject = $mailTemplate->mail_subject;
      $mailBody = $mailTemplate->mail_body;
    }

    // second get the website title & mail's smtp info from db
    $info = DB::table('basic_settings')
      ->select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
      ->first();

    $customerName = $bookingInfo->fname . ' ' . $bookingInfo->lname;
    $orderId = $bookingInfo->booking_id;

    $language = $this->getLanguage();
    $eventContent = EventContent::where('event_id', $bookingInfo->event_id)->where('language_id', $language->id)->first();
    $eventTitle = $eventContent ? $eventContent->title : '';

    $websiteTitle = $info->website_title;

    $mailBody = str_replace('{customer_name}', $customerName, $mailBody);
    $mailBody = str_replace('{order_id}', $orderId, $mailBody);
    $mailBody = str_replace('{title}', '<a href="' . route('event.details', [$eventContent->slug, $eventContent->event_id]) . '">' . $eventTitle . '</a>', $mailBody);
    $mailBody = str_replace('{website_title}', $websiteTitle, $mailBody);

    if ($bookingInfo->paymentStatusBooking == 'pending') {
      $mailBody = str_replace('{complete_your_payment}', '<a href="' . $bookingInfo->invoice_url_booking . '">Complete Your Payment</a>', $mailBody);
    }

    // initialize a new mail
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // if smtp status == 1, then set some value for PHPMailer
    if ($info->smtp_status == 1) {
      $mail->isSMTP();
      $mail->Host       = $info->smtp_host;
      $mail->SMTPAuth   = true;
      $mail->Username   = $info->smtp_username;
      $mail->Password   = $info->smtp_password;

      if ($info->encryption == 'TLS') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      }

      $mail->Port       = $info->smtp_port;
    }

    // finally add other informations and send the mail
    try {
      // Recipients
      $mail->setFrom($info->from_mail, $info->from_name);
      $mail->addAddress($bookingInfo->email);

      // Attachments (Invoice)
      $mail->addAttachment(public_path('assets/admin/file/invoices/') . $bookingInfo->invoice);

      // Content
      $mail->isHTML(true);
      $mail->Subject = $mailSubject;
      $mail->Body    = $mailBody;

      $mail->send();

      return;
    } catch (\Exception $e) {
      return session()->flash('error', 'Mail could not be sent! Mailer Error: ' . $e);
    }
  }

  public function generateInvoice($bookingInfo, $eventId)
  {
    try {
      $fileName = $bookingInfo->booking_id . '.pdf';
      $directory = public_path('assets/admin/file/invoices/');

      @mkdir($directory, 0775, true);

      $fileLocated = $directory . $fileName;

      //generate qr code
      @mkdir(public_path('assets/admin/qrcodes/'), 0775, true);
      QrCode::size(200)->generate($bookingInfo->booking_id, public_path('assets/admin/qrcodes/') . $bookingInfo->booking_id . '.svg');

      //generate qr code end

      // get course title
      $language = $this->getLanguage();

      $eventInfo = EventContent::where('event_id', $bookingInfo->event_id)->where('language_id', $language->id)->first();

      $width = "50%";
      $float = "right";
      $mb = "35px";
      $ml = "18px";

      PDF::loadView('frontend.event.invoice', compact('bookingInfo', 'eventInfo', 'width', 'float', 'mb', 'ml'))->save($fileLocated);

      return $fileName;
    } catch (\Exception $th) {
    }
  }
}
