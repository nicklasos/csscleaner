<?php

require '../vendor/autoload.php';

use Nicklasos\CssCleaner\Css,
    Nicklasos\CssCleaner\Parser;

function wtf($what)
{
    echo '<pre>';
    print_r($what);
    echo '</pre>';
}

$selector = '#header, #footer .widget-tabs>.nav-tabs>li+a ';

$selector = trim($selector);
$selector = preg_replace('/\s+/', ' ', $selector);

$css = new Css;

$cssFile = file_get_contents('style.css');
$htmlFiles = [
    file_get_contents('index.html')
];

//wtf($css->getUnusedSelectors($cssFile, $htmlFiles));




$parser = new Parser;

/*
$siteLinks = [
    'http://localhost/csscleaner/test/',
    'http://localhost/csscleaner/test/test.html'
];
*/

$siteLinks = ['http://fapl.ru'];

$unusedSelectors = $parser->run($siteLinks);

wtf('----result-----');
wtf($unusedSelectors);