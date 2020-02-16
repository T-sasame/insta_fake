{{-- layouts/front.blade.phpを読み込む --}}
@extends('layouts.front')


{{-- main.blade.phpの@yield('title')に埋め込む --}}
@section('title', 'マイページ')

{{-- main.blade.phpの@yield('content')に以下のタグを埋め込む --}}
@section('content')
    <div class="container">
        <h2>プロフィール</h2>
        <div class="row">
            <div class="profile_icon col-md-3">
                <img src="{{ $user->icon }}">
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-2">
                        <label>ユーザーネーム:</label>
                    </div>
                    <div class="col-md-10">
                        <label>{{ $user->name }}</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <label>メールアドレス:</label>
                    </div>
                    <div class="col-md-10">
                        <label>{{ $user->email }}</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <label>コメント:</label>
                    </div>
                    <div class="col-md-10">
                        <label>{{ $user->profile }}</label>
                    </div>
                </div>
                <!-- フォロー関連をVueのコンポーネントで表示 -->
                <!-- 対象ユーザー(:others)は$userを渡し、閲覧ユーザー(:user)や、フォロー中か否か(:following)はnullを渡す -->
                <follows :others = "{{ $user }}"
                         :user = " '' "
                         :following = " '' "
                         :followcount = "{{ $followcount }}"
                         :followercount = "{{ $followercount }}">
                </follows>
            </div>
        </div>
        <div class="col-md-12" style="text-align: center; margin-bottom: 15px">
            <a href="{{ action('User\InstaController@post') }}" role="button" class="btn btn-primary">新規投稿の作成</a>
            <a href="{{ action('User\InstaController@profile_edit') }}" role="button" class="btn btn-primary">プロフィールの編集</a>
        </div>
        <hr color="#c0c0c0">
        <!-- 自分の投稿画像をVueのコンポーネントで表示 -->
        <!-- コンポーネントに" :変数名 = "渡すデータ" "で画像毎のデータを渡す -->
        <postsmypage :user = "{{ $user }}"
                     :posts = "{{ $posts }}">
        </postsmypage>
    </div>
@endsection
