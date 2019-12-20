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

//Pull at posts and tags
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

$app->map(['GET', 'POST'],'/detail/[{id}]', function ($request, $response, $args) {
 // $this->logger->addInfo("Detail Edit");
      //pull post
      $post = BlogPost\Post::find($args['id']);
      //$tags = BlogPost\Post::with('tags')->has('tags')->where('id', $args['id'])->get();
    //   $tags = BlogPost\Post::where('id', $args['id'])->with('tags')->get();

      $specTags = $post->tags;


      $comments = $post->comments;


      if($request->getMethod() == "POST") {
        $args = array_merge($args, $request->getParsedBody());
        var_dump($args);
        //var_dump($args);
        $create = BlogPost\Comment::create(['post_id' => $args['id'], 'name' => $args['name'],'date' => date('d-m-Y') , 'body' => $args['comment']]);
      }



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
        'args' => $args,
        'post' => $post,
        'tags' => $specTags,
        'comments' => $comments
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

  if($request->getMethod() == "POST") {
    return $this->view->render($response, 'new.html');
  }
 
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

