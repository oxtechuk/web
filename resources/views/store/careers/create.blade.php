@extends('store.layouts.app')
@section('title', __('انضم لفريقنا - وظائف') . ' | ' . (is_array($globalSettings['site_name'] ?? null) ? ($globalSettings['site_name'][App::getLocale()] ?? ($globalSettings['site_name']['ar'] ?? 'GR Motors')) : ($globalSettings['site_name'] ?? 'GR Motors')))
@section('meta_description', __('انضم إلى فريق العمل في GR Motors وقدم طلب التوظيف الخاص بك. نحن نبحث عن المبدعين والشغوفين في عالم السيارات الفاخرة.'))

@section('css')
<style>
    /* Premium Careers Page Styles */
    .careers-page {
        background-color: #0b0b0d;
        color: #ffffff;
        font-family: 'Cairo', sans-serif;
        padding-bottom: 80px;
    }

    .careers-hero {
        position: relative;
        padding: 100px 0 80px;
        background: linear-gradient(180deg, rgba(227, 6, 19, 0.15) 0%, rgba(11, 11, 13, 0) 100%), url('{{ asset("assets/images/careers_bg.jpg") }}') center center / cover no-repeat;
        text-align: center;
        overflow: hidden;
    }

    .careers-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(11, 11, 13, 0.85);
        z-index: 1;
    }

    .careers-hero__inner {
        position: relative;
        z-index: 2;
        max-width: 800px;
        margin: 0 auto;
    }

    .careers-hero__badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(227, 6, 19, 0.12);
        border: 1px solid rgba(227, 6, 19, 0.3);
        color: #e30613;
        padding: 6px 16px;
        border-radius: 100px;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 24px;
        animation: fadeInDown 0.6s ease;
    }

    .careers-hero__title {
        font-size: 2.8rem;
        font-weight: 800;
        line-height: 1.3;
        margin-bottom: 16px;
        color: #ffffff;
        animation: fadeInUp 0.6s ease 0.1s both;
    }

    .careers-hero__title span {
        color: #e30613;
        background: linear-gradient(135deg, #ff3b47, #e30613);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .careers-hero__subtitle {
        font-size: 16px;
        color: #a0a0a5;
        line-height: 1.8;
        max-width: 600px;
        margin: 0 auto;
        animation: fadeInUp 0.6s ease 0.2s both;
    }

    /* Benefits Section */
    .benefits-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 60px;
    }

    .benefit-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
    }

    .benefit-card:hover {
        transform: translateY(-8px);
        background: rgba(227, 6, 19, 0.03);
        border-color: rgba(227, 6, 19, 0.2);
        box-shadow: 0 12px 40px rgba(227, 6, 19, 0.1);
    }

    .benefit-card__icon {
        width: 64px;
        height: 64px;
        background: rgba(227, 6, 19, 0.1);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 28px;
        color: #e30613;
        transition: all 0.3s ease;
    }

    .benefit-card:hover .benefit-card__icon {
        background: #e30613;
        color: #ffffff;
        transform: rotateY(180deg);
    }

    .benefit-card__title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 12px;
        color: #ffffff;
    }

    .benefit-card__desc {
        font-size: 14px;
        color: #8a8a93;
        line-height: 1.6;
        margin: 0;
    }

    /* Form Container */
    .form-section {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 16px;
    }

    .form-wrapper {
        background: rgba(18, 18, 22, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.06);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
    }

    .form-title {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 30px;
        text-align: center;
        color: #ffffff;
        position: relative;
        padding-bottom: 12px;
    }

    .form-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 50px;
        height: 3px;
        background: #e30613;
        border-radius: 10px;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        font-weight: 600;
        font-size: 14px;
        color: #c7c7cc;
        margin-bottom: 8px;
        display: block;
    }

    .form-control-premium {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        color: #ffffff;
        padding: 14px 18px;
        font-size: 14px;
        transition: all 0.3s ease;
        width: 100%;
        outline: none;
    }

    .form-control-premium:focus {
        background: rgba(255, 255, 255, 0.06);
        border-color: #e30613;
        box-shadow: 0 0 0 4px rgba(227, 6, 19, 0.15);
    }

    /* Custom File Input */
    .file-upload-area {
        border: 2px dashed rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        padding: 30px 20px;
        text-align: center;
        background: rgba(255, 255, 255, 0.01);
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .file-upload-area:hover, .file-upload-area.dragover {
        border-color: #e30613;
        background: rgba(227, 6, 19, 0.02);
    }

    .file-upload-area input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }

    .file-upload-icon {
        font-size: 36px;
        color: #a0a0a5;
        margin-bottom: 12px;
        transition: color 0.3s ease;
    }

    .file-upload-area:hover .file-upload-icon {
        color: #e30613;
    }

    .file-upload-text {
        font-size: 14px;
        font-weight: 600;
        color: #ffffff;
        margin-bottom: 4px;
    }

    .file-upload-hint {
        font-size: 12px;
        color: #8a8a93;
    }

    .file-name-preview {
        margin-top: 10px;
        font-size: 13px;
        color: #e30613;
        font-weight: 700;
        display: none;
    }

    /* Submit Button */
    .btn-submit-premium {
        background: linear-gradient(135deg, #e30613, #b0050e);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 16px 32px;
        font-size: 15px;
        font-weight: 700;
        width: 100%;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 8px 24px rgba(227, 6, 19, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-submit-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(227, 6, 19, 0.45);
        opacity: 0.95;
    }

    .btn-submit-premium:active {
        transform: translateY(0);
    }

    /* Alert Styling */
    .alert-premium {
        background: rgba(0, 201, 80, 0.08);
        border: 1px solid rgba(0, 201, 80, 0.2);
        color: #00e05c;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 30px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .alert-premium i {
        font-size: 24px;
        flex-shrink: 0;
    }

    .alert-premium h5 {
        font-weight: 700;
        margin: 0 0 4px;
        font-size: 15px;
    }

    .alert-premium p {
        font-size: 13.5px;
        margin: 0;
        opacity: 0.85;
        line-height: 1.6;
    }

    /* Keyframes */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
        .careers-hero__title {
            font-size: 2.2rem;
        }
        .form-wrapper {
            padding: 25px 20px;
        }
    }
</style>
@endsection

@section('content')
<div class="careers-page">

    {{-- Hero Section --}}
    <section class="careers-hero">
        <div class="container careers-hero__inner">
            <div class="careers-hero__badge">
                <i class="bi bi-rocket-takeoff-fill"></i> {{ __('فرص وظيفية واعدة') }}
            </div>
            <h1 class="careers-hero__title">
                {{ __('ابنِ مستقبلك مع') }} <span>{{ __('جي آر موتورز') }}</span>
            </h1>
            <p class="careers-hero__subtitle">
                {{ __('نحن نؤمن بأن قوتنا تكمن في موظفينا. إذا كنت تبحث عن بيئة عمل ملهمة وتنافسية في قطاع السيارات الفاخرة، فنحن نرحب بك معنا.') }}
            </p>
        </div>
    </section>

    {{-- Core Section --}}
    <div class="container mt-5">
        
        {{-- Success Message --}}
        @if(session('success'))
        <div class="form-section">
            <div class="alert-premium">
                <i class="bi bi-patch-check-fill"></i>
                <div>
                    <h5>{{ __('تم إرسال طلبك بنجاح!') }}</h5>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Benefits Grid --}}
        <div class="benefits-grid">
            <div class="benefit-card">
                <div class="benefit-card__icon"><i class="bi bi-briefcase"></i></div>
                <h3 class="benefit-card__title">{{ __('بيئة عمل احترافية') }}</h3>
                <p class="benefit-card__desc">{{ __('نقدم بيئة عمل راقية ومحفزة تساعدك على إطلاق طاقاتك الإبداعية وتحقيق أهدافك المهنية.') }}</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-card__icon"><i class="bi bi-graph-up-arrow"></i></div>
                <h3 class="benefit-card__title">{{ __('تطور مهني مستمر') }}</h3>
                <p class="benefit-card__desc">{{ __('فرص تدريبية مستمرة لتعلم أحدث التقنيات وأفضل الأساليب الإدارية في قطاع تجارة السيارات الفخمة.') }}</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-card__icon"><i class="bi bi-gem"></i></div>
                <h3 class="benefit-card__title">{{ __('مزايا وحوافز مجزية') }}</h3>
                <p class="benefit-card__desc">{{ __('باقة حوافز ومكافآت شهرية وسنوية منافسة ترتبط مباشرة بالأداء والتميز الفردي والجماعي.') }}</p>
            </div>
        </div>

        {{-- Application Form Section --}}
        <section class="form-section">
            <div class="form-wrapper">
                <h2 class="form-title">{{ __('طلب انضمام للفريق') }}</h2>
                
                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-3 mb-4 p-3 text-start" dir="rtl" style="background: rgba(227, 6, 19, 0.08); border: 1px solid rgba(227, 6, 19, 0.15); color: #ff5252;">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('store.careers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="name">{{ __('الاسم بالكامل') }} <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control-premium" placeholder="{{ __('مثال: أحمد محمد') }}" value="{{ old('name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="phone">{{ __('رقم الجوال / الهاتف') }} <span class="text-danger">*</span></label>
                                <input type="text" id="phone" name="phone" class="form-control-premium" placeholder="05xxxxxxxx" value="{{ old('phone') }}" required dir="ltr">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="email">{{ __('البريد الإلكتروني') }} <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" class="form-control-premium" placeholder="name@domain.com" value="{{ old('email') }}" required dir="ltr">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="job_title">{{ __('الوظيفة أو المجال المطلوب') }} <span class="text-danger">*</span></label>
                                <input type="text" id="job_title" name="job_title" class="form-control-premium" placeholder="{{ __('مثال: مبيعات، موارد بشرية، تسويق...') }}" value="{{ old('job_title') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('السيرة الذاتية (CV)') }} <span class="text-danger">*</span></label>
                        <div class="file-upload-area" id="dropArea">
                            <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
                            <i class="bi bi-cloud-arrow-up file-upload-icon"></i>
                            <div class="file-upload-text">{{ __('اسحب وأفلت السيرة الذاتية هنا') }}</div>
                            <div class="file-upload-hint">{{ __('أو اضغط للتصفح من جهازك (الملفات المقبولة: PDF, DOC, DOCX. الحجم الأقصى: 10 ميجابايت)') }}</div>
                            <div class="file-name-preview" id="fileNamePreview"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="cover_letter">{{ __('رسالة التغطية / نبذة إضافية عنك') }}</label>
                        <textarea id="cover_letter" name="cover_letter" class="form-control-premium" rows="5" placeholder="{{ __('اكتب نبذة مختصرة عن مؤهلاتك وخبراتك التي تجعلك مناسباً لفريقنا...') }}">{{ old('cover_letter') }}</textarea>
                    </div>

                    @include('store.partials.recaptcha')

                    <button type="submit" class="btn-submit-premium">
                        <span>{{ __('إرسال طلب الانضمام') }}</span>
                        <i class="bi bi-send-fill"></i>
                    </button>
                </form>
            </div>
        </section>

    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const fileInput = document.getElementById('resume');
        const dropArea = document.getElementById('dropArea');
        const preview = document.getElementById('fileNamePreview');

        fileInput.addEventListener('change', (e) => {
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                preview.textContent = `📎 ${file.name} (${(file.size / (1024 * 1024)).toFixed(2)} MB)`;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        });

        // Drag and drop handlers
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropArea.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropArea.classList.remove('dragover');
            }, false);
        });

        dropArea.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                fileInput.files = files;
                const event = new Event('change');
                fileInput.dispatchEvent(event);
            }
        });
    });
</script>
@endsection
