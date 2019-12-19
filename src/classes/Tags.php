<?php

namespace BlogPost;

use Illuminate\Database\Eloquent\Model as Model;


class Tag extends Model {

    public $timestamps = false;
    

    protected $fillable = ['tags'];
    //Stop showing pivot table in data https://github.com/laravel/framework/issues/745
    protected $hidden = array('pivot');
   

    public function posts() {
        return $this->belongsToMany('BlogPost\Post');
    }



}