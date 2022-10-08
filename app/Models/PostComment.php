<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PostComment extends Model
{
    use HasFactory;

    public $table = "post_comment";

    protected $fillable = [
        "text"
    ];

    protected $visible = [
        "id",
        "user_id",
        "post_id",
        "text"
    ];

    protected $appends = [
        "is_my",
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, "post_id", "id");
    }

    public function scopeMy($query)
    {
        return $query->where("user_id", Auth::user()->id);
    }
    
    // my accessors and mutators 
    public function getIsMyAttribute()
    {
        return Auth::user()->id === $this->attributes["user_id"];
    }
}
