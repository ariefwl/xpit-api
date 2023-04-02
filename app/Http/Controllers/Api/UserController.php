<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = User::all();
            return response()->json([
                'data' => $user,
                'success' => true 
            ]);
        } catch (QueryException $e) {
            $error = [
                'error' => $e->getMessage()
            ];
            return response()->json($error, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|unique:users|email',
                'username' => 'required',
                'name' => 'required',
                // 'company_id' => 'required',
                // 'type' => 'required',
                // 'avatarImage' => 'required|image|mimes:jpg,png,jpeg|max:2048',
                'password' => 'required',
                // 'confirm_password' => 'required|same:password'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ada kesalahan !',
                    'data' => $validator->errors()
                ], 422);
            }
    
            //upload foto
            // $foto = $request->file('avatarImage');
            // $foto->storeAs('public/avatar', $foto->hashName());
    
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
    
            // $success['token'] = $user->createToken('auth_token')->plainTextToken;
            // $success['name'] = $user->name;

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil !',

            ], Response::HTTP_CREATED);

        } catch (QueryException $e) {
            $error = [
                'error' => $e->getMessage()
            ];
            return response()->json($error, Response::HTTP_INTERNAL_SERVER_ERROR);
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                // 'email' => 'required|unique:users|email',
                'username' => 'required',
                'name' => 'required',
                'company_id' => 'required',
                'type' => 'required',
                // 'avatarImage' => 'required|image|mimes:jpg,png,jpeg|max:2048',
                'password' => 'required',
                'confirm_password' => 'required|same:password'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ada kesalahan !',
                    'data' => $validator->errors()
                ], 422);
            }
            
            if ($request->hasFile('avatarImage')) {
                //upload avatar
                $foto = $request->file('image');
                $foto->storeAs('public/avatar', $foto->hashName());
    
                //hapus avatar lama
                Storage::delete('public/avatar'.basename($user->avatarImage));
    
                //update post with new image
                $user->update([
                    'email' => $request->email,
                    'username' => $request->username,
                    'name' => $request->name,
                    'company_id' => $request->company_id,
                    'type' => $request->type,
                    // 'avatarImage' => $foto->hashName()
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil di update !'
                ], Response::HTTP_OK);
            } else {
                //update data tanpa avatar
                $user->update([
                    'email' => $request->email,
                    'username' => $request->username,
                    'name' => $request->name,
                    'company_id' => $request->company_id,
                    'type' => $request->type,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil di update !'
                ], Response::HTTP_OK);
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Tidak ada data !',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
