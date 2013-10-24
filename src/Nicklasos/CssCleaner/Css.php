<?php

namespace Nicklasos\CssCleaner;

use Symfony\Component\CssSelector\CssSelector;

/**
 * Class Css
 * Methods to work with css selectors
 * @package Nicklasos\CssCleaner
 */
class Css
{
    /**
     * Parse selectors from css string
     *
     * Return array of selectors
     *
     * IMPORTANT! If exists multiple selectors, like this: #header, #footer
     * Then will be two elements in array:
     *
     * array(
     *    '#header' => '#header, #footer',
     *    '#footer' => '#header, #footer'
     * )
     *
     * So we can find out, what parts of multiple selectors are in use :)
     *
     * @param string $fileContent css file
     * @return bool|array css selectors
     */
    public function getSelectors($fileContent)
    {
        preg_match_all('/(.+?)\s?\{\s?(.+?)\s?\}/', $fileContent, $matches);

        $selectors = [];

        if (isset($matches[1])) {

            foreach ($matches[1] as $selector) {

                $selector = trim($selector);

                // Check each part of multiple selectors
                $parts = explode(',', $selector);
                foreach ($parts as $part) {
                    $selectors[$part] = $selector;
                }
            }

            return $selectors;

        } else {
            return false;
        }
    }

    /**
     * @param string $cssFile content
     * @param array $htmlFiles html content
     * @return array
     */
    public function getUnusedSelectors($cssFile, array $htmlFiles)
    {
        $dom = new \DOMDocument();
        $dom->validateOnParse = false;

        $selectors = $this->getSelectors($cssFile);

        foreach ($htmlFiles as $htmlFile) {

            $dom->loadHTML($htmlFile);

            $xpath = new \DOMXPath($dom);

            foreach ($selectors as $part => $selector) {

                // Check if selector is present on page
                $node = $xpath->query(CssSelector::toXPath($part));

                if ($node->length) {
                    unset($selectors[$part]);
                }
            }
        }

        return $selectors;
    }
}