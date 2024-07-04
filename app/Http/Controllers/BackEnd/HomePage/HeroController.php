<?php

namespace App\Http\Controllers\BackEnd\HomePage;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Models\HomePage\HeroSection;
use App\Models\Language;
use App\Rules\ImageMimeTypeRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class HeroController extends Controller
{
  public function index(Request $request)
  {
    $language = Language::where('code', $request->language)->firstOrFail();
    $information['language'] = $language;

    $information['data'] = $language->heroSec()->first();
    // dd($information['data']);
    $information['langs'] = Language::all();

    $information['themeInfo'] = DB::table('basic_settings')->select('theme_version')->first();

    return view('backend.home-page.hero-section', $information);
  }

  public function storeImagesHeroSection(Request $request)
  {
    $language = Language::where('code', $request->language_code)->firstOrFail();
    $heroData = $language->heroSec()->first();
    $in = $request->all();
    $in['language_code'] = $request->language_code;

    $img = $request->file('file');
    $allowedExts = array('jpg', 'png', 'jpeg');

    $rules = [
      'file' => [
        function ($attribute, $value, $fail) use ($img, $allowedExts) {
          $ext = $img->getClientOriginalExtension();
          if (!in_array($ext, $allowedExts)) {
            return $fail("Only png, jpg, jpeg images are allowed");
          }
        }
      ]
    ];

    $validator = Validator::make($in, $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    $filename = time() . '-' . uniqid() . '.' . $img->getClientOriginalExtension();
    @mkdir(public_path('assets/admin/img/hero-section/'), 0775, true);
    $img->move(public_path('assets/admin/img/hero-section/'), $filename);

    if (empty($heroData)) {
      $heroSection = HeroSection::create([
        'language_id' => $language->id,
        'background_image' => $filename,
        'image' => '',
        'video_url' => ''
      ]);
      $heroSection->save();
      return response()->json(['status' => 'success', 'file_id' => $heroSection->id]);
    } else {
      $heroSection = HeroSection::create([
        'language_id' => $language->id,
        'background_image' => $filename,
        'image' => $heroData->image,
        'video_url' => $heroData->video,
        'first_title' => $heroData->first_title,
        'second_title' => $heroData->second_title,
        'first_button' => $heroData->first_button,
        'first_button_url' => $heroData->first_button_url,
        'second_button' => $heroData->second_button,
        'second_button_url' => $heroData->second_button_url,
      ]);
      return response()->json(['status' => 'success', 'file_id' => $heroSection->id]);
    }
  }

  public function rmvImageHeroSection(Request $request)
  {
    try {
      $pi = HeroSection::where('id', $request->fileid)->first();
      $language = Language::where('id', $pi->language_id)->firstOrFail();
      $heroData = $language->heroSec()->count();
      @unlink(public_path('assets/admin/img/hero-section/') . $pi->background_image);

      $pi->delete();
      return $pi->id;
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  public function imagedbrmv(Request $request)
  {
    try {
      $pi = HeroSection::where('id', $request->fileid)->first();
      $language = Language::where('id', $pi->language_id)->firstOrFail();
      $heroData = $language->heroSec()->count();

      if ($heroData < 2) {
        return response()->json([
          'message' => 'Delete failed, because background image at least 1!'
        ], 403);
      } else {
        @unlink(public_path('assets/admin/img/hero-section/') . $pi->background_image);

        $pi->delete();
        return $pi->id;
      }
    } catch (\Exception $e) {
      return $e->getMessage();
      // return "Delete failed, because background image min at least 1!";
    }
  }

  public function imagesHeroSection($languageCode)
  {
    $language = Language::where('code', $languageCode)->firstOrFail();
    $heroData = $language->heroSec()->get();

    if (empty($heroData)) {
      return [];
    } else {
      foreach ($heroData as $keyHD => $valHD) {
        $heroData[$keyHD]->image = $valHD->background_image;
      }
      return $heroData;
    }
  }

  public function update(Request $request)
  {
    $language = Language::where('code', $request->language)->first();

    $heroInfo = $language->heroSec()->first();

    $themeInfo = DB::table('basic_settings')->select('theme_version')->first();

    $rules = [];

    // if (empty($heroInfo)) {
    //   $rules['background_image'] = 'required';
    // }

    // if ($request->hasFile('background_image')) {
    //   $rules['background_image'] = new ImageMimeTypeRule();
    // }

    if ($themeInfo->theme_version == 3 && $request->hasFile('image')) {
      $rules['image'] = new ImageMimeTypeRule();
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    // format video link
    $link = NULL;

    if ($request->filled('video_url')) {
      $link = $request->video_url;

      if (strpos($link, '&') != 0) {
        $link = substr($link, 0, strpos($link, '&'));
      }
    }

    // insert data into db
    if (empty($heroInfo)) {
      $backgroundImageName = UploadFile::store(public_path('assets/admin/img/hero-section/'), $request->file('background_image'));

      $imageName = NULL;

      if ($themeInfo->theme_version == 3 && $request->hasFile('image')) {
        $imageName = UploadFile::store(public_path('assets/admin/img/hero-section/'), $request->file('image'));
      }

      HeroSection::create($request->except('language_id', 'background_image', 'image', 'video_url') + [
        'language_id' => $language->id,
        'background_image' => $backgroundImageName,
        'image' => $imageName,
        'video_url' => $link
      ]);

      Session::flash('success', 'Added Successfully');

      return redirect()->back();
    } else {
      if ($request->hasFile('background_image')) {
        $backgroundImageName = UploadFile::update(public_path('assets/admin/img/hero-section/'), $request->file('background_image'), $heroInfo->background_image);
      }

      if ($themeInfo->theme_version == 3 && $request->hasFile('image')) {
        $imageName = UploadFile::update(public_path('assets/admin/img/hero-section/'), $request->file('image'), $heroInfo->image);
      }

      // $heroInfo->update($request->except('background_image', 'image', 'video_url') + [
      //   'background_image' => $request->hasFile('background_image') ? $backgroundImageName : $heroInfo->background_image,
      //   'image' => $request->hasFile('image') ? $imageName : $heroInfo->image,
      //   'video_url' => $link
      // ]);

      $heroInfo->update($request->except('image', 'video_url') + [
        'image' => $request->hasFile('image') ? $imageName : $heroInfo->image,
        'video_url' => $link
      ]);

      Session::flash('success', 'Updated Successfully');

      return redirect()->back();
    }
  }
}
