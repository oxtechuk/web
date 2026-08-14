# خطة تكامل HyperPay (Server-to-Server) مع نظام الحجز

## نظرة عامة

ربط بوابة الدفع **HyperPay** (Server-to-Server Synchronous) بنظام حجز السيارات الموجود في المشروع.
الهدف: السماح للعميل بدفع عربون/مبلغ الحجز أثناء تقديم الطلب من الموقع، مع معالجة جميع السيناريوهات المحتملة (نجاح، رفض، ضغط مزدوج، أخطاء الاتصال، إلخ).

> **الوضع الحالي**: المشروع يعمل على Test Mode. الإعدادات الخاصة بـ Live ستظهر في صفحة الإعدادات لكن تكون معطلة (Disabled) حتى يتم تفعيلها.

---

## Open Questions

> [!IMPORTANT]
> **ما مبلغ الدفع المطلوب؟**
> هل العميل يدفع:
> - مبلغ رمزي ثابت (مثلاً: 100 ﷼ رسوم حجز)؟
> - أم نسبة من المقدم (down_payment)؟
> - أم المقدم كاملاً؟
>
> **الافتراض في هذه الخطة**: مبلغ ثابت قابل للتعديل من الإعدادات (booking_fee).

> [!IMPORTANT]
> **هل الدفع إلزامي؟**
> هل يمكن للعميل تجاوز الدفع وتقديم الطلب بدونه؟ أم الدفع شرط لإتمام الحجز؟

> [!NOTE]
> **Currency**: الافتراض أن العملة هي SAR (ريال سعودي). تأكد من Entity ID المخصص لك من HyperPay.

---

## User Review Required

> [!WARNING]
> **PCI DSS Compliance**: Server-to-Server يعني أن بيانات البطاقة تمر عبر سيرفرك. هذا يتطلب **PCI DSS Level 1** certification. تأكد مع HyperPay من أنك مسموح باستخدام هذا الـ integration type.

> [!CAUTION]
> **HTTPS مطلوب**: HyperPay لا يعمل على HTTP في Production. السيرفر الحالي `http://localhost/ga` يعمل فقط على Test Mode. عند الانتقال للـ Live يجب أن يكون الموقع على HTTPS.

---

## الملفات المطلوب إنشاؤها/تعديلها

---

### 1. قاعدة البيانات (Database)

#### [NEW] Migration: إضافة أعمدة الدفع لجدول الحجوزات

```
database/migrations/2026_07_27_000001_add_payment_fields_to_bookings_table.php
```

أعمدة جديدة:
- `payment_status` — enum: `pending | paid | failed | skipped`
- `payment_amount` — decimal(10,2): مبلغ الدفع
- `payment_transaction_id` — string nullable: رقم المعاملة من HyperPay
- `payment_result_code` — string nullable: كود النتيجة
- `payment_idempotency_key` — string unique nullable: لمنع الدفع المزدوج
- `payment_at` — timestamp nullable: وقت الدفع

---

### 2. الإعدادات (Settings)

#### [MODIFY] `GeneralSettingController.php`
إضافة مفاتيح HyperPay إلى whitelist الـ `update()`:
- `hyperpay_mode` — `test | live`
- `hyperpay_test_entity_id`
- `hyperpay_test_access_token`
- `hyperpay_live_entity_id`
- `hyperpay_live_access_token`
- `hyperpay_booking_fee` — مبلغ رسوم الحجز
- `hyperpay_currency` — العملة (SAR)

#### [MODIFY] `resources/views/crm/settings/integrations.blade.php`
إضافة قسم HyperPay في صفحة الإعدادات يحتوي على:
- زر toggle للـ Test/Live Mode
- حقول الـ Test API credentials
- حقول الـ Live API credentials (معطلة بصرياً في Test Mode، مع تنبيه)
- حقل مبلغ رسوم الحجز
- حقل العملة
- زر اختبار الاتصال (Test Connection)

---

### 3. الـ Service (منطق التكامل)

#### [NEW] `app/Services/HyperPayService.php`

المسؤوليات:
```
- getApiUrl()         → يختار Test أو Live URL حسب الإعداد
- getEntityId()       → يختار الـ Entity ID المناسب
- getHeaders()        → Authorization Bearer token
- initiatePayment()   → ترسل طلب الدفع (Server-to-Server)
- parseResultCode()   → يحلل result.code ويرجع: success | pending | failed
- generateIdempotencyKey() → UUID فريد لكل عملية دفع
```

**Test URLs**: `https://eu-test.oppwa.com/v1/payments`
**Live URLs**: `https://eu-prod.oppwa.com/v1/payments`

---

### 4. الـ Routes

#### [MODIFY] `routes/web.php`
إضافة routes جديدة في Store group:

```php
// HyperPay Payment
Route::post('/booking/payment', [StoreBookingController::class, 'initiatePayment'])
     ->name('store.booking.payment');
Route::get('/booking/payment/callback', [StoreBookingController::class, 'paymentCallback'])
     ->name('store.booking.payment.callback');
Route::get('/booking/{booking}/payment-failed', [StoreBookingController::class, 'paymentFailed'])
     ->name('store.booking.payment.failed');
```

---

### 5. Store BookingController

#### [MODIFY] `app/Http/Controllers/Store/BookingController.php`

**Flow جديد (مع الدفع)**:

```
1. store()          → يحفظ الحجز بـ payment_status = 'pending'
                      يولد idempotency_key فريد
                      يوجه لـ initiatePayment()

2. initiatePayment()→ يستدعي HyperPayService::initiatePayment()
                      ✅ نجح: يحدّث payment_status = 'paid' → redirect success
                      ❌ فشل: يحدّث payment_status = 'failed' → redirect failed
                      ⏳ Pending: ينتظر callback

3. paymentCallback()→ يتحقق من نتيجة الدفع من HyperPay
                      يحدث حالة الحجز

4. paymentFailed()  → يعرض صفحة الفشل مع خيار إعادة المحاولة
```

**معالجة الضغط المزدوج (Double Submit)**:
- الـ `idempotency_key` يُنشأ مرة واحدة عند حفظ الحجز
- قبل إرسال أي طلب دفع: نتحقق من `payment_status` != 'paid'
- في الـ Frontend: تعطيل زر الدفع بعد الضغط الأول (JS)

---

### 6. Store Booking View

#### [MODIFY] `resources/views/store/booking/create.blade.php`
إضافة قسم الدفع:
- عرض ملخص الدفع (المبلغ المطلوب)
- حقول بطاقة الائتمان (Card Number, Expiry, CVV)
- زر "ادفع وأكمل الحجز" مع:
  - Spinner عند التحميل
  - تعطيل بعد الضغط الأول (anti-double-click)
  - CSRF protection

#### [NEW] `resources/views/store/booking/payment-failed.blade.php`
صفحة فشل الدفع تحتوي على:
- رسالة خطأ واضحة
- سبب الرفض (إن أمكن)
- زر "حاول مرة أخرى"
- زر "اتصل بنا"

---

### 7. CRM - تفاصيل الحجز

#### [MODIFY] `resources/views/crm/bookings/show.blade.php`
إضافة قسم "بيانات الدفع" يعرض:
- حالة الدفع (مدفوع / فاشل / معلق)
- رقم المعاملة
- المبلغ المدفوع
- تاريخ الدفع

---

## السيناريوهات المعالجة

| السيناريو | التعامل |
|-----------|---------|
| ✅ دفع ناجح | حفظ الحجز، تحديث `payment_status = paid`، إرسال إشعار، redirect للـ Success |
| ❌ رفض البطاقة | تحديث `payment_status = failed`، عرض السبب، خيار إعادة المحاولة |
| 🔄 ضغط الزر مرتين | الزر يُعطَّل بعد أول ضغط + التحقق من `idempotency_key` في الـ Backend |
| 🌐 انقطاع الاتصال | Exception handling، تسجيل في Log، عرض رسالة "حاول لاحقاً" |
| ⏰ Timeout | تحديد timeout للـ cURL request، معالجة حالة الـ pending |
| 🔁 Webhook مكرر | التحقق من `payment_transaction_id` قبل التحديث |
| 💳 بيانات بطاقة خاطئة | رسائل خطأ واضحة من HyperPay result codes |
| 🚫 رصيد غير كافٍ | result code `800.100.151` → رسالة مخصصة |
| 🔒 Test Mode في Production | منع إرسال بيانات Live credentials في Test Mode |

---

## Result Codes المهمة (HyperPay)

| الكود | المعنى | الإجراء |
|-------|--------|---------|
| `000.000.000` | نجاح تام | ✅ قبول |
| `000.100.110` | نجاح (Test Mode) | ✅ قبول في Test |
| `800.100.151` | بطاقة مرفوضة | ❌ رفض مع رسالة |
| `800.100.153` | بطاقة منتهية | ❌ رفض مع رسالة |
| `800.100.155` | رصيد غير كافٍ | ❌ رفض مع رسالة |
| `000.200.000` | معاملة معلقة | ⏳ انتظر Webhook |
| `800.300.401` | Entity ID غير صحيح | ⚠️ تحقق من الإعدادات |
| `100.396.104` | Idempotency conflict | 🔄 عملية مكررة، أرجع النتيجة السابقة |

---

## خطوات التنفيذ

- [x] **Step 1**: إنشاء Migration لأعمدة الدفع ✅ (Ran - Batch 4)
- [x] **Step 2**: تحديث Booking Model بالـ fillable الجديد ✅ (+ casts + PAYMENT_STATUSES + isPaid() + hasPayment())
- [x] **Step 3**: إنشاء `HyperPayService.php` ✅ (338 سطر - كامل مع error handling)
- [x] **Step 4**: تحديث `GeneralSettingController.php` بمفاتيح HyperPay ✅ (+ testHyperPayConnection())
- [x] **Step 5**: تحديث صفحة الإعدادات `integrations.blade.php` ✅ (Test/Live mode toggle + credentials + Test Connection button)
- [x] **Step 6**: تحديث `routes/web.php` ✅ (pay.form, pay POST, payment.failed, payment.pending)
- [x] **Step 7**: تحديث Store `BookingController.php` ✅ (store, payForm, pay, success, paymentFailed, paymentPending)
- [x] **Step 8**: تحديث نموذج الحجز `create.blade.php` ✅ (خيار الدفع الاختياري - يظهر عند تفعيل HyperPay)
- [x] **Step 9**: إنشاء صفحة الدفع `pay.blade.php` ✅ + صفحة فشل الدفع `payment-failed.blade.php` ✅ + صفحة انتظار `payment-pending.blade.php` ✅
- [x] **Step 10**: تحديث صفحة تفاصيل الحجز في CRM `show.blade.php` ✅ (قسم "بيانات دفع رسوم الحجز (HyperPay)" مع transaction_id, result_code, payment_at)
- [x] **Step 10b**: إضافة بادج نجاح الدفع في `success.blade.php` ✅ (يظهر المبلغ ورقم المعاملة)
- [x] **Step 11**: تشغيل Migration ✅ — **الآن: تجربة في Test Mode**

---

## Verification Plan

### Test Mode Testing
استخدام بيانات البطاقة التجريبية من HyperPay:
- **Visa ناجحة**: `4200000000000000` / Exp: `05/30` / CVV: `123`
- **Visa مرفوضة**: `4444444444444444`
- **MasterCard ناجحة**: `5454545454545454`

### اختبار الـ Scenarios
1. دفع ناجح → التحقق من `payment_status = paid` في DB
2. دفع مرفوض → التحقق من صفحة الفشل والرسالة
3. ضغط زر مرتين → التأكد من إرسال طلب واحد فقط
4. إغلاق المتصفح أثناء الدفع → التحقق من حالة الحجز

### Manual Verification
- افتح CRM → Bookings → تأكد من عرض بيانات الدفع بشكل صحيح
- افتح الإعدادات → تأكد من حقول HyperPay والـ Live Mode (معطلة)
