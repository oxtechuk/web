<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // حالة الدفع
            $table->enum('payment_status', ['none', 'pending', 'paid', 'failed'])
                ->default('none')
                ->after('source')
                ->comment('حالة الدفع: none=لم يدفع، pending=جاري، paid=مدفوع، failed=فاشل');

            // مبلغ الدفع المطلوب
            $table->decimal('payment_amount', 10, 2)
                ->nullable()
                ->after('payment_status')
                ->comment('مبلغ رسوم الحجز المدفوعة');

            // رقم المعاملة من HyperPay
            $table->string('payment_transaction_id')->nullable()->after('payment_amount');

            // كود النتيجة من HyperPay
            $table->string('payment_result_code')->nullable()->after('payment_transaction_id');

            // رسالة النتيجة (للعرض للعميل أو في CRM)
            $table->string('payment_result_description')->nullable()->after('payment_result_code');

            // مفتاح الـ Idempotency لمنع الدفع المزدوج
            $table->string('payment_idempotency_key')->nullable()->unique()->after('payment_result_description');

            // وقت إتمام الدفع
            $table->timestamp('payment_at')->nullable()->after('payment_idempotency_key');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique(['payment_idempotency_key']);
            $table->dropColumn([
                'payment_status',
                'payment_amount',
                'payment_transaction_id',
                'payment_result_code',
                'payment_result_description',
                'payment_idempotency_key',
                'payment_at',
            ]);
        });
    }
};
