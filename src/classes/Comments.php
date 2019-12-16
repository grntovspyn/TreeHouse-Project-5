<?php

namespace BlogPost;

use Illuminate\Database\Eloquent\Model as Model;


class Comment extends Model {

    public $timestamps = false;


    protected $fillable = ['post_id','name','body'];
   


    public function posts() {
        return $this->belongsTo('BlogPost\Post');
    }



}