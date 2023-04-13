<?php

namespace hcrow\EasyPHPToWordpress;

abstract class AbstractWordpressList
{
    private const LISTING_ERROR = 1;
    private const ERROR_MESSAGES = [
        self::LISTING_ERROR => "Cannot read taxonomy list. Reason: ",
    ];
    private const TAGS_TEXT = "TAGS";
    private const CATEGORIES_URL = 'wp-json/wp/v2/categories';
    private const TAGS_URL = 'wp-json/wp/v2/tags';
    protected string $pathList;
    protected string $taxonomy;
    protected WordpressAPI $wordpressAPI;

    public function __construct(WordpressAPI $wordpressAPI, string $taxonomy = "CATEGORY")
    {
        $this->wordpressAPI = $wordpressAPI;
        $this->taxonomy = $taxonomy;
    }
    public function listURL(): string
    {
        return $this->wordpressAPI->wordpressSiteURL() . ((strtoupper($this->taxonomy) == self::TAGS_TEXT) ? self::TAGS_URL : self::CATEGORIES_URL);
    }
    protected function taxonomy(?int $parent): array
    {
        $page = 1;
        $morePages = true;
        $tags = array();
        while ($morePages) {
            $url = $this->listURL() . "?page=" . $page . (($this->taxonomy <> self::TAGS_TEXT) ? "&parent=" . $parent : "");
            try {
                $curlRequest = new CurlRequest($url);
                $curlRequest->execute();
                if ($curlRequest->isSuccessful()) {
                    $rawTags = $curlRequest->getResult();
                    if (!$this->isEmptyResponse($rawTags)) {
                        $tempTags = array();
                        $parsedTags = $this->parseTaxonomy($rawTags);
                        if ($this->notErrorCode($parsedTags['errorCode'])) {
                            $tempTags = $parsedTags['data'];
                            foreach ($tempTags as $branch) {
                                $tags[] = $branch;
                            }
                        }
                    } else {
                        $morePages = false;
                    }
                }
                $page++;
            } catch (HttpRequestException $e) {
            }
        }
        return $tags;
    }
    protected function isEmptyResponse(string $value): bool
    {
        return ($value == "[]");
    }
    protected function notErrorCode(int $errorCode): bool
    {
        return $errorCode == 0;
    }
    protected function parseTaxonomy(string $rawTaxonomy): array
    {
        $rawTaxonomy = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $rawTaxonomy);
        $rawTaxonomy = mb_convert_encoding($rawTaxonomy, 'UTF-8');
        $rawTaxonomy = json_decode($rawTaxonomy);
        $error = json_last_error();
        if ($error === JSON_ERROR_NONE) :
            $taxonomies = array();
            foreach ($rawTaxonomy as $taxonomy) :
                $taxonomies[] = ["id" => $taxonomy->id, "name" => $taxonomy->name];
            endforeach;
        else :
            $errorMessage = "Wordpress said: " . $error;
            throw new ParseTaxonomyException(self::ERROR_MESSAGES[self::LISTING_ERROR] . $errorMessage);
        endif;
        return ["errorCode" => 0, "data" => $taxonomies];
    }
}
