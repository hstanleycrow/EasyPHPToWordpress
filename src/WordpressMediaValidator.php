<?php

namespace hcrow\EasyPHPToWordpress;

class WordpressMediaValidator
{
    private const INVALID_IMAGE_TYPE = 1;
    private const INVALID_FILE = 2;
    private const ERROR_MESSAGES = [
        self::INVALID_IMAGE_TYPE => "Invalid Mime Type, try with jpg, png or gif.",
        self::INVALID_FILE => "invalid file or file not exist in the path",
    ];
    const VALID_WORDPRESS_MIME_TYPES = array(
        "image/gif",
        "image/jpg",
        "image/png",
        "image/webp",
    );

    private string $imagePath;
    private string $mimeType;

    public function __construct(string $imagePath)
    {
        $this->imagePath = $imagePath;
    }

    public function isValidWordpressMediaFile(): bool
    {
        if ($this->isAFileImage()) :
            if ($this->isImageFileValid()) :
                if ($this->mimeType = $this->setMimeType()) :
                    if ($this->isValidWPMimeType()) :
                        return true;
                    else :
                        throw new InvalidImageFormatException(self::ERROR_MESSAGES[self::INVALID_IMAGE_TYPE]);
                    endif;
                else :
                    throw new InvalidImageFormatException(self::ERROR_MESSAGES[self::INVALID_IMAGE_TYPE]);
                endif;
            else :
                throw new InvalidFileException(self::ERROR_MESSAGES[self::INVALID_FILE]);
            endif;
        endif;
        return false;
    }
    private function isAFileImage(): bool
    {
        return (stripos($this->imagePath, "base64") === false) ? true : false;
    }
    private function isImageFileValid(): bool
    {
        if ($this->isLocalFile()) :
            return file_exists($this->imagePath);
        else :
            $urlValidator = new URLValidator($this->imagePath);
            return $urlValidator->isValidResponse();
        endif;
    }
    private function isLocalFile(): bool
    {
        return (stripos($this->imagePath, "http://") === false && stripos($this->imagePath, "https://") === false) ? true : false;
    }
    private function setMimeType(): mixed
    {
        $mimes  = array(
            IMAGETYPE_GIF => "image/gif",
            IMAGETYPE_JPEG => "image/jpg",
            IMAGETYPE_PNG => "image/png",
            IMAGETYPE_SWF => "image/swf",
            IMAGETYPE_PSD => "image/psd",
            IMAGETYPE_BMP => "image/bmp",
            IMAGETYPE_TIFF_II => "image/tiff",
            IMAGETYPE_TIFF_MM => "image/tiff",
            IMAGETYPE_JPC => "image/jpc",
            IMAGETYPE_JP2 => "image/jp2",
            IMAGETYPE_JPX => "image/jpx",
            IMAGETYPE_JB2 => "image/jb2",
            IMAGETYPE_SWC => "image/swc",
            IMAGETYPE_IFF => "image/iff",
            IMAGETYPE_WBMP => "image/wbmp",
            IMAGETYPE_XBM => "image/xbm",
            IMAGETYPE_ICO => "image/ico"
        );
        if (($image_type = @exif_imagetype($this->imagePath)) && (array_key_exists($image_type, $mimes))) {
            return $mimes[$image_type];
        } else {
            return false;
        }
    }
    private function isValidWPMimeType(): bool
    {
        return in_array($this->mimeType, self::VALID_WORDPRESS_MIME_TYPES);
    }

    /**
     * Get the value of mimeType
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }
}
