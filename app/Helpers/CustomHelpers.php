<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;








/* -------------------------------------------------------------------------- */
/*                     explode string  if delimetyer exist                    */
/* -------------------------------------------------------------------------- */
if (!function_exists('subString')) {

    function subString(string $string, string $delimiter = ",",int $index=0): string
    {
        if (str_contains($string, $delimiter)) {
            $parts = explode($delimiter, $string);
            $index = $index > count($parts) - 1 ? count($parts) - 1 : $index;
            return trim($parts[$index]);
        }

        return trim($string);
    }
}




/* -------------------------------------------------------------------------- */
/*                                     Log                                    */
/* -------------------------------------------------------------------------- */
if (!function_exists('clg')) {
    /**
     * Clear log file and write new logs
     */
    function clg(...$datas): void
    {
        // Clear log file before writing
        $logPath = storage_path('logs/laravel.log');
        file_put_contents($logPath, "");

        try {
            foreach ($datas as $data) {
                $original = $data;

                // Convert Collection to array
                if ($data instanceof Collection) {
                    $data = $data->toArray();
                }

                // If array or object
                if (is_array($data) || is_object($data)) {
                    foreach ($data as $key => $item) {
                        Log::info(
                            gettype($original) . ": {$key} => " .
                            json_encode($item, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
                        );
                    }
                } else {
                    // If simple value
                    Log::info($data);
                }

                Log::info("===============================================================");
            }
        } catch (\Throwable $th) {
            Log::info('error in logging data');
        }
    }
}



/* -------------------------------------------------------------------------- */
/*                                getEnumValues                               */
/* -------------------------------------------------------------------------- */
if (!function_exists('getEnumValues'))
{
    /**
     * Get enum values from database column
     *
     * @param string $table
     * @param string $column
     * @return array
     */
    function getEnumValues(string $table, string $column): array
    {
        // Run SQL to get column type
        $type = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = '{$column}'")[0]->Type;

        // Extract values from enum('...')
        preg_match("/^enum\('(.*)'\)$/", $type, $matches);

        if (!isset($matches[1])) {
            return [];
        }

        // Convert to array of [label, value]
        return collect(explode("','", $matches[1]))
            ->map(fn($value) => [
                'label' => ucfirst($value),
                'value' => $value,
            ])
            ->toArray();
    }
}


/* -------------------------------------------------------------------------- */
/*                             dd Inertia Response                            */
/* -------------------------------------------------------------------------- */

use Inertia\Response as InertiaResponse;

if (!function_exists('dd_inertia')) {
    /**
     * Dump Inertia payload for debugging.
     *
     * @param \Inertia\Response $response
     * @param string|null $key   Nested key path inside props (optional).
     */
    function dd_inertia(InertiaResponse $response, string $key = null)
    {
        // Force Inertia headers so response content is JSON
        request()->headers->set('X-Inertia', true);

        // Convert Inertia\Response to HTTP response
        $http = $response->toResponse(request());

        // Decode JSON payload
        $payload = json_decode($http->getContent(), true);

        // If user passed a nested key (e.g. "orders"), extract it
        if ($key !== null) {
            $segments = explode('.', $key);
            $value = $payload['props'];

            foreach ($segments as $segment) {
                if (isset($value[$segment])) {
                    $value = $value[$segment];
                } else {
                    $value = null;
                    break;
                }
            }

            dd($value);
        }

        // Otherwise whole payload
        clg($payload);
        dd($payload);
    }
}

