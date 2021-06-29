<?php


class TableRow
{

    private string $id;
    private string $url;
    private bool $hasImage;
    private array $fields;

    public function __construct()
    {
        $this->fields = array();

    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return bool
     */
    public function isHasImage(): bool
    {
        return $this->hasImage;
    }

    /**
     * @param bool $hasImage
     */
    public function setHasImage(bool $hasImage): void
    {
        $this->hasImage = $hasImage;
    }

    /**
     * @return string[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param string $field
     */
    public function addField(string $field): void
    {
        array_push($this->fields, $field);
    }






}