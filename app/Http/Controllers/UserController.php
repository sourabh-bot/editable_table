<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStore;
use App\Http\Requests\UserUpdate;
use App\Models\Category;
use App\Models\Hobby;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $user = User::all();

        $categories = Category::pluck('name', 'id')->toArray();
        if($request->wantsJson()){
            foreach($user as $us){
                $us->profile_pic = asset($us->profile_pic);
//                $us->hobbies = implode(',', $us->hobbies->name);
            }

            return response()->json(["user"=>$user, "categories"=>$categories]);
        }
        return view('user', ["user"=>$user, "categories"=>$categories]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserStore $request)
    {
        $validated = $request->validated();
        $profile_pic = $request->file('profile_pic');
        $profile_pic = $profile_pic->move('images/profile', uniqid().'.'.$profile_pic->getClientOriginalExtension());
        if($profile_pic){
            $validated['profile_pic'] = $profile_pic;
            $user = User::create($validated);
            if($user){
                foreach ($request->hobby as $hobby){
                    $user_hobby = new Hobby();
                    $user_hobby->name = $hobby;
                    $user_hobby->user_id = $user->id;
                    $user_hobby->save();
                }
                return response()->json(['status'=>'success', 'message'=>'User is created Successfully.'], 201);
            }else{
                return response()->json(['status'=>'user_error', 'message'=>'Sorry! user is not created.'], 409);
            }

        }else{
            return response()->json(['status'=>'img_error', 'message'=>'Sorry! something wrong with image'], 409);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdate $request, User $user)
    {
        //
        $validated = $request->validated();
        if ($request->has('profile_pic')){
            $profile_pic = $request->file('profile_pic');
            $profile_pic = $profile_pic->move('images/profile', uniqid().'.'.$profile_pic->getClientOriginalExtension());
            if($profile_pic){
                $validated['profile_pic'] = $profile_pic;
            }else{
                return response(['status'=>'image_error', 'message'=>'Sorry! image is not updated'], 409);
            }
        }
        if($user->update($validated)){
            $user->hobbies()->delete();
            $hobbies = explode(',', $request->hobby);
            foreach ($hobbies as $hobby){
                $user_hobby = new Hobby();
                $user_hobby->name = $hobby;
                $user_hobby->user_id = $user->id;
                $user_hobby->save();
            }
            return response()->json(['status'=>'success', 'message'=>'User update successfully'], 200);
        }else{
            return response()->json(['status'=>'user_error', 'message'=>'Sorry! user is not updated'], 409);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user_id = explode(',', $id);
        $user = User::find($user_id);
        foreach ($user as $u_id){
            unlink($u_id->profile_pic);
            $u_id->hobbies()->delete();
            $u_id->delete();
        }
        return response()->json(['message'=>'Data is delete successfully'], 200);

    }

}
