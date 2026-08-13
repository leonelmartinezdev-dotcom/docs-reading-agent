<?php

namespace App\Models;

use App\DocumentStatus;
use App\Events\DocumentCreated;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Override;

#[Fillable([
    'url',
    'title',
    'extension',
    'size',
    'original_name',
    'requires_analysis',
    'status',
    'status_changed_by',
    'status_changed_at',
    'description',
    'owner_id',
    'document_type_id',
    'documentable_type',
    'documentable_id',
])]

class Document extends Model
{
    protected function casts(): array
    {
        return [
            'status_changed_at' => 'datetime',
            'requires_analysis' => 'boolean',
            'documentable_id' => 'integer',
            'owner_id' => 'integer',
            'status' => DocumentStatus::class,
        ];
    }


    #[Override]
    protected static function booted()
    {

        static::addGlobalScope('owner', function (Builder $builder) {
            //$builder->where('owner_id', auth()->user()->id);
        });


        static::created(function (Document $document) {
            if ($document->requires_analysis) {
                DocumentCreated::dispatch($document, auth()->user());
            }
        });

        static::updated(function (Document $document) {
            //
        });
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function getIsApprobalAttribute(): bool
    {
        return $this->status == DocumentStatus::Approved;
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', DocumentStatus::Approved);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', DocumentStatus::Rejected);
    }


    public function analysis(): HasMany
    {
        return $this->hasMany(DocumentAnalysis::class);
    }

    public function latestAnalysis(): HasOne
    {
        return $this->hasOne(DocumentAnalysis::class)->latestOfMany();
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function statusChangedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'status_changed_by');
    }
}
