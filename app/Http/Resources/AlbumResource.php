<?php

namespace App\Http\Resources;

use App\Models\artist;
use App\Models\Music;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $artist = artist::findOrFail($this->artist_id);

        if(!$artist){
            return response()->json([
                'message' => 'Artist Not Found'
            ],404);
        }

        $music = Music::where('artist_id','=',$artist->id)->get();


        return [
            'id' => $this->id,
            'artist_id' => $this->artist_id,
            'album' => $this->album,
            'album_image' => url(str_replace('public','storage',$this->album_image)),
            'artist' => [
            'id' => $artist->id,
            'artist' => $artist->artist,
            'artist_image' => asset(str_replace('public', 'storage', $artist->artist_image)),
            'about' => $artist->about,
            'birth' => $artist->birth,
        ],
            'music' => $music->map(function ($track) {
                return [
                    'id' => $track->id,
                    'name' => $track->name,
                    'song_mp3' => url(str_replace('public', 'storage', $track->song_mp3)),
                    'description' => $track->description,
                    'song_image' => url(str_replace('public', 'storage', $track->song_image)),
                    'release_date' => Carbon::parse($track->created_at)->isoFormat('MMM Do YYYY'),
                ];
            })
        ];
    }
}
