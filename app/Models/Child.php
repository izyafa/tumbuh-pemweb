<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Child extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nik',
        'full_name',
        'date_of_birth',
        'gender',
        'birth_weight',
        'birth_height',
        'parent_name',
        'parent_phone',
        'address',
        'public_token',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'birth_weight' => 'float',
        'birth_height' => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(function (Child $child) {
            if (empty($child->public_token)) {
                $child->public_token = (string) Str::uuid();
            }
        });
    }

    public function measurementRecords(): HasMany
    {
        return $this->hasMany(MeasurementRecord::class, 'child_id')->orderBy('measurement_date', 'asc');
    }

    public function latestMeasurement(): HasOne
    {
        return $this->hasOne(MeasurementRecord::class, 'child_id')->latestOfMany('measurement_date');
    }

    public function aiConsultations(): HasMany
    {
        return $this->hasMany(AiConsultation::class, 'child_id')->orderBy('created_at', 'desc');
    }

    public function immunizationRecords(): HasMany
    {
        return $this->hasMany(ImmunizationRecord::class, 'child_id')->orderBy('administered_date', 'asc');
    }

    public function getAgeInMonthsAttribute(): int
    {
        return (int) $this->date_of_birth->diffInMonths(Carbon::now());
    }

    public function getGenderLabelAttribute(): string
    {
        return $this->gender === 'male' ? 'Laki-laki' : 'Perempuan';
    }
}
