<?php

namespace Modules\Faq\Entities;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Starter\Entities\BaseModel;

class FaqGroup extends BaseModel
{

    protected $appends = [
        'created_at_datetime'
    ];

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class);
    }
}