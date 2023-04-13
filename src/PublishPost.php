<?php

namespace hcrow\EasyPHPToWordpress;

include_once('simple_html_dom.php');
class PublishPost
{
    private const POST_URL = '/wp-json/wp/v2/posts/';
    private const PUBLISH_STATUS = "publish";
    private const VALID_STATUS = [
        'publish',
        'future',
        'draft',
        'pending',
        'private'
    ];
    private string $title;
    private string $rawContent;
    private simple_html_dom $htmlContent;
    private string $date;
    private string $status;
    private array $categories;
    private string $featuredImagePath;
    private int $featuredImageId;
    private string $slug;
    private bool $published;
    private string $postUrl;
    private WordpressAPI $wordpressAPI;

    public function __construct(WordpressAPI $wordpressAPI, string $title, string $rawContent, array $categories, string $featuredImagePath = "")
    {
        $this->wordpressAPI = $wordpressAPI;
        $this->title = $title;
        $this->rawContent = $rawContent;
        $this->featuredImagePath = $featuredImagePath;
        $this->htmlContent = new simple_html_dom();
        $this->categories = $categories;
        $this->setPublishDate();
        $this->setStatus();
    }
    public function setPublishDate(?string $publishDate = ""): void
    {
        if (empty($publishDate))
            $publishDate = date('Y-m-d H:i:s');
        $publishDate = str_replace("T", " ", $publishDate);
        $publishDate = date('Y-m-d H:i:s', strtotime($publishDate));
        $this->date = $publishDate;
    }
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }
    public function setStatus(string $status = self::PUBLISH_STATUS): void
    {
        $this->status = ($this->isValidStatus($status)) ? strtolower($status) : self::PUBLISH_STATUS;
    }
    private function isValidStatus(string $status): bool
    {
        return (in_array(strtolower($status), self::VALID_STATUS));
    }
    public function publish(): mixed
    {
        ini_set('user_agent', 'My-Application/2.5');
        $this->htmlContent->load($this->rawContent);
        $this->publishFeaturedImage();
        $this->rawContent = (new ImageURLConverter($this->wordpressAPI, $this->htmlContent))->convert();
        $this->doPublish();
        if ($this->published())
            return $this->postUrl();
        return false;
    }
    private function publishFeaturedImage(): void
    {
        $featuredMediaManager = new FeaturedImageManager($this->wordpressAPI, $this->featuredImagePath, $this->title, $this->title);
        $featuredMediaManager->publish();
        $this->featuredImageId = $featuredMediaManager->isPublished() ? $featuredMediaManager->featuredImageId() : 0;
    }
    private function doPublish(): void
    {
        $this->published = false;
        $this->encodeContent();
        if ($postResult = $this->makeRequest()) :
            $this->processPostResult($postResult);
            $this->published = true;
        endif;
    }
    private function encodeContent(): void
    {
        $this->rawContent = mb_convert_encoding($this->rawContent, "UTF-8");
    }
    private function makeRequest(): mixed
    {
        $postData = json_encode([
            'title' => $this->title,
            'content' => $this->rawContent,
            'date' => $this->date,
            'status' => $this->status,
            'categories' => $this->categories,
            'featured_media' => $this->featuredImageId,
            'slug' => $this->slug(),
        ]);
        $curlRequest = new CurlRequest($this->wordpressAPI->wordpressSiteURL() . self::POST_URL);
        $curlRequest->setPost(true);
        $curlRequest->setPostData($postData);
        $curlRequest->setHttpHeader([
            'Content-Type: application/json; Charset=UTF-8',
            'Content-Length: ' . strlen($postData),
            'Authorization: Basic ' . base64_encode($this->wordpressAPI->wordpressUsername() . ':' . $this->wordpressAPI->wordpressApplicationPassword()),
        ]);
        $curlRequest->execute();
        if ($curlRequest->isSuccessful())
            return $curlRequest->getResult();
        return false;
    }
    private function processPostResult(mixed $postResult): void
    {
        $postResult = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $postResult);
        $postResult = mb_convert_encoding($postResult, "UTF-8");
        $postResult = json_decode($postResult);
        $this->postUrl = $postResult->link;
    }
    private function slug(): string
    {
        return !empty($this->slug) ? $this->slug : $this->title;
    }

    /**
     * Get the value of postUrl
     */
    public function postUrl()
    {
        return $this->postUrl;
    }

    /**
     * Get the value of published
     */
    public function published()
    {
        return $this->published;
    }
}
