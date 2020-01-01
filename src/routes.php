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

//Pull all posts and tags and order by date

try {
    $post = BlogPost\Post::with('tags')->orderBy('date', 'desc')->get();
} catch(Exception $e){
  echo "Unable to retrieve posts" . $e->getMessage();
}

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

$app->map(['GET', 'POST'],'/detail/{slug}', function ($request, $response, $args) {
    $post = $specTags = $comments = "";
    $basePath = $request->getUri()->getBasePath();
    $this->logger->addInfo("Detail Page");
      

    // Retrieve Post by Slug

    try {
        $post = BlogPost\Post::where('slug', '=', $args['slug'])->first();
    }catch(Exception $e){
      echo "Unable to get post details" . $e->getMessage();
    }
    

    // Stops user from putting in slugs that do not exist
    if(!is_object($post)){
      return $response->withStatus(302)->withHeader('Location', $basePath . '/');
    }
    
    // Retrieve Tags

    $specTags = $post->tags;
    $comments = $post->comments;


      if($request->getMethod() == "POST") {
        $args = array_merge($args, $request->getParsedBody());
        $postId = $post->id;

        try {
            $create = BlogPost\Comment::create(['post_id' => $postId, 'name' => $args['name'], 'date' => date('d-m-Y'), 'body' => $args['comment']]);
        } catch(Exception $e) {
          echo "Unable to create new post" . $e->getMessage();
          die();
        }

      
       return $response->withStatus(302)->withHeader('Location', $basePath . '/detail/' . $args['slug']);
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

/****************** EDIT PAGE ROUTE **************************/
$app->map(['GET', 'POST'],'/edit/{slug}', function ($request, $response, $args) {
  $slug = "";
  $basePath = $request->getUri()->getBasePath();
  $this->logger->addInfo("Edit Page");
  
  
  try{
    $post = BlogPost\Post::where('slug', $args['slug'])->first();
  } catch(Exception $e) {
    echo "Unable to retrieve post information" . $e->getMessage();
    die();
  }
  
   // Stops user from putting in slugs that do not exist
  if(!is_object($post)){
    return $response->withStatus(302)->withHeader('Location', $basePath . '/');
  }

  $specTags = $post->tags;

  
//Edit post
  if($request->getMethod() == "POST") {



    try {
        if (isset($_POST['delete'])) {
            $post->tags()->detach($args['id']);
            $post->delete($args['id']);

            return $response->withStatus(302)->withHeader('Location', $basePath.'/');
        }
    } catch(Exception $e){
      echo "Unable to delete post" . $e->getMessage();
      die();
    }


    $args = array_merge($args, $request->getParsedBody());
    $postId = $post->id;
    $args = $post->sanitize($args);
    $slug = $post->slugify($args['title']);

    //Makes sure slug is unique before editing it
    if($post->where('id', '!=', $postId )->where('slug', '=', $slug)->exists()){
      $slug .= "-1";
    }

    try {
        $post->where('id', $postId)->update(['title' => $args['title'], 'date' => $args['date'], 'body' => $args['body'], 'slug' => $slug]);
    }catch(Exception $e) {
      echo "Unable to update post" . $e->getMessage();
    }

    if ('' != $args['tags']) {
        $newTags = explode(',', (str_replace(' ', '', $args['tags'])));
    } else {
      $newTags = NULL;
    }
    $arr = array();
    
   
    if($newTags !== NULL){
      try{

      foreach ($newTags as $value) {
        $value = filter_var($value, FILTER_SANITIZE_STRING);
          $tag = new BlogPost\Tag(['tags' => $value]);
          $id = $post->tags()->save($tag)->id;
          
          $arr[] = $id;
      }
      $post->tags()->sync($arr);
  }catch(Exception $e) {
    echo "Unable to update tags" . $e->getMessage();
    die();
  } 
  } else {
    $post->tags()->detach();
} 

   return $response->withStatus(302)->withHeader('Location', $basePath . '/');

  }

  // CSRF token name and value
  $csrf = $this->get('csrf');
  $nameKey = $csrf->getTokenNameKey();
  $valueKey = $csrf->getTokenValueKey();
  $csrf = [
      $nameKey => $request->getAttribute($nameKey),
      $valueKey => $request->getAttribute($valueKey),
  ];
 
  return $this->view->render($response, 'edit.twig', [
      'csrf' => $csrf,
      'args' => $args,
      'post' => $post,
      'tags' => $specTags,
  ]);
})->setName('edit');




/****************** CREATE POST ROUTE **************************/

$app->map(['GET', 'POST'],'/newpost', function ($request, $response, $args) {
  $this->logger->addInfo("New Post Page");
  

$post = new BlogPost\Post;

  $basePath = $request->getUri()->getBasePath();
//Create post
  if($request->getMethod() == "POST") {

    $args = array_merge($args, $request->getParsedBody());
    $args = $post->sanitize($args);
    $slug = $post->slugify($args['title']);

    //Makes sure slug is unique before creating it
    if($post->where('slug', '=', $slug)->exists()){
      $slug .= "-1";
    }

    try {
        $postId = $post->create(['title' => $args['title'], 'date' => $args['date'], 'body' => $args['body'], 'slug' => $slug])->id;
    } catch(Exception $e) {
      echo "Unable to create new post" . $e->getMessage();
    }

    if ('' != $args['tags']) {
        $newTags = explode(',', (str_replace(' ', '', $args['tags'])));
    } else {
      $newTags = NULL;
    }
    $arr = array();
    
   
    if (null !== $newTags) {
        try {
            foreach ($newTags as $value) {
              $value = filter_var($value, FILTER_SANITIZE_STRING);
                $post = BlogPost\Post::find($postId);
                $tag = new BlogPost\Tag(['tags' => $value]);

                $id = $post->tags()->save($tag)->id;

                $arr[] = $id;
            }
            $post->tags()->sync($arr);
        }catch (Exception $e) {
          echo "Unable to create new tags with new post" . $e->getMessage();
          die();
       }
    }
    return $response->withStatus(302)->withHeader('Location', $basePath . '/');

  }

  // CSRF token name and value
  $csrf = $this->get('csrf');
  $nameKey = $csrf->getTokenNameKey();
  $valueKey = $csrf->getTokenValueKey();
  $csrf = [
      $nameKey => $request->getAttribute($nameKey),
      $valueKey => $request->getAttribute($valueKey),
  ];
  return $this->view->render($response, 'new.twig', [
      'csrf' => $csrf,
      'args' => $args,
      'post' => $post,
  ]);
})->setName('newpost');
