<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function show($id)
    {

    }

    public function likes(Request $request, $id)
    {
        $post = Post::with("likes")->find($id);
        if (!$post){
            $this->resp["error"] = "Post não encontrado";
            return $post;
        }
        return $post->makeVisible("likes");
    }

    public function like(Request $request, $id)
    {
        $post = Post::find($id);
        $me = $request->user();

        if (!$post){
            $this->resp["error"] = "Post não encontrado";
            return $this->resp;
        }

        $postLike = PostLike::iLike($me->id, $post->id)->first();

        if ($postLike){
            $postLike->delete();
        }
        else{
            $newPostLike = new PostLike();
            $newPostLike->user_id = $me->id;
            $newPostLike->post_id = $post->id;
            $newPostLike->save();
        }

        return $post;
    }

    public function comments(Request $request)
    {
        $post = Post::find($request->post);
        
        if (!$post){
            $this->resp["error"] = "Post não encontrado";
            return $this->resp;
        }
        return [
            "post" => $post,
            "comments" => $post->comments->makeVisible("is_my")
        ];
    }

    public function comment(Request $request, $id)
    {
        $validated = Validator::make([
            "text" => $request->text,
            "post" => $request->post
        ], [
            "text" => ["required", "min:1", "string"],
            "post" => ["required", "exists:post,id"]
        ]);

        if ($validated->fails()){
            $this->resp = $validated->messages();
            return $this->resp;
        }

        $me = $request->user();
        $post = Post::find($id);

        $newPostComment = new PostComment();
        $newPostComment->user_id = $me->id;
        $newPostComment->post_id = $post->id;
        $newPostComment->text = $request->text;
        $newPostComment->save();

        return [
            "last_comment" => $newPostComment,
            "post" => $post
        ];
    }

    public function create(Request $request)
    {
        $rules = [
            "text" => ["required_without:image", "min:1"],
            "image" => ["required_without:text", "image"]
        ];

        $validated = Validator::make($request->all(), $rules);

        if ($validated->fails()){
            $this->resp["error"] = $validated->messages();
            return $this->resp;
        }

        $newPost = new Post();
        $newPost->user_id = $request->user()->id;
        $newPost->text = $request->text;

        if ($request->file("image")){
            $newPost->image = $request->image->store("post");
        }
        $newPost->save();

        return $this->resp;
    }


    public function update(Request $request, $id)
    {
        $rules = [
            "text" => ["required_without:image"],
            "image" => ["required_without:text"]
        ];

        $post = Post::me()->find($id);
        
        if (!$post){
            $this->resp["error"] = "Post não encontrada";
            return response()->json($this->resp);
        }

        if ($request->exists("text")){
            $post->text = $request->text;
        }
        if ($request->exists("image")){
            if ($post->image){
                Storage::delete($post->image);
            }

            if ($request->file("image")){
                $post->image = $request->image->store("post");
            }
            else{
                $post->image = null;
            }
        }
        $post->save();


        return $this->resp;
    }
}
