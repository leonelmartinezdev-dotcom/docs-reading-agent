<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'label',
    'description',
    'color',
    'prompt',
    'active'
])]
class DocumentType extends Model
{
    protected function casts()
    {
        return [
            'active' => 'bool'
        ];
    }


    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
