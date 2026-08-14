@extends('store.layouts.app')
@section('title', __('الدفع قيد المعالجة') . ' | ' . __('حجز سيارة'))

@section('content')
<div class="py-5" style="min-height:80vh; background: linear-gradient(135deg, #0a1628 0%, #162040 50%, #0a1628 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">

                {{-- Pending Icon --}}
                <div class="text-center mb-5">
                    <div class="position-relative d-inline-block mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto"
                            style="width:100px;height:100px;background:rgba(255,193,7,0.15);border:2px solid #ffc107;animation:spin-pulse 2s infinite;">
                            <i class="bi bi-hourglass-split text-warning" style="font-size:3rem;"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold text-white mb-2">{{ __('الدفع قيد المعالجة') }}</h2>
                    <p class="text-white-50">{{ __('طلب الدفع مُرسل بنجاح وهو الآن قيد المراجعة من البنك.') }}</p>
                </div>

                {{-- Info Card --}}
                <div class="card border-0 rounded-4 mb-4">
                    <div class="card-body p-4" style="background:rgba(255,193,7,0.08);border:1px solid rgba(255,193,7,0.3);">
                        <h6 class="text-warning fw-bold mb-3">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            {{ __('ما يحدث الآن') }}
                        </h6>
                        <ul class="text-white-50 small mb-0 ps-3">
                            <li class="mb-2">{{ __('طلب الدفع مُرسل إلى HyperPay وهو تحت المراجعة') }}</li>
                            <li class="mb-2">{{ __('سيتم تحديث حالة الدفع تلقائياً عند اكتمال المراجعة') }}</li>
                            <li class="mb-2">{{ __('ستصلك رسالة تأكيد خلال دقائق') }}</li>
                            <li>{{ __('حجزك محفوظ بالكامل بغض النظر عن نتيجة الدفع') }}</li>
                        </ul>
                    </div>
                </div>

                {{-- Booking Summary --}}
                <div class="card border-0 rounded-4 mb-5">
                    <div class="card-body p-4" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);">
                        <div class="row g-2">
                            <div class="col-6">
                                <p class="text-white-50 small mb-1">{{ __('رقم الحجز') }}</p>
                                <p class="text-white fw-bold mb-0">#{{ $booking->id }}</p>
                            </div>
                            <div class="col-6">
                                <p class="text-white-50 small mb-1">{{ __('حالة الدفع') }}</p>
                                <span class="badge bg-warning text-dark">{{ __('قيد المعالجة') }}</span>
                            </div>
                            @if($booking->payment_transaction_id)
                            <div class="col-12">
                                <p class="text-white-50 small mb-1">{{ __('رقم المعاملة') }}</p>
                                <p class="text-white-50 small mb-0 font-monospace">{{ $booking->payment_transaction_id }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="d-flex flex-column gap-3">
                    <a href="{{ route('store.home') }}"
                        class="btn py-3 fw-bold rounded-3 text-white border-0 fs-6"
                        style="background: linear-gradient(135deg, #f4a417, #e8920a);">
                        <i class="bi bi-house me-2"></i>
                        {{ __('العودة للرئيسية') }}
                    </a>
                    <a href="{{ route('store.booking.success', $booking->id) }}"
                        class="btn btn-outline-light py-3 rounded-3 fs-6">
                        <i class="bi bi-receipt me-2"></i>
                        {{ __('عرض تفاصيل الحجز') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
@keyframes spin-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(255,193,7,0.4); }
    50% { box-shadow: 0 0 0 15px rgba(255,193,7,0); }
}
.font-monospace { font-family: monospace; font-size: 12px; }
</style>
@endsection
