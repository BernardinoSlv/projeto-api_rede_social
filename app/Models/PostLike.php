<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PostLike extends Model
{
    use HasFactory;

    public $table = "post_like";

    protected $visible = [];
    
    protected $appends = [
        "is_my"
    ];


    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function scopeILike($query, $user_id, $post_id)
    {
        return $query->where([
            "user_id" => $user_id,
            "post_id" => $post_id
        ]);
    }

    // my custom accessors and mutators 

    public function getIsMyAttribute()
    {
        return Auth::user()->id === $this->attributes["user_id"];
    }
}
