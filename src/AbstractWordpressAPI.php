<?php

namespace hcrow\EasyPHPToWordpress;

abstract class AbstractWordpressAPI
{
    protected string $wordpressSiteURL;
    protected string $wordpressUsername;
    protected string $wordpressApplicationPassword;

    public function __construct(string $wordpressSiteURL, string $wordpressUsername, string $wordpressApplicationPassword)
    {
        $this->wordpressSiteURL = $this->checkAndFixURL($wordpressSiteURL);
        $this->wordpressUsername = $wordpressUsername;
        $this->wordpressApplicationPassword = $wordpressApplicationPassword;
    }
    protected function checkAndFixURL($url): string
    {
        $url = (stripos($url, "https://") === false) ? "https://" . $url : $url;
        return (strpos($url, "/", -1) === false) ? $url . "/" : $url;
    }
    public function wordpressSiteURL(): string
    {
        return $this->wordpressSiteURL;
    }
    public function wordpressUsername(): string
    {
        return $this->wordpressUsername;
    }
    public function wordpressApplicationPassword(): string
    {
        return $this->wordpressApplicationPassword;
    }
}
