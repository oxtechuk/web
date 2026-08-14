@extends('store.layouts.app')

@section('title', __('الأسئلة الشائعة') . ' | ' . (is_array($globalSettings['site_name'] ?? null) ? ($globalSettings['site_name'][App::getLocale()] ?? ($globalSettings['site_name']['ar'] ?? 'GR Motors')) : ($globalSettings['site_name'] ?? 'GR Motors')))

@section('breadcrumb-title', __('الأسئلة الشائعة'))

@section('css')
<style>
  .faq-section {
    margin: 4rem auto;
    max-width: 900px;
    padding: 0 15px;
  }
  
  .faq-search-wrapper {
    position: relative;
    max-width: 600px;
    margin: 0 auto 3rem;
  }
  
  .faq-search-input {
    width: 100%;
    padding: 16px 24px;
    padding-right: 50px;
    border-radius: 50px;
    border: 1px solid rgba(0,0,0,0.08);
    background: #fff;
    box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    font-size: 16px;
    font-weight: 600;
    transition: all 0.3s ease;
    outline: none;
  }
  
  .faq-search-input:focus {
    border-color: var(--primary);
    box-shadow: 0 10px 30px rgba(238, 30, 38, 0.08);
  }
  
  .faq-search-icon {
    position: absolute;
    top: 50%;
    right: 20px;
    transform: translateY(-50%);
    font-size: 20px;
    color: #999;
    pointer-events: none;
    transition: color 0.3s ease;
  }
  
  .faq-search-input:focus + .faq-search-icon {
    color: var(--primary);
  }

  /* Swapping icon side for English */
  html[lang="en"] .faq-search-input {
    padding-right: 24px;
    padding-left: 50px;
  }
  html[lang="en"] .faq-search-icon {
    right: auto;
    left: 20px;
  }
  
  .faq-accordion {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
  }
  
  .faq-item {
    background: #fff;
    border-radius: 16px;
    border: 1px solid rgba(0,0,0,0.04);
    box-shadow: 0 4px 20px rgba(0,0,0,0.01);
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  }
  
  .faq-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.04);
    border-color: rgba(238, 30, 38, 0.15);
  }
  
  .faq-item.active {
    border-color: rgba(238, 30, 38, 0.2);
    box-shadow: 0 15px 35px rgba(238, 30, 38, 0.05);
  }
  
  .faq-trigger {
    width: 100%;
    padding: 22px 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: none;
    border: none;
    cursor: pointer;
    text-align: right;
    outline: none;
  }
  
  html[lang="en"] .faq-trigger {
    text-align: left;
  }
  
  .faq-question {
    font-size: 18px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
    transition: color 0.3s ease;
  }
  
  .faq-item.active .faq-question {
    color: var(--primary);
  }
  
  .faq-arrow {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #f8f9fa;
    color: #1a1a1a;
    transition: all 0.3s ease;
    flex-shrink: 0;
  }
  
  .faq-item.active .faq-arrow {
    background: var(--gradient-primary);
    color: #fff;
    transform: rotate(180deg);
  }
  
  .faq-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
  }
  
  .faq-answer {
    padding: 0 28px 28px;
    font-size: 16px;
    line-height: 1.8;
    color: #555;
    font-weight: 500;
    border-top: 1px solid rgba(0,0,0,0.02);
    padding-top: 20px;
  }
  
  /* Modern Accordion slide animation classes */
  .faq-no-results {
    text-align: center;
    padding: 3rem;
    background: #fff;
    border-radius: 16px;
    border: 1px dashed rgba(0,0,0,0.1);
    display: none;
  }
  
  .faq-no-results i {
    font-size: 40px;
    color: #ccc;
    display: block;
    margin-bottom: 1rem;
  }
  
  .faq-cta-box {
    text-align: center;
    margin-top: 5rem;
    padding: 3rem 2rem;
    background: linear-gradient(135deg, #fff 0%, #fef8f8 100%);
    border-radius: 24px;
    border: 1px solid rgba(238, 30, 38, 0.05);
    box-shadow: 0 10px 40px rgba(0,0,0,0.02);
  }
  
  .faq-cta-title {
    font-size: 24px;
    font-weight: 800;
    margin-bottom: 10px;
    color: #1a1a1a;
  }
  
  .faq-cta-text {
    color: #666;
    margin-bottom: 25px;
    font-size: 16px;
  }
  
  .faq-cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: var(--gradient-primary);
    color: #fff;
    padding: 14px 32px;
    border-radius: 50px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 10px 20px rgba(238, 30, 38, 0.2);
    transition: all 0.3s ease;
  }
  
  .faq-cta-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 30px rgba(238, 30, 38, 0.3);
    color: #fff;
  }
</style>
@endsection

@section('content')
@include('partials.Store.breadcrumb')

<div class="container faq-section">
  
  {{-- Live Search --}}
  <div class="faq-search-wrapper">
    <input type="text" id="faqSearch" class="faq-search-input" placeholder="{{ __('ابحث عن إجابتك هنا...') }}">
    <i class="bi bi-search faq-search-icon"></i>
  </div>

  {{-- FAQ Accordion --}}
  <div class="faq-accordion" id="faqAccordion">
    @forelse($faqs as $faq)
      <div class="faq-item" data-question="{{ strtolower($faq->getTranslation('question', App::getLocale())) }} {{ strtolower($faq->getTranslation('answer', App::getLocale())) }}">
        <button class="faq-trigger" type="button" aria-expanded="false">
          <h3 class="faq-question">{{ $faq->getTranslation('question', App::getLocale()) }}</h3>
          <span class="faq-arrow">
            <i class="bi bi-chevron-down"></i>
          </span>
        </button>
        <div class="faq-content">
          <div class="faq-answer">
            {!! nl2br(e($faq->getTranslation('answer', App::getLocale()))) !!}
          </div>
        </div>
      </div>
    @empty
      <div class="card border-0 shadow-sm rounded-4 p-5 text-center w-100">
        <i class="bi bi-question-circle fs-1 d-block mb-3 opacity-25"></i>
        <h6 class="fw-bold">{{ __('لا يوجد أسئلة شائعة حالياً') }}</h6>
      </div>
    @endforelse
    
    {{-- No Results Placeholder --}}
    <div class="faq-no-results" id="faqNoResults">
      <i class="bi bi-search"></i>
      <h5 class="fw-bold mb-1">{{ __('لا توجد نتائج مطابقة') }}</h5>
      <p class="text-muted small mb-0">{{ __('جرب استخدام كلمات بحث مختلفة أو تصفح الأسئلة أدناه.') }}</p>
    </div>
  </div>

  {{-- CTA Box --}}
  <div class="faq-cta-box">
    <h3 class="faq-cta-title">{{ __('لديك سؤال آخر؟') }}</h3>
    <p class="faq-cta-text">{{ __('إذا لم تجد الإجابة التي تبحث عنها، يمكنك التواصل معنا مباشرة.') }}</p>
    @if($whatsapp = $globalSettings['contact_whatsapp'] ?? $globalSettings['contact_phone'] ?? null)
      <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsapp) }}" target="_blank" class="faq-cta-btn">
        <i class="bi bi-whatsapp"></i> {{ __('تواصل معنا عبر واتساب') }}
      </a>
    @endif
  </div>

</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Accordion Logic
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const trigger = item.querySelector('.faq-trigger');
        const content = item.querySelector('.faq-content');
        
        trigger.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            
            // Close all active items
            faqItems.forEach(otherItem => {
                if (otherItem !== item && otherItem.classList.contains('active')) {
                    otherItem.classList.remove('active');
                    otherItem.querySelector('.faq-content').style.maxHeight = '0';
                    otherItem.querySelector('.faq-trigger').setAttribute('aria-expanded', 'false');
                }
            });
            
            // Toggle current item
            if (isActive) {
                item.classList.remove('active');
                content.style.maxHeight = '0';
                trigger.setAttribute('aria-expanded', 'false');
            } else {
                item.classList.add('active');
                content.style.maxHeight = content.scrollHeight + 'px';
                trigger.setAttribute('aria-expanded', 'true');
            }
        });
    });

    // Real-time Search Logic
    const searchInput = document.getElementById('faqSearch');
    const noResults = document.getElementById('faqNoResults');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            let visibleCount = 0;
            
            faqItems.forEach(item => {
                const text = item.getAttribute('data-question');
                if (text.includes(query)) {
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                    // If active, close it
                    if (item.classList.contains('active')) {
                        item.classList.remove('active');
                        item.querySelector('.faq-content').style.maxHeight = '0';
                    }
                }
            });
            
            if (visibleCount === 0 && query !== '') {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }
        });
    }
});
</script>
@endsection
