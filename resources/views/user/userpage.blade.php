{{-- layouts/front.blade.phpを読み込む --}}
@extends('layouts.front')


{{-- main.blade.phpの@yield('title')に埋め込む --}}
@section('title', 'ユーザーページ')

{{-- main.blade.phpの@yield('content')に以下のタグを埋め込む --}}
@section('content')
    <div class="container">
        <div class="row">
            <h2>プロフィール</h2>
        </div>
        <div class="row">
            <div class="profile_icon col-md-3">
                <img src="{{ $others->icon }}">
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-2">
                        <label>ユーザーネーム:</label>
                    </div>
                    <div class="col-md-10">
                        <p>{{ $others->name }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <label>コメント:</label>
                    </div>
                    <div class="col-md-10">
                        <p>{{ $others->profile }}</p>
                    </div>
                </div>

                <!-- guest elseでログイン中か否かで表示を切り替え -->
                @guest
                    <!-- フォロー関連をVueのコンポーネントで表示 -->
                    <!-- コンポーネントに" :変数名 = "渡すデータ" "で画像毎のデータを渡す -->
                    <!-- ログインしていない場合は:userにnullを格納 -->
                    <follows :others = "{{ $others }}"
                             :user = " '' "
                             :following = "{{ $following }}"
                             :followcount = "{{ $followcount }}"
                             :followercount = "{{ $followercount }}">
                    </follows>
                @else
                    <!-- ログインしている場合は、取得した$userを:userに格納 -->
                    <follows :others = "{{ $others }}"
                             :user = "{{ $user }}"
                             :following = "{{ $following }}"
                             :followcount = "{{ $followcount }}"
                             :followercount = "{{ $followercount }}">
                    </follows>
                @endguest
            </div>
        </div>
        <hr color="#c0c0c0">

        <!-- guest elseでログイン中か否かで表示を切り替え -->
        <!-- :userについては、上記のfollowsコンポーネントと同じ処理をしている -->
        @guest
            <posts :posts = "{{ $posts }}"
                   :user = " '' "
                   :others = "{{ $others->id }}">
            </posts>
        @else
            <posts :posts = "{{ $posts }}"
                   :user = "{{ $user }}"
                   :others = "{{ $others->id }}">
            </posts>
        @endguest
    </div>
@endsection
