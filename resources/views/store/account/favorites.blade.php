@extends('store.account.layout')

@section('account_content')
<div class="premium-content-card">
    <h2 class="premium-page-title">
        {{ __('المفضلة') }}
    </h2>

    @if($favoriteCars->count() > 0)
        <div class="premium-fav-grid">
            @foreach($favoriteCars as $car)
                <div class="premium-fav-card">
                    <div class="premium-fav-image-container">
                        @if($car->thumbnail)
                            <img src="{{ asset('storage/' . $car->thumbnail) }}" class="premium-fav-img" alt="{{ $car->getTranslation('name', App::getLocale()) }}">
                        @else
                            <div class="premium-fav-img-placeholder">
                                <i class="bi bi-car-front-fill"></i>
                            </div>
                        @endif
                    </div>

                    <div class="premium-fav-body">
                        <h4 class="premium-fav-title">{{ $car->getTranslation('name', App::getLocale()) }}</h4>
                        
                        <div class="premium-fav-meta">
                            <span class="premium-fav-badge-avail {{ $car->availability_status ? 'avail-yes' : 'avail-no' }}">
                                {{ $car->availability_status ? __('متاحة') : __('غير متاحة') }}
                            </span>
                            <span class="premium-fav-price">{{ number_format($car->cash_price) }} {{ __('ر.س') }}</span>
                        </div>

                        <div class="premium-fav-actions">
                            <a href="{{ route('store.cars.show', $car->slug) }}" class="fav-action-btn fav-btn-details">
                                {{ __('عرض التفاصيل والطلب') }}
                            </a>
                            <form action="{{ route('store.account.favorites.toggle', $car->id) }}" method="POST" class="m-0 p-0">
                                @csrf
                                <button type="submit" class="fav-action-btn fav-btn-delete" title="{{ __('حذف من المفضلة') }}">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @if($favoriteCars->hasPages())
            <div class="premium-pagination">
                {{ $favoriteCars->links() }}
            </div>
        @endif
    @else
        {{-- Custom Redesigned Empty Favorites State --}}
        <div class="premium-empty-state">
            <div class="empty-state-glow"></div>
            <div class="empty-state-icon-container">
                <i class="bi bi-heartbreak-fill"></i>
            </div>
            <h4 class="empty-state-title">{{ __('قائمة المفضلة فارغة') }}</h4>
            <p class="empty-state-desc">{{ __('لم تقم بإضافة أي سيارات إلى قائمة المفضلة حتى الآن. تصفح معرضنا وأضف سياراتك المفضلة للمقارنة والوصول السريع.') }}</p>
            <a href="{{ route('store.cars.index') }}" class="premium-cta-btn">
                <i class="bi bi-car-front-fill"></i> {{ __('استكشف المعرض') }}
            </a>
        </div>
    @endif
</div>

<style>
/* Favorites Grid layout */
.premium-fav-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

.premium-fav-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid rgba(0, 0, 0, 0.04);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01);
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    display: flex;
    flex-direction: column;
}

.premium-fav-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.05);
    border-color: rgba(238, 30, 38, 0.12);
}

.premium-fav-image-container {
    width: 100%;
    height: 180px;
    overflow: hidden;
    position: relative;
    background-color: #f8f9fa;
}

.premium-fav-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.premium-fav-card:hover .premium-fav-img {
    transform: scale(1.06);
}

.premium-fav-img-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ccc;
    font-size: 55px;
}

.premium-fav-body {
    padding: 20px;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.premium-fav-title {
    font-size: 17px;
    font-weight: 800;
    color: #1a1a1a;
    margin: 0 0 15px 0;
    line-height: 1.4;
}

.premium-fav-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.premium-fav-badge-avail {
    display: inline-block;
    padding: 4px 12px;
    font-size: 11px;
    font-weight: 800;
    border-radius: 6px;
}

.avail-yes {
    background-color: #e6f7ef;
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.1);
}

.avail-no {
    background-color: #fef2f2;
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.1);
}

.premium-fav-price {
    font-size: 16px;
    font-weight: 800;
    color: var(--primary, linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%));
}

.premium-fav-actions {
    display: flex;
    gap: 10px;
    margin-top: auto;
}

.fav-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 40px;
    border-radius: 12px;
    transition: all 0.25s ease;
    cursor: pointer;
    font-weight: 700;
}

.fav-btn-details {
    flex: 1;
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    color: #495057;
    text-decoration: none;
    font-size: 13px;
}

.fav-btn-details:hover {
    background-color: var(--primary, linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%));
    color: #ffffff;
    border-color: var(--primary, linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%));
}

.fav-btn-delete {
    width: 40px;
    background: none;
    border: 1px solid rgba(239, 68, 68, 0.2);
    color: #ef4444;
}

.fav-btn-delete:hover {
    background-color: #fef2f2;
    color: #b91c1c;
    border-color: #ef4444;
}

/* Pagination Wrapper */
.premium-pagination {
    margin-top: 25px;
    display: flex;
    justify-content: center;
}

/* Responsive grid rules */
@media (max-width: 768px) {
    .premium-fav-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}
</style>
@endsection
