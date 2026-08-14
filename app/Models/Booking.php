<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 'car_id', 'assigned_to', 'client_name', 'client_phone', 'client_email',
        'down_payment', 'duration_years', 'interest_rate', 'monthly_installment',
        'total_price', 'notes', 'status', 'source', 'last_contacted_at',
        'booking_type', 'location', 'locale',
        // HyperPay Payment Fields
        'payment_status', 'payment_amount', 'payment_transaction_id',
        'payment_result_code', 'payment_result_description',
        'payment_idempotency_key', 'payment_at',
    ];

    protected $casts = [
        'last_contacted_at' => 'datetime',
        'payment_at' => 'datetime',
        'payment_amount' => 'decimal:2',
    ];

    const BOOKING_TYPES = ['test_drive', 'purchase', 'inquiry'];

    const PAYMENT_STATUSES = [
        'none' => ['label' => 'لم يدفع',       'color' => 'secondary'],
        'pending' => ['label' => 'جاري الدفع',    'color' => 'warning'],
        'paid' => ['label' => 'مدفوع ✓',       'color' => 'success'],
        'failed' => ['label' => 'فشل الدفع',     'color' => 'danger'],
    ];

    const BOOKING_TYPES_LABELS = [
        'test_drive' => 'تجربة قيادة',
        'purchase' => 'شراء',
        'inquiry' => 'استفسار',
    ];

    const BOOKING_TYPES_LABELS_EN = [
        'test_drive' => 'Test Drive',
        'purchase' => 'Purchase',
        'inquiry' => 'Inquiry',
    ];

    const STATUSES = [
        'new' => ['label' => 'جديد',          'color' => 'primary'],
        'contacted' => ['label' => 'تم التواصل',     'color' => 'info'],
        'interested' => ['label' => 'مهتم',           'color' => 'warning'],
        'rejected' => ['label' => 'مرفوض',          'color' => 'danger'],
        'sold' => ['label' => 'تم البيع ✓',     'color' => 'success'],
    ];

    const STATUSES_EN = [
        'new' => ['label' => 'New', 'color' => 'primary'],
        'contacted' => ['label' => 'Contacted', 'color' => 'info'],
        'interested' => ['label' => 'Interested', 'color' => 'warning'],
        'rejected' => ['label' => 'Rejected', 'color' => 'danger'],
        'sold' => ['label' => 'Sold ✓', 'color' => 'success'],
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function notes_list(): HasMany
    {
        return $this->hasMany(BookingNote::class)->latest();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(BookingDocument::class)->latest();
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status]['label'] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUSES[$this->status]['color'] ?? 'secondary';
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeInProgress($query)
    {
        return $query->whereIn('status', ['contacted', 'interested']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'sold');
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status]['label'] ?? $this->payment_status;
    }

    public function getPaymentStatusColorAttribute(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status]['color'] ?? 'secondary';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function hasPayment(): bool
    {
        return in_array($this->payment_status, ['pending', 'paid', 'failed']);
    }
}
