<?php

namespace Nicklasos\CssCleaner;

/**
 * Class Parser
 * @package Nicklasos\CssCleaner
 */
class Parser
{
    /**
     * Put links, pull unused css selectors
     * It's pure magic (and little govnokod) :)
     * @param array $siteLinks
     * @return array
     */
    public function run(array $siteLinks)
    {
        $css = new Css;
        $pagesCache = []; // To prevent multiple downloads
        $cssParams = [];
        $result = [];

        foreach ($siteLinks as $siteLink) {
            list($siteContent, $cssLinks) = $this->getCss($siteLink);

            $cssFromPage = $this->getCssFromPage($siteContent);

            if ($cssFromPage) {
                $unusedFromPage = $css->getUnusedSelectors($cssFromPage, [$siteLink]);

                if ($unusedFromPage) {
                    $result[$siteLink] = $unusedFromPage;
                }
            }

            foreach ($cssLinks as $cssLink) {
                $cssParams[$cssLink]['pages'][$siteLink] = $siteLink;
                $cssContent = file_get_contents($cssLink);

                $cssParams[$cssLink]['css'] = $cssContent;

                $pagesCache[$siteLink] = $siteContent;
            }
        }

        foreach ($cssParams as $link => $param) {
            $pages = [];

            foreach ($param['pages'] as $page) {
                $pages[$page] = $pagesCache[$page];
            }

            $unused = $css->getUnusedSelectors($param['css'], $pages);
            if ($unused) {
                $result[$link] = $unused;
            }
        }

        return $result;
    }

    /**
     * Get all css from style tags on page
     * @param string $content
     * @return string
     */
    public function getCssFromPage($content)
    {
        $result = '';

        if (preg_match_all('/<style.*?>(.*?)<\/style>/sei', $content, $matches)) {
            if (isset($matches[1])) {
                foreach ($matches[1] as $match) {
                    $result .= $match;
                }
            }
        }

        return $result;
    }

    /**
     * Get css links from page
     * @param string $link
     * @return array
     */
    public function getCss($link)
    {
        $cssLinks = [];
        $content = file_get_contents($link);

        if (preg_match_all('/(@import) (url)\(\"([^)]+)\"\)/', $content, $matches)) {
            if (isset($matches[3])) {
                foreach ($matches[3] as $match) {
                    $cssLinks[] =$this->getFullLink($link, $match);
                }
            }
        }

        if (preg_match_all('/<link.+href=[\'"]([^\'"]+)[\'"].*>/', $content, $matches)) {
            if (isset($matches[1])) {
                foreach ($matches[1] as $match) {
                    if (!preg_match('/\.(xml|ico)$/', $match)) {
                        $cssLinks[] = $this->getFullLink($link, $match);
                    }
                }
            }
        }

        return [$content, $cssLinks];
    }

    /**
     * Get full link to resource
     * @param string $source
     * @param string $link
     * @return string
     */
    private function getFullLink($source, $link)
    {
        preg_match('/.(html|php|asp|jsp)$/', $source, $matches);

        if (isset($matches[1])) {

            $source = explode('/', $source);
            array_pop($source);
            $source = implode('/', $source) . '/';

        } else if (substr($source, -1) != '/') {
            $source .= '/';
        }

        if (preg_match('/^http/', $link, $match)) {

            $source = $link;

        } else if ($link[0] == '/') {

            $url = parse_url($source);
            $source = $url['scheme'] . '://' . $url['host'] . $link;

        } else {
            $source .= $link;
        }

        return $source;
    }
}