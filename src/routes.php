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

/******** INDEX PAGE ROUTES **************/

$app->get('/', function ($request, $response, $args){


    $post = BlogPost\Post::with('tags')->orderBy('date', 'desc')->get();

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


})->setName('blogPost');

/************** DETAIL PAGE ROUTES ****************/

$app->get('/detail/[{id}]', function ($request, $response, $args) {
    // CSRF token name and value
    $csrf = $this->get('csrf');
    $nameKey = $csrf->getTokenNameKey();
    $valueKey = $csrf->getTokenValueKey();
    $csrf = [
        $nameKey => $request->getAttribute($nameKey),
        $valueKey => $request->getAttribute($valueKey)
      ];

      $post = BlogPost\Post::find($args['id']);
      //$tags = BlogPost\Post::with('tags')->has('tags')->where('id', $args['id'])->get();
    //   $tags = BlogPost\Post::where('id', $args['id'])->with('tags')->get();

      $specTags = $post->tags;
    return $this->view->render($response, "detail.twig", [
        'csrf' => $csrf,
        'args' => $args,
        'post' => $post,
        'tags' => $specTags
    ]);

    /*
       Render HTML form which POSTs to /bar with two hidden input fields for the
       name and value:
       <input type="hidden" name="<?= $nameKey ?>" value="<?= $name ?>">
       <input type="hidden" name="<?= $valueKey ?>" value="<?= $value ?>">
     */
});


$app->map(['GET', 'POST'],'/edit/[{id}]', function ($request, $response, $args) {
  // CSRF token name and value
  $csrf = $this->get('csrf');
  $nameKey = $csrf->getTokenNameKey();
  $valueKey = $csrf->getTokenValueKey();
  $csrf = [
      $nameKey => $request->getAttribute($nameKey),
      $valueKey => $request->getAttribute($valueKey),
  ];

  $post = BlogPost\Post::find($args['id']);
  //$tags = BlogPost\Post::with('tags')->has('tags')->where('id', $args['id'])->get();
  //   $tags = BlogPost\Post::where('id', $args['id'])->with('tags')->get();

  $specTags = $post->tags;
  var_dump($csrf);
  return $this->view->render($response, 'edit.twig', [
      'csrf' => $csrf,
      'args' => $args,
      'post' => $post,
      'tags' => $specTags,
  ]);
})->setName('edit');
  /*
     Render HTML form which POSTs to /bar with two hidden input fields for the
     name and value:
     <input type="hidden" name="<?= $nameKey ?>" value="<?= $name ?>">
     <input type="hidden" name="<?= $valueKey ?>" value="<?= $value ?>">
   */



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

