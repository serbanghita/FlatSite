<?php
require_once 'Config.php';
require_once 'Storage/StorageInterface.php';
require_once 'Storage/FileSystem.php';
require_once 'View.php';
require_once 'Node/Post.php';
require_once 'Node/PostRenderer.php';
require_once 'Builder/BuilderInterface.php';
require_once 'Builder/Builder.php';
require_once 'FlatSite.php';

use FlatSite\FlatSite;
use FlatSite\Config;
use FlatSite\Storage\FileSystem;
use FlatSite\View;
use FlatSite\Builder\Builder;

$config = new Config([
    'privatePath' => dirname(__FILE__) . '/../example/private/',
    'privatePostsPath' => dirname(__FILE__) . '/../example/private/posts/',
    'publicPath' => dirname(__FILE__) . '/../example/public/'
]);
$storage = new FileSystem($config);
$view = new View($config, $storage);
$builder = new Builder($config, $storage, $view);
$fs = new FlatSite($builder);
$fs->build();
