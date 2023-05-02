<?php

namespace hcrow\EasyPHPToWordpress;

use hstanleycrow\EasyPHPcURLRequest\CurlRequest;

class PublishMedia
{
    private const PUBLISH_ERROR = 1;
    private const UPDATE_ERROR = 2;
    private const ERROR_MESSAGES = [
        self::PUBLISH_ERROR => "Cannot publish. Reason: ",
        self::UPDATE_ERROR => "Cannot update media info. Reason: ",
    ];
    private const MEDIA_URL = 'wp-json/wp/v2/media/';
    private string $imagePath;
    private string $mimeType;
    private int $mediaId;
    private string $mediaURL;
    private string $permalink;
    private string $title;
    private string $altText;
    private mixed $mediaData;
    private bool $uploaded = false;
    private bool $updated = false;
    private WordpressAPI $wordpressAPI;

    public function __construct(WordpressAPI $wordpressAPI)
    {
        $this->wordpressAPI = $wordpressAPI;
    }
    public function publish(string $imagePath, ?string $title = "", ?string $altText = ""): object
    {
        $this->imagePath = $imagePath;
        $this->title = $title;
        $this->altText = $altText;
        $imageValidator = new WordpressMediaValidator($this->imagePath);
        if ($imageValidator->isValidWordpressMediaFile()) :
            $this->mimeType = $imageValidator->getMimeType();
            $this->mediaData = $this->doPublish();
            if ($this->isValidMediaData()) :
                $this->setMediaProperties();
                $this->uploaded = true;
                if ($this->hasMediaInfo()) :
                    $this->updateMediaInfo();
                endif;
            else :
                $errorMessage = "Wordpress said: " . $this->mediaData->data->status . " " . $this->mediaData->message;
                throw new \Exception(self::ERROR_MESSAGES[self::PUBLISH_ERROR] . " " . $errorMessage);
            endif;
        endif;
        return $this;
    }
    private function doPublish(): mixed
    {
        return $this->makeCurlRequest(
            $this->wordpressAPI->wordpressSiteURL() . self::MEDIA_URL,
            $this->getImage(),
            array(
                'Content-Type: ' . $this->mimeType,
                'Content-Disposition: attachment; filename="' . basename($this->imagePath) . '"',
                'Authorization: Basic ' . base64_encode($this->wordpressAPI->wordpressUsername() . ':' . $this->wordpressAPI->wordpressApplicationPassword())
            )
        );
    }
    private function makeCurlRequest(string $url, $postData, $httpHeader): mixed
    {
        $curlRequest = new CurlRequest($url);
        $curlRequest->setPost(true);
        $curlRequest->setPostData($postData);
        $curlRequest->setHttpHeader($httpHeader);
        $curlRequest->execute();
        if ($curlRequest->isSuccessful()) :
            return json_decode($curlRequest->getResult());
        endif;
    }
    private function getImage(): mixed
    {
        return file_get_contents($this->imagePath);
    }
    private function isValidMediaData(): bool
    {
        return (!empty($this->mediaData->id) && $this->mediaData->id > 0);
    }
    private function setMediaProperties(): void
    {
        $this->mediaId = $this->mediaData->id;
        $this->mediaURL = $this->mediaData->source_url;
        $this->permalink = $this->mediaData->permalink_template;
    }
    private function hasMediaInfo(): bool
    {
        return !(empty($this->title) && empty($this->altText));
    }
    private function updateMediaInfo(): mixed
    {
        $postData = json_encode([
            'title' => $this->title,
            'alt_text' => $this->altText,
        ]);
        return $this->makeCurlRequest(
            $this->wordpressAPI->wordpressSiteURL() . self::MEDIA_URL . $this->mediaId,
            $postData,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($postData),
                'Authorization: Basic ' . base64_encode($this->wordpressAPI->wordpressUsername() . ':' . $this->wordpressAPI->wordpressApplicationPassword())
            )
        );
    }

    public function getPermalink(): string
    {
        return $this->permalink;
    }

    /**
     * Get the value of mediaURL
     */
    public function mediaURL()
    {
        return $this->mediaURL;
    }

    /**
     * Get the value of uploaded
     */
    public function uploaded()
    {
        return $this->uploaded;
    }

    /**
     * Get the value of updated
     */
    public function updated()
    {
        return $this->updated;
    }

    /**
     * Get the value of mediaId
     */
    public function mediaId()
    {
        return $this->mediaId;
    }
}
