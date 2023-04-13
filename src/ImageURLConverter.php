<?php

namespace hcrow\EasyPHPToWordpress;

class ImageURLConverter
{
    private WordpressAPI $wordpressAPI;
    private simple_html_dom $htmlContent;

    public function __construct(WordpressAPI $wordpressAPI, simple_html_dom $htmlContent)
    {
        $this->wordpressAPI = $wordpressAPI;
        $this->htmlContent = $htmlContent;
    }
    public function convert(): object
    {
        $html = $this->htmlContent;
        $failedImages = array();

        if ($html->find('img')) :
            foreach ($html->find('img') as $image) :
                if (!empty($image->src)) :
                    $image->src = $this->checkAndFixURLScheme($image->src);
                    try {
                        $publishMedia = new PublishMedia($this->wordpressAPI);
                        if ($publishMedia->publish(
                            $image->src,
                            $image->title,
                            $image->alt
                        )->uploaded())
                            $image->src = $publishMedia->mediaURL();
                        $html->save();
                        usleep(0500000); // Esperar 0.5 segundos
                    } catch (\Exception $e) {
                        $failedImages[] = $image->src;
                    }
                endif;
            endforeach;
            if (!empty($failedImages)) {
                $this->wordpressAPI->addImagesWithError($failedImages);
            }
            $rawContent = $html->load($html->save());
            return $rawContent;
        endif;
    }
    private function checkAndFixURLScheme(string $imageSrc): string
    {
        if (empty(parse_url($imageSrc, PHP_URL_SCHEME)))
            $imageSrc = str_replace("//", "https://", $imageSrc);
        return $imageSrc;
    }
}
