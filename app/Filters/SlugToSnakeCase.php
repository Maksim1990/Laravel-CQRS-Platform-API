<?php
namespace App\Filters;

use Closure;

class SlugToSnakeCase
{
    public function handle($request, Closure $next)
    {
        if(isset($request['slug'])) {
            $request['slug']= str_replace('_', '-', $request['slug']);
        }

        return  $next($request);
    }
}
