<?php

namespace hcrow\EasyPHPToWordpress;

class URLValidator
{
    const INVALID_URL_STRUCTURE = 1;
    const INVALID_RESPONSE = 2;
    const ERROR_MESSAGES = [
        self::INVALID_URL_STRUCTURE => "The URL is invalid, It seems like there are folders into the URL.",
        self::INVALID_RESPONSE => "URL not responding, it must to resolve http 200 status",
    ];
    private string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function isValidURL(): bool
    {
        if (!$this->isValidURLStructure()) :
            throw new \Exception(self::ERROR_MESSAGES[self::INVALID_URL_STRUCTURE]);
            return false;
        endif;
        if (!$this->isValidResponse()) :
            throw new \Exception(self::ERROR_MESSAGES[self::INVALID_RESPONSE]);
            return false;
        endif;
        return true;
    }

    private function isValidURLStructure(): bool
    {
        return (substr_count($this->url, "/") == 3);
    }

    public function isValidResponse(): bool
    {
        $headers = @get_headers($this->url);
        return ($headers && strpos($headers[0], "200"));
    }
}
