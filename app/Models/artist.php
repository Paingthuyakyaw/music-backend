<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class artist extends Model
{
    use HasFactory;

    public function music()
    {
        return $this->hasMany(Music::class);
    }

    public function album()
    {
        return $this->hasMany(Album::class);
    }

    protected $fillable = [
        'artist',
        'artist_image'
    ];
}
