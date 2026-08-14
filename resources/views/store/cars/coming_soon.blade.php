@extends('store.layouts.app')

@section('title', __('سيارات قريباً في السوق') . ' | ' . (is_array($globalSettings['site_name'] ?? null) ? ($globalSettings['site_name'][App::getLocale()] ?? ($globalSettings['site_name']['ar'] ?? 'GR Motors')) : ($globalSettings['site_name'] ?? 'GR Motors')))

@section('css')
<style>
:root {
  --primary-gradient: linear-gradient(135deg, #FD7277 0%, #EE1E26 100%);
  --secondary-gradient: linear-gradient(135deg, #1c1c1c 0%, #111111 100%);
  --color-black:      #111111;
  --color-dark:       #1a1a1a;
  --color-gray-900:   #222222;
  --color-gray-700:   #444444;
  --color-gray-500:   #777777;
  --color-gray-300:   #cccccc;
  --color-gray-100:   #f4f5f8;
  --color-white:      #ffffff;
  --color-border:     #eef0f3;
  --transition:       0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  --container-max:    1200px;
  --container-pad:    clamp(16px, 4vw, 40px);
}

.explore {
  padding: 80px 0 100px;
  background-color: #f8f9fb;
}

.explore__container {
  max-width: var(--container-max);
  margin: 0 auto;
  padding: 0 var(--container-pad);
}

.explore__title {
  font-size: clamp(28px, 4vw, 40px);
  font-weight: 800;
  color: var(--color-black);
  text-align: center;
  margin-bottom: 12px;
  position: relative;
  padding-bottom: 16px;
}

.explore__title::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 60px;
  height: 4px;
  background: var(--primary-gradient);
  border-radius: 10px;
}

.explore__title--coming-soon {
  color: #EE1E26 !important;
}

.explore__count {
  font-size: 15px;
  color: var(--color-gray-500);
  margin-bottom: 40px;
  text-align: center;
  font-weight: 600;
}

.explore__count strong {
  color: #EE1E26;
  font-size: 17px;
}

.explore__layout {
  padding-bottom: 24px;
}

/* ============================================================
   CARS GRID (Restricting to exactly 2 columns on desktop)
   ============================================================ */
.cars-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 30px;
}

/* ============================================================
   CAR CARD REDESIGN
   ============================================================ */
.car-card {
  background: var(--color-white);
  border: 1px solid var(--color-border);
  border-radius: 28px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: var(--transition);
  height: 100%;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.02);
}

.car-card:hover {
  box-shadow: 0 20px 45px rgba(0, 0, 0, 0.08);
  transform: translateY(-6px);
  border-color: rgba(238, 30, 38, 0.15);
}

.car-card__image-wrap {
  position: relative;
  background: var(--color-gray-100);
  overflow: hidden;
  aspect-ratio: 16 / 10;
}

.car-card__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
}

.car-card:hover .car-card__image {
  transform: scale(1.06);
}

.car-card__badge {
  position: absolute;
  top: 20px;
  {{ App::getLocale() == 'ar' ? 'right' : 'left' }}: 20px;
  background: rgba(255, 255, 255, 0.85);
  color: #111111;
  font-size: 12px;
  font-weight: 800;
  padding: 8px 16px;
  border-radius: 50px;
  backdrop-filter: blur(10px);
  z-index: 5;
  display: flex;
  align-items: center;
  gap: 6px;
  border: 1px solid rgba(255, 255, 255, 0.5);
  cursor: pointer;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  transition: var(--transition);
}

.car-card__badge:hover {
  background: linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%);
  color: #fff;
  transform: scale(1.05);
  box-shadow: 0 6px 20px rgba(238, 30, 38, 0.3);
}

.car-card__offer-badge {
  position: absolute;
  top: 20px;
  {{ App::getLocale() == 'ar' ? 'left' : 'right' }}: 20px;
  background: var(--primary-gradient);
  color: #fff;
  font-size: 11px;
  font-weight: 800;
  padding: 6px 14px;
  border-radius: 50px;
  z-index: 3;
  display: flex;
  align-items: center;
  gap: 5px;
  box-shadow: 0 6px 15px rgba(238, 30, 38, 0.3);
}

.car-card__year {
  position: absolute;
  bottom: 20px;
  {{ App::getLocale() == 'ar' ? 'left' : 'right' }}: 20px;
  background: rgba(17, 17, 17, 0.75);
  color: #fff;
  font-size: 13px;
  font-weight: 800;
  padding: 6px 14px;
  border-radius: 50px;
  backdrop-filter: blur(8px);
  z-index: 2;
  box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}

.car-card__body {
  padding: 30px 25px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  flex: 1;
  text-align: {{ App::getLocale() == 'ar' ? 'right' : 'left' }};
}

.car-card__title {
  font-size: 24px;
  font-weight: 800;
  color: #111111;
  margin: 0;
}

.car-card__price-box {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 0;
}

.car-card__price--coming-soon {
  color: #EE1E26;
  font-weight: 800;
  font-size: 16px;
  background-color: rgba(238, 30, 38, 0.04);
  padding: 6px 16px;
  border-radius: 8px;
  display: inline-block;
}

.car-card__specs {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  margin: 0;
  padding: 0;
  list-style: none;
}

.car-card__spec {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  color: #555555;
  font-weight: 600;
  padding: 10px 14px;
  background-color: #f8f9fb;
  border-radius: 12px;
  border: 1px solid rgba(0,0,0,0.01);
  justify-content: flex-end;
  flex-direction: row-reverse !important;
}

.car-card__spec i {
  font-size: 18px;
  color: #EE1E26;
}

.car-card__cta {
  display: block;
  width: 100%;
  padding: 14px;
  border-radius: 50px;
  font-size: 16px;
  font-weight: 800;
  text-align: center;
  transition: all 0.3s ease;
  margin-top: auto;
  text-decoration: none;
}

.car-card__cta--coming-soon {
  background: linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%);
  color: #fff !important;
  box-shadow: 0 8px 20px rgba(238, 30, 38, 0.2);
}

.car-card__cta--coming-soon:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 28px rgba(238, 30, 38, 0.35);
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 991px) {
  .cars-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }
}

@media (max-width: 768px) {
  .cars-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }
}

/* Pagination Styling */
.pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 50px;
    padding: 0;
    list-style: none;
}

.pagination .page-item {
    list-style: none;
}

.pagination .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 1.5px solid var(--color-border);
    color: var(--color-gray-700);
    text-decoration: none;
    font-weight: 700;
    transition: all var(--transition);
}

.pagination .page-item.active .page-link {
    background: linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%);
    color: var(--color-white);
    border-color: #EE1E26;
}

.pagination .page-item.disabled .page-link {
    opacity: 0.5;
    pointer-events: none;
    background: #f5f5f5;
    color: #999;
}

.pagination .page-link:hover:not(.active) {
    border-color: #EE1E26;
    color: #EE1E26;
}
</style>
@endsection

@section('breadcrumb-title', __('قريباً في السوق'))

@section('content')
@include('partials.Store.breadcrumb')

  <!-- ===================== EXPLORE SECTION ===================== -->
  <section class="explore">
    <div class="explore__container">
      <h2 class="explore__title">
        {{ __('سيارات') }} <span class="explore__title--coming-soon">{{ __('قريباً في السوق') }}</span>
      </h2>

      <p class="explore__count"><strong>{{ $cars->total() }}</strong> {{ __('سيارات منتظرة') }}</p>

      <div class="explore__layout">
        
        @if($cars->count() > 0)
          <!-- ========== Car Grid ========== -->
          <div class="cars-grid">
            @foreach($cars as $car)
              <article class="car-card">
                <div class="car-card__image-wrap">
                  <div class="car-card__badge" onclick="addToCompare('{{ $car->slug }}')" data-slug="{{ $car->slug }}">
                    <i class="bi bi-arrow-left-right"></i>
                    {{ __('أضف للمقارنة') }}
                  </div>
                  
                  <div class="car-card__offer-badge">
                    <i class="bi bi-clock-history"></i>
                    {{ __('قريباً') }}
                  </div>

                  @if($car->year)
                    <span class="car-card__year">{{ $car->year }}</span>
                  @endif
                  
                  <img loading="lazy" class="car-card__image" src="{{ $car->thumbnail ? asset('storage/' . $car->thumbnail) : asset('assets/images/placeholder-car.jpg') }}" alt="{{ $car->name }}" />
                </div>
                
                <div class="car-card__body">
                  <h3 class="car-card__title">{{ $car->name }}</h3>

                  <div class="car-card__price-box">
                    <span class="car-card__price--coming-soon">{{ __('قريباً في السوق') }}</span>
                  </div>

                  <ul class="car-card__specs">
                    @if(isset($car->specs['seats']))
                    <li class="car-card__spec">
                      <span>{{ $car->specs['seats'] }} {{ __('مقاعد') }}</span>
                      <i class="bi bi-people"></i>
                    </li>
                    @endif
                    
                    @if(isset($car->specs['fuel']))
                    <li class="car-card__spec">
                      <span>{{ $car->specs['fuel'] }}</span>
                      <i class="bi bi-fuel-pump"></i>
                    </li>
                    @endif

                    @if(isset($car->specs['hp']))
                    <li class="car-card__spec">
                      <span>{{ $car->specs['hp'] }} {{ __('حصان') }}</span>
                      <i class="bi bi-speedometer2"></i>
                    </li>
                    @endif

                    @if(isset($car->specs['gearbox']))
                    <li class="car-card__spec">
                      <span>{{ $car->specs['gearbox'] }}</span>
                      <i class="bi bi-gear-wide-connected"></i>
                    </li>
                    @endif
                  </ul>

                  <a href="{{ route('store.cars.show', $car->slug) }}" class="car-card__cta car-card__cta--coming-soon">{{ __('عرض التفاصيل') }}</a>
                </div>
              </article>
            @endforeach
          </div>

          <!-- Pagination -->
          <div class="pagination-wrap">
              {{ $cars->links() }}
          </div>
        @else
          <div class="text-center py-5">
            <i class="bi bi-car-front fs-1 text-muted opacity-25 d-block mb-3"></i>
            <h5 class="fw-bold">{{ __('لا توجد سيارات معلنة قريباً حالياً') }}</h5>
            <p class="text-muted">{{ __('يرجى زيارتنا لاحقاً لمتابعة أحدث الإعلانات والسيارات المنتظرة.') }}</p>
            <a href="{{ route('store.home') }}" class="btn btn--red mt-3 text-decoration-none" style="border-radius:30px;">{{ __('العودة للرئيسية') }}</a>
          </div>
        @endif

      </div>
    </div>
  </section>

  <!-- ===== Compare Button JS script ===== -->
  @if($cars->count() > 0)
  <script>
    function addToCompare(slug) {
        const stored = localStorage.getItem('compareSlug');
        let url = '{{ route("store.compare") }}';

        if (!stored || stored === slug) {
            localStorage.setItem('compareSlug', slug);
            url += '?cars[]=' + slug;
        } else {
            url += '?cars[]=' + stored + '&cars[]=' + slug;
            localStorage.removeItem('compareSlug');
        }
        window.location.href = url;
    }
  </script>
  @endif

@endsection
