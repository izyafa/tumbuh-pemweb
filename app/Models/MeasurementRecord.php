<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeasurementRecord extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'child_id',
        'measured_by_id',
        'measurement_date',
        'age_in_months',
        'weight',
        'height',
        'head_circum',
        'arm_circum',
        'zscore_tb_u',
        'zscore_bb_u',
        'zscore_bb_tb',
        'stunting_status',
        'is_weight_stagnant',
        'notes',
    ];

    protected $casts = [
        'measurement_date' => 'date',
        'weight' => 'float',
        'height' => 'float',
        'head_circum' => 'float',
        'arm_circum' => 'float',
        'zscore_tb_u' => 'float',
        'zscore_bb_u' => 'float',
        'zscore_bb_tb' => 'float',
        'is_weight_stagnant' => 'boolean',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }

    public function measuredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'measured_by_id');
    }

    public function getStuntingStatusLabelAttribute(): string
    {
        return match ($this->stunting_status) {
            'severely_stunted' => 'Sangat Pendek (Severely Stunted)',
            'stunted' => 'Pendek (Stunted)',
            'normal' => 'Normal',
            'tall' => 'Tinggi',
            default => 'Normal',
        };
    }

    public function getBadgeClassAttribute(): string
    {
        return match ($this->stunting_status) {
            'normal' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'tall' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'stunted' => 'bg-amber-50 text-amber-700 border-amber-200',
            'severely_stunted' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }

    public function getWastingStatusAttribute(): string
    {
        $z = (float) $this->zscore_bb_tb;
        if ($z < -3.0) return 'severely_wasted';
        if ($z < -2.0) return 'wasted';
        if ($z <= 1.0) return 'normal';
        if ($z <= 2.0) return 'risk_of_overweight';
        if ($z <= 3.0) return 'overweight';
        return 'obese';
    }

    public function getWastingStatusLabelAttribute(): string
    {
        return match ($this->wasting_status) {
            'severely_wasted' => 'Gizi Buruk',
            'wasted' => 'Gizi Kurang',
            'normal' => 'Gizi Baik',
            'risk_of_overweight' => 'Risiko Gizi Lebih',
            'overweight' => 'Gizi Lebih',
            'obese' => 'Obesitas',
            default => 'Gizi Baik',
        };
    }

    public function getWastingBadgeClassAttribute(): string
    {
        return match ($this->wasting_status) {
            'normal' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'wasted' => 'bg-amber-50 text-amber-700 border-amber-200',
            'severely_wasted' => 'bg-rose-50 text-rose-700 border-rose-200',
            'risk_of_overweight' => 'bg-sky-50 text-sky-700 border-sky-200',
            'overweight', 'obese' => 'bg-purple-50 text-purple-700 border-purple-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }
}
