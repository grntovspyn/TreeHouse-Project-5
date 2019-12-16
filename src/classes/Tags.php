<?php

namespace BlogPost;

use Illuminate\Database\Eloquent\Model as Model;


class Tag extends Model {

    public $timestamps = false;


    protected $fillable = ['post_id', 'tag_id'];
   

    public function posts() {
        return $this->belongsToMany('BlogPosts\Post');
    }



}