<?php

namespace hcrow\EasyPHPToWordpress;

class WordpressTagsList extends AbstractWordpressList
{
    public function __construct(WordpressAPI $wordpressAPI)
    {
        parent::__construct($wordpressAPI, "TAGS");
    }

    public function tags(): array
    {
        return $this->taxonomy(null);
    }
}
