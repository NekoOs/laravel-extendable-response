<?php

namespace NekoOs\Pagination;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

use function config;
use function request;

class PaginationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $config = config('response', []);
        
        if (isset($config['wrapping']) && !$config['wrapping']) {
            JsonResource::withoutWrapping();
        }
        
        $config = isset($config['pagination']) ? $config['pagination'] : [];
    
        /**
         * @param AbstractPaginator $paginator
         *
         * @return AbstractPaginator
         */
        $callback = static function ($paginator) use ($config) {
            $name = isset($config['page_name']) ? $config['page_name'] : 'page';
            $query = empty($config['preserve_query']) ? [] : request()->query();
        
            return $paginator
                ->setPageName($name)
                ->appends($query);
        };
    
        $this->app->resolving(LengthAwarePaginator::class, $callback);
        $this->app->resolving(Paginator::class, $callback);
    }
}
