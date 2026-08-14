@extends('partials.Layouts.crm-master')
@section('title', __('تفاصيل طلب التوظيف') . ' | AutoCRM')

@section('content')
    <div class="container-fluid" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="{{ route('crm.job-applications.index') }}" class="btn btn-light rounded-3 px-3 py-2 mb-2 small fw-bold">
                    <i class="bi bi-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} me-1"></i>
                    {{ __('العودة للقائمة') }}
                </a>
                <h4 class="mb-0 fw-bold">{{ __('تفاصيل طلب التوظيف للمتقدم:') }} {{ $jobApplication->name }}</h4>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4">
                <span class="fw-bold">{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            {{-- Left column: Applicant details --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 border-bottom pb-2 text-dark">{{ __('البيانات الأساسية') }}</h5>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">{{ __('اسم المتقدم بالكامل') }}</div>
                                <div class="fw-bold text-dark fs-5">{{ $jobApplication->name }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">{{ __('الوظيفة المتقدم لها') }}</div>
                                <div class="fw-bold text-primary fs-5">{{ $jobApplication->job_title }}</div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">{{ __('البريد الإلكتروني') }}</div>
                                <div class="fw-bold" dir="ltr" style="text-align: right;"><i class="bi bi-envelope me-1"></i> {{ $jobApplication->email }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">{{ __('رقم الهاتف / الجوال') }}</div>
                                <div class="fw-bold" dir="ltr" style="text-align: right;"><i class="bi bi-telephone me-1"></i> {{ $jobApplication->phone }}</div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">{{ __('تاريخ تقديم الطلب') }}</div>
                                <div class="text-muted fw-semibold">{{ $jobApplication->created_at->format('Y-m-d H:i:s') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Cover Letter Card --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark">{{ __('رسالة التغطية / نبذة إضافية') }}</h5>
                        @if ($jobApplication->cover_letter)
                            <div class="p-3 bg-light rounded-3 text-muted" style="white-space: pre-line; line-height: 1.6; font-size: 15px;">
                                {{ $jobApplication->cover_letter }}
                            </div>
                        @else
                            <div class="text-muted py-3 text-center small">
                                <i class="bi bi-chat-left-text d-block fs-3 opacity-25 mb-2"></i>
                                {{ __('لا توجد رسالة تغطية مرفقة مع هذا الطلب.') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right column: Actions & Status --}}
            <div class="col-lg-4">
                {{-- Status Change Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3 text-dark">{{ __('حالة الطلب الحالية') }}</h5>
                        
                        <div class="mb-4">
                            @php
                                $statusInfo = $statuses[$jobApplication->status] ?? ['label' => $jobApplication->status, 'class' => 'bg-secondary text-white'];
                            @endphp
                            <span class="badge {{ $statusInfo['class'] }} w-100 py-3 fs-6 rounded-3 fw-bold">
                                {{ $statusInfo['label'] }}
                            </span>
                        </div>

                        <form action="{{ route('crm.job-applications.status', $jobApplication) }}" method="POST">
                            @csrf @method('PATCH')
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">{{ __('تحديث حالة الطلب') }}</label>
                                <select name="status" class="form-select bg-light border-0 shadow-none fw-bold" required>
                                    @foreach ($statuses as $key => $val)
                                        <option value="{{ $key }}" {{ $jobApplication->status === $key ? 'selected' : '' }}>
                                            {{ $val['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold py-2.5 shadow-sm">
                                <i class="bi bi-check2-circle me-1"></i>
                                {{ __('تحديث الحالة') }}
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Resume Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 text-center">
                        <h5 class="fw-bold mb-3 text-start text-dark">{{ __('ملف السيرة الذاتية') }}</h5>
                        
                        <div class="py-3">
                            <i class="bi bi-file-earmark-pdf fs-1 text-danger"></i>
                            <div class="mt-2 fw-bold text-dark small text-truncate px-2">{{ basename($jobApplication->resume_path) }}</div>
                        </div>

                        <a href="{{ asset('storage/' . $jobApplication->resume_path) }}" target="_blank" class="btn btn-light border w-100 rounded-3 fw-bold py-2 mb-2">
                            <i class="bi bi-box-arrow-up-right me-1 text-primary"></i>
                            {{ __('فتح الملف') }}
                        </a>

                        <a href="{{ asset('storage/' . $jobApplication->resume_path) }}" download class="btn btn-success w-100 rounded-3 fw-bold py-2 text-white">
                            <i class="bi bi-download me-1"></i>
                            {{ __('تحميل الملف') }}
                        </a>
                    </div>
                </div>

                {{-- Danger Zone Card --}}
                @can('manage-job-applications')
                <div class="card border-0 shadow-sm rounded-4" style="border: 1px solid rgba(227, 6, 19, 0.1) !important;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3 text-danger">{{ __('إجراءات خطرة') }}</h5>
                        <p class="text-muted small mb-3">{{ __('حذف هذا الطلب سيؤدي لإزالة كافة بيانات المتقدم وملف السيرة الذاتية بشكل نهائي.') }}</p>
                        
                        <form action="{{ route('crm.job-applications.destroy', $jobApplication) }}" method="POST" onsubmit="return confirm('{{ __('هل أنت متأكد من حذف هذا الطلب نهائياً؟') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100 rounded-3 fw-bold py-2.5">
                                <i class="bi bi-trash me-1"></i>
                                {{ __('حذف الطلب نهائياً') }}
                            </button>
                        </form>
                    </div>
                </div>
                @endcan
            </div>
        </div>
    </div>

    <style>
        .bg-info-subtle { background-color: #e0f7fa !important; color: #006064 !important; }
        .bg-warning-subtle { background-color: #fff8e1 !important; color: #ff6f00 !important; }
        .bg-primary-subtle { background-color: #e3f2fd !important; color: #0d47a1 !important; }
        .bg-danger-subtle { background-color: #ffebee !important; color: #b71c1c !important; }
        .bg-success-subtle { background-color: #e8f5e9 !important; color: #1b5e20 !important; }
    </style>
@endsection
