<?php
namespace FlatSite\Builder;

use FlatSite\Config;
use FlatSite\Storage\StorageInterface;

use FlatSite\Node\Post;
use FlatSite\Node\PostRenderer;
use FlatSite\View;

class Builder implements BuilderInterface
{
    protected $config;
    protected $storage;
    protected $view;

    protected $templateCache = null;
    protected $postsCache = [];
    protected $tagsCache = [];

    public function __construct(Config $config, StorageInterface $storage, View $view)
    {
        $this->config = $config;
        $this->storage = $storage;
        $this->view = $view;
    }

    public function buildIndex()
    {

    }

    public function buildPostsAndTags()
    {
        $di = new \DirectoryIterator($this->config->get('privatePostsPath'));

        foreach ($di as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            $fileContents = $this->storage->read($fileInfo->getRealPath());

            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $dom->loadHTML($fileContents);
            $finder = new \DOMXPath($dom);

            $post = new Post();
            $post->setUrl($fileInfo->getFilename());

            $className = "post-title";
            $nodes = $finder->query("//*[contains(@class, '$className')]");
            $post->setTitle($nodes->item(0)->nodeValue);

            $className = "post-body";
            $nodes = $finder->query("//*[contains(@class, '$className')]");
            $post->setBody($nodes->item(0)->ownerDocument->saveHTML($nodes->item(0)));

            $className = "post-tags";
            $nodes = $finder->query("//*[contains(@class, '$className')]");
            $tags = explode(',', $nodes->item(0)->nodeValue);
            $post->setTags($tags);

            $pm = new PostRenderer($post, $this->view);
            $renderedPost = $pm->render();

            // Building the tags cache.
            $this->tagsCache = array_merge($this->tagsCache, $post->getTags());

            // Building the posts cache.
            $this->postsCache[$fileInfo->getFilename()] = [
                'title' => $post->getTitle(),
                'content' => $renderedPost
                ];
        }
    }

    public function buildComments()
    {

    }

    public function save()
    {
        $tagsBlock = $this->view->render('tags', [
            'tags' => array_unique($this->tagsCache)
        ]);

        // Save each post page.
        foreach ($this->postsCache as $fileName => $post) {
            $renderedPostPage = $this->view->render('template',
                [
                    'title' => $post['title'],
                    'content' => $post['content'],
                    'tagsBlock' => $tagsBlock
                ]
            );
            $this->storage->write($this->config->get('publicPath') . $fileName, $renderedPostPage);
        }

        // Save index page.
        $renderedIndex = $this->view->render('index', [
            'posts' => $this->postsCache
        ]);

        $renderedIndexPage = $this->view->render('template',
            [
                'title' => null,
                'content' => $renderedIndex,
                'tagsBlock' => $tagsBlock
            ]
        );

        $this->storage->write($this->config->get('publicPath') . 'index.html', $renderedIndexPage);

        $this->saveTheme();

    }

    public function saveTheme()
    {
        $source = $this->config->get('privatePath') . 'themes/';
        $destination = $this->config->get('publicPath') . 'themes/';

        if (!is_dir($destination)) {
            mkdir($destination, 0755);
        }

        foreach (
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                if (!is_dir($destination . '/' . $item->getFilename())) {
                    mkdir($destination . '/' . $item->getFilename());
                }
            } else {
                if ($item->getExtension() != 'php') {
                    copy($item, $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                }
            }
        }
    }

}