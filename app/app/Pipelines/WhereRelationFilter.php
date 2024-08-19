<?php

namespace App\Pipelines;

use Closure;

class WhereRelationFilter
{
    protected $requestField;
    protected $field;
    protected $relation;

    public function __construct($requestField, $relation, $field = null)
    {
        $this->requestField = $requestField;
        $this->field = $field;
        $this->relation = $relation;
    }

    public function handle($request, Closure $next)
    {
        if (!request()->has($this->requestField) || request()->get($this->requestField) == '')
            return $next($request);

        return $next($request)->whereRelation($this->relation, $this->field ? $this->field : $this->requestField, request()->query($this->requestField));
    }
}
