<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Models\Admin;
use App\Models\BasicSettings\Basic;
use App\Models\Customer;
use App\Models\Earning;
use App\Models\Event;
use App\Models\Event\Booking;
use App\Models\Event\EventCategory;
use App\Models\Event\EventContent;
use App\Models\Journal\Blog;
use App\Models\Language;
use App\Models\Organizer;
use App\Models\ShopManagement\Product;
use App\Models\ShopManagement\ProductOrder;
use App\Models\Transaction;
use App\Models\ParticipantCompetitions;
use App\Rules\ImageMimeTypeRule;
use App\Rules\MatchEmailRule;
use App\Rules\MatchOldPasswordRule;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use App\Exports\ParticipantExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
  public function login()
  {
    return view('backend.login');
  }

  public function authentication(Request $request)
  {
    $rules = [
      'username' => 'required',
      'password' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    if (
      Auth::guard('admin')->attempt([
        'username' => $request->username,
        'password' => $request->password
      ])
    ) {
      $authAdmin = Auth::guard('admin')->user();

      // check whether the admin's account is active or not
      if ($authAdmin->status == 0) {
        Session::flash('alert', 'Sorry, your account has been deactivated!');

        // logout auth admin as condition not satisfied
        Auth::guard('admin')->logout();

        return redirect()->back();
      } else {
        return redirect()->route('admin.dashboard');
      }
    } else {
      return redirect()->back()->with('alert', 'Oops, username or password does not match!');
    }
  }

  public function forgetPassword()
  {
    return view('backend.forget-password');
  }

  public function sendMail(Request $request)
  {
    $rules = [
      'email' => [
        'required',
        'email:rfc,dns',
        new MatchEmailRule('admin')
      ]
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors())->withInput();
    }

    // create a new password and store it in db
    $newPassword = uniqid();

    $admin = Admin::where('email', $request->email)->first();

    $admin->update([
      'password' => Hash::make($newPassword)
    ]);

    // send newly created password to admin via email
    $info = DB::table('basic_settings')
      ->select('smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
      ->first();

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
      $mail->setFrom($info->from_mail, $info->from_name);
      $mail->addAddress($request->email);

      $mail->isHTML(true);
      $mail->Subject = 'Reset Password';
      $mail->Body = 'Hello ' . $admin->first_name . ',<br/><br/>Your password has reset. Your new password is: ' . $newPassword . '<br/><br/>Now, you can login with your new password. You can change your password later.<br/><br/>Thank you.';

      $mail->send();

      Session::flash('success', 'A mail has been sent to your email address.');
    } catch (Exception $e) {
      Session::flash('warning', 'Mail could not be sent. Mailer Error: ' . $mail->ErrorInfo);
    }

    return redirect()->back();
  }

  public function redirectToDashboard()
  {
    $language = Language::query()->where('is_default', '=', 1)->first();

    $information['basic'] = Basic::where('uniqid', 12345)->select('base_currency_symbol', 'base_currency_symbol_position')->first();

    $information['totalEvents'] = Event::query()->count();
    $information['totalEventCategories'] = EventCategory::where('language_id', $language->id)->count();
    $information['totalEventBookings'] = Booking::query()->count();
    $information['totalOrganizers'] = Organizer::query()->count();
    $information['totalBlog'] = Blog::query()->count();
    $information['totalRegisteredUsers'] = Customer::query()->count();
    $information['totalProducts'] = Product::query()->count();
    $information['totalOrders'] = ProductOrder::query()->count();
    $information['transcation_count'] = Transaction::query()->count();

    $information['total_earning'] = Earning::first();


    //income of event bookings 
    $eventBookingTotalIncomes = DB::table('bookings')
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(price) as total'))
      ->where('paymentStatus', '=', 'completed')
      ->groupBy('month')
      ->whereYear('created_at', '=', date('Y'))
      ->get();
    //income from tax
    $monthWiseTotaltaxs = DB::table('bookings')
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(tax) as total'))
      ->where('paymentStatus', '=', 'completed')
      ->groupBy('month')
      ->whereYear('created_at', '=', date('Y'))
      ->get();

    $TotalEventBookings = DB::table('bookings')
      ->select(DB::raw('month(created_at) as month'), DB::raw('count(id) as total'))
      ->where('paymentStatus', '=', 'completed')
      ->groupBy('month')
      ->whereYear('created_at', '=', date('Y'))
      ->get();

    //income of Product Order 
    $produtOrderTotalIncomes = DB::table('product_orders')
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(total) as total'))
      ->where('payment_status', '=', 'completed')
      ->groupBy('month')
      ->whereYear('created_at', '=', date('Y'))
      ->get();

    $totalProductOrder = DB::table('product_orders')
      ->select(DB::raw('month(created_at) as month'), DB::raw('count(id) as total'))
      ->where('payment_status', '=', 'completed')
      ->groupBy('month')
      ->whereYear('created_at', '=', date('Y'))
      ->get();

    $eventMonths = [];

    $eventIncomes = [];
    $eventTaxes = [];
    $totalBookings = [];

    $productIncome = [];
    $totalOders = [];


    //event icome calculation
    for ($i = 1; $i <= 12; $i++) {
      // get all 12 months name
      $monthNum = $i;
      $dateObj = DateTime::createFromFormat('!m', $monthNum);
      $monthName = $dateObj->format('M');
      array_push($eventMonths, $monthName);

      // get all 12 months's income
      $incomeFound = false;
      foreach ($eventBookingTotalIncomes as $eventIncomeInfo) {
        if ($eventIncomeInfo->month == $i) {
          $incomeFound = true;
          array_push($eventIncomes, $eventIncomeInfo->total);
          break;
        }
      }
      if ($incomeFound == false) {
        array_push($eventIncomes, 0);
      }
      // get all 12 months's taxes
      $taxFound = false;
      foreach ($monthWiseTotaltaxs as $monthWiseTotaltax) {
        if ($monthWiseTotaltax->month == $i) {
          $taxFound = true;
          array_push($eventTaxes, $monthWiseTotaltax->total);
          break;
        }
      }
      if ($taxFound == false) {
        array_push($eventTaxes, 0);
      }


      // get all 12 months's c
      $bookingFound = false;

      foreach ($TotalEventBookings as $eventInfo) {
        if ($eventInfo->month == $i) {
          $bookingFound = true;
          array_push($totalBookings, $eventInfo->total);
          break;
        }
      }

      if ($bookingFound == false) {
        array_push($totalBookings, 0);
      }

      // get all 12 months's 
      $orderFound = false;

      foreach ($produtOrderTotalIncomes as $productInfo) {
        if ($productInfo->month == $i) {
          $orderFound = true;
          array_push($productIncome, $productInfo->total);
          break;
        }
      }

      if ($orderFound == false) {
        array_push($productIncome, 0);
      }
      // get all 12 months's 
      $orderTotalFound = false;

      foreach ($totalProductOrder as $productTotalInfo) {
        if ($productTotalInfo->month == $i) {
          $orderTotalFound = true;
          array_push($totalOders, $productTotalInfo->total);
          break;
        }
      }

      if ($orderTotalFound == false) {
        array_push($totalOders, 0);
      }
    }
    $arry = [];
    foreach ($eventIncomes as $key => $eventIncome) {
      array_push($arry, round($eventIncome + $eventTaxes[$key], 2));
    }

    $information['eventIncomes'] = $arry;
    $information['eventMonths'] = $eventMonths;
    $information['totalBookings'] = $totalBookings;

    $information['productIncome'] = $productIncome;
    $information['totalOders'] = $totalOders;

    return view('backend.admin.dashboard', $information);
  }

  public function changeTheme(Request $request)
  {
    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      ['admin_theme_version' => $request->admin_theme_version]
    );

    return redirect()->back();
  }

  public function editProfile()
  {
    $adminInfo = Auth::guard('admin')->user();

    return view('backend.admin.edit-profile', compact('adminInfo'));
  }

  public function updateProfile(Request $request)
  {
    $admin = Auth::guard('admin')->user();

    $rules = [];

    if (!$request->filled('image') && is_null($admin->image)) {
      $rules['image'] = 'required';
    }
    if ($request->hasFile('image')) {
      $rules['image'] = new ImageMimeTypeRule();
    }

    $rules['username'] = [
      'required',
      Rule::unique('admins')->ignore($admin->id)
    ];

    $rules['email'] = [
      'required',
      'email:rfc,dns',
      Rule::unique('admins')->ignore($admin->id)
    ];

    $rules['first_name'] = 'required';

    $rules['last_name'] = 'required';

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    if ($request->hasFile('image')) {
      $imageName = UploadFile::update(public_path('assets/admin/img/admins/'), $request->file('image'), $admin->image);
    }

    $admin->update([
      'first_name' => $request->first_name,
      'last_name' => $request->last_name,
      'image' => $request->hasFile('image') ? $imageName : $admin->image,
      'username' => $request->username,
      'email' => $request->email,
      'phone' => $request->phone,
      'address' => $request->address,
      'details' => $request->details,
    ]);
    Session::flash('success', 'Profile updated successfully!');

    return redirect()->back();
  }

  public function changePassword()
  {
    return view('backend.admin.change-password');
  }

  public function updatePassword(Request $request)
  {
    $rules = [
      'current_password' => [
        'required',
        new MatchOldPasswordRule('admin')
      ],
      'new_password' => 'required|confirmed',
      'new_password_confirmation' => 'required'
    ];

    $messages = [
      'new_password.confirmed' => 'Password confirmation does not match.',
      'new_password_confirmation.required' => 'The confirm new password field is required.'
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $admin = Auth::guard('admin')->user();

    $admin->update([
      'password' => Hash::make($request->new_password)
    ]);

    Session::flash('success', 'Updated Successfully');

    return response()->json(['status' => 'success'], 200);
  }

  public function logout(Request $request)
  {
    Auth::guard('admin')->logout();

    return redirect()->route('admin.login');
  }

  //transcation 
  public function transcation(Request $request)
  {
    $transcation_id = null;
    if ($request->filled('transcation_id')) {
      $transcation_id = $request->transcation_id;
    }

    $transcations = Transaction::when($transcation_id, function ($query) use ($transcation_id) {
      return $query->where('transcation_id', 'like', '%' . $transcation_id . '%');
    })
      ->orderBy('id', 'desc')->paginate(10);
    return view('backend.admin.transaction', compact('transcations'));
  }
  //destroy
  public function destroy(Request $request)
  {
    $transcation = Transaction::where('id', $request->id)->first();
    $transcation->delete();
    Session::flash('success', 'Deleted Successfully');

    return back();
  }

  //destroy
  public function bulk_destroy(Request $request)
  {
    $ids = $request->ids;
    foreach ($ids as $id) {
      $transcation = Transaction::where('id', $id)->first();
      $transcation->delete();
    }
    Session::flash('success', 'Deleted Successfully');

    return response()->json(['status' => 'success'], 200);
  }

  //monthly  earning
  public function monthly_earning(Request $request)
  {
    if ($request->filled('year')) {
      $date = $request->input('year');
    } else {
      $date = date('Y');
    }
    $monthWiseTotalIncomes = DB::table('transactions')
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(grand_total) as total'))
      ->where('payment_status', 1)
      ->where(function ($query) {
        return $query->whereNotIn('transcation_type', [3, 4, 5]);
      })
      ->groupBy('month')
      ->whereYear('created_at', '=', $date)
      ->get();

    $monthWiseTotaltaxs = DB::table('transactions')
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(tax) as total'))
      ->where('payment_status', 1)
      ->where(function ($query) {
        return $query->whereNotIn('transcation_type', [2, 3, 4, 5]);
      })
      ->groupBy('month')
      ->whereYear('created_at', '=', $date)
      ->get();


    $months = [];
    $incomes = [];
    $taxs = [];
    for ($i = 1; $i <= 12; $i++) {
      // get all 12 months name
      $monthNum = $i;
      $dateObj = DateTime::createFromFormat('!m', $monthNum);
      $monthName = $dateObj->format('F');
      array_push($months, $monthName);

      // get all 12 months's income of equipment booking
      $incomeFound = false;
      foreach ($monthWiseTotalIncomes as $incomeInfo) {
        if ($incomeInfo->month == $i) {
          $incomeFound = true;
          array_push($incomes, $incomeInfo->total);
          break;
        }
      }
      if ($incomeFound == false) {
        array_push($incomes, 0);
      }

      // get all 12 months's income of equipment booking
      $taxFound = false;
      foreach ($monthWiseTotaltaxs as $taxInfo) {
        if ($taxInfo->month == $i) {
          $taxFound = true;
          array_push($taxs, $taxInfo->total);
          break;
        }
      }
      if ($taxFound == false) {
        array_push($taxs, 0);
      }
    }
    $information['months'] = $months;
    $information['incomes'] = $incomes;
    $information['taxs'] = $taxs;

    return view('backend.admin.earning', $information);
  }

  //monthly  income
  public function monthly_profit(Request $request)
  {
    if ($request->filled('year')) {
      $date = $request->input('year');
    } else {
      $date = date('Y');
    }
    $monthWiseTotalIncomes = DB::table('transactions')
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(commission) as total'))
      ->where('payment_status', 1)
      ->where('organizer_id', '!=', null)
      ->groupBy('month')
      ->whereYear('created_at', '=', $date)
      ->get();
      
    $monthWiseTotalProfits = DB::table('transactions')
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(tax) as total'))
      ->where('payment_status', 1)
      ->groupBy('month')
      ->whereYear('created_at', '=', $date)
      ->get();

    $monthWiseTotalAdminProfits = DB::table('transactions')
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(grand_total) as total'))
      ->where('payment_status', 1)
      ->where('organizer_id', '=', null)
      ->groupBy('month')
      ->whereYear('created_at', '=', $date)
      ->get();



    $months = [];
    $incomes = [];
    $taxs = [];
    $admin_profit = [];
    for ($i = 1; $i <= 12; $i++) {
      // get all 12 months name
      $monthNum = $i;
      $dateObj = DateTime::createFromFormat('!m', $monthNum);
      $monthName = $dateObj->format('M');
      array_push($months, $monthName);

      // get all 12 months's income of event booking
      $incomeFound = false;
      foreach ($monthWiseTotalIncomes as $incomeInfo) {
        if ($incomeInfo->month == $i) {
          $incomeFound = true;
          array_push($incomes, $incomeInfo->total);
          break;
        }
      }
      if ($incomeFound == false) {
        array_push($incomes, 0);
      }

      // get all 12 months's tax's of event booking
      $taxFound = false;
      foreach ($monthWiseTotalProfits as $profitInfo) {
        if ($profitInfo->month == $i) {
          $taxFound = true;
          array_push($taxs, $profitInfo->total);
          break;
        }
      }
      if ($taxFound == false) {
        array_push($taxs, 0);
      }

      // get all 12 months's tax's of event booking
      $adminProfitFound = false;
      foreach ($monthWiseTotalAdminProfits as $AdminProfit) {
        if ($AdminProfit->month == $i) {
          $adminProfitFound = true;
          array_push($admin_profit, $AdminProfit->total);
          break;
        }
      }
      if ($adminProfitFound == false) {
        array_push($admin_profit, 0);
      }
    }
    $information['months'] = $months;
    $information['incomes'] = $incomes;
    $information['taxs'] = $taxs;
    $information['admin_profit'] = $admin_profit;

    return view('backend.admin.profit', $information);
  }


  //participant 
  public function participant(Request $request) {
    $language = $this->getLanguage();
    $language_id = $language->id;

    $title = $request->input('title');

    $participant = ParticipantCompetitions::query()
        ->select(DB::raw('event_contents.title as event_name, participant_competitions.*, participant.fname, participant.lname, tickets.title as competition_type, ticket_contents.title as ticket_title, participant_competitions.category, 
        CASE
            WHEN LOWER(participant_competitions.category) = "club" THEN (SELECT clubs.name FROM clubs WHERE clubs.id=participant_competitions.delegation_id)
            WHEN LOWER(participant_competitions.category) = "school/universities" THEN (SELECT school.name FROM school WHERE school.id=participant_competitions.delegation_id)
            WHEN LOWER(participant_competitions.category) = "organization" THEN (SELECT organization.name FROM organization WHERE organization.id=participant_competitions.delegation_id)
            WHEN LOWER(participant_competitions.category) = "country" THEN (SELECT international_countries.name FROM international_countries WHERE international_countries.id=participant_competitions.delegation_id)
            WHEN LOWER(participant_competitions.category) = "province" THEN (SELECT indonesian_province.name FROM indonesian_province WHERE indonesian_province.id=participant_competitions.delegation_id)
            ELSE (SELECT indonesian_cities.name FROM indonesian_cities WHERE indonesian_cities.id=delegation_id)
        END as delegation_name'))
        ->leftJoin('participant', 'participant.id', '=', 'participant_competitions.participant_id')
        ->leftJoin('ticket_contents', 'ticket_contents.ticket_id', '=', 'participant_competitions.ticket_id')
        ->leftJoin('tickets', 'tickets.id', '=', 'participant_competitions.ticket_id')
        ->leftJoin('event_contents', 'event_contents.event_id', '=', 'participant_competitions.event_id')
        ->leftJoin('bookings', 'bookings.id', '=', 'participant_competitions.booking_id')->where('bookings.paymentStatus', 'completed')
        ->leftJoin('events', 'events.id', '=', 'participant_competitions.event_id')
        ->leftJoin('clubs', 'clubs.id', '=', 'participant_competitions.delegation_id')
        ->leftJoin('school', 'school.id', '=', 'participant_competitions.delegation_id')
        ->leftJoin('organization', 'organization.id', '=', 'participant_competitions.delegation_id')
        ->leftJoin('international_countries', 'international_countries.id', '=', 'participant_competitions.delegation_id')
        ->leftJoin('indonesian_province', 'indonesian_province.id', '=', 'participant_competitions.delegation_id')
        ->leftJoin('indonesian_cities', 'indonesian_cities.id', '=', 'participant_competitions.delegation_id')
        ->where('ticket_contents.language_id', $language_id)
        ->where('event_contents.language_id', $language_id)
        ->when($title, function ($query) use ($title) {
            return $query->where(function ($q) use ($title) {
                $q->where('event_contents.title', 'like', '%' . $title . '%')
                  ->orWhere('participant.fname', 'like', '%' . $title . '%')
                  ->orWhere('participant.lname', 'like', '%' . $title . '%')
                  ->orWhere('ticket_contents.title', 'like', '%' . $title . '%')
                  ->orWhere('participant_competitions.category', 'like', '%' . $title . '%')
                  ->orWhere(DB::raw('
                      CASE
                          WHEN LOWER(participant_competitions.category) = "club" THEN (SELECT clubs.name FROM clubs WHERE clubs.id=participant_competitions.delegation_id)
                          WHEN LOWER(participant_competitions.category) = "school/universities" THEN (SELECT school.name FROM school WHERE school.id=participant_competitions.delegation_id)
                          WHEN LOWER(participant_competitions.category) = "organization" THEN (SELECT organization.name FROM organization WHERE organization.id=participant_competitions.delegation_id)
                          WHEN LOWER(participant_competitions.category) = "country" THEN (SELECT international_countries.name FROM international_countries WHERE international_countries.id=participant_competitions.delegation_id)
                          WHEN LOWER(participant_competitions.category) = "province" THEN (SELECT indonesian_province.name FROM indonesian_province WHERE indonesian_province.id=participant_competitions.delegation_id)
                          ELSE (SELECT indonesian_cities.name FROM indonesian_cities WHERE indonesian_cities.id=delegation_id)
                      END
                  '), 'like', '%' . $title . '%');
            });
        })
        ->orderBy('participant_competitions.created_at', 'desc')
        ->paginate(25);

    $information['participant'] = $participant;
    $information['event_name'] = $title;
    return view('backend.admin.participant', $information);
  }

  public function detail_participant(Request $request, $id) {
    $language = $this->getLanguage();
    $language_id = $language->id;

    $event_name = $request->input('event_name');

    // Mendapatkan judul acara
    $event = EventContent::where('event_id', $id)->where('language_id', $language_id)->first();
    $event_title = $event ? $event->title : 'Event Title Not Found';

    $participant = ParticipantCompetitions::query()
        ->select(DB::raw('event_contents.title as event_name, participant_competitions.*, participant.fname, participant.lname, tickets.title as competition_type, ticket_contents.title as ticket_title, participant_competitions.category, 
        CASE
            WHEN LOWER(participant_competitions.category) = "club" THEN (SELECT clubs.name FROM clubs WHERE clubs.id=participant_competitions.delegation_id)
            WHEN LOWER(participant_competitions.category) = "school/universities" THEN (SELECT school.name FROM school WHERE school.id=participant_competitions.delegation_id)
            WHEN LOWER(participant_competitions.category) = "organization" THEN (SELECT organization.name FROM organization WHERE organization.id=participant_competitions.delegation_id)
            WHEN LOWER(participant_competitions.category) = "country" THEN (SELECT international_countries.name FROM international_countries WHERE international_countries.id=participant_competitions.delegation_id)
            WHEN LOWER(participant_competitions.category) = "province" THEN (SELECT indonesian_province.name FROM indonesian_province WHERE indonesian_province.id=participant_competitions.delegation_id)
            ELSE (SELECT indonesian_cities.name FROM indonesian_cities WHERE indonesian_cities.id=delegation_id)
        END as delegation_name'))
        ->leftJoin('participant', 'participant.id', '=', 'participant_competitions.participant_id')
        ->leftJoin('ticket_contents', 'ticket_contents.ticket_id', '=', 'participant_competitions.ticket_id')
        ->leftJoin('tickets', 'tickets.id', '=', 'participant_competitions.ticket_id')
        ->leftJoin('event_contents', 'event_contents.event_id', '=', 'participant_competitions.event_id')
        ->leftJoin('bookings', 'bookings.id', '=', 'participant_competitions.booking_id')->where('bookings.paymentStatus', 'completed')
        ->leftJoin('events', 'events.id', '=', 'participant_competitions.event_id')
        ->leftJoin('clubs', 'clubs.id', '=', 'participant_competitions.delegation_id')
        ->leftJoin('school', 'school.id', '=', 'participant_competitions.delegation_id')
        ->leftJoin('organization', 'organization.id', '=', 'participant_competitions.delegation_id')
        ->leftJoin('international_countries', 'international_countries.id', '=', 'participant_competitions.delegation_id')
        ->leftJoin('indonesian_province', 'indonesian_province.id', '=', 'participant_competitions.delegation_id')
        ->leftJoin('indonesian_cities', 'indonesian_cities.id', '=', 'participant_competitions.delegation_id')
        ->where('ticket_contents.language_id', $language_id)
        ->where('event_contents.language_id', $language_id)
        ->where('participant_competitions.event_id', $id)
        ->when($event_name, function ($query) use ($event_name) {
            return $query->where(function ($q) use ($event_name) {
                $q->where('event_contents.title', 'like', '%' . $event_name . '%')
                  ->orWhere('participant.fname', 'like', '%' . $event_name . '%')
                  ->orWhere('participant.lname', 'like', '%' . $event_name . '%')
                  ->orWhere('ticket_contents.title', 'like', '%' . $event_name . '%')
                  ->orWhere('participant_competitions.category', 'like', '%' . $event_name . '%')
                  ->orWhere(DB::raw('
                      CASE
                          WHEN LOWER(participant_competitions.category) = "club" THEN (SELECT clubs.name FROM clubs WHERE clubs.id=participant_competitions.delegation_id)
                          WHEN LOWER(participant_competitions.category) = "school/universities" THEN (SELECT school.name FROM school WHERE school.id=participant_competitions.delegation_id)
                          WHEN LOWER(participant_competitions.category) = "organization" THEN (SELECT organization.name FROM organization WHERE organization.id=participant_competitions.delegation_id)
                          WHEN LOWER(participant_competitions.category) = "country" THEN (SELECT international_countries.name FROM international_countries WHERE international_countries.id=participant_competitions.delegation_id)
                          WHEN LOWER(participant_competitions.category) = "province" THEN (SELECT indonesian_province.name FROM indonesian_province WHERE indonesian_province.id=participant_competitions.delegation_id)
                          ELSE (SELECT indonesian_cities.name FROM indonesian_cities WHERE indonesian_cities.id=delegation_id)
                      END
                  '), 'like', '%' . $event_name . '%');
            });
        })
        ->orderBy('participant_competitions.created_at', 'desc')
        ->paginate(25);

    return view('backend.admin.detail-participant', compact('participant', 'event_title'));
  }

  public function participant_export(Request $request){
    // Mengambil bahasa yang digunakan
    $language = $this->getLanguage();
    $language_id = $language->id;

    $event_name = null;
    // Mengecek apakah ada input event pada request
    if ($request->filled('event')) {
        $event_name = $request->event;
    }
    
    // Query untuk mengambil data participant competitions
    $participant = ParticipantCompetitions::when($event_name, function ($query) use ($event_name) {
        // Menambahkan kondisi where jika event_name diisi
        return $query->where('event_contents.title', 'like', '%' . $event_name . '%');
    })
    ->select(DB::raw('event_contents.title as event_name, participant_competitions.*, participant.fname, participant.lname, tickets.title as competition_type, ticket_contents.title, 
        CASE
            WHEN LOWER(participant_competitions.category) = "club" THEN (SELECT clubs.name FROM clubs WHERE clubs.id=participant_competitions.delegation_id)
            WHEN LOWER(participant_competitions.category) = "school/universities" THEN (SELECT school.name FROM school WHERE school.id=participant_competitions.delegation_id)
            WHEN LOWER(participant_competitions.category) = "organization" THEN (SELECT organization.name FROM organization WHERE organization.id=participant_competitions.delegation_id)
            WHEN LOWER(participant_competitions.category) = "country" THEN (SELECT international_countries.name FROM international_countries WHERE international_countries.id=participant_competitions.delegation_id)
            WHEN LOWER(participant_competitions.category) = "province" THEN (SELECT indonesian_province.name FROM indonesian_province WHERE indonesian_province.id=participant_competitions.delegation_id)
            ELSE (SELECT indonesian_cities.name FROM indonesian_cities WHERE indonesian_cities.id=delegation_id)
        END as delegation,
        CASE
            WHEN LOWER(participant_competitions.status) = 2 THEN "Cancel"
            WHEN LOWER(participant_competitions.status) = 3 THEN "Refund"
            ELSE "Active"
        END as status'))
    ->leftjoin('participant', 'participant.id', 'participant_competitions.participant_id') // Join dengan tabel participant
    ->leftjoin('ticket_contents', 'ticket_contents.ticket_id', 'participant_competitions.ticket_id') // Join dengan tabel ticket_contents
    ->leftJoin('tickets', 'tickets.id', '=', 'participant_competitions.ticket_id') // Join dengan tabel tickets
    ->leftjoin('event_contents', 'event_contents.event_id', 'participant_competitions.event_id') // Join dengan tabel event_contents
    ->leftjoin('bookings', 'bookings.id', 'participant_competitions.booking_id') // Join dengan tabel bookings
    ->where('bookings.paymentStatus', 'completed') // Kondisi untuk paymentStatus harus 'completed'
    ->where('participant_competitions.status', 1) // Kondisi untuk status harus 1 Untuk menampilkan yg terdaftar saja
    ->where('ticket_contents.language_id', $language_id) // Kondisi untuk language_id pada ticket_contents
    ->where('event_contents.language_id', $language_id) // Kondisi untuk language_id pada event_contents
    ->orderBy('ticket_contents.title', 'asc') // Mengurutkan berdasarkan ticket_contents.title (Kategori Perlombaan)
    // Mengambil data
    ->get();

    // Mengecek apakah ada data participant yang diambil
    if (empty($participant) || count($participant) == 0) {
        // Menampilkan pesan peringatan jika tidak ada data
        Session::flash('warning', 'There is no participant to export');
        return back();
    }

    // Mengunduh data dalam format Excel
    return Excel::download(new ParticipantExport($participant), 'participant.xlsx');
}

  public function update_participant(Request $request, $id){
    $data = ParticipantCompetitions::where('id', $id)->first();
    $data->status = $request->status;
    $data->save();

    Session::flash('success', 'Update Successfully');
    return redirect()->back();
  }

}
