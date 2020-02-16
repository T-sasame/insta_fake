@extends('layouts.front')

@section('title', '認証画面')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card-body" style="text-align:center">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <p>ログインが完了しました！</p>
                <a href="{{ action('MainController@index') }}" role="button" class="btn btn-primary">TOPページへ移動</a>
                <a href="{{ action('User\InstaController@profile') }}" role="button" class="btn btn-primary">マイページへ移動</a>
            </div>
        </div>
    </div>
</div>
@endsection
