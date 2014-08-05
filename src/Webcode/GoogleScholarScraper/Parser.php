<?php

namespace Webcode\GoogleScholarScraper;

class Parser {

    const BASE_URL = "http://scholar.google.com/";

    public function getCitations($user, $sortBy = 'pubdate') {
        $html = fopen(BASE_URL . 'citations?' . http_build_query(array('sortby' => $sortBy, 'user' => $user)), "r");
        return json_encode($this->_htmlToObject($html));
    }

    private function _htmlToObject($html) {
        $dom = new DOMDocument();
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
