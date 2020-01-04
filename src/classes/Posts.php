<?php

namespace BlogPost;

use Illuminate\Database\Eloquent\Model as Model;


class Post extends Model {

    public $timestamps = false;

    protected $fillable = ['title','date','body', 'slug'];

    //Stop showing pivot table in data https://github.com/laravel/framework/issues/745
    protected $hidden = array('pivot');
   

    public function tags() {
        return $this->belongsToMany('BlogPost\Tag');
    }

    public function comments() {
        return $this->hasMany('BlogPost\Comment');
    }

    public function sanitize($args = array()){

        foreach ($this->fillable as $key) {
            $args[$key] = trim(filter_var($args[$key], FILTER_SANITIZE_STRING));

        }

        return $args;

    }


    // Creating slugs https://ourcodeworld.com/articles/read/253/creating-url-slugs-properly-in-php-including-transliteration-support-for-utf-8
/**
 * Return the slug of a string to be used in a URL.
 *
 * @return String
 */
function slugify($text){
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, '-');

    // remove duplicated - symbols
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
      return 'n-a';
    }

    return $text;
}


    

}