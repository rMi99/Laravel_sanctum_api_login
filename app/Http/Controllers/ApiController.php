<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Str;

class ApiController extends Controller
{
/**  Get User By Token
* @param Request
* @return User $user
*/
public function getUser(Request $request){
     
    return response()->json(['user'=>$request->user()]);
}
/**  Get User By Token
* @param Request
* @return User $user
*/
public function checkAdmin(Request $request){
   $user = $request->user();
   if($user->tokenCan('admin')){

    return response()->json([
        'status' => 200,
        'message' => $user->name."is admin!!"
    ],200);
   } 
   return response()->json([
    'status' => 401,
    'message' => $user->name."is not a admin privilages!!"
],401);
   
}



/**  Get User By Token
* @param Request
* @return User $user
*/
public function logout(Request $request){
     
    $user = $request->user();
    $user->currentAccessToken()->delete();
    return response()->json([
        'status' => 200,
        'message' => "Successfully Logout!!"
    ],200);
}

/**  Register user
* @Request $request
* @return Bolean $result
*/

public function register(Request $request){
    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required|email',
        'password' => 'required', // You should specify validation rules here.
    ]);
    
    // Now you can check if validation fails and take appropriate action.
    if ($validator->fails()) {
        return response()->json([
            'status' => 400,
            'message' => "Bad Request"
        ],400);
    }

$user =new User();
$user->name= $request->name;
$user->email= $request->email;
$user->password=Hash::make($request->input('password'));
$user->roles  ='user';
$user->save();

return response()->json([
    'status' => 200,
    'message' => "User Registered"
],200);

}
/**  Login user
* @Request $request
* @return$user with token
*/

public function login(Request $request){
    $validator = Validator::make($request->all(), [
         'email' => 'required|email',
        'password' => 'required', // You should specify validation rules here.
    ]);
    
    // Now you can check if validation fails and take appropriate action.
    if ($validator->fails()) {
        return response()->json([
            'status' => 400,
            'message' => "Bad Request"
        ],400);
    }

    if(!Auth::attempt($request->only('email','password'))){
        return response()->json([
            'status' => 401,
            'message' => "Unautherized"
        ],401);
    }
        $user = User::where ("email",$request->email)->select('id','name','email','roles')->first();
       $token= $user->createToken("API TOKEN")->plainTextToken;

       Arr::add($user,'token',$token);
        return response()->json($user);
    


}


}

