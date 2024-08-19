<?php

namespace App\Pipelines;

use Closure;

class LikeFilter
{
    private $requestField;
    private $field;

    public function __construct($requestField, $field = null)
    {
        $this->requestField = $requestField;
        $this->field = $field;
    }

    public function handle($request, Closure $next)
    {
        if (!request()->has($this->requestField)) {
            return $next($request);
        }

        return $next($request)->where($this->field ? $this->field : $this->requestField, 'LIKE', '%' . request()->query($this->requestField) . '%');
    }
}
