<?php

namespace Illuminate\Http\Resources\Json;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

/**
 * @property-read AnonymousResourceCollection $resource
 */
class PaginatedResourceResponse extends ResourceResponse
{
    protected $headers = [];
    
    /**
     * @inheritDoc
     */
    public function toResponse($request)
    {
        $response = response();
        $data = $this->wrap(
            $this->resource->resolve($request),
            array_merge_recursive(
                $this->paginationInformation($request),
                $this->resource->with($request),
                $this->resource->additional
            )
        );
        
        return tap($response->json($data, $this->calculateStatus()), function ($response) use ($request) {
            /** @var JsonResponse $response */
            $response->original = $this->resource->resource->map(function ($item) {
                return is_array($item) ? Arr::get($item, 'resource') : $item->resource;
            });
            $response->withHeaders($this->headers);
            $this->resource->withResponse($request, $response);
        });
    }
    
    protected function meta($paginated)
    {
        return Arr::except($paginated, [
            'data',
            'first_page_url',
            'last_page_url',
            'prev_page_url',
            'next_page_url',
        ]);
    }
    
    protected function paginationInformation($request)
    {
        $paginated = $this->resource->preserveQuery()->resource->toArray();
        
        $maps = config('response.pagination.headers', []);
        
        $output = [
            'links' => $this->paginationLinks($paginated),
            'meta'  => $this->meta($paginated),
            'data'  => $paginated['data'],
        ];
        
        foreach ($maps as $key => $map) {
            if ($map instanceof Closure) {
                $this->headers[$key] = $map($output, $request);
            } elseif (class_exists($map)) {
                $transform = new $map;
                $this->headers[$key] = $transform($output, $request);
            } else {
                $this->headers[$key] = Arr::pull($output, $map);
            }
        }
        
        unset($output['data']);
        
        return array_filter($output, function ($item) {
            return count($item);
        });
    }
    
    protected function paginationLinks($paginated)
    {
        return [
            'first' => isset($paginated['first_page_url']) ? $paginated['first_page_url'] : null,
            'last'  => isset($paginated['last_page_url']) ? $paginated['last_page_url'] : null,
            'prev'  => isset($paginated['prev_page_url']) ? $paginated['prev_page_url'] : null,
            'next'  => isset($paginated['next_page_url']) ? $paginated['next_page_url'] : null,
        ];
    }
}
