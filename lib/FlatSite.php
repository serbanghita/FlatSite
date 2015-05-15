<?php
namespace FlatSite;

use FlatSite\Builder\BuilderInterface;

class FlatSite
{
    protected $builder;

    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    public function build()
    {
        $this->builder->buildPostsAndTags();
        $this->builder->buildIndex();
        $this->builder->save();
    }
}