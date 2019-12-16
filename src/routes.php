<?php

use Slim\Http\Request;
use Slim\Http\Response;
use BlogPost\Post;
use BlogPost\Comment;
use BlogPost\Tag;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Illuminate\Database\Query\Builder;
use App\WidgetController;

$app->map(['GET', 'POST'], '/new', function ($request, $response, $args){
 // CSRF token name and value
 $csrf = $this->get('csrf');
 $nameKey = $csrf->getTokenNameKey();
 $valueKey = $csrf->getTokenValueKey();
 $csrf = [
     $nameKey => $request->getAttribute($nameKey),
     $valueKey => $request->getAttribute($valueKey)
   ];

  

   //$create = BlogPost\Post::create(['title' => 'Test Entry','date' => '2010-01-03', 'body' => 'Something to test out the new eloquent orm']);


  // $all = BlogPost\Post::has('tags')->get();

   //$single =  $all->tags()->where('id',32)->get();

//    foreach ($all as $key => $value ) {
//        echo $key;
//        echo $value;
//    }

//   $post = new Post();
//    $tag = BlogPost\Post::find(6);

//    foreach ($tag->tags as $key) {
//        echo $key;
//    }

    $post = BlogPost\Post::with('tags')->get();


    
     //$tags = BlogPost\Tag::has('posts')->get();

    // echo "<pre>";
    // var_dump($tags);
    // echo "</pre>";

 return $this->view->render($response, "index.twig", [
     'csrf' => $csrf,
     'args' => $args,
     'post' => $post,
     //'tag'  => $tags
 ]);


});


$app->get('/foo', function ($request, $response, $args) {
    // CSRF token name and value
    $csrf = $this->get('csrf');
    $nameKey = $csrf->getTokenNameKey();
    $valueKey = $csrf->getTokenValueKey();
    $csrf = [
        $nameKey => $request->getAttribute($nameKey),
        $valueKey => $request->getAttribute($valueKey)
      ];

     

     $post = new Post();
      $val = $post->getAllPosts();
    
     foreach ($val as $key => $value){
         echo $key;
         echo $value;
     }
    return $this->view->render($response, "index.twig", [
        'csrf' => $csrf,
        'args' => $args
    ]);
    /*
       Render HTML form which POSTs to /bar with two hidden input fields for the
       name and value:
       <input type="hidden" name="<?= $nameKey ?>" value="<?= $name ?>">
       <input type="hidden" name="<?= $valueKey ?>" value="<?= $value ?>">
     */
});

$app->post('/bar', function ($request, $response, $args) {
    // CSRF protection successful if you reached
    // this far.
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->get('/', function ($request, $response, $args) {

    return $this->view->render($response, 'index.twig', $args);

});

$app->get('/detail/{post}/{id}', function ($request, $response, $args) {

    return $this->view->render($response, 'detail.twig', $args);

});

