<?php
namespace FlatSite\Node;

class Post
{
    protected $url;
    protected $title;
    protected $subtitle;
    protected $body;
    protected $tags = [];
    protected $dateInsert;

    public function __construct(
        $url = null,
        $title = null,
        $subtitle = null,
        $body = null,
        $tags = [],
        $dateInsert = null
    ) {
        $this->url = $url;
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->body = $body;
        $this->tags = $tags;
        $this->dateInsert = $dateInsert;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    }

    public function getSubtitle()
    {
        return $this->subtitle;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setTags(array $tags)
    {
        $this->tags = $tags;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setDateInsert($dateInsert)
    {
        $this->dateInsert = $dateInsert;
    }

    public function getDateInsert()
    {
        return $this->dateInsert;
    }
}