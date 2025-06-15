<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class ConvertCamelToSnake
{
    public function handle($request, Closure $next)
    {
        if ($request->isJson() && $request->getContent()) {
            $data = json_decode($request->getContent(), true);

            if (is_array($data)) {
                $snakeCased = $this->convertKeysToSnakeCase($data);
                $request->replace($snakeCased);
            }
        }

        return $next($request);
    }

    private function convertKeysToSnakeCase($value)
    {
        if (is_array($value)) {
            $converted = [];

            foreach ($value as $key => $item) {
                $newKey = is_string($key) ? Str::snake($key) : $key;
                $converted[$newKey] = $this->convertKeysToSnakeCase($item);
            }

            return $converted;
        }

        return $value;
    }
}
