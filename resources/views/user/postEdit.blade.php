{{-- layouts/front.blade.phpを読み込む --}}
@extends('layouts.front')


{{-- main.blade.phpの@yield('title')に埋め込む --}}
@section('title', '投稿画像の編集')

{{-- main.blade.phpの@yield('content')に以下のタグを埋め込む --}}
@section('content')
    <div class="container">
        <div class="row">
            <h2>投稿画像の編集</h2>
        </div>

        <form action="{{ action('User\InstaController@post_update') }}" method="post" enctype="multipart/form-data">
            @if (count($errors) > 0)
                <ul>
                    @foreach($errors->all() as $e)
                        <li style="color:red; list-style-type: none;">{{ $e }}</li>
                    @endforeach
                </ul>
            @endif
            <div class="row">
                <label class="col-md-2" for="title">投稿中の画像</label>
                <div class="col-md-10">
                    <div class="modalImage">
                        <img src="{{ $post->image }}">
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-2" for="title">コメント</label>
                <div class="col-md-10">
                    <textarea class="form-control" name="comment" rows="5" placeholder="255文字以内でコメントを入力">{{ $post->comment }}</textarea>
                </div>
            </div>
            <div>
                <input type="hidden" name="id" value="{{ $post->id }}">
                {{ csrf_field() }}
                <input type="submit" class="btn btn-primary" value="変更内容を送信">
            </div>
        </form>

    </div>
@endsection
