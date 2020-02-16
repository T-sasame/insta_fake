{{-- layouts/front.blade.phpを読み込む --}}
@extends('layouts.front')


{{-- main.blade.phpの@yield('title')に埋め込む --}}
@section('title', '投稿画面')

{{-- main.blade.phpの@yield('content')に以下のタグを埋め込む --}}
@section('content')
    <div class="container">
        <div class="row">
            <h2>新規投稿</h2>
        </div>

        <form action="{{ action('User\InstaController@post_create') }}" method="post" enctype="multipart/form-data">
            @if (count($errors) > 0)
                <ul>
                    @foreach($errors->all() as $e)
                        <li style="color:red; list-style-type: none;">{{ $e }}</li>
                    @endforeach
                </ul>
            @endif
            <div class="form-group row">
                <label class="col-md-2" for="title">画像の投稿</label>
                <div class="col-md-10">
                    <input type="file" class="form-control-file" name="image">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-2" for="title">コメント</label>
                <div class="col-md-10">
                    <textarea class="form-control" name="comment" rows="5" placeholder="255文字以内でコメントを入力">{{ old('comment') }}</textarea>
                </div>
            </div>
            {{ csrf_field() }}
            <input type="submit" class="btn btn-primary" value="画像を投稿">
        </form>

    </div>
@endsection
