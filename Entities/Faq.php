<?php

namespace Modules\Faq\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Starter\Entities\BaseModel;

class Faq extends BaseModel
{
    protected $casts = [
        'is_active' => 'boolean'
    ];

    protected $appends = [
        'created_at_datetime'
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(FaqGroup::class, 'faq_group_id');
    }
}