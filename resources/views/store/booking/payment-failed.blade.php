@extends('store.layouts.app')
@section('title', __('فشل الدفع') . ' | ' . __('حجز سيارة'))

@section('content')
<div class="py-5" style="min-height:80vh; background: linear-gradient(135deg, #1a0505 0%, #2d0a0a 50%, #1a0505 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">

                {{-- Failure Icon --}}
                <div class="text-center mb-5">
                    <div class="position-relative d-inline-block mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto"
                            style="width:100px;height:100px;background:rgba(220,53,69,0.15);border:2px solid #dc3545;animation:pulse 2s infinite;">
                            <i class="bi bi-x-circle-fill text-danger" style="font-size:3rem;"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold text-white mb-2">{{ __('فشل الدفع') }}</h2>
                    <p class="text-white-50">{{ __('لم يتم خصم أي مبلغ من بطاقتك. حجزك محفوظ.') }}</p>
                </div>

                {{-- Error Details --}}
                <div class="card border-0 rounded-4 mb-4 overflow-hidden">
                    <div class="card-body p-4" style="background:rgba(220,53,69,0.08);border:1px solid rgba(220,53,69,0.3);">
                        <h6 class="text-danger fw-bold mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ __('سبب الرفض') }}
                        </h6>
                        <p class="text-white mb-2">
                            {{ session('payment_error') ?? ($booking->payment_result_description ?? __('حدث خطأ أثناء معالجة الدفع.')) }}
                        </p>
                        @if(session('payment_code') && session('payment_code') !== 'CURL_ERROR' && session('payment_code') !== 'EXCEPTION')
                        <p class="text-white-50 small mb-0">
                            <code class="text-danger">{{ session('payment_code') }}</code>
                        </p>
                        @endif
                    </div>
                </div>

                {{-- Booking Info --}}
                <div class="card border-0 rounded-4 mb-4">
                    <div class="card-body p-4" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);">
                        <h6 class="text-white-50 small mb-3">{{ __('بيانات الحجز') }}</h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <p class="text-white-50 small mb-1">{{ __('رقم الحجز') }}</p>
                                <p class="text-white fw-bold mb-0">#{{ $booking->id }}</p>
                            </div>
                            <div class="col-6">
                                <p class="text-white-50 small mb-1">{{ __('السيارة') }}</p>
                                <p class="text-white fw-bold mb-0">{{ $booking->car?->name ?? '—' }}</p>
                            </div>
                            <div class="col-6">
                                <p class="text-white-50 small mb-1">{{ __('الاسم') }}</p>
                                <p class="text-white fw-bold mb-0">{{ $booking->client_name }}</p>
                            </div>
                            <div class="col-6">
                                <p class="text-white-50 small mb-1">{{ __('الحالة') }}</p>
                                <span class="badge bg-danger">{{ __('فشل الدفع') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- نصائح --}}
                <div class="card border-0 rounded-4 mb-5">
                    <div class="card-body p-4" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);">
                        <h6 class="text-white fw-bold mb-3">
                            <i class="bi bi-lightbulb-fill text-warning me-2"></i>
                            {{ __('ما يمكنك فعله') }}
                        </h6>
                        <ul class="text-white-50 small mb-0 ps-3">
                            <li class="mb-2">{{ __('تأكد من صحة رقم البطاقة وتاريخ الانتهاء والـ CVV') }}</li>
                            <li class="mb-2">{{ __('تأكد من وجود رصيد كافٍ في البطاقة') }}</li>
                            <li class="mb-2">{{ __('جرب استخدام بطاقة أخرى (Visa، Mastercard، mada)') }}</li>
                            <li class="mb-2">{{ __('تواصل مع البنك للتأكد من عدم وجود قيود على البطاقة') }}</li>
                            <li>{{ __('يمكنك إكمال الحجز بدون دفع والتواصل معنا لاحقاً') }}</li>
                        </ul>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex flex-column gap-3">
                    {{-- إعادة المحاولة --}}
                    <a href="{{ route('store.booking.pay.form', $booking->id) }}"
                        class="btn py-3 fw-bold rounded-3 text-white border-0 fs-6"
                        style="background: linear-gradient(135deg, #f4a417, #e8920a);">
                        <i class="bi bi-arrow-repeat me-2"></i>
                        {{ __('حاول الدفع مرة أخرى') }}
                    </a>

                    {{-- إكمال بدون دفع --}}
                    <a href="{{ route('store.booking.success', $booking->id) }}"
                        class="btn btn-outline-light py-3 fw-bold rounded-3 fs-6">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ __('إكمال الحجز بدون دفع') }}
                    </a>

                    {{-- الرجوع للرئيسية --}}
                    <a href="{{ route('store.home') }}"
                        class="btn btn-link text-white-50 text-decoration-none text-center">
                        <i class="bi bi-house me-1"></i>
                        {{ __('العودة للرئيسية') }}
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
@keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(220,53,69,0.4); }
    50% { box-shadow: 0 0 0 15px rgba(220,53,69,0); }
}
</style>
@endsection
