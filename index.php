<?php
require 'model/Post.php';
require 'Router.php';

$router = new Api\Router();
$router::get('/api/posts', function() {
    $model = new Api\Model\Post();
    echo(json_encode($model::getAll()));exit();
});
