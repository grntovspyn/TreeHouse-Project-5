<?php

namespace BlogPost;

use Illuminate\Database\Eloquent\Model as Model;


class Post extends Model {

    public $timestamps = false;

    protected $fillable = ['title','date','body',];
   

    public function tags() {
        return $this->belongsToMany('BlogPost\Tag');
    }

    public function comments() {
        return $this->hasMany('BlogPost\Comment');
    }

    

}