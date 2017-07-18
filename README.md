# wordpress-api
A simple wordpress API to get JSON from your BDD

## Use 

```php 
require('api/index.php');
```
If you go on ` http://your_site.com/api/posts`, you will see your last posts to JSON.


### api/index.php
```php
require 'model/Post.php';
require 'Router.php';

$router = new Api\Router();

$router::get('/api/posts', function() {

    $model = new Api\Model\Post();
    echo(json_encode($model::getAll()));exit();
});
```
