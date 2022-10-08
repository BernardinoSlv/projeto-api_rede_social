<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function user(Request $request)
    {
        $users = [];
        if ($request->s){
            $users = User::where("name", "LIKE", "%{$request->s}%")
                ->get()
                ->each(function ($user){
                    $user->makeVisible(["avatar_url", "cover_url"]);
                });
        }

        return [
            "users" => $users 
        ];
    }
}
