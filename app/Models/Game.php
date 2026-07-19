<?php

namespace App\Models;

use App\Models\GameImage;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'developer',
        'publisher',
        'type',
        'engine',
        'platform',
        'version',
        'size',
        'thumbnail',
        'banner',
        'game_file',
        'play_url',
        'download_url',
        'status'
    ];

    protected $appends = [
        'thumbnail_url',
        'banner_url'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function images()
    {
        return $this->hasMany(GameImage::class);
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail
            ? asset('storage/'.$this->thumbnail)
            : null;
    }

    public function getBannerUrlAttribute()
    {
        return $this->banner
            ? asset('storage/'.$this->banner)
            : null;
    }

}