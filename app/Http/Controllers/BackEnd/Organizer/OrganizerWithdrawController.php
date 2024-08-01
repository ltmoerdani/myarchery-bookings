<?php

namespace App\Http\Controllers\BackEnd\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\WithdrawRequest;
use App\Models\Language;
use App\Models\Organizer;
use App\Models\Transaction;
use App\Models\Withdraw;
use App\Models\WithdrawMethodInput;
use App\Models\WithdrawPaymentMethod;
use App\Models\BankAccount;
use App\Models\MasterBank;
use App\Models\Disbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\BasicSettings\Basic;
use App\Models\BasicSettings\MailTemplate;
use PHPMailer\PHPMailer\PHPMailer;

class OrganizerWithdrawController extends Controller{

  public function index(){
    $currencyInfo = $this->getCurrencyInfo();
    $collection = Withdraw::with('method')->where('organizer_id', Auth::guard('organizer')->user()->id)->orderby('id', 'desc')->get();
    return view('organizer.withdraw.index', compact('collection', 'currencyInfo'));
  }

  //create
  public function create(){
    $information = [];
    $language = Language::where('code', request()->input('language'))->firstOrFail();
    $lang_id = $language->id;
    $lang = Language::where('id', $lang_id)->firstOrFail();
    $methods = WithdrawPaymentMethod::where('status', '=', 1)->get();
    $information['lang'] = $lang;
    $information['methods'] = $methods;

    $account_bank = 1;
    $bank_account = BankAccount::with('bank')->where('organizer_id', Auth::guard('organizer')->user()->id)->get();
    if($bank_account->isEmpty()){
      $account_bank = 0;
    }
    $information['account_bank'] = $account_bank;
    $information['bank_account'] = $bank_account;
    return view('organizer.withdraw.create', $information);
  }

  //get_inputs
  public function get_inputs($id){
    $method = WithdrawPaymentMethod::find($id);
    $payment = $method->name;

    if(strtolower($payment) == 'xendit'){
      $data = BankAccount::with('bank')->where('organizer_id', Auth::guard('organizer')->user()->id)->where('is_active', 1)->get();
    }else{
      $data = WithdrawMethodInput::with('options')->where('withdraw_payment_method_id', $id)->orderBy('order_number', 'asc')->get();
    }

    return $data;
  }

  //balance_calculation
  public function balance_calculation($method, $amount){
    if (Auth::guard('organizer')->user()->amount < $amount) {
      return 'error';
    }
    $method = WithdrawPaymentMethod::find($method);
    $fixed_charge = $method->fixed_charge;
    $percentage = $method->percentage_charge;

    $percentage_balance = (($amount - $fixed_charge) * $percentage) / 100;

    $total_charge = $percentage_balance + $fixed_charge;
    $receive_balance = $amount - $total_charge;
    $user_balance = Auth::guard('organizer')->user()->amount - $amount;
    return ['total_charge' => round($total_charge, 2), 'receive_balance' => round($receive_balance, 2), 'user_balance' => round($user_balance, 2)];
  }

  //send disbursement
  public function send_disbursement($data, $header){    
    $id = $data['withdraw_id'];
    $bank_account = $data['bank_account'];
    $withdraw = Withdraw::where('id', $id)->first();
    if($withdraw->method_id == 6){
      $external_id = Str::random(10);
      $secret_key = 'Basic ' . config('xendit.key_auth');
      $data_request = Http::withHeaders([
          'Authorization' => $secret_key
      ])->post('https://api.xendit.co/disbursements', [
          'external_id' => $external_id,
          'amount' => $withdraw->payable_amount,
          'bank_code' => $bank_account->bank->bank_code,
          'account_holder_name' => $bank_account->account_name,
          'account_number' => $bank_account->account_no	,
          'description' => "Disbursements ".$withdraw->amount." (Withdraw ID ".$withdraw->withdraw_id.")",
      ]);
      $response = $data_request->object();
      $response = json_decode(json_encode($response), true);

      $disb['withdraw_id'] = $withdraw->withdraw_id;
      $disb['payment_type'] = 'Xendit';
      $disb['callback'] = json_encode($response);
      $disb['req_header'] = json_encode($header);
      $disb['callback_id'] = $response['id'];
      $disb['external_id'] = $response['external_id'];
      $disb['amount'] = $response['amount'];
      $disb['bank_code'] = $response['bank_code'];
      $disb['account_holder_name'] = $response['account_holder_name'];
      $disb['disbursement_description'] = $response['disbursement_description'];
      $disb['status'] = $response['status'];
      $disb['currency'] = 'IDR';
      $disb['description'] = null;
      $disb = Disbursement::create($disb);
    }

    //mail sending
    // get the website title & mail's smtp information from db
    $info = Basic::select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name', 'base_currency_symbol_position', 'base_currency_symbol')
      ->first();

    //preparing mail info
    // get the mail template info from db
    $mailTemplate = MailTemplate::query()->where('mail_type', '=', 'withdraw_approve')->first();
    $mailData['subject'] = $mailTemplate->mail_subject;
    $mailBody = $mailTemplate->mail_body;

    // get the website title info from db
    $website_info = Basic::select('website_title')->first();

    $organizer = $withdraw->organizer()->first();

    // preparing dynamic data
    $organizerName = $organizer->username;
    $organizerEmail = $organizer->email;
    $organizer_amount = $organizer->amount;
    $withdraw_amount = $withdraw->amount;
    $total_charge = $withdraw->total_charge;
    $payable_amount = $withdraw->payable_amount;

    $method = $withdraw->method()->select('name')->first();

    $websiteTitle = $website_info->website_title;

    // replacing with actual data
    $mailBody = str_replace('{organizer_username}', $organizerName, $mailBody);
    $mailBody = str_replace('{withdraw_id}', $withdraw->withdraw_id, $mailBody);

    $mailBody = str_replace('{current_balance}', $info->base_currency_symbol . $organizer_amount, $mailBody);
    $mailBody = str_replace('{withdraw_amount}', $info->base_currency_symbol . $withdraw_amount, $mailBody);
    $mailBody = str_replace('{charge}', $info->base_currency_symbol . $total_charge, $mailBody);
    $mailBody = str_replace('{payable_amount}', $info->base_currency_symbol . $payable_amount, $mailBody);

    $mailBody = str_replace('{withdraw_method}', $method->name, $mailBody);
    $mailBody = str_replace('{transaction_id}', $withdraw->withdraw_id, $mailBody);
    $mailBody = str_replace('{website_title}', $websiteTitle, $mailBody);

    $mailData['body'] = $mailBody;

    $mailData['recipient'] = $organizerEmail;
    //preparing mail info end

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

    // add other informations and send the mail
    try {
      $mail->setFrom($info->from_mail, $info->from_name);
      $mail->addAddress($mailData['recipient']);

      $mail->isHTML(true);
      $mail->Subject = $mailData['subject'];
      $mail->Body = $mailData['body'];

      $mail->send();
      Session::flash('success', 'Withdraw Request Approved Successfully!');
    } catch (Exception $e) {
      Session::flash('warning', 'Mail could not be sent. Mailer Error: ' . $mail->ErrorInfo);
    }

    // $withdraw->status = 1;
    $transcation = Transaction::where('booking_id', $withdraw->id)->where('transcation_type', 3)->first();
    if ($transcation) {
      $transcation->update(['payment_status' => 1]);
    }
    //mail sending end
    $withdraw->save();
    return redirect()->back();
  }

  //send_request
  public function send_request(WithdrawRequest $request){    
    $method = WithdrawPaymentMethod::where('id', $request->withdraw_method)->first();
    $organizer = Organizer::where('id', Auth::guard('organizer')->user()->id)->first();

    if (intval($request->withdraw_amount) < $method->min_limit) {
      return Response::json(
        [
          'errors' => [
            'withdraw_amount' => [
              'Minimum withdraw limit is ' . $method->min_limit
            ]
          ]
        ],
        400
      );
    } elseif (intval($request->withdraw_amount) > $method->max_limit) {
      return Response::json(
        [
          'errors' => [
            'withdraw_amount' => [
              'Maximum withdraw limit is ' . $method->max_limit
            ]
          ]
        ],
        400
      );
    } elseif ($organizer->amount < $request->withdraw_amount) {
      return Response::json(
        [
          'errors' => [
            'withdraw_amount' => [
              'You do not have enough balance to Withdraw.'
            ]
          ]
        ],
        400
      );
    }

    $rules = [
      'withdraw_method' => 'required',
      'withdraw_amount' => 'required'
    ];

    $inputs = WithdrawMethodInput::where('withdraw_payment_method_id', $request->withdraw_method)->orderBy('order_number', 'asc')->get();
    foreach ($inputs as $input) {
      if ($input->required == 1) {
        $rules["$input->name"] = 'required';
      }

      $fields = [];
      foreach ($inputs as $key => $input) {
        $in_name = $input->name;
        if ($request["$in_name"]) {
          $fields["$in_name"] = $request["$in_name"];
        }
      }
      $jsonfields = json_encode($fields);
      $jsonfields = str_replace("\/", "/", $jsonfields);;
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()
      ], 400);
    }

    //calculation
    $fixed_charge = $method->fixed_charge;
    $percentage = $method->percentage_charge;

    $percentage_balance = (($request->withdraw_amount - $fixed_charge) * $percentage) / 100;
    $total_charge = $percentage_balance + $fixed_charge;
    $receive_balance = $request->withdraw_amount - $total_charge;
    //calculation end

    $save = new Withdraw;
    $save->withdraw_id = uniqid();
    $save->organizer_id = Auth::guard('organizer')->user()->id;
    $save->method_id = $request->withdraw_method;

    $organizer = Organizer::where('id', Auth::guard('organizer')->user()->id)->first();
    $pre_balance = $organizer->amount;
    $organizer->amount = ($organizer->amount - ($request->withdraw_amount));
    $organizer->save();
    $after_balance = $organizer->amount;

    $save->amount = $request->withdraw_amount;
    $save->payable_amount = $receive_balance;
    $save->total_charge = $total_charge;
    $save->additional_reference = $request->additional_reference;

    if(strtolower($method->name) != 'xendit'){
      $save->feilds = json_encode($fields);
    }else{
      $bank_account['Account_Bank'] = $request->bank_account; 
      $save->feilds = json_encode($bank_account); 
    }
    
    $save->save();

    $data['withdraw_id'] = $save->id;
    $data['bank_account'] = BankAccount::with('bank')->where('organizer_id', Auth::guard('organizer')->user()->id)->where('id', $request->bank_account)->first();    
    $header = $request->header();
    $this->send_disbursement($data, $header);

    //store data to transcation table 
    $currencyInfo = $this->getCurrencyInfo();
    $transcation = Transaction::create([
      'transcation_id' => time(),
      'booking_id' => $save->id,
      'transcation_type' => 3,
      'user_id' => null,
      'organizer_id' => Auth::guard('organizer')->user()->id,
      'payment_status' => 0,
      'payment_method' => $save->method_id,
      'grand_total' => $save->amount,
      'pre_balance' => $pre_balance,
      'after_balance' => $after_balance,
      'gateway_type' => null,
      'currency_symbol' => $currencyInfo->base_currency_symbol,
      'currency_symbol_position' => $currencyInfo->base_currency_text_position,
    ]);

    Session::flash('success', 'Withdraw Request Send Successfully!');
    return Response::json(['status' => 'success'], 200);
  }

  //Delete
  public function Delete(Request $request)
  {
    $delete = Withdraw::where([['organizer_id', Auth::guard('organizer')->user()->id], ['id', $request->id]])->first();
    $delete->delete();
    return redirect()->back()->with('success', 'Withdraw Request Deleted Successfully!');
  }

  //bulkDelete
  public function bulkDelete(Request $request)
  {
    $ids = $request->ids;
    foreach ($ids as $id) {
      $withdraw = Withdraw::where([['organizer_id', Auth::guard('organizer')->user()->id], ['id', $id]])->first();
      $withdraw->delete();
    }
    Session::flash('success', 'Deleted Successfully');

    return Response::json(['status' => 'success'], 200);
  }

  //account
  public function account()
  {
    $account = BankAccount::with('bank')->where('organizer_id', Auth::guard('organizer')->user()->id)->orderby('id', 'asc')->paginate(10);
    return view('organizer.withdraw.account', compact('account'));
  }

  //add account
  public function addaccount()
  {
    $information = [];
    $language = Language::where('code', request()->input('language'))->firstOrFail();
    $lang_id = $language->id;
    $lang = Language::where('id', $lang_id)->firstOrFail();
    $bank = MasterBank::where('is_active', 1)->where('endpoint', 'disbursement')->where('type','Bank')->get();

    $information['lang'] = $lang;
    $information['bank'] = $bank;
    return view('organizer.withdraw.addaccount', $information);
  }

  //save_account
  public function save_account(Request $request){
    $organizer = Organizer::where('id', Auth::guard('organizer')->user()->id)->first();
    $rules = [
      'bank_name' => 'required',
      'account_number' => 'required',
      'account_name' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()
      ], 400);
    }

    $save = new BankAccount;
    $save->role = 'organizer';
    $save->organizer_id = Auth::guard('organizer')->user()->id;
    $save->bank_id = $request->bank_name;
    $save->account_no = $request->account_number;
    $save->account_name = $request->account_name;
    $save->is_active = 1;
    $save->save();

    Session::flash('success', 'Save Account Bank Successfully!');
    // return Response::json(['status' => 'success'], 200);
    return redirect()->route('organizer.withdraw.account');
  }
}
