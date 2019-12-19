<?php

namespace BlogPost;

use Illuminate\Database\Eloquent\Model as Model;


class Post extends Model {

    public $timestamps = false;

    protected $fillable = ['title','date','body',];

    //Stop showing pivot table in data https://github.com/laravel/framework/issues/745
    protected $hidden = array('pivot');
   

    public function tags() {
        return $this->belongsToMany('BlogPost\Tag');
    }

    public function comments() {
        return $this->hasMany('BlogPost\Comment');
    }

    

}