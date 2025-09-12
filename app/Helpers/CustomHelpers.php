<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;







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