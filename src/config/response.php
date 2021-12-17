<?php

use NekoOs\Resource\HeaderLinkTransform;
use NekoOs\Resource\HeaderPaginationLinkTransform;

return [
    'wrapping'   => false,
    'pagination' => [
        'page_name'      => '_page',
        'preserve_query' => true,
        'headers'        => [
            'Links'                   => HeaderLinkTransform::class,
            'Pagination-Links'        => HeaderPaginationLinkTransform::class,
            'Pagination-Path'         => 'meta.path',
            'Pagination-Current-Page' => 'meta.current_page',
            'Pagination-Per-Page'     => 'meta.per_page',
            'Pagination-Total-Count'  => 'meta.total',
            'Pagination-Last-Page'    => 'meta.last_page',
            'Pagination-First-Record' => 'meta.from',
            'Pagination-Last-Record'  => 'meta.to',
            'Pagination-Page-Count'   => static function ($output) {
                return count($output['data']);
            },
        ],
    ],
];
