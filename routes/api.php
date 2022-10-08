<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FeedController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get("/ping", function(){
    return ["pong" => true];
});
Route::match(["GET", "DELETE", "PUT", "PATCH", "POST"], "unauthorized", function(){
    return response()->json([
        "error" => "Não autorizado"
    ], 401);
})->name("login");


Route::controller(AuthController::class)->prefix("auth")->as("auth.")->group(function(){
    Route::post("/user", "create")->name("create");

    Route::post("/", "login")->name("login");
    Route::get("/check", "check")->name("check");

    Route::delete("/", "logout")->name("logout")->middleware("auth:api");
    Route::patch("/", "refresh")->name("refresh")->middleware("auth:api");
});

// rotas privadas 
Route::middleware("auth:api")->group(function(){
    Route::controller(UserController::class)->prefix("user")->as("user.")->group(function(){
        Route::get("/", "index")->name("index");
        Route::post ("/", "update")->name("update");
        Route::get("/{user}", "show")->name("show");
        Route::post("/{user}/follow", "follow")->name("follow");
        Route::get("/{user}/following", "following")->name("following");
        Route::get("/{user}/followers", "followers")->name("followers");
    });

    Route::controller(FeedController::class)->as("feed.")->group(function(){
        Route::get("/feed", "index")->name("index");
        Route::get("/me/feed", "me")->name("me");
        Route::get("/user/{user}/feed", "show")->name("show");
    });

    Route::controller(PostController::class)->as("post.")->group(function(){
        Route::post("/post", "create")->name("create");
        Route::post("/post/{post}/update", "update")->name("update");
        Route::get("/post/{post}/likes", "likes")->name("likes");
        Route::post("/post/{post}/like", "like")->name("like");
        Route::post("/post/{post}/comment", "comment")->name("comment");
        Route::get("/post/{post}/comments", "comments")->name("comments");
    });

    Route::controller(CommentController::class)->as("comment.")->prefix("comment")->group(function(){
        Route::delete("/{comment}", "destroy")->name("destroy");
        Route::put("/{comment}", "update")->name("update");
    });

    Route::controller(SearchController::class)->prefix("search")->as("search.")->group(function(){
        Route::get("/user", "user")->name("user");
    });
});



