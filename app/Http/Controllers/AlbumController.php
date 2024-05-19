<?php

namespace App\Http\Controllers;

use App\Http\Resources\AlbumResource;
use App\Models\Album;
use App\Models\artist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $album = Album::query(); // Initialize a query builder instance

        if ($request->search) {
            $searchTerm = $request->search;
            $album->where('album', 'like', '%' . $searchTerm . '%');
        }

        // Paginate the results
        $album = $album->paginate($request->size); // Use paginate() here

        return response()->json([
            'message' => 'Music',
            'data' => AlbumResource::collection($album),
            'pagination' => [
                'page' => $album->currentPage(),
                'totalPage' => $album->total(),
                'size' => $album->perPage(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'album' => 'required|unique:albums,album',
            'album_image' => 'required|image',
            'artist_id' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => $validation->errors()
            ]);
        };

        // artist condition
        $art =  artist::find($request->artist_id);
        if (!$art) {
            return response()->json([
                'message' => 'Artist Not Found'
            ]);
        }

        // create albumn
        $album = new Album();
        $album->album = $request->album;
        $album->artist_id = $request->artist_id;
        if ($request->hasFile('album_image')) {
            $image = $request->file('album_image');
            $imageStore = $image->store('public/album');
            $album->album_image = $imageStore;
        }
        $album->save();

        return response()->json([
            'message' => 'Successfully Album create',
            'data' =>  $album
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $album = Album::findOrFail($request->id);

        return response()->json([
            'message' => "Album",
            'data' => new AlbumResource($album)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $album = Album::find($id);

        if (!$album) {
            return response()->json([
                'message' => 'Album not found!',
            ], 500);
        }

        $validation = Validator::make($request->all(), [
            'album' => 'required',
            'album_image' => 'required|image',
            'artist_id' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => $validation->errors()
            ]);
        };



        $art =  artist::find($request->artist_id);
        if (!$art) {
            return response()->json([
                'message' => 'Artist Not Found'
            ]);
        }

        $album->album = $request->album;
        $album->artist_id = $art->id;

        if ($request->hasFile('album_image')) {
            if ($album->album_image) {
                Storage::delete($album->album_image);
            }

            $image = $request->file('album_image');
            $imageStore = $image->store('public/album');
            $album->album_image = $imageStore;
        }

        $album->update();

        return response()->json([
            'message' => "Successfully album update",
            'data' => $album
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $album = Album::find($id);

        if ($album->album_image) {
            Storage::delete($album->album_image);
        }

        $album->delete();

        return response()->json([
            'message' => 'Delete artist'
        ]);
    }
}
