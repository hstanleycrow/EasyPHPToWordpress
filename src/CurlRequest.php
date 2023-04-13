<?php

namespace hcrow\EasyPHPToWordpress;

class CurlRequest
{
    const HTTP_REQUEST_ERROR = 1;
    const ERROR_MESSAGES = [
        self::HTTP_REQUEST_ERROR => "Error on Http Request, reason: ",
    ];

    private string $url;
    private mixed $postData;
    private array $httpHeader;
    private bool $post;
    private mixed $result;
    private bool $completed;

    public function __construct(string $url)
    {
        $this->url = $url;
        $this->post = false;
        $this->completed = false;
    }

    public function execute()
    {
        assert(!empty($this->url), "You must to define the URL first");
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, $this->post);
            (isset($this->postData)) ? curl_setopt($ch, CURLOPT_POSTFIELDS, $this->postData) : null;
            (isset($this->httpHeader)) ? curl_setopt($ch, CURLOPT_HTTPHEADER, $this->httpHeader) : null;
            $this->result = curl_exec($ch);
            curl_close($ch);
            $this->completed = ($this->result ? true : false);
        } catch (\Exception $e) {
            throw new HttpRequestException(self::ERROR_MESSAGES[self::HTTP_REQUEST_ERROR] . " " . $e->getMessage());
        }
    }

    public function isSuccessful(): bool
    {
        return $this->completed;
    }
    public function setPostData(mixed $postData): void
    {
        $this->postData = $postData;
    }

    public function setHttpHeader(array $httpHeader): void
    {
        $this->httpHeader = $httpHeader;
    }

    public function setPost(bool $post): void
    {
        $this->post = $post;
    }

    public function getResult(): mixed
    {
        return $this->result;
    }
}
