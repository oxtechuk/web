<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        $cars = \App\Models\Car::select('id', 'name')->where('is_active', true)->orderBy('name')->get();

        $bentoCars = $settings['bento_cars'] ?? [];
        if (! is_array($bentoCars) && is_string($bentoCars)) {
            $bentoCars = json_decode($bentoCars, true) ?: [];
        }

        $socialMedia = $settings['social_media'] ?? [];
        if (! is_array($socialMedia) && is_string($socialMedia)) {
            $socialMedia = json_decode($socialMedia, true) ?: [];
        }

        return view('crm.settings.general', compact('settings', 'cars', 'bentoCars', 'socialMedia'));
    }

    public function seo()
    {
        $settings = Setting::all()->pluck('value', 'key');

        return view('crm.settings.seo', compact('settings'));
    }

    public function integrations()
    {
        $settings = Setting::all()->pluck('value', 'key');

        return view('crm.settings.integrations', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'portfolio_pdf_ar' => 'nullable|file|mimes:pdf|max:40960',
            'portfolio_pdf_en' => 'nullable|file|mimes:pdf|max:40960',
        ]);

        // Whitelist of settings to update
        $keys = [
            'site_name', 'footer_text', 'contact_email', 'contact_phone',
            'portfolio_link_ar', 'portfolio_link_en',
            'contact_whatsapp', 'contact_address', 'bento_cars',
            'hero_ad_1_link', 'hero_ad_2_link', 'auto_assign_bookings',
            'page_loader_enabled', 'site_font',
            'twilio_sid', 'twilio_auth_token', 'twilio_whatsapp_number', 'twilio_sms_number',
            'whatsapp_template_new_lead', 'whatsapp_template_status_update',
            'google_analytics_id', 'meta_pixel_id',
            'meta_title', 'meta_description', 'meta_keywords',
            // Coming Soon
            'coming_soon_enabled', 'coming_soon_date', 'coming_soon_title_ar',
            'coming_soon_title_en', 'coming_soon_subtitle_ar', 'coming_soon_subtitle_en',
            // Branches Settings
            'branch_1_name_ar', 'branch_1_name_en', 'branch_1_address_ar', 'branch_1_address_en', 'branch_1_phone', 'branch_1_map',
            'branch_2_name_ar', 'branch_2_name_en', 'branch_2_address_ar', 'branch_2_address_en', 'branch_2_phone', 'branch_2_map',
            // Cookie Consent
            'cookie_consent_enabled', 'cookie_consent_text_ar', 'cookie_consent_text_en',
            'cookie_consent_link',
            // OTP
            'otp_enabled',
            // HyperPay
            'hyperpay_mode',
            'hyperpay_test_entity_id',
            'hyperpay_test_access_token',
            'hyperpay_live_entity_id',
            'hyperpay_live_access_token',
            'hyperpay_booking_fee',
            'hyperpay_currency',
            'hyperpay_enabled',
            // Email SMTP
            'mail_driver',
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_from_address',
            'mail_from_name',
        ];

        // Update text/array settings
        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::updateOrCreate(['key' => $key], ['value' => $request->get($key)]);
            } elseif (in_array($key, ['twilio_sid', 'twilio_auth_token', 'twilio_whatsapp_number', 'twilio_sms_number', 'whatsapp_template_new_lead', 'whatsapp_template_status_update'])) {
                // Allow empty values for these keys
                Setting::updateOrCreate(['key' => $key], ['value' => $request->get($key, '')]);
            }
        }

        // Checkbox unchecked case for page_loader_enabled, coming_soon_enabled, cookie_consent_enabled
        if (! $request->has('page_loader_enabled')) {
            Setting::updateOrCreate(['key' => 'page_loader_enabled'], ['value' => '0']);
        }
        if (! $request->has('coming_soon_enabled')) {
            Setting::updateOrCreate(['key' => 'coming_soon_enabled'], ['value' => '0']);
        }
        if (! $request->has('cookie_consent_enabled')) {
            Setting::updateOrCreate(['key' => 'cookie_consent_enabled'], ['value' => '0']);
        }
        if (! $request->has('otp_enabled')) {
            Setting::updateOrCreate(['key' => 'otp_enabled'], ['value' => '0']);
        }
        if (! $request->has('hyperpay_enabled')) {
            Setting::updateOrCreate(['key' => 'hyperpay_enabled'], ['value' => '0']);
        }

        // Handle Social Media Array
        $socialIcons = $request->input('social_icon', []);
        $socialLinks = $request->input('social_link', []);
        $socialColors = $request->input('social_color', []);
        $socialMedia = [];
        foreach ($socialIcons as $index => $icon) {
            if (! empty($icon) && ! empty($socialLinks[$index])) {
                $socialMedia[] = [
                    'icon' => $icon,
                    'link' => $socialLinks[$index],
                    'color' => $socialColors[$index] ?? '#333333',
                ];
            }
        }
        Setting::updateOrCreate(['key' => 'social_media'], ['value' => $socialMedia]);

        // Handle Default Car Thumbnail Deletion
        if ($request->input('delete_default_car_thumbnail') === '1') {
            $existing = Setting::where('key', 'default_car_thumbnail')->value('value');
            if ($existing && \Storage::disk('public')->exists($existing)) {
                \Storage::disk('public')->delete($existing);
            }
            Setting::where('key', 'default_car_thumbnail')->delete();
        }

        // Handle File Uploads (Only if new files are uploaded)
        $files = ['site_logo', 'site_favicon', 'breadcrumb_bg', 'hero_video', 'hero_ad_1_image', 'hero_ad_2_image', 'page_loader_image', 'coming_soon_bg_image', 'default_car_thumbnail'];
        foreach ($files as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $existing = Setting::where('key', $fileKey)->value('value');
                if ($existing && \Storage::disk('public')->exists($existing)) {
                    \Storage::disk('public')->delete($existing);
                }
                $path = $request->file($fileKey)->store('settings', 'public');
                Setting::updateOrCreate(['key' => $fileKey], ['value' => $path]);
            }
        }

        // Handle Portfolio PDF Uploads
        foreach (['ar', 'en'] as $lang) {
            $pdfKey    = "portfolio_pdf_{$lang}";
            $deleteKey = "delete_portfolio_pdf_{$lang}";
            $existing  = Setting::where('key', $pdfKey)->value('value');

            // Delete requested
            if ($request->input($deleteKey) === '1') {
                if ($existing && \Storage::disk('public')->exists($existing)) {
                    \Storage::disk('public')->delete($existing);
                }
                Setting::where('key', $pdfKey)->delete();
                continue;
            }

            // New file uploaded
            if ($request->hasFile($pdfKey)) {
                $file = $request->file($pdfKey);
                // Validate PDF
                if ($file->getClientOriginalExtension() !== 'pdf') {
                    continue;
                }
                // Delete old
                if ($existing && \Storage::disk('public')->exists($existing)) {
                    \Storage::disk('public')->delete($existing);
                }
                $path = $file->store('settings/portfolio', 'public');
                Setting::updateOrCreate(['key' => $pdfKey], ['value' => $path]);
            }
        }

        // Handle Hero Slides (Carousel Slider Banners)
        if ($request->has('hero_slides_submitted')) {
            $existingSlidesSetting = Setting::where('key', 'hero_slides')->first();
            $existingSlides = [];
            if ($existingSlidesSetting && ! empty($existingSlidesSetting->value)) {
                $existingSlides = is_array($existingSlidesSetting->value) ? $existingSlidesSetting->value : (json_decode($existingSlidesSetting->value, true) ?: []);
            }

            $newSlides = [];
            $slidesData = $request->input('hero_slides', []);

            foreach ($slidesData as $index => $slide) {
                $imagePathAr = $slide['image_path_ar'] ?? $slide['image_path'] ?? $slide['image'] ?? null;
                $imagePathEn = $slide['image_path_en'] ?? $slide['image_en'] ?? null;

                // Check for new Arabic file upload
                if ($request->hasFile("hero_slides.{$index}.image_ar")) {
                    $path = $request->file("hero_slides.{$index}.image_ar")->store('settings/hero', 'public');
                    if ($imagePathAr && \Storage::disk('public')->exists($imagePathAr)) {
                        \Storage::disk('public')->delete($imagePathAr);
                    }
                    $imagePathAr = $path;
                } elseif ($request->hasFile("hero_slides.{$index}.image")) {
                    $path = $request->file("hero_slides.{$index}.image")->store('settings/hero', 'public');
                    if ($imagePathAr && \Storage::disk('public')->exists($imagePathAr)) {
                        \Storage::disk('public')->delete($imagePathAr);
                    }
                    $imagePathAr = $path;
                }

                // Check for new English file upload
                if ($request->hasFile("hero_slides.{$index}.image_en")) {
                    $path = $request->file("hero_slides.{$index}.image_en")->store('settings/hero', 'public');
                    if ($imagePathEn && \Storage::disk('public')->exists($imagePathEn)) {
                        \Storage::disk('public')->delete($imagePathEn);
                    }
                    $imagePathEn = $path;
                }

                $linkAr = $slide['link_ar'] ?? $slide['link'] ?? '';
                $linkEn = $slide['link_en'] ?? '';
                $btnTextAr = $slide['button_text_ar'] ?? $slide['button_text'] ?? __('اكتشف السيارات');
                $btnTextEn = $slide['button_text_en'] ?? '';
                $ytLinkAr = $slide['youtube_link_ar'] ?? $slide['youtube_link'] ?? '';
                $ytLinkEn = $slide['youtube_link_en'] ?? '';

                if ($imagePathAr || $imagePathEn || ! empty($ytLinkAr) || ! empty($ytLinkEn)) {
                    $newSlides[] = [
                        'image' => $imagePathAr ?: $imagePathEn,
                        'image_ar' => $imagePathAr,
                        'image_en' => $imagePathEn,
                        'link' => $linkAr ?: $linkEn,
                        'link_ar' => $linkAr,
                        'link_en' => $linkEn,
                        'button_text' => $btnTextAr ?: $btnTextEn,
                        'button_text_ar' => $btnTextAr,
                        'button_text_en' => $btnTextEn,
                        'youtube_link' => $ytLinkAr ?: $ytLinkEn,
                        'youtube_link_ar' => $ytLinkAr,
                        'youtube_link_en' => $ytLinkEn,
                    ];
                }
            }

            // Clean up deleted slide images from disk
            $newPathsAr = array_filter(array_column($newSlides, 'image_ar'));
            $newPathsEn = array_filter(array_column($newSlides, 'image_en'));
            $allNewPaths = array_merge($newPathsAr, $newPathsEn);

            foreach ($existingSlides as $oldSlide) {
                foreach (['image', 'image_ar', 'image_en'] as $key) {
                    $oldPath = $oldSlide[$key] ?? null;
                    if ($oldPath && ! in_array($oldPath, $allNewPaths)) {
                        if (\Storage::disk('public')->exists($oldPath)) {
                            \Storage::disk('public')->delete($oldPath);
                        }
                    }
                }
            }

            Setting::updateOrCreate(['key' => 'hero_slides'], ['value' => $newSlides]);
        }

        // Handle Promo Popup
        $existingPopup = Setting::where('key', 'promo_popup')->first();
        $existingPopupValue = [];
        if ($existingPopup && ! empty($existingPopup->value)) {
            $existingPopupValue = is_array($existingPopup->value) ? $existingPopup->value : (json_decode($existingPopup->value, true) ?: []);
        }

        $popupImage = $existingPopupValue['image'] ?? null;
        if ($request->hasFile('popup_image')) {
            $popupImage = $request->file('popup_image')->store('settings/popup', 'public');
            if (! empty($existingPopupValue['image']) && \Storage::disk('public')->exists($existingPopupValue['image'])) {
                \Storage::disk('public')->delete($existingPopupValue['image']);
            }
        }

        $popupData = [
            'enabled' => $request->boolean('popup_enabled', false),
            'image' => $popupImage,
            'title' => $request->input('popup_title', ''),
            'text' => $request->input('popup_text', ''),
            'link' => $request->input('popup_link', ''),
            'button_text' => $request->input('popup_button_text', __('تصفح العروض')),
        ];
        Setting::updateOrCreate(['key' => 'promo_popup'], ['value' => $popupData]);

        // Handle Main Gallery (Multiple Images)
        if ($request->hasFile('main_gallery')) {
            $existingGallery = Setting::where('key', 'main_gallery')->first();
            $galleryPaths = [];
            if ($existingGallery && ! empty($existingGallery->value)) {
                $galleryPaths = is_array($existingGallery->value) ? $existingGallery->value : (json_decode($existingGallery->value, true) ?: []);
            }

            foreach ($request->file('main_gallery') as $image) {
                $path = $image->store('settings/gallery', 'public');
                $galleryPaths[] = $path;
            }
            Setting::updateOrCreate(['key' => 'main_gallery'], ['value' => $galleryPaths]);
        }

        // Handle Gallery Image Deletion
        if ($request->has('delete_gallery_image')) {
            $imageToDelete = $request->get('delete_gallery_image');
            $existingGallery = Setting::where('key', 'main_gallery')->first();
            if ($existingGallery && ! empty($existingGallery->value)) {
                $galleryPaths = is_array($existingGallery->value) ? $existingGallery->value : (json_decode($existingGallery->value, true) ?: []);
                $galleryPaths = array_values(array_filter($galleryPaths, function ($path) use ($imageToDelete) {
                    return $path !== $imageToDelete;
                }));
                Setting::updateOrCreate(['key' => 'main_gallery'], ['value' => $galleryPaths]);

                // Optionally delete the physical file
                if (\Storage::disk('public')->exists($imageToDelete)) {
                    \Storage::disk('public')->delete($imageToDelete);
                }
            }
        }

        // Clear settings cache
        try {
            app(\App\Services\Cache\BaseCacheService::class)->forgetSettings();
        } catch (\Throwable $e) {
            // Ignore if cache service unavailable
        }

        return back()->with('success', __('تم تحديث الإعدادات بنجاح'));
    }

    public function clearCache()
    {
        try {
            // Clear Laravel cache
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');

            // Clear application services cache
            app(\App\Services\Cache\BaseCacheService::class)->forgetSettings();
            app(\App\Services\Cache\HomeCacheService::class)->forgetHome();

            return response()->json([
                'success' => true,
                'message' => __('تم مسح الذاكرة المؤقتة (Cache) بنجاح!')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * اختبار الاتصال ببوابة HyperPay من صفحة الإعدادات
     */
    public function testHyperPayConnection()
    {
        try {
            $result = app(\App\Services\HyperPayService::class)->testConnection();
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * إرسال إيميل اختباري لاختبار إعدادات SMTP
     */
    public function testEmailConnection(Request $request)
    {
        $to = $request->input('to');
        if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['success' => false, 'message' => 'يرجى إدخال بريد إلكتروني صحيح']);
        }

        $result = app(\App\Services\MailConfigService::class)->sendTestEmail($to);
        return response()->json($result);
    }

    /**
     * Handle HyperPay checkbox fields that may not be submitted
     */
    protected function handleHyperPayCheckboxes(Request $request): void
    {
        if (! $request->has('hyperpay_enabled')) {
            Setting::updateOrCreate(['key' => 'hyperpay_enabled'], ['value' => '0']);
        }
    }
}
