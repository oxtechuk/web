@extends('partials.Layouts.crm-master')
@section('title', __('طلبات التوظيف') . ' | AutoCRM')

@section('content')
    <div class="container-fluid" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h4 class="mb-1 fw-bold">{{ __('إدارة طلبات التوظيف') }}</h4>
                <p class="text-muted mb-0 small">{{ __('إجمالي طلبات التوظيف المستلمة:') }} {{ $applications->total() }} {{ __('طلب') }}</p>
            </div>
        </div>

        {{-- Filters card --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-body p-4">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted">{{ __('بحث بالاسم أو الجوال') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-0 shadow-none" value="{{ request('search') }}"
                                placeholder="{{ __('الاسم أو الجوال...') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-muted">{{ __('تصفية بالحالة') }}</label>
                        <select name="status" class="form-select bg-light border-0 shadow-none">
                            <option value="">{{ __('كل الحالات') }}</option>
                            @foreach ($statuses as $key => $val)
                                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $val['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4 rounded-3 w-100 fw-bold">{{ __('تصفية') }}</button>
                        <a href="{{ route('crm.job-applications.index') }}" class="btn btn-light px-3 rounded-3"><i class="bi bi-arrow-counterclockwise"></i></a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-muted fw-bold small">#</th>
                            <th class="py-3 text-muted fw-bold small">{{ __('المتقدم') }}</th>
                            <th class="py-3 text-muted fw-bold small">{{ __('بيانات الاتصال') }}</th>
                            <th class="py-3 text-muted fw-bold small">{{ __('الوظيفة المطلوبة') }}</th>
                            <th class="py-3 text-muted fw-bold small text-center">{{ __('السيرة الذاتية') }}</th>
                            <th class="py-3 text-muted fw-bold small text-center">{{ __('الحالة') }}</th>
                            <th class="py-3 text-muted fw-bold small">{{ __('تاريخ التقديم') }}</th>
                            <th class="py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($applications as $app)
                            <tr>
                                <td class="px-4 text-muted small">{{ $app->id }}</td>
                                <td class="fw-bold text-dark">{{ $app->name }}</td>
                                <td>
                                    <div class="d-flex flex-column gap-1 small text-muted">
                                        <span dir="ltr" class="text-start"><i class="bi bi-envelope me-1"></i> {{ $app->email }}</span>
                                        <span dir="ltr" class="text-start"><i class="bi bi-telephone me-1"></i> {{ $app->phone }}</span>
                                    </div>
                                </td>
                                <td class="fw-bold text-primary">{{ $app->job_title }}</td>
                                <td class="text-center">
                                    @if ($app->resume_path)
                                        <a href="{{ asset('storage/' . $app->resume_path) }}" target="_blank" class="btn btn-sm btn-light border rounded-2 px-3 fw-bold">
                                            <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i>
                                            {{ __('تحميل السيرة الذاتية') }}
                                        </a>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $statusInfo = $statuses[$app->status] ?? ['label' => $app->status, 'class' => 'bg-secondary text-white'];
                                    @endphp
                                    <span class="badge {{ $statusInfo['class'] }} px-3 py-2 rounded-pill small fw-bold">
                                        {{ $statusInfo['label'] }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $app->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-end px-3">
                                        <a href="{{ route('crm.job-applications.show', $app) }}" class="btn btn-sm btn-primary-subtle text-primary rounded-2" title="{{ __('عرض التفاصيل') }}">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        @can('manage-job-applications')
                                        <form action="{{ route('crm.job-applications.destroy', $app) }}" method="POST" onsubmit="return confirm('{{ __('هل أنت متأكد من حذف هذا الطلب نهائياً؟') }}')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger-subtle text-danger rounded-2" title="{{ __('حذف') }}"><i class="bi bi-trash"></i></button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <div class="py-4">
                                        <i class="bi bi-file-person fs-1 d-block mb-3 opacity-25"></i>
                                        <h6 class="fw-bold">{{ __('لا يوجد طلبات توظيف حالياً') }}</h6>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($applications->hasPages())
                <div class="card-footer bg-white py-3 border-0">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        .btn-danger-subtle { background: #ffebee; border: none; }
        .btn-primary-subtle { background: #e3f2fd; border: none; }
        .bg-info-subtle { background-color: #e0f7fa !important; color: #006064 !important; }
        .bg-warning-subtle { background-color: #fff8e1 !important; color: #ff6f00 !important; }
        .bg-primary-subtle { background-color: #e3f2fd !important; color: #0d47a1 !important; }
        .bg-danger-subtle { background-color: #ffebee !important; color: #b71c1c !important; }
        .bg-success-subtle { background-color: #e8f5e9 !important; color: #1b5e20 !important; }
    </style>
@endsection
