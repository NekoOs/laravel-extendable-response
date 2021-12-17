<?php

namespace NekoOs\Resource;

class HeaderPaginationLinkTransform extends HeaderLinkTransform
{
    protected $key = 'meta.links';
    
    protected function format($key, $value)
    {
        return sprintf('<%s>; rel="index"; title="%s"', $value['url'], $value['label']);
    }
}