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

$app->get('/', function ($request, $response, $args){


  

   //$create = BlogPost\Post::create(['title' => 'Test Entry','date' => '2010-01-03', 'body' => 'Something to test out the new eloquent orm']);


  // $all = BlogPost\Post::has('tags')->get();

   //$single =  $all->tags()->where('id',32)->get();


    $post = BlogPost\Post::with('tags')->get();





     // CSRF token name and value
 $csrf = $this->get('csrf');
 $nameKey = $csrf->getTokenNameKey();
 $valueKey = $csrf->getTokenValueKey();
 $csrf = [
     $nameKey => $request->getAttribute($nameKey),
     $valueKey => $request->getAttribute($valueKey)
   ];

 return $this->view->render($response, "index.twig", [
     'csrf' => $csrf,
     'args' => $args,
     'post' => $post,
   
 ]);


});



$app->get('/detail/{id}', function ($request, $response, $args) {
    // CSRF token name and value
    $csrf = $this->get('csrf');
    $nameKey = $csrf->getTokenNameKey();
    $valueKey = $csrf->getTokenValueKey();
    $csrf = [
        $nameKey => $request->getAttribute($nameKey),
        $valueKey => $request->getAttribute($valueKey)
      ];

  
    return $this->view->render($response, "detail.twig", [
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

// $app->get('/', function ($request, $response, $args) {

//     return $this->view->render($response, 'index.twig', $args);

// });

$app->get('/detail/{post}/{id}', function ($request, $response, $args) {

    return $this->view->render($response, 'detail.twig', $args);

});

