<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Brand;
use App\Models\Car;
use App\Models\Employee;
use App\Models\Setting;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        if ($isAdmin) {
            $stats = \Illuminate\Support\Facades\Cache::remember('crm_dashboard_stats', 300, function () {
                return [
                    'total' => Booking::count(),
                    'new' => Booking::new()->count(),
                    'in_progress' => Booking::inProgress()->count(),
                    'sold' => Booking::completed()->count(),
                    'rejected' => Booking::where('status', 'rejected')->count(),
                ];
            });

            // أكثر 5 سيارات عليها طلبات
            $topCars = \Illuminate\Support\Facades\Cache::remember('crm_dashboard_top_cars', 300, function () {
                return Car::withCount('bookings')
                    ->with('brand') // Eager load brand for grid
                    ->orderByDesc('bookings_count')
                    ->limit(6) // increased to 6 for a better grid
                    ->get();
            });

            // إحصائيات الأسبوع (آخر 7 أيام) للـ Chart
            $weeklyBookings = \Illuminate\Support\Facades\Cache::remember('crm_dashboard_weekly_bookings', 300, function () {
                return Booking::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(6))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
            });

            $recentBookings = Booking::with('car.brand')
                ->latest()
                ->limit(10)
                ->get();
        } else {
            $userId = $user->id;

            $stats = \Illuminate\Support\Facades\Cache::remember('crm_dashboard_stats_user_' . $userId, 300, function () use ($userId) {
                return [
                    'total' => Booking::where('assigned_to', $userId)->count(),
                    'new' => Booking::where('assigned_to', $userId)->where('status', 'new')->count(),
                    'in_progress' => Booking::where('assigned_to', $userId)->where('status', 'in_progress')->count(),
                    'sold' => Booking::where('assigned_to', $userId)->where('status', 'completed')->count(),
                    'rejected' => Booking::where('assigned_to', $userId)->where('status', 'rejected')->count(),
                ];
            });

            $topCars = \Illuminate\Support\Facades\Cache::remember('crm_dashboard_top_cars_user_' . $userId, 300, function () use ($userId) {
                return Car::withCount(['bookings' => function ($q) use ($userId) {
                        $q->where('assigned_to', $userId);
                    }])
                    ->with('brand')
                    ->orderByDesc('bookings_count')
                    ->limit(6)
                    ->get();
            });

            $weeklyBookings = \Illuminate\Support\Facades\Cache::remember('crm_dashboard_weekly_bookings_user_' . $userId, 300, function () use ($userId) {
                return Booking::where('assigned_to', $userId)
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(6))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
            });

            $recentBookings = Booking::with('car.brand')
                ->where('assigned_to', $userId)
                ->latest()
                ->limit(10)
                ->get();
        }

        $totals = \Illuminate\Support\Facades\Cache::remember('crm_dashboard_totals', 300, function () {
            return [
                'cars' => Car::where('is_active', true)->count(),
                'brands' => Brand::count(),
                'employees' => Employee::count(),
            ];
        });

        $totalCars = $totals['cars'];
        $totalBrands = $totals['brands'];
        $totalEmployees = $totals['employees'];

        $trackingGA = Setting::where('key', 'google_analytics_id')->first()?->value ?? '';
        $trackingPixel = Setting::where('key', 'meta_pixel_id')->first()?->value ?? '';

        return view('crm.dashboard', compact(
            'stats', 'topCars', 'weeklyBookings', 'recentBookings',
            'totalCars', 'totalBrands', 'totalEmployees',
            'trackingGA', 'trackingPixel'
        ));
    }
}
