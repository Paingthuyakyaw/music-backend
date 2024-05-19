<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    use HasFactory;

    public function artist()
    {
    return     $this->belongsTo(artist::class);
    }

    public function album()
    {
     return    $this->belongsTo(Album::class);
    }

    protected $fillable = [
        'name',
        'song_mp3',
        'description',
        'song_image',
        'artist_id'
    ];
}
