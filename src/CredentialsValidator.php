<?php

namespace hcrow\EasyPHPToWordpress;

class CredentialsValidator
{
    private const TEST_IMAGE_PATH = 'testImage.jpg';
    private WordpressAPI $wordpressAPI;
    private PublishMedia $publishMedia;

    public function __construct(WordpressAPI $wordpressAPI)
    {
        $this->wordpressAPI = $wordpressAPI;
        $this->publishMedia = new PublishMedia($this->wordpressAPI);
    }

    public function test(): bool
    {
        return $this->publishMedia->publish(self::TEST_IMAGE_PATH)->uploaded();
    }
}
