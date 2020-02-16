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
        $posts = Post::join('users','users.id','=','posts.userId')->
                       select('posts.*','users.name')->orderBy('created_at', 'desc')->get();

        // ログインしているユーザー本人の情報を取得
        $user = Auth::user();

        return view('index', ['posts' => $posts, 'user' => $user]);
    }

    // 新規ユーザー登録を行う
    public function new_user()
    {
        return view('auth.register');
    }
}
