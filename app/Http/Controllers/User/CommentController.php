<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
//ログイン情報を参照する為、Authの定義
use Illuminate\Support\Facades\Auth;
//モデルの定義
use App\User;
use App\Comment;
use App\Good;

class CommentController extends Controller
{
    //画像へのコメント投稿
    public function post_comment(Request $request)
    {
        // Varidationを行う
        $this->validate($request, Comment::$rules);

        $comment = new Comment;

        //投稿したユーザーのidをcommentsテーブルのuserIdに格納する
        $user = Auth::user();
        $comment->userId = $user->id;
        //投稿されたデータを、commentsテーブルに格納する
        $comment->postId = $request->input('postId'); //投稿画像のid
        $comment->comment = $request->input('comment'); //投稿したコメント

        $comment->save();
    }

    //画像へのコメント一覧を取得
    public function get_comment(Request $request)
    {
        // コメントを投稿した画像のidで検索をかけて、一致したコメントのみ表示
        // その際、usersテーブルのidと、$commentsのuserIdが一致するテーブル同士を結合して、selectで必要なデータのみ取得
        // paginateで件数を指定する
        $comments = Comment::where('postId', $request->id)->join('users','users.id','=','comments.userId')->
                    select('comments.*','users.name','users.icon')->orderBy('created_at', 'desc')->paginate(10);

        return $comments;
    }

    //画像へのいいねを保存
    public function post_good(Request $request)
    {
        // goodsテーブルのuserId,postIdの2つのカラムを対象にuniqueでバリデーション
        $request->validate([
            'postId' => Rule::unique('goods')->where('userId', $request->input('userId')),
        ]);

        $good = new Good;

        //投稿したユーザーのデータをgoodsテーブルに格納する
        $good->userId = $request->input('userId');
        //いいねされた画像のidを、goodsテーブルに格納する
        $good->postId = $request->input('postId');

        $good->save();
    }

    //画像へのいいねを取得
    public function get_good(Request $request)
    {
        //投稿画像のidで検索をかけて、いいね！をされた回数を取得
        $count = Good::where('postId', $request->id)->count();

        return $count;
    }
}
