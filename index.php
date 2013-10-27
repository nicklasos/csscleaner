<?php

require 'vendor/autoload.php';

use Nicklasos\Router\App,
    Nicklasos\Router\View,
    Nicklasos\CssCleaner\Parser;

$view = new View;
$view->setViewsPath(__DIR__ . '/views');
$view->setLayout('layout');

$app = new App;

$app->get('/', function () use ($view) {
    return $view->render('index');
});

$app->get('parse', function () use ($view) {
    if ($_POST && isset($_POST['links'])) {
        $links = explode("\n", $_POST['links']);

        $parser = new Parser;
        $unusedSelectors = $parser->run($links);

        print_r($unusedSelectors);
    }
});

$app->notFound(function () {
    return '404';
});

$app->run();