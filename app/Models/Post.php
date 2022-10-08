<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{    
    use HasFactory;

    public $table = "post";


    protected $fillable = [
        "text",
    ];

    protected $visible = [
        "id",
        "user_id",
        "text",
        "image",
        "created_at",
        "updated_at",
        "user",
        "count_likes",
        "count_comments",
        "iLiked",
        "iCommented"
    ];

    public $appends = [
        "user",
        "count_likes",
        "count_comments",
        "iLiked",
        "iCommented"
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class, "post_id", "id")->with("user");
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class, "post_id", "id");
    }


    public function scopeMe($query)
    {
        return $query->where("user_id", Auth::user()->id);
    }

    // my accessors and mutators 
    public function getUserAttribute()
    {
        return $this->owner()->first()->makeHidden(["email"]);
    }


    public function getCountLikesAttribute()
    {
        return $this->likes()->count();
    }

    public function getCountCommentsAttribute()
    {
        return $this->comments()->count();
    }

    public function getILikedAttribute()
    {
        return $this->likes()->where("user_id", Auth::user()->id)->count() === 1;
    }

    public function getICommentedAttribute()
    {
        return $this->comments()->where("user_id", Auth::user()->id)->count() > 1;
    }
}
