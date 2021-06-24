<?php


/**
 * Class Image
 * Represents an image to be used in the website
 * It holds image metadata like its url, href link, alt string, etc
 */
class Image {

    public function __construct(private string $url, private string $href, private string $alt)
    {
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * @return string
     */
    public function getAlt(): string
    {
        return $this->alt;
    }

}