<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PostComment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function update(Request $request)
    {
        $validated = Validator::make([
            "id" => $request->comment,
            "text" => $request->text,
        ], [
            "id" => ["required", "exists:post_comment,id"],
            "text" => ["required", "min:1", "string"],
        ]);

        if ($validated->fails()){
            $this->resp = $validated->messages();
            return $this->resp;
        }

        $postComment = PostComment::my()->find($request->comment);

        if (!$postComment){
            $this->resp["error"] = "Esse comentário não é seu";
            return $this->resp;
        }
        $postComment->text = $request->text;
        $postComment->save();
        return [
            "updated_comment" => $postComment,
            "post" => $postComment->post
        ];
    }

    public function destroy($comment_id)
    {
        $postComment = PostComment::my()->find($comment_id);
        $me = User::find(Auth::user()->id);

        if (!$postComment){
            $this->resp["error"] = "Comentário não encontrado";
            return $this->resp;
        }

        $postComment->delete();

        return [
            "deleted_comment" => $postComment,
            "post" => $postComment->post
        ];
    }
}
