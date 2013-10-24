<?php

require 'vendor/autoload.php';

use Nicklasos\Router\App,
    Nicklasos\Router\View;

$view = new View;
$view->setViewsPath(__DIR__ . '/views');
$view->setLayout('layout');

$app = new App;

$app->get('/', function () use ($view) {
    return $view->render('index');
});

$app->notFound(function () {
    return '404';
});

$app->run();