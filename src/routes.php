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
 

      $specTags = $post->tags;


      $comments = $post->comments;


      if($request->getMethod() == "POST") {
        $args = array_merge($args, $request->getParsedBody());
        
        //var_dump($post);
        
        $create = BlogPost\Comment::create(['post_id' => $args['id'], 'name' => $args['name'],'date' => date('d-m-Y') , 'body' => $args['comment']]);
        $basePath = $request->getUri()->getBasePath();
       return $response->withStatus(302)->withHeader('Location', $basePath . '/detail/' . $args['id']);
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
  $specTags = $post->tags;

  $basePath = $request->getUri()->getBasePath();
//Edit post
  if($request->getMethod() == "POST") {

    if(isset($_POST['delete'])) {
      $post->tags()->detach($args['id']);
      $post->delete($args['id']);
      return $response->withStatus(302)->withHeader('Location', $basePath . '/');
    }


    $args = array_merge($args, $request->getParsedBody());
    $args = $post->sanitize($args);
    $slug = $post->slugify($args['title']);
    $post->where('id', $args['id'])->update(['title' => $args['title'],'date' => $args['date'] , 'body' => $args['body'], 'slug' => $args['body']]);

    if ('' != $args['tags']) {
        $newTags = explode(',', (str_replace(' ', '', $args['tags'])));
    } else {
      $newTags = NULL;
    }
    $arr = array();
    
   
    if($newTags !== NULL){
    foreach ($newTags as $value) {
        //$filteredValue = $post->sanitize($value);
        $tag = new BlogPost\Tag(['tags' => $value]);
        $id = $post->tags()->save($tag)->id;
        
        $arr[] = $id;
    }
    $post->tags()->sync($arr);
  } else {
    $post->tags()->detach();
  }
    

    return $response->withStatus(302)->withHeader('Location', $basePath . '/');

  }

 
  return $this->view->render($response, 'edit.twig', [
      'csrf' => $csrf,
      'args' => $args,
      'post' => $post,
      'tags' => $specTags,
  ]);
})->setName('edit');

$app->map(['GET', 'POST'],'/new', function ($request, $response, $args) {
  // CSRF token name and value
  $csrf = $this->get('csrf');
  $nameKey = $csrf->getTokenNameKey();
  $valueKey = $csrf->getTokenValueKey();
  $csrf = [
      $nameKey => $request->getAttribute($nameKey),
      $valueKey => $request->getAttribute($valueKey),
  ];
  

$post = new BlogPost\Post;

  $basePath = $request->getUri()->getBasePath();
//Create post
  if($request->getMethod() == "POST") {

    $args = array_merge($args, $request->getParsedBody());
    $args = $post->sanitize($args);
    $slug = $post->slugify($args['title']);
    $postId = $post->create(['title' => $args['title'],'date' => $args['date'] , 'body' => $args['body'], 'slug' => $slug])->id;

    if ('' != $args['tags']) {
        $newTags = explode(',', (str_replace(' ', '', $args['tags'])));
    } else {
      $newTags = NULL;
    }
    $arr = array();
    
   
    if($newTags !== NULL){
    foreach ($newTags as $value) {
        $filteredValue = $post->sanitize($value);
        $post = BlogPost\Post::find($postId);
        $tag = new BlogPost\Tag(['tags' => $value]);
       
        $id = $post->tags()->save($tag)->id;
        
        $arr[] = $id;
    }
    $post->tags()->sync($arr);
  }
    
    return $response->withStatus(302)->withHeader('Location', $basePath . '/');

  }

 
  return $this->view->render($response, 'new.twig', [
      'csrf' => $csrf,
      'args' => $args,
      'post' => $post,
  ]);
})->setName('new');
