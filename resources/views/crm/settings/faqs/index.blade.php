@extends('partials.Layouts.crm-master')
@section('title', __('الأسئلة الشائعة') . ' | AutoCRM')

@section('content')
    <div class="container-fluid" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-1 fw-bold">{{ __('الأسئلة الشائعة (FAQs)') }}</h4>
                <p class="text-muted mb-0 small">{{ __('إجمالي') }} {{ $faqs->count() }} {{ __('سؤال شائع مسجل') }}</p>
            </div>
            @can('manage-faqs')
            <a href="{{ route('crm.settings.faqs.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-plus-circle me-1"></i> {{ __('إضافة سؤال جديد') }}
            </a>
            @endcan
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 fw-bold small text-muted text-uppercase" style="width: 80px;">#</th>
                            <th class="py-3 fw-bold small text-muted text-uppercase">{{ __('السؤال (العربية)') }}</th>
                            <th class="py-3 fw-bold small text-muted text-uppercase">{{ __('السؤال (الإنجليزية)') }}</th>
                            <th class="py-3 fw-bold small text-muted text-uppercase" style="width: 100px;">{{ __('الترتيب') }}</th>
                            <th class="py-3 fw-bold small text-muted text-uppercase" style="width: 120px;">{{ __('الحالة') }}</th>
                            <th class="px-4 py-3 fw-bold small text-muted text-uppercase text-center" style="width: 180px;">{{ __('العمليات') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faqs as $faq)
                            <tr>
                                <td class="px-4 py-3 text-muted">{{ $loop->iteration }}</td>
                                <td class="py-3 fw-bold text-dark">{{ $faq->getTranslation('question', 'ar') }}</td>
                                <td class="py-3 text-muted">{{ $faq->getTranslation('question', 'en') }}</td>
                                <td class="py-3"><span class="badge bg-light text-dark border px-2 py-1">{{ $faq->sort_order }}</span></td>
                                <td class="py-3">
                                    @if($faq->is_active)
                                        <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>{{ __('نشط') }}</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill"><i class="bi bi-x-circle-fill me-1"></i>{{ __('غير نشط') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        @can('manage-faqs')
                                        <a href="{{ route('crm.settings.faqs.edit', $faq) }}" class="btn btn-sm btn-white border shadow-xs rounded-2 px-3">
                                            <i class="bi bi-pencil me-1"></i> {{ __('تعديل') }}
                                        </a>
                                        <form action="{{ route('crm.settings.faqs.destroy', $faq) }}" method="POST" onsubmit="return confirm('{{ __('هل أنت متأكد من حذف هذا السؤال الشائع؟') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger border shadow-xs rounded-2 px-3">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="p-4">
                                        <i class="bi bi-question-circle fs-1 d-block mb-3 opacity-25"></i>
                                        <h6 class="fw-bold">{{ __('لا يوجد أسئلة شائعة مسجلة حالياً') }}</h6>
                                        <div class="mt-3">
                                            <a href="{{ route('crm.settings.faqs.create') }}" class="btn btn-primary btn-sm rounded-pill px-4">{{ __('إضافة أول سؤال شائع') }}</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

<style>
    .btn-white { background: #fff; }
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
</style>
