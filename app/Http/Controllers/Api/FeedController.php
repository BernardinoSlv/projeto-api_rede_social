<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Following;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        $me = $request->user();

        // id dos usuários que estou seguindo 
        $iFollowingsId = Following::following()->get()
            ->map(function($item){
                return $item->user_id_to;
            })->push($me->id)->toArray();
        

        $page = (int) $request->page;
        $perPage = intval($request->per_page) > 0 ? (int) $request->per_page : 2;

        $posts = Post::whereIn("user_id", $iFollowingsId)->with("owner")->orderBy("created_at", "DESC")->paginate($perPage);
        
        return [
            "posts" => $posts->items(),
            "current_page" => $posts->currentPage(),
            "total" => $posts->total(),
            "next_page_url" => $posts->nextPageUrl(),
            "total_pages" => $posts->lastPage()
        ];
    }

    public function me(Request $request)
    {
        $me = $request->user();
        
        $perPage = (intval($request->per_page) > 0 ? (int) $request->per_page : 2);

        $posts = $me->posts()->orderBy("created_at", "DESC")->paginate($perPage);

        return [
            "posts" => $posts->items(),
            "current_page" => $posts->currentPage(),
            "total" => $posts->total(),
            "next_page_url" => $posts->nextPageUrl(),
            "total_pages" => $posts->lastPage()
        ];
    }

    public function show(Request $request, $user_id)
    {
        $user = User::find($user_id);

        if (!$user){
            $this->resp["error"] = "Usuário não encontrado";
            return $this->resp;
        }

        $perPage = (intval($request->per_page) ? (int) $request->per_page : 2);

        $posts = $user->posts()->orderBy("created_at", "DESC")->paginate($perPage);

        return [
            "posts" => $posts->items(),
            "current_page" => $posts->currentPage(),
            "total" => $posts->total(),
            "next_page_url" => $posts->nextPageUrl(),
            "total_pages" => $posts->lastPage()
        ];
    }
}
