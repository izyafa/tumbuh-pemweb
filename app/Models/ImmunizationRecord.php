<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImmunizationRecord extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'child_id',
        'recorded_by_id',
        'vaccine_name',
        'category',
        'administered_date',
        'batch_number',
        'notes',
    ];

    protected $casts = [
        'administered_date' => 'date',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_id');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'vitamin_a' => 'Suplementasi Vitamin A',
            'obat_cacing' => 'Pencegahan Cacingan',
            default => 'Imunisasi Dasar / Lanjutan',
        };
    }
}
