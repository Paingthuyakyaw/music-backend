<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = User::query();

        if($request->search){
            $search = $request->search;
           $user->where('username','like',"%$search%");
        }

        $user = $user->paginate($request->size);

        return response()->json([
            'message' => 'All user list',
            'data' => UserResource::collection($user),
            'pagination' => [
                'page' => $user->currentPage(),
                'totalPage' => $user->total(),
                'size' => $user->perPage(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
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
        //
    }

    public function me(){
        return response()->json([
            'data' => auth()->user(),
            'message' => "Fetching Successfullly"
        ]);
    }
}
