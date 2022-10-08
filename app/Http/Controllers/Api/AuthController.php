<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function create(Request $request):array 
    {
        $rules = [
            "name" => ["required"],
            "email" => ["required", "unique:user,email"],
            "password" => ["required", "min:4"],
            "birth_date" => ["required", "date"],
            "city" => ["min:2", "nullable"],
            "work" => ["min:2", "nullable"],
            "avatar" => ["image", "nullable"],
            "cover" => ["image", "nullable"]
        ];


        $validated = Validator::make($request->all(), $rules);

        if ($validated->fails()){
            $this->resp["error"] = true;
            $this->resp["message"] = $validated->messages();
            return $this->resp;
        }

        $newUser = new User();
        $newUser->fill($request->all());
        $newUser->password = bcrypt($request->password);
        $newUser->save();
        return $this->resp;
    }

    public function login(Request $request):array 
    {
        $rules = [
            "email" => ["required", "email"],
            "password" => ["required", "min:4"]
        ];

        $validated = Validator::make($request->only("email", "password"), $rules);

        if ($validated->fails()){
            $this->resp["error"] = $validated->messages();
            return $this->resp;
        }

        $token = Auth::attempt([
            "email" => $request->email,
            "password" => $request->password
        ]);

        if (!$token){
            $this->resp["error"] = "E-mail e/ou senha incorreto(s)";
            return $this->resp;
        }
        $this->resp["token"] = $token;
        return $this->resp;
    }

    public function check():array 
    {
        $this->resp["error"] = ! Auth::check();
        return $this->resp;
    }

    public function refresh():array 
    {
        $this->resp["token"] = Auth::refresh();
        return $this->resp;
    }

    public function logout():array 
    {
        Auth::logout();
        return $this->resp;
    }
}
