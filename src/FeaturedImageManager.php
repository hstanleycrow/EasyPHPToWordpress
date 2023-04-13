<?php

namespace hcrow\EasyPHPToWordpress;

class FeaturedImageManager
{
    private string $featuredImagePath;
    private int $featuredImageId = 0;
    private string $title;
    private string $altText;
    private WordpressAPI $wordpressAPI;
    private PublishMedia $publishMedia;

    public function __construct(WordpressAPI $wordpressAPI, string $featuredImagePath, string $title = "", string $altText = "")
    {
        $this->wordpressAPI = $wordpressAPI;
        $this->featuredImagePath = $featuredImagePath;
        $this->title = $title;
        $this->altText = $altText;
        $this->publishMedia = new PublishMedia($this->wordpressAPI);
    }
    public function publish(): int
    {
        assert(!empty($this->featuredImagePath), "You must to define the featured image path first");
        try {
            if ($this->publishMedia->publish($this->featuredImagePath, $this->title, $this->altText)->uploaded())
                $this->featuredImageId = $this->publishMedia->mediaId();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $this->featuredImageId;
    }
    public function isPublished(): bool
    {
        return ($this->featuredImageId() > 0);
    }
    public function featuredImageId(): int
    {
        return $this->featuredImageId;
    }
}
