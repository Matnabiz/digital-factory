<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller {
 public function login(Request $r){$data=$r->validate(['email'=>'required|email','password'=>'required|string']);if(!Auth::attempt($data))return response()->json(['message'=>'Invalid credentials'],422);$u=Auth::user();return response()->json(['token'=>$u->createToken('web')->plainTextToken,'user'=>$u]);}
 public function me(Request $r){return $r->user();}
 public function logout(Request $r){$r->user()->currentAccessToken()->delete();return response()->json(['ok'=>true]);}
}
