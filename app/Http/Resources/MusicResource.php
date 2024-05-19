<?php

namespace App\Http\Resources;

use App\Models\Album;
use App\Models\artist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MusicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $artist = artist::findOrFail($this->artist_id);
        $album = Album::findOrFail($this->album_id);
        return [
            'id' => $this->id,
            "name" => $this->name,
            'song_mp3' => url(str_replace('public', 'storage', $this->song_mp3)),
            'description' => $this->description,
            'song_image' => url(str_replace('public', 'storage', $this->song_image)),
            'artist' => $artist->artist,
            'artist_id' => $artist->id,
            'album' => $album->album,
            'album_id' => $album->id,
            'release_date' => Carbon::parse($this->created_at, 'UTC')->isoFormat('MMM Do YYYY'),
        ];
    }
}
