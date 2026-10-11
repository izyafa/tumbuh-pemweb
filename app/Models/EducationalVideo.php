<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationalVideo extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'title',
        'category',
        'youtube_url',
        'summary',
        'quiz_data',
    ];

    protected $casts = [
        'quiz_data' => 'array',
    ];

    protected $appends = [
        'youtube_id',
        'youtube_thumbnail_url',
        'youtube_embed_url',
    ];

    public function getYoutubeIdAttribute(): ?string
    {
        $url = $this->youtube_url;
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function getYoutubeThumbnailUrlAttribute(): string
    {
        $id = $this->youtube_id;
        if ($id) {
            return "https://img.youtube.com/vi/{$id}/hqdefault.jpg";
        }
        return 'https://img.youtube.com/vi/default/hqdefault.jpg';
    }

    public function getYoutubeEmbedUrlAttribute(): string
    {
        $id = $this->youtube_id;
        if ($id) {
            return 'https://www.youtube-nocookie.com/embed/' . $id;
        }
        return $this->youtube_url;
    }
}
