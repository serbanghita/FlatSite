<?php
namespace FlatSite\Node;

use FlatSite\View;

class PostRenderer
{
    protected $post;
    protected $storage;
    protected $config;

    protected $renderedPost;
    protected $renderedPostWithTheme;

    public function __construct(Post $post, View $view)
    {
        $this->post = $post;
        $this->view = $view;
    }

    public function render()
    {
        $renderedPost = $this->view->render('post',
            [
                'title' => $this->post->getTitle(),
                'dateInsert' => $this->post->getDateInsert(),
                'dateInsertFormatted' => $this->post->getDateInsert(),
                'body' => $this->post->getBody(),
                'tags' => implode(', ', $this->post->getTags())
            ]
        );

        return $renderedPost;
    }
}