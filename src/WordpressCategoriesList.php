<?php

namespace hcrow\EasyPHPToWordpress;

class WordpressCategoriesList extends AbstractWordpressList
{
    public function __construct(WordpressAPI $wordpressAPI)
    {
        parent::__construct($wordpressAPI, "CATEGORY");
    }

    public function categories(): array
    {
        $categories = $this->taxonomy(0);
        foreach ($categories as &$category) {
            $category['children'] = $this->taxonomy($category['id']);
        }
        return $categories;
    }
}
