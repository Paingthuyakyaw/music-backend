<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use App\Models\Music;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function PHPSTORM_META\map;

class FavouriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
       
        $favourites = $user->favourites()->with('music')->get();

        $musics = $favourites->map(function($favourite) {
            return $favourite->music;
        });

         

        return  response()->json([
            'data' => $musics->map(function ($mus) {
              return [
                'id' => $mus->id,
                'name' => $mus->name,
                'song_mp3' => $mus->song_mp3,
                'song_mp3' => url(str_replace('public', 'storage', $mus->song_mp3)),
                'song_image' => url(str_replace('public', 'storage', $mus->song_image)),
                'created_at' => $mus->created_at,
                'updated_at' => $mus->updated_at,
              ];
            }  ),
            
            'username'  => $user->username
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

      $valid =  $request->validate([
            'music_id' => 'required',
        ]);


      try{
        $music = Music::find($request->music_id);


        if(!$music){
            return response()->json([
                'message' => 'Music is not exits'
            ],400);
        }

        if(!Auth::user()){
            return response()->json([
                'message' => 'User is not exits'
            ],400);
        }

        $fav = new Favourite();
        $fav->user_id = Auth::id();
        $fav->music_id = $music->id;
        $fav->save();

        return response()->json([
            'message' => 'Add to favourite',
            'data' => $fav
        ]);
      }catch(Exception $e){
        return response()->json([
            'message' => 'Add to fail',
            'error' => $e
        ]);
      }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $favourite = Favourite::where('user_id', Auth::id())->where('music_id', $id)->first();

            if (!$favourite) {
                return response()->json([
                    'message' => 'Favourite not found'
                ], 404);
            }

            $favourite->delete();

            return response()->json([
                'message' => 'Favourite deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete favourite',
                'error' => $e->getMessage()
            ]);
        }
    }
}
