{{-- layouts/front.blade.phpを読み込む --}}
@extends('layouts.front')


{{-- front.blade.phpの@yield('title')に埋め込む --}}
@section('title', 'ゲーム特化インスタ')

{{-- front.blade.phpの@yield('content')に以下のタグを埋め込む --}}
@section('content')
  <div class="container">
      <h2>新規投稿一覧</h2><hr color="#c0c0c0">
      <!-- guest elseでログイン中か否かで表示を切り替え -->
      @guest
          <!-- 投稿画像をVueのコンポーネントで表示 -->
          <!-- コンポーネントに" :変数名 = "渡すデータ" "で画像毎のデータを渡す -->
          <!-- ログインしていない場合は:userにnullを格納 -->
          <poststop :posts = "{{ $posts }}"
                    :user = " '' ">
          </poststop>
      @else
          <!-- ログインしている場合は、取得した$userを:userに格納 -->
          <poststop :posts = "{{ $posts }}"
                    :user = "{{ $user }}">
          </poststop>
      @endguest
  </div>
@endsection
