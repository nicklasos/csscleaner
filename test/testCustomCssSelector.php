<?php

include '../vendor/autoload.php';

function wtf($what)
{
    echo '<pre>';
    print_r($what);
    echo '</pre>';
}

function getElementsByClassName(DOMDocument $DOMDocument, $ClassName)
{
    $Elements = $DOMDocument->getElementsByTagName("*");
    $Matched = array();

    foreach($Elements as $node)
    {
        if(!$node->hasAttributes())
            continue;

        $classAttribute = $node->attributes->getNamedItem('class');

        if(!$classAttribute)
            continue;

        $classes = explode(' ', $classAttribute -> nodeValue);

        if(in_array($ClassName, $classes))
            $Matched[] = $node;
    }

    return $Matched;
}

$selector = ' #header , #footer   .widget-tabs >.nav-tabs>li+a ';

wtf($selector);

$selector = trim($selector);
$selector = preg_replace('/\s+/', ' ', $selector);
$selector = preg_replace('/( > | >|> )/', '>', $selector);
$selector = preg_replace('/( , | ,|, )/', ',', $selector);

wtf($selector);

$selectorEntities = [];
foreach (explode(',', $selector) as $part) {
    $selectorEntities[] = preg_split('/([ .#>+])/', $part, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
}

$dom = new DOMDocument();
$dom->loadHTML(file_get_contents('index.html'));

foreach ($selectorEntities as $entity) {
    wtf($entity);

    $element = $dom;

    $delimiter = '';
    for ($key = 0; $key < count($entity); $key++) {
        switch ($entity[$key]) {

            case '.':
                wtf('class');

                $elements = $element->getElementsByTagName('*');

                foreach ($elements as $node) {
                    if (!$node->hasAttributes()) continue;

                    $class = $node->attributes->getNamedItem('class');

                    if (!$class) continue;

                    $classes = explode(' ', $class->nodeValue);

                    wtf($classes);

                    if (!in_array($entity[$key+1], $classes)) {
                        wtf('Not found class ' . $entity[$key+1]);
                    } else {
                        $element = $node;
                        break;
                    }
                }

                break;

            case '#':
                $element = $element->getElementById($entity[++$key]);
                var_dump($element);

                if ($element === null) {
                    wtf('Not found');
                } else {
                    wtf('Found id'); wtf($element->tagName);
                }

                break;

            case ' ':
                wtf('inside');
                $delimiter = 'inside';
                break;

            case '>':
                wtf('child');
                $delimiter = 'child';
                break;

            case '+':
                wtf('after');
                $delimiter = 'after';
                break;

            default: // selector name
                break;
        }

    }
}




/*
$css =  file_get_contents('http://localhost/Backend/design/css/style.css');
preg_match_all('/(.+?)\s?\{\s?(.+?)\s?\}/', $css, $matches);
wtf($matches[1]);
*/