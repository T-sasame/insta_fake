<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = array('id');

    public static $rules = array(
        'image' => ['required', 'image'],
        'comment' => ['max:255'],
    );
}
