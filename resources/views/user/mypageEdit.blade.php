{{-- layouts/front.blade.phpを読み込む --}}
@extends('layouts.front')


{{-- main.blade.phpの@yield('title')に埋め込む --}}
@section('title', 'マイページ編集')

{{-- main.blade.phpの@yield('content')に以下のタグを埋め込む --}}
@section('content')
    <div class="container">
        <h2>プロフィール編集</h2>
        <form action="{{ action('User\InstaController@profile_update') }}" method="post" enctype="multipart/form-data">
            @if (count($errors) > 0)
                <ul>
                    @foreach($errors->all() as $e)
                        <li style="color:red; list-style-type: none;">{{ $e }}</li>
                    @endforeach
                </ul>
            @endif
            <div class="row">
                <div class="profile_icon col-md-3">
                    <img src="{{ $user->icon }}">

                    <div class="form-group row">
                        <div>
                            <label>アイコンの変更:</label>
                            <input type="file" class="form-control-file" name="icon">
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <label>ユーザーネーム:</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="name" value="{{ $user->name }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2">
                            <label>メールアドレス:</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="email" value="{{ $user->email }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2">
                            <label>コメント:</label>
                        </div>
                        <div class="col-md-10">
                            <textarea class="form-control" name="profile" rows="5" placeholder="プロフィールコメントを入力">{{ $user->profile }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12" style="text-align: center; margin-bottom: 15px">
                <input type="hidden" name="id" value="{{ $user->id }}">
                {{ csrf_field() }}
                <input type="submit" class="btn btn-primary" value="プロフィールの変更を送信">
            </div>
        </form>
    </div>
@endsection
