<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\UserCollection;
use App\Profile;
use App\Role;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;



class UserController extends Controller
{

    ////////////////////////////////////////////////
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      //return response()->json(['users' => User::paginate($per_page)],200);

    //without Eloquent API Resource to get all data of user and role
    //return response()->json(['users' => User::with('role')->paginate($per_page)],200);

    //with Eloquent API Resource to get the data we need only

    $per_page = $request->per_page ? $request->per_page : 5;
      return response()->json([
        'users' => new UserCollection(User::paginate($per_page)),
        'roles' =>Role::pluck('name')->all()],200);
    }

    // /**
    //  * Show the form for creating a new resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //adding record in multiple tables

        $role = Role::where('name', $request->role)->first();
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'phone_no'=> $request->phone_no,
            'password' => bcrypt($request->password),
            'api_token' => Hash::make($request->password)
        ]);
        $user->role()->associate($role);
        $user->save();
        $user->profile()->save(new Profile());
        return response()->json(['user' => new UserResource($user)] , 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $users = User::where('name','LIKE',"%$id%")->paginate();
        return response()->json(['users' => $users],200);
    }

    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function edit($id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = Role::where('name',$request->role)->first();
        $user =User::find($id);
        $user->name = $request->name;
        $user->role()->dissociate($user->role);
        $user->role()->associate($role);
        $user->save();
        return response()->json(['user' => new UserResource($user) ] , 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id)->delete();
        Profile::where('user_id',$id)->delete();
        return response()->json(['user' => $user] , 200);
    }

    public function deleteAll(Request $request){
        User::whereIn('id',$request->users)->delete();
        return response()->json(['message','Records Deleted Successfully'],200);
    }
    ///////////////////////////////////////////////
    public function login(Request $request)
    {
        // //echo json_encode($request->all());exit;  //for debugging
        // $username =  $request->email;
        // $password =  bcrypt($request->password);
        // $user = User::where('email', $username)->where('password', $password)->first();
        // echo json_encode($user);exit;
        // if ($user) {
        //     $token = Hash::make($request->password);
        //     $user->api_token = $token;
        //     $user->save();
        //     return $token;
        // }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $token = Str::random(80);
            Auth::user()->api_token = $token;
            Auth::user()->save();
            //return response()->json(['token'=>$token], 200);

            $admin = Auth::user()->isAdmin();
            return response()->json(['token'=>$token, 'isAdmin' => $admin], 200);
        }

        return response()->json(['status'=>'Email or Password is invaild'], 403);
    }

    public function verify(Request $request)
    {
        return $request->user()->only('name', 'email', 'api_token');
    }

    public function updateUserRole(Request $request)
    {
       $user = User::find($request->user);
       $logedInUser = $request->user();
       if ($user->id === $logedInUser->id) {
           return response()->json(['user' => new UserResource($logedInUser)],422);
       }
       $role = Role::where('name' , $request->role)->first();
       $user->role()->associate($role);
       $user->save();
       return response()->json(['user'=> new UserResource($user)] , 200);
    }
    public function changePhoto(Request $request)
    {
        $user = User::find($request->user);
        $profile = Profile::where('user_id', $request->user)->first();
        $ext = $request->photo->extension();
        $photoName= Str::random(20).".{$ext}";
        $photo = $request->photo->storeAs('images',$photoName,'public');
        $profile->photo = $photoName;
        $user->profile()->save($profile);
        return response()->json(['user' => new UserResource($user)], 200);
    }

    public function verifyEmail(Request $request)
    {
       $request->validate([
        'email' => 'required|unique:users'
       ]);
       return response()->json(['message'=> 'Valid Email'], 200);
    }

    public function signup(Request $request)
    {
        $user = new User;
        $user->email = $request->email;
        $user->name = $request->name;
        $user->phone_no = $request->phone_no;
        $user->password = bcrypt($request->password);
        $user->api_token = Hash::make($request->password);
       // $token = Str::random(80);
        $user->role_id = "1";
        $user->save();
        $user->profile()->save(new Profile());

    }
}
