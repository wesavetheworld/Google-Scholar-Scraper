<?php

namespace Webcode\GoogleScholarScraper;

class Parser {

    const BASE_URL = "http://scholar.google.com/";

    public function getCitations($user, $sortBy = 'pubdate') {
        $publications = array();
        $obj = $this->_getRawData('citations', array('sortby' => $sortBy, 'user' => $user));
        unset($obj['children'][1]['children'][2]['children'][0]['children'][1]['children'][1]['children'][5]['children'][2]['children'][0]['children'][0]['children'][0]);
        $citationsRaw = array_values($obj['children'][1]['children'][2]['children'][0]['children'][1]['children'][1]['children'][5]['children'][2]['children'][0]['children'][0]['children']);
        foreach ($citationsRaw as $publicationRow) {
            $publications[$publicationRow['children'][0]['children'][0]['html']]['title'] = $publicationRow['children'][0]['children'][0]['href'];
        }
        return $publications;
    }

    public function _getRawData($route, $args) {
        $html = file_get_contents(self::BASE_URL . $route . '?' . http_build_query($args));
        return $this->_htmlToObject($html);
    }

    private function _htmlToObject($html) {
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        return $this->_elementToObject($dom->documentElement);
    }

    private function _elementToObject($element) {
        $obj = array("tag" => $element->tagName);
        foreach ($element->attributes as $attribute) {
            $obj[$attribute->name] = $attribute->value;
        }
        foreach ($element->childNodes as $subElement) {
            if ($subElement->nodeType == XML_TEXT_NODE) {
                $obj["html"] = $subElement->wholeText;
            } else {
                $obj["children"][] = $this->_elementToObject($subElement);
            }
        }
        return $obj;
    }

}
