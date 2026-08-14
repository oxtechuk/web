@extends('partials.Layouts.crm-master')
@section('title', __('إضافة سؤال شائع') . ' | AutoCRM')

@section('content')
    <div class="container-fluid" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
        <div class="mb-4 d-flex align-items-center">
            <a href="{{ route('crm.settings.faqs.index') }}" class="btn btn-sm btn-white border shadow-xs rounded-2 {{ app()->getLocale() == 'ar' ? 'ms-3' : 'me-3' }}">
                <i class="bi bi-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
            </a>
            <div>
                <h4 class="mb-1 fw-bold">{{ __('إضافة سؤال شائع جديد') }}</h4>
                <p class="text-muted mb-0 small">{{ __('أدخل بيانات السؤال والجواب باللغتين العربية والإنجليزية') }}</p>
            </div>
        </div>

        <form action="{{ route('crm.settings.faqs.store') }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4 rounded-4">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h5 class="card-title mb-0 fw-bold">{{ __('بيانات السؤال والجواب') }}</h5>
                        </div>
                        <div class="card-body p-4 pt-2">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold small text-muted">{{ __('السؤال (بالعربية)') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="question[ar]" class="form-control bg-light border-0 shadow-none py-2" required placeholder="{{ __('مثال: ما هي شروط تمويل السيارات؟') }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold small text-muted">{{ __('السؤال (بالإنجليزية)') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="question[en]" class="form-control bg-light border-0 shadow-none py-2" required placeholder="e.g.: What are the car financing conditions?">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold small text-muted">{{ __('الإجابة (بالعربية)') }} <span class="text-danger">*</span></label>
                                    <textarea name="answer[ar]" class="form-control bg-light border-0 shadow-none" rows="5" required placeholder="{{ __('اكتب الإجابة باللغة العربية هنا...') }}"></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold small text-muted">{{ __('الإجابة (بالإنجليزية)') }} <span class="text-danger">*</span></label>
                                    <textarea name="answer[en]" class="form-control bg-light border-0 shadow-none" rows="5" required placeholder="Write the answer in English here..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4 rounded-4">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h5 class="card-title mb-0 fw-bold">{{ __('إعدادات إضافية') }}</h5>
                        </div>
                        <div class="card-body p-4 pt-2">
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted">{{ __('ترتيب العرض (Sort Order)') }}</label>
                                <input type="number" name="sort_order" class="form-control bg-light border-0 shadow-none" value="0" min="0">
                                <small class="text-muted d-block mt-1">{{ __('الأرقام الأصغر تظهر أولاً') }}</small>
                            </div>
                            
                            <div class="form-check form-switch p-3 bg-light rounded-3">
                                <input class="form-check-input {{ app()->getLocale() == 'ar' ? 'ms-0 me-2 float-none' : '' }}" type="checkbox" name="is_active" value="1" id="isActive" checked style="cursor: pointer;">
                                <label class="form-check-label fw-bold ms-2" for="isActive" style="cursor: pointer;">{{ __('تفعيل العرض') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-body p-4">
                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm">
                                <i class="bi bi-plus-circle me-1"></i> {{ __('إضافة السؤال الشائع') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

<style>
    .btn-white { background: #fff; }
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
</style>
