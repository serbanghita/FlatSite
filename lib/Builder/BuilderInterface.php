<?php
namespace FlatSite\Builder;

use FlatSite\Config;
use FlatSite\Storage\StorageInterface;
use FlatSite\View;

interface BuilderInterface
{
    public function __construct(Config $config, StorageInterface $storage, View $view);
    public function buildIndex();
    public function buildPostsAndTags();
    public function buildComments();
    public function save();
}