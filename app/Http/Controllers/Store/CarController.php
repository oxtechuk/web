<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Services\Cache\CarCacheService;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function __construct(
        private readonly CarCacheService $cache,
    ) {}

    public function index(Request $request)
    {
        $hero = $this->cache->rememberHeroSetting('store_hero');

        $query = Car::with(['brand', 'activeOffers'])->where('is_active', true)->where('is_highlighted', '!=', 'coming_soon');

        if ($request->filled('brands') && is_array($request->brands)) {
            $query->whereIn('brand_id', $request->brands);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        if ($request->filled('min_price')) {
            $query->where('cash_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('cash_price', '<=', $request->max_price);
        }
        if ($request->filled('search') || $request->filled('q')) {
            $s = $request->search ?: $request->q;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('model', 'like', "%{$s}%")
                    ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('offer_id')) {
            $query->whereHas('offers', function ($q) use ($request) {
                $q->where('offers.id', $request->offer_id);
            });
        }

        match ($request->sort ?? 'latest') {
            'price_asc' => $query->orderBy('cash_price'),
            'price_desc' => $query->orderByDesc('cash_price'),
            'year_desc' => $query->orderByDesc('year'),
            default => $query->latest('id'),
        };

        $cars = $query->paginate(12)->withQueryString();

        $filterData = $this->cache->rememberCarFilters();
        $brands = $filterData['brands'];
        $years = $filterData['years'];

        $types = ['sedan' => 'سيدان', 'suv' => 'SUV', 'coupe' => 'كوبيه', 'hatchback' => 'هاتشباك', 'pickup' => 'بيك آب', 'van' => 'فان', 'other' => 'أخرى'];

        return view('store.cars.index', compact('cars', 'brands', 'years', 'types', 'hero'));
    }

    public function show($slug)
    {
        $car = Car::with(['brand', 'images', 'offers' => fn ($q) => $q->active()])
            ->where(function ($q) use ($slug) {
                $q->where('slug->en', $slug)
                    ->orWhere('slug->ar', $slug);
            })
            ->where('is_active', true)
            ->firstOrFail();

        $car->increment('views');

        $related = Car::with(['brand', 'activeOffers'])
            ->where('brand_id', $car->brand_id)
            ->where('id', '!=', $car->id)
            ->where('is_active', true)
            ->where('is_highlighted', '!=', 'coming_soon')
            ->limit(4)
            ->get();

        return view('store.cars.show', compact('car', 'related'));
    }

    public function downloadPdf($id)
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(120);

        $car = Car::with(['brand', 'category', 'specifications', 'features_list', 'images'])
            ->where('id', $id)
            ->orWhere('slug->ar', $id)
            ->orWhere('slug->en', $id)
            ->firstOrFail();

        $filename = \Illuminate\Support\Str::slug($car->name ?: 'car') . '-specs.pdf';

        // If custom spec_file is uploaded in CRM, serve it as PDF
        if (!empty($car->spec_file) && \Storage::disk('public')->exists($car->spec_file)) {
            $filePath = storage_path('app/public/' . $car->spec_file);
            return response()->download($filePath, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        // Writable Directory Fallbacks for Fonts & Temp
        $fontDir = storage_path('fonts');
        if (!file_exists($fontDir)) {
            @mkdir($fontDir, 0777, true);
        }
        if (!is_writable($fontDir)) {
            $fontDir = sys_get_temp_dir();
        }

        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            @mkdir($tempDir, 0777, true);
        }
        if (!is_writable($tempDir)) {
            $tempDir = sys_get_temp_dir();
        }

        $pdfService = app(\App\Services\CarPdfService::class);

        try {
            $pdf = null;
            if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('store.cars.pdf_report', ['car' => $car, 'pdfService' => $pdfService]);
            } elseif (class_exists(\Barryvdh\DomPDF\Facade\PDF::class)) {
                $pdf = \Barryvdh\DomPDF\Facade\PDF::loadView('store.cars.pdf_report', ['car' => $car, 'pdfService' => $pdfService]);
            } elseif (app()->bound('dompdf.wrapper')) {
                $pdf = app('dompdf.wrapper')->loadView('store.cars.pdf_report', ['car' => $car, 'pdfService' => $pdfService]);
            }

            if (!$pdf) {
                abort(500, __('حزمة إنشاء PDF غير مثبتة على السيرفر'));
            }

            $pdf->setPaper('a4', 'portrait')
                ->setOption('fontDir', $fontDir)
                ->setOption('fontCache', $fontDir)
                ->setOption('tempDir', $tempDir)
                ->setOption('chroot', [public_path(), storage_path(), base_path(), sys_get_temp_dir()])
                ->setOption('isRemoteEnabled', true)
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isFontSubsettingEnabled', true);

            $output = $pdf->output();

            return response($output, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Content-Length' => strlen($output),
                'Cache-Control' => 'public, must-revalidate, max-age=0',
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Car PDF Download Error: ' . $e->getMessage());
            abort(500, __('حدث خطأ أثناء إنشاء ملف PDF. يرجى المحاولة لاحقاً.'));
        }
    }

    public function comingSoonShowroom()
    {
        $hero = [
            'title' => __('قريباً في السوق'),
            'subtitle' => __('تصفح السيارات الحصرية المنتظرة وقريباً في صالات عرضنا.'),
            'image' => null,
        ];

        $cars = Car::with(['brand', 'activeOffers'])
            ->where('is_active', true)
            ->where('is_highlighted', 'coming_soon')
            ->latest('id')
            ->paginate(12);

        $types = ['sedan' => 'سيدان', 'suv' => 'SUV', 'coupe' => 'كوبيه', 'hatchback' => 'هاتشباك', 'pickup' => 'بيك آب', 'van' => 'فان', 'other' => 'أخرى'];

        return view('store.cars.coming_soon', compact('cars', 'types', 'hero'));
    }
}
