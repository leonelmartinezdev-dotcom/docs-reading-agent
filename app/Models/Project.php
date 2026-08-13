<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable(['name', 'description', 'start_at', 'end_at'])]
class Project extends Model
{
    protected function casts(): array
    {
        return [
            'start_at' => 'date',
            'end_at' => 'date'
        ];
    }


    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function approvedDocuments(): MorphMany
    {
        return $this->documents()->approved();
    }

    public function rejectedDocuments(): MorphMany
    {
        return $this->documents()->rejected();
    }
}
