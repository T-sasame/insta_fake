<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//ログイン情報を参照する為、Authの定義
use Illuminate\Support\Facades\Auth;
//モデルの定義
use App\Post;

class MainController extends Controller
{
    // TOPページを表示する
    public function index()
    {
        // 投稿画像のデータに投稿者の名前を結合して取得
        // paginateで件数を指定、結果をjson形式で返す
        $posts = Post::join('users','users.id','=','posts.userId')->
                       select('posts.*','users.name')->orderBy('created_at', 'desc')->paginate(6)->toJson();

        // ログインしているユーザー本人の情報を取得
        $user = Auth::user();

        return view('index', ['posts' => $posts, 'user' => $user]);
    }

    // 新規ユーザー登録を行う
    public function new_user()
    {
        return view('auth.register');
    }

    //投稿画像一覧を取得
    public function get_post_top()
    {
        // usersテーブルのidと、postsテーブルのuserIdが一致するテーブル同士を結合して、selectで必要なデータのみ取得
        // paginateで件数を指定、結果をjson形式で返す
        $posts = Post::join('users','users.id','=','posts.userId')->
                       select('posts.*','users.name')->orderBy('created_at', 'desc')->paginate(6)->toJson();

        return $posts;
    }
}
