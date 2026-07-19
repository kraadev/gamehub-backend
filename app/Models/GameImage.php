<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameImage extends Model
{
    protected $fillable = [
        'game_id',
        'image',
        'sort_order'
    ];

    protected $appends = [
        'image_url'
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image);
    }
}