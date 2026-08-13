<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;


#[Fillable([
    'document_id',
    'approved',
    'description',
    'confidence',
    'content',
    'errors',
    'warnings',
    'payload'
])]
class DocumentAnalysis extends Model
{

    protected function casts()
    {
        return [
            'document_id' => 'int',
            'approved' => 'boolean',
            'confidence' => 'float',
        ];
    }


    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function getErrorListAttribute()
    {
        return $this->errors ? explode(", ", $this->errors) : null;
    }

    public function getWarningListAttribute()
    {
        return $this->warnings ? explode(", ", $this->warnings) : null;
    }
}
