<?php

namespace NekoOs\Resource;

use Illuminate\Support\Arr;

class HeaderLinkTransform
{
    protected $key = 'links';
    protected $join = false;
    
    public static function using($key, $join = null)
    {
        $self = new static();
        
        $self->key = $key;
        $self->join = isset($self->join) ? $self->join : $join;
        
        return $self;
    }
    
    public function __invoke(&$output)
    {
        $links = [];
        $items = Arr::pull($output, $this->key, []);
        
        foreach ($items as $key => $value) {
            $links[] = $this->format($key, $value);
        }
        
        return $this->join ? implode(',', $links) : $links;
    }
    
    /**
     * @param $key
     * @param $value
     *
     * @return string
     */
    protected function format($key, $value)
    {
        return sprintf('<%s>; rel="%s"', $value, $key);
    }
}