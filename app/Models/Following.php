<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Following extends Model
{
    use HasFactory;

    public $table = "following";

    public function scopeFollowing($query)
    {
        return $query->where("user_id_from", Auth::user()->id);
    }



    // checa se eu (logado) estou seguindo o @\App\Models\User
    public function scopeIFollow($query)
    {
        return $query->where("user_id_from", Auth::user()->id);
    }
}
