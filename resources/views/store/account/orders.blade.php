@extends('store.account.layout')

@section('account_content')
<div class="orders-page">

    {{-- DYNAMIC ORDERS LIST --}}
    @if($bookings->count() > 0)
        <div class="orders-cards-list">
            @foreach($bookings as $booking)
            <div class="order-main-card">

                {{-- CARD HEADER ROW --}}
                <div class="order-card-header">
                    <div class="order-card-title-group">
                        <h3 class="order-number-title">
                            {{ __('طلب رقم #') }}{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}
                        </h3>
                        <div class="order-sub-info">
                            <span>1 {{ __('سيارة') }}</span>
                            <span class="dot-sep">•</span>
                            <span>{{ $booking->client_name }}</span>
                            <span class="dot-sep">•</span>
                            <span>{{ $booking->created_at->format('h:i A, Y/m/d') }}</span>
                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="order-card-actions">
                        @if($booking->user_id === Auth::id())
                            <a href="{{ route('store.account.bookings.invoice', $booking->id) }}"
                               class="btn-order-action btn-download-pdf" target="_blank">
                                <i class="bi bi-download"></i>
                                <span>{{ __('تحميل الفاتورة') }}</span>
                            </a>
                        @endif

                        @if($booking->car)
                            <a href="{{ route('store.cars.show', $booking->car->slug) }}"
                               class="btn-order-action btn-order-details" title="{{ __('عرض السيارة') }}">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="order-header-divider"></div>

                {{-- ORDER META DETAILS --}}
                <div class="order-meta-grid">
                    <div class="order-meta-item">
                        <span class="meta-label">{{ __('حالة الطلب:') }}</span>
                        @php
                            $statusColors = [
                                'new'        => 'status-orange',
                                'contacted'  => 'status-blue',
                                'interested' => 'status-purple',
                                'rejected'   => 'status-red',
                                'sold'       => 'status-green',
                            ];
                            $statusClass = $statusColors[$booking->status] ?? 'status-orange';
                        @endphp
                        <span class="status-value-pill {{ $statusClass }}">
                            {{ $booking->status_label }}
                        </span>
                    </div>

                    <div class="order-meta-item">
                        <span class="meta-label">{{ __('تاريخ الطلب:') }}</span>
                        <span class="meta-val">{{ $booking->created_at->format('Y/m/d') }}</span>
                    </div>

                    @if($booking->location)
                    <div class="order-meta-item">
                        <span class="meta-label">{{ __('الموقع:') }}</span>
                        <span class="meta-val">{{ $booking->location }}</span>
                    </div>
                    @endif

                    <div class="order-meta-item">
                        <span class="meta-label">{{ __('الإجمالي / القسط:') }}</span>
                        <span class="meta-val text-bold-price">
                            @if($booking->monthly_installment)
                                {{ number_format($booking->monthly_installment) }} <small>{{ __('ريال/شهر') }}</small>
                            @elseif($booking->total_price)
                                {{ number_format($booking->total_price) }} <small>{{ __('ريال') }}</small>
                            @else
                                —
                            @endif
                        </span>
                    </div>
                </div>

                {{-- VEHICLE SUB-CARD (Product Grid Style) --}}
                @if($booking->car)
                <div class="order-car-subcard-grid mt-3">
                    <div class="car-subcard">
                        <div class="car-subcard-img-wrap">
                            <img src="{{ $booking->car->thumbnail ? asset('storage/' . $booking->car->thumbnail) : asset('assets/images/placeholder-car.jpg') }}"
                                 alt="{{ $booking->car->name }}"
                                 class="car-subcard-img">
                        </div>
                        <div class="car-subcard-info">
                            <h4 class="car-subcard-name">
                                <a href="{{ route('store.cars.show', $booking->car->slug) }}" class="text-decoration-none text-dark">
                                    {{ $booking->car->brand?->name }} {{ $booking->car->name }} {{ $booking->car->year }}
                                </a>
                            </h4>
                            <div class="car-subcard-specs">
                                @if($booking->down_payment !== null)
                                    <span>{{ __('المقدم:') }} {{ number_format($booking->down_payment) }} {{ __('ريال') }}</span>
                                @endif
                                @if($booking->duration_years)
                                    <span class="ms-2 me-2">• {{ __('مدة التمويل:') }} {{ $booking->duration_years }} {{ __('سنوات') }} ({{ $booking->duration_years * 12 }} {{ __('شهر') }})</span>
                                @endif
                                <span class="ms-2 me-2">• {{ __('نوع الطلب:') }} {{ \App\Models\Booking::BOOKING_TYPES_LABELS[$booking->booking_type] ?? $booking->booking_type }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- PAYMENT BUTTON IF PENDING --}}
                @if($booking->payment_status === 'pending')
                    <div class="mt-3 text-end">
                        <a href="{{ route('store.booking.pay.form', $booking->id) }}" class="btn btn-danger btn-sm rounded-pill fw-bold px-4">
                            <i class="bi bi-credit-card me-1"></i> {{ __('استكمال عملية الدفع') }}
                        </a>
                    </div>
                @endif

            </div>
            @endforeach
        </div>

        {{-- PAGINATION --}}
        @if($bookings->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $bookings->links() }}
            </div>
        @endif

    @else
        {{-- EMPTY STATE --}}
        <div class="orders-empty-state">
            <div class="empty-icon"><i class="bi bi-box-seam"></i></div>
            <h4 class="empty-title">{{ __('لا توجد طلبات في حسابك بعد') }}</h4>
            <p class="empty-desc">{{ __('تصفح المعرض واطلب سيارة أحلامك بسهولة من موقعنا.') }}</p>
            <a href="{{ route('store.cars.index') }}" class="btn btn-danger rounded-pill fw-bold px-4 py-2">
                <i class="bi bi-car-front-fill me-1"></i> {{ __('تصفح السيارات') }}
            </a>
        </div>
    @endif

</div>

<style>
/* ===== MAIN ORDER CARD ===== */
.orders-cards-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.order-main-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    transition: box-shadow 0.2s ease;
}
.order-main-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

/* Header Row */
.order-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
}
.order-number-title {
    font-size: 18px;
    font-weight: 900;
    color: #0f172a;
    margin: 0 0 4px 0;
}
.order-sub-info {
    font-size: 13px;
    color: #64748b;
}
.dot-sep {
    margin: 0 4px;
    opacity: 0.5;
}

/* Header Actions */
.order-card-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}
.btn-download-pdf {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    background: #ffffff;
    color: #1e293b;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
}
.btn-download-pdf:hover {
    border-color: #EE1E26;
    color: #EE1E26;
    background: #fef2f2;
}
.btn-order-details {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
    color: #64748b;
    text-decoration: none;
    transition: all 0.2s ease;
}
.btn-order-details:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.order-header-divider {
    height: 1px;
    background: #f1f5f9;
    margin: 16px 0;
}

/* Order Meta Grid */
.order-meta-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 20px 30px;
    font-size: 13px;
    margin-bottom: 16px;
}
.order-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
}
.meta-label {
    color: #64748b;
    font-weight: 600;
}
.meta-val {
    color: #0f172a;
    font-weight: 700;
}
.text-bold-price {
    font-size: 14px;
    font-weight: 900;
    color: #0f172a;
}

/* Status Badges */
.status-value-pill {
    padding: 3px 12px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 800;
}
.status-orange { background: #fff7ed; color: #c2410c; }
.status-purple { background: #faf5ff; color: #7e22ce; }
.status-blue   { background: #eff6ff; color: #1d4ed8; }
.status-red    { background: #fef2f2; color: #b91c1c; }
.status-green  { background: #f0fdf4; color: #15803d; }

/* VEHICLE SUB-CARD (Product Grid Style) */
.order-car-subcard-grid {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.car-subcard {
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 12px;
    padding: 12px 16px;
    display: flex;
    gap: 16px;
    align-items: center;
}
.car-subcard-img-wrap {
    width: 90px;
    height: 70px;
    border-radius: 8px;
    overflow: hidden;
    background: #ffffff;
    flex-shrink: 0;
    border: 1px solid #e2e8f0;
}
.car-subcard-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.car-subcard-name {
    font-size: 15px;
    font-weight: 800;
    margin: 0 0 6px 0;
    line-height: 1.3;
}
.car-subcard-specs {
    font-size: 12px;
    color: #64748b;
    line-height: 1.5;
}

/* EMPTY STATE */
.orders-empty-state {
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 16px;
    padding: 50px 20px;
    text-align: center;
}
.empty-icon {
    font-size: 48px;
    color: #cbd5e1;
    margin-bottom: 12px;
}
.empty-title {
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 6px;
}
.empty-desc {
    font-size: 14px;
    color: #64748b;
    margin-bottom: 20px;
}

/* RTL / LTR ADJUSTMENTS */
html[dir="rtl"] .order-card-header { text-align: right; }
html[dir="ltr"] .order-card-header { text-align: left; }
html[dir="rtl"] .car-subcard { text-align: right; }
html[dir="ltr"] .car-subcard { text-align: left; }

@media (max-width: 600px) {
    .order-card-header {
        flex-direction: column;
    }
    .order-card-actions {
        width: 100%;
        justify-content: space-between;
    }
    .btn-download-pdf {
        flex-grow: 1;
        justify-content: center;
    }
    .order-meta-grid {
        flex-direction: column;
        gap: 10px;
    }
}
</style>
@endsection
