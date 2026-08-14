<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'job_title',
        'resume_path',
        'cover_letter',
        'status',
    ];

    const STATUSES = [
        'new' => [
            'label' => 'جديد',
            'class' => 'bg-info-subtle text-info',
        ],
        'reviewed' => [
            'label' => 'تحت المراجعة',
            'class' => 'bg-warning-subtle text-warning',
        ],
        'shortlisted' => [
            'label' => 'مقبول مبدئياً',
            'class' => 'bg-primary-subtle text-primary',
        ],
        'rejected' => [
            'label' => 'مرفوض',
            'class' => 'bg-danger-subtle text-danger',
        ],
        'hired' => [
            'label' => 'تم التعيين',
            'class' => 'bg-success-subtle text-success',
        ],
    ];
}
