<?php

namespace App\Pipelines;

use Closure;

class WhereFilter
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
        if (!request()->has($this->requestField) || request()->get($this->requestField) == '')  {
            return $next($request);
        }

        return $next($request)->where($this->field ? $this->field : $this->requestField, request()->query($this->requestField));
    }
}
