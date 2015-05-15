<?php
namespace FlatSite;

use FlatSite\Storage\StorageInterface;

class View
{
    public function __construct(Config $config, StorageInterface $storage)
    {
        $this->config = $config;
        $this->storage = $storage;
    }


    public function render($entityName, array $view)
    {
        extract($view);
        ob_start();
        include $this->config->get('privatePath') . 'themes/default/'. $entityName .'.html.php';
        $renderedView = ob_get_clean();
        return $renderedView;
    }
}