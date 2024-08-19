<?php

namespace App\Pipelines;

use Closure;

class LikesFilter
{
    private $requestField;
    private $fields;

    public function __construct($requestField, $fields = [])
    {
        $this->requestField = $requestField;
        $this->fields = is_array($fields) ? $fields : [$fields];
    }

    public function handle($request, Closure $next)
    {
        if (!request()->has($this->requestField)) {
            return $next($request);
        }

        $query = $next($request);
        $searchTerm = '%' . request()->query($this->requestField) . '%';

        return $query->where(function ($query) use ($searchTerm) {
            foreach ($this->fields as $field) {
                $query->orWhere($field, 'LIKE', $searchTerm);
            }
        });
    }
}
