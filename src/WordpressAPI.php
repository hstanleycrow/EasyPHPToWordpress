<?php

namespace hcrow\EasyPHPToWordpress;

class WordpressAPI extends AbstractWordpressAPI
{
    protected string $wordpressSiteURL;
    protected string $wordpressUsername;
    protected string $wordpressApplicationPassword;
    protected string $errorMessage;
    protected array $imagesError;

    public function __construct(string $wordpressSiteURL, string $wordpressUsername, string $wordpressApplicationPassword)
    {
        parent::__construct($wordpressSiteURL, $wordpressUsername, $wordpressApplicationPassword);
        $this->wordpressSiteURL = $this->checkAndFixURL($wordpressSiteURL);
        $this->wordpressUsername = $wordpressUsername;
        $this->wordpressApplicationPassword = $wordpressApplicationPassword;
        $this->imagesError = [];
    }
    public function validateURL(): bool
    {
        try {
            $urlValidator = new URLValidator($this->wordpressSiteURL);
            $isValidURL = $urlValidator->isValidURL();
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }
        return $isValidURL;
    }
    public function validateCredentials(): bool
    {
        if ($this->validateURL())
            try {
                return (new CredentialsValidator($this))->test();
            } catch (\Exception $e) {
                $this->errorMessage = $e->getMessage();
            }
        return false;
    }
    public function publishPost(string $title, string $rawContent, array $categories, string $featuredImagePath = ""): mixed
    {
        if ($this->validateURL()) :
            try {
                return (new PublishPost($this, $title, $rawContent, $categories, $featuredImagePath))->publish();
            } catch (\Exception $e) {
                $this->errorMessage = $e->getMessage();
            }
        endif;
        return false;
    }
    public function categories(): array
    {
        $categories = array();
        if ($this->validateURL()) :
            try {
                $categories = (new WordpressCategoriesList($this))->categories();
            } catch (ParseTaxonomyException $e) {
                $this->errorMessage = $e->getMessage();
            }
        endif;
        return $categories;
    }
    public function tags(): array
    {
        $tags = array();
        if ($this->validateURL()) :
            try {
                $tags = (new WordpressTagsList($this))->tags();
            } catch (ParseTaxonomyException $e) {
                $this->errorMessage = $e->getMessage();
            }
        endif;
        return $tags;
    }

    /**
     * Get the value of wordpressSiteURL
     */
    public function wordpressSiteURL(): string
    {
        return $this->wordpressSiteURL;
    }

    /**
     * Get the value of errorMessage
     */
    public function errorMessage()
    {
        return $this->errorMessage;
    }
    public function addImagesWithError(array $listOfImages): void
    {
        $this->imagesError = $listOfImages;
    }

    /**
     * Get the value of imagesError
     */
    public function imagesError(): array
    {
        return $this->imagesError;
    }
    public function hasImagesErrors(): bool
    {
        return count($this->imagesError()) > 0;
    }
}
