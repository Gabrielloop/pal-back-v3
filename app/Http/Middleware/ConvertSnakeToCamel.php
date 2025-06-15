<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class ConvertSnakeToCamel
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (
            $response instanceof JsonResponse &&
            $response->headers->get('Content-Type') === 'application/json'
        ) {
            $original = $response->getOriginalContent();

            // Forcer conversion d'objets Eloquent/JsonResource en array
            if (is_object($original) && method_exists($original, 'toArray')) {
                $original = $original->toArray();
            }

            if (is_array($original)) {
                $camelCased = $this->convertKeysToCamelCase($original);
                $response->setData($camelCased);
            }
        }

        return $response;
    }

    private function convertKeysToCamelCase($value)
    {
        // Si c’est un modèle ou un objet avec `toArray`, on le convertit
        if (is_object($value)) {
            if (method_exists($value, 'toArray')) {
                $value = $value->toArray();
            } else {
                $value = (array) $value;
            }
        }

        if (is_array($value)) {
            $converted = [];

            foreach ($value as $key => $item) {
                $newKey = is_string($key) ? Str::camel($key) : $key;
                $converted[$newKey] = $this->convertKeysToCamelCase($item);
            }

            return $converted;
        }

        return $value;
    }
}
