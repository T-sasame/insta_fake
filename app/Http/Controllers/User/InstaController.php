<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
//ログイン情報を参照する為、Authの定義
use Illuminate\Support\Facades\Auth;
//AWS S3を使用する為の定義
use Storage;
//モデルの定義
use App\Post;
use App\User;
use App\Comment;
use App\Follow;

class InstaController extends Controller
{
    //マイプロフィールページの表示
    public function profile(Request $request)
    {
        //Authを使い、ユーザー本人のデータを抜き出す
        $user = Auth::user();
        //ユーザーのidで検索をかけ、一致した画像のみ表示
        //その際、usersテーブルのidと、$postsのuserIdが一致するテーブル同士を結合して、selectで必要なデータのみ取得
        //paginateで件数を指定、結果をjson形式で返す
        $posts = Post::where('userId', $user->id)->join('users','users.id','=','posts.userId')->
                       select('posts.*','users.name')->orderBy('created_at', 'desc')->paginate(6)->toJson();

        //フォローとフォロワーの人数を取得
        $followcount = Follow::where('followerId', $user->id)->count();
        $followercount = Follow::where('followId', $user->id)->count();

        return view('user/mypage', ['user' => $user, 'posts' => $posts,
                                    'followcount' => $followcount, 'followercount' => $followercount]);
    }

    //マイプロフィール編集ページの表示
    public function profile_edit(Request $request)
    {
        //Authを使い、ユーザー本人のデータを抜き出す
        $user = Auth::user();

        return view('user/mypageEdit', ['user' => $user]);
    }

    //マイプロフィールの更新処理
    public function profile_update(Request $request)
    {
        $user = Auth::user();
        $profile = $request->all();

        //ユーザーネームの変更があった場合、$user->nameに格納
        if ($profile['name'] != $user->name) {
            // Varidationを行う
            $this->validate($request, [
                'name' => ['required', 'string', 'max:255', 'unique:users'],
            ]);
            $user->name = $profile['name'];
        }

        //メールアドレスの変更があった場合、$user->emailに格納
        if ($profile['email'] != $user->email) {
            // Varidationを行う
            $this->validate($request, [
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            ]);
            $user->email = $profile['email'];
        }

        //アイコン画像の指定があった場合はファイルをAWS s3に保存し、画像のパスを$user->iconに格納
        if ($request->file('icon')) {
            // Varidationを行う
            $this->validate($request, [
                'icon' => 'image',
            ]);
            $path = Storage::disk('s3')->putFile('/',$profile['icon'],'public');
            $user->icon = Storage::disk('s3')->url($path);
        }

        //プロフィールコメントの変更があった場合、$user->profileに格納
        if ($profile['profile'] != $user->profile) {
            $user->profile = $profile['profile'];
        }

        //フォームから送信されてきた_token、アイコン画像を削除する
        unset($profile['_token']);
        unset($profile['icon']);
        //データベースに保存する
        $user->fill($profile)->save();

        return redirect('user/mypage');
    }

    //新規投稿作成ページの表示
    public function post()
    {
        return view('user.post');
    }

    //新規投稿作成の処理
    public function post_create(Request $request)
    {
        // Varidationを行う
        $this->validate($request, Post::$rules);

        $post = new Post;
        $form = $request->all();

        //画像を保存して、$post->imageに画像のパスを保存する
        $path = Storage::disk('s3')->putFile('/',$form['image'],'public');
        $post->image = Storage::disk('s3')->url($path);

        //投稿したユーザーのidをpostsテーブルのuserIdに格納する
        $user = Auth::user();
        $post->userId = $user->id;

        // フォームから送信されてきた_token、投稿画像を削除する
        unset($form['_token']);
        unset($form['image']);

        // データベースに保存する
        $post->fill($form)->save();

        return redirect('user/mypage');
    }

    //投稿画像の編集処理
    public function post_update(Request $request)
    {
        // Varidationを行う
        $this->validate($request, [
            'comment' => ['string', 'max:255'],
        ]);

        //モデルからデータを取得する
        $post = Post::find($request->id);

        $post->comment = $request->comment;

        //上書きして保存する
        $post->save();

        return redirect('user/mypage');
    }

    //投稿画像編集ページの表示
    public function post_edit($id)
    {
        //URLパラメータの$idで検索をかける
        $post = Post::find($id);

        return view('user/postEdit', ['post' => $post]);
    }

    //投稿画像の削除処理
    public function post_delete(Request $request)
    {
        //該当するデータを取得
        $post = Post::find($request->input('id'));

        //削除する画像に寄せられていたコメントを削除
        Comment::where('postId', $post->id)->delete();

        //削除する
        Storage::disk('s3')->delete($post->image);
        $post->delete();
    }

    //ユーザーページの表示
    public function open_userpage($name)
    {
        //Authを使い、ユーザー本人のデータを抜き出す
        $user = Auth::user();
        //guestユーザー($userがnull)だった場合は何もせず、
        //遷移先が自分自身のユーザーページだった場合はマイページに移動
        if($user != '') {
            if($user->name == $name) {
                return redirect('user/mypage');
            }
        }

        //URLパラメータの$nameで検索をかけ、一致した画像のみ表示
        //その際、usersテーブルのidと、postsテーブルのuserIdが一致するテーブル同士を結合して、selectで必要なデータのみ取得
        //paginateで件数を指定、結果をjson形式で返す
        $posts = User::where('name', $name)->join('posts','posts.userId','=','users.id')->
                       select('posts.*','users.name')->orderBy('created_at', 'desc')->paginate(6)->toJson();

        //ユーザーページ毎のユーザーデータを$othersに取得
        $others = User::where('name', $name)->first();

        //guestユーザー($userがnull)だった場合は何もせず、
        //既にユーザーページのユーザーをフォローしていた場合、$followingにデータが格納
        if($user != '') {
            $following = Follow::where('followId', $others->id)->where('followerId', $user->id)->first();
        }
        //vueに渡す値がnullだとエラーになるので
        //フォローしていない場合は、$followingに数字の1を格納
        if(empty($following)) {
            $following = 1;
        }

        //フォローとフォロワーの数を取得
        $followcount = Follow::where('followerId', $others->id)->count();
        $followercount = Follow::where('followId', $others->id)->count();

        return view('user/userpage', ['user' => $user, 'posts' => $posts, 'others' => $others, 'following' => $following,
                                      'followcount' => $followcount, 'followercount' => $followercount]);
    }

    //フォローを保存
    public function follow(Request $request)
    {
        // followsテーブルのfollowId,followerIdの2つのカラムを対象にuniqueでバリデーション
        $request->validate([
            'followerId' => Rule::unique('follows')->where('followId', $request->input('followId')),
        ]);

        $follow = new Follow;

        //フォローした人、された人のデータを格納する
        $follow->followId = $request->input('followId');
        $follow->followerId = $request->input('followerId');

        $follow->save();
    }

    //フォローの解除
    public function follow_delete(Request $request)
    {
        //該当するデータを取得
        $follow = Follow::where('followId', $request->input('followId'))->where('followerId', $request->input('followerId'))->first();

        //削除する
        $follow->delete();
    }

    //ユーザーのフォロー一覧を取得
    public function get_follow(Request $request)
    {
        //検索をかけた際に、ヒットしたユーザーのnameとiconを結合して一緒に取得
        $follows = Follow::where('followerId', $request->id)->join('users','users.id','=','follows.followId')->
                           select('follows.*','users.name', 'users.icon')->get();

        return $follows;
    }

    //ユーザーのフォロワー一覧を取得
    public function get_follower(Request $request)
    {
        //検索をかけた際に、ヒットしたユーザーのnameとiconを結合して一緒に取得
        $followers = Follow::where('followId', $request->id)->join('users','users.id','=','follows.followerId')->
                             select('follows.*','users.name', 'users.icon')->get();

        return $followers;
    }

    //投稿画像一覧を取得
    public function get_post(Request $request)
    {
        //ユーザーのidで検索をかけ、一致した画像のみ表示
        //その際、usersテーブルのidと、$postsのuserIdが一致するテーブル同士を結合して、selectで必要なデータのみ取得
        //paginateで件数を指定、結果をjson形式で返す
        $posts = Post::where('userId', $request->id)->join('users','users.id','=','posts.userId')->
                       select('posts.*','users.name')->orderBy('created_at', 'desc')->paginate(6)->toJson();

        return $posts;
    }
}
