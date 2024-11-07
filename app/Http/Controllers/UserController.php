<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::paginate(10);
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8',
            'age' => 'nullable|integer|min:0',
            'membership_status' => 'nullable|in:free,premium,vip',
        ]);
    
        try {
            // Encrypt the password
            $validatedData['password'] = bcrypt($validatedData['password']);
    
            // Create the new user
            $user = User::create($validatedData);
    
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user
            ], 201);
        } catch (ModelNotFoundException $e) {
            // Handle database errors
            return response()->json([
                'success' => false,
                'message' => 'Database error occurred while creating user',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            // Handle general errors
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,'.$user->id.'|max:255',
            'password' => 'sometimes|required|string|min:8',
            'age' => 'sometimes|required|integer|min:0',
            'membership_status' => 'sometimes|required|in:free,premium,vip',
        ]);

        $user->update($validated);
        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user =User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }
}
