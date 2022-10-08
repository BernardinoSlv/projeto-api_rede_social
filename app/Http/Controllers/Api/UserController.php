<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Following;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $me = User::find(Auth::user()->id);
        $me->makeVisible(["age", "email", "count_followings", "count_followers", "count_posts"]);

        return $me;
    }

    public function show($id):User|array 
    {
        $user = User::find($id);
        $me = User::find(Auth::user()->id);

        if (!$user)
        {
            $this->resp["error"] = "Usuário não encontrado";
            return $this->resp;
        }

        $user["iFollow"] = (bool) $me->following()->where("user_id_to", $user->id)->count();
        $user->makeVisible(["age", "count_followings", "count_followers", "count_posts", "iFollow"]);

        return $user;
    }

    public function update(Request $request):array 
    {
        $rules = [
            "password" => ["nullable", "min:4"],
            "password_confirm" => [($request->has("password") ? "required" : "nullable") ,"same:password"],
            "birth_date" => ["nullable", "date"],
            "city" => ["min:2", "nullable"],
            "work" => ["min:2", "nullable"],
            "avatar" => ["image", "nullable"],
            "cover" => ["image", "nullable"]
        ];
        $validated = Validator::make($request->all(), $rules);
        if ($validated->fails()){
            $this->resp["error"] = $validated->messages();
            return $this->resp;
        }

        $user = $request->user();
        $user->fill($request->only("password", "birth_date", "city", "work"));

        // checagem se existe e se é um arquivo, pois caso for um valor nulo, irá remover a image que antes estava
        if ($request->exists("cover")){
            if ($user->cover){
                Storage::delete($user->cover);
            }
            if ($request->file("cover")){
                $user->cover = $request->cover->store("cover");
            }
            else{
                $user->cover = null;
            }
        }
        if ($request->exists("avatar")){
            if ($user->avatar){
                Storage::delete($user->avatar);
            }
            if ($request->file("avatar")){
                $user->avatar = $request->avatar->store("avatar");
            }
            else{
                $user->avatar = null;
            }
        }

        $user->save();
        return $this->resp;
    }

    public function follow(Request $request)
    {
        $me = $request->user();
        $user = User::find($request->user);

        if (!$user){
            $this->resp["error"] = "Usuário não encontrado";
            return $this->resp;
        }
        if ($me->id === $user->id){
            $this->resp["error"] = "Você não pode seguir a si mesmo :)";
            return $this->resp;
        }

        echo $user->i_follow;

        // se eu @me estiverseguindo o @user->id, eu deleto ele do meus seguidores
        if ($user->i_follow){
            $me->followingPivot()->where("user_id_to", $user->id)->delete();
        }
        else{
            $newFollowing = new Following();
            $newFollowing->user_id_from = $me->id;
            $newFollowing->user_id_to = $user->id;
            $newFollowing->save();
        }

        return User::find($request->user)
            ->makeVisible("i_follow");
    }

    public function following($id)
    {
        $user = User::find($id);

        if (!$user){
            $this->resp["error"] = "Usuário não encontrado";
            return $this->resp;
        }

        return $user->followers;
    }

    public function followers($id)
    {
        $user = User::find($id);

        if (!$user){
            $this->resp["error"] = "Usuário não encontrado";
            return $this->resp;
        }

        return $user->following;
    }
}
