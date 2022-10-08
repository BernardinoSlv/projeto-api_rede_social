<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    public $table = "user";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        "birth_date",
        "city",
        "work"
    ];

    protected $visible = [
        "id",
        "name",
        "birth_date",
        "city",
        "work",
        "cover",
        "cover_url",
        "avatar",
        "avatar_url"
    ];

    protected $appends = [
        "avatar_url",
        "cover_url",
        "age",
        "count_following",
        "count_followers",
        "count_posts",
        "i_follow"
    ];
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];


    public function following()
    {
        return $this->belongsToMany(User::class, "following", "user_id_from", "user_id_to");
    }

    public function followingPivot()
    {
        return $this->hasMany(Following::class, "user_id_from", "id");
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, "following", "user_id_to", "user_id_from");
    }

    public function followersPivot()
    {
        return $this->hasMany(Following::class, "user_id_to", "id");
    }

    public function posts()
    {
        return $this->hasMany(Post::class, "user_id", "id");
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // my mutators and accessors 
    public function getAvatarUrlAttribute()
    {
        if (!$this->attributes["avatar"]){
            return null;
        }
        return Storage::url($this->attributes["avatar"]);
    }

    public function getCoverUrlAttribute()
    {
        if (!$this->attributes["cover"]){
            return null;
        }
        return Storage::url($this->attributes["cover"]);
    }

    public function getAgeAttribute()
    {
        return (new DateTime($this->attributes["birth_date"]))->diff(new DateTime())->y;
    }

    public function getCountFollowingAttribute()
    {
        return $this->following()->count();
    }

    public function getCountFollowersAttribute()
    {
        return $this->followers()->count();
    }

    public function getCountPostsAttribute()
    {
        return $this->posts()->count();
    }


    // checa se eu (logado) estou seguindo o @\App\Models\User
    public function getIFollowAttribute()
    {
        return (bool) $this->followersPivot()->iFollow()->count();
    }

}
