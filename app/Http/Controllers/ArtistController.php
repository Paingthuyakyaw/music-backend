<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArtistResource;
use App\Models\Album;
use App\Models\artist;
use App\Models\Music;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ArtistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $artist = artist::query(); // Initialize a query builder instance

        if ($request->search) {
            $searchTerm = $request->search;
            $artist->where('artist', 'like', '%' . $searchTerm . '%');
        }

        // Paginate the results
        $artist = $artist->paginate($request->size); // Use paginate() here

        return response()->json([
            'message' => 'Music',
            'data' => ArtistResource::collection($artist),
            'pagination' => [
                'page' => $artist->currentPage(),
                'totalPage' => $artist->total(),
                'size' => $artist->perPage(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'artist' => 'required|unique:artists,artist',
            'artist_image' => 'required|image',
            'about' => 'required',
            'birth' => 'required'

        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => $validation->errors()
            ]);
        };


        try {
            $artist = new artist();
            $artist->artist = $request->artist;
            $artist->about = $request->about;
            $artist->birth = $request->birth;
            if ($request->hasFile('artist_image')) {
                $image = $request->file('artist_image');
                // $fileName = time() . '_' . $image->getClientOriginalName();
                $imageStore = $image->store('public/artist');
                $artist->artist_image = $imageStore;
            }
            $artist->save();

            return response()->json([
                'message' => "Artist successfully create",
                'data' => $artist
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong!'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $artist = artist::findOrFail($id);

        return response()->json([
            'message' => 'Artist detail',
            'data'  => new ArtistResource($artist)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the artist by ID
        $artist = artist::find($id);

        // Check if the artist exists
        if (!$artist) {
            return response()->json([
                'message' => "Artist not found",
            ], 404); // 404 status code indicates resource not found
        }

        // Validate request data
        $validation = Validator::make($request->all(), [
            'artist' => 'required|unique:artists,artist,' . $id,
            'artist_image' => 'sometimes|required|image',
            'about' => 'required',
            'birth' => 'required'
        ]);

        // If validation fails, return error response
        if ($validation->fails()) {
            return response()->json([
                'message' => $validation->errors()
            ], 400); // 400 status code indicates bad request
        }

        try {


            // Update artist details
            $artist->artist = $request->artist;
            $artist->about = $request->about;
            $artist->birth = $request->birth;

            // Handle artist image update
            if ($request->hasFile('artist_image')) {

                if ($artist->artist_image) {
                    Storage::delete($artist->artist_image);
                }

                $image = $request->file('artist_image');
                $imageStore = $image->store('public/artist');
                $artist->artist_image = $imageStore;
            }

            // Save the updated artist
            $artist->save();

            // Return success response
            return response()->json([
                'message' => "Artist successfully updated",
                'data' => $artist
            ]);
        } catch (\Exception $e) {
            // If an exception occurs, return error response
            return response()->json([
                'message' => 'Something went wrong!'
            ], 500); // 500 status code indicates internal server error
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the artist by ID
        $artist = Artist::find($id);

        // If artist not found, return error response
        if (!$artist) {
            return response()->json([
                'message' => "Artist not found",
            ], 404); // Use 404 for not found
        }

        // Retrieve albums associated with the artist
        $albums = Album::where('artist_id', $artist->id)->get();

        // Retrieve music associated with the artist
        $music = Music::where('artist_id', $artist->id)->get();

        // Use a transaction to ensure atomicity
        DB::beginTransaction();
        try {
            // Delete artist's image if it exists
            if ($artist->artist_image) {
                Storage::delete($artist->artist_image);
            }

            // Iterate over albums and delete their images
            foreach ($albums as $album) {
                if ($album->album_image) {
                    Storage::delete($album->album_image);
                }
                $album->delete(); // Delete the album
            }

            // Iterate over music and delete their records
            foreach ($music as $track) {
                $track->delete();
            }

            // Delete the artist
            $artist->delete();

            // Commit the transaction
            DB::commit();

            return response()->json([
                'message' => 'Artist, their albums, and their music deleted successfully'
            ]);
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred while deleting the artist, their albums, and their music',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
