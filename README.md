# TreeHouse-Project-5
 Project 5 - Blog with Slim

Blog style project that allows you to create, update, and delete posts.  The posts can have comments and tags added. Multiple tags can be inserted by seperating each tag by a comma. Database interactions are done with Eloquent ORM.  

### Installation ####

Be sure to run:

`composer install`
`composer dumpautoload -o`

This will ensure you have all required dependencies to ensure everything works correctly.

### Extra Features ###

*Comments associated with a post will be deleted along with the post.
*When updating or changing tags, once a tag is no longer associated with any posts it will also get deleted.
*Comments allow you to not put in anything in the name field and it will change the name to Anonymous.

