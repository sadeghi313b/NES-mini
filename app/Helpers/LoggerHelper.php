<?php

namespace App\Helpers;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class LoggerHelper
{
    /**
     * Clear the log file and write new log
     *
     * @param string $title
     * @param mixed $data
     * @return void
     */
    public static function clgTemp(...$datas): void
    {
        // clear log file
        $logPath = storage_path('logs/laravel.log');
        file_put_contents($logPath, "");



        try {

            foreach ($datas as $data) {
                $data0 = $data;
                if ($data instanceof Collection) {
                    $data = $data->toArray();
                }

                // اگر آرایه یا آبجکت بود
                if (is_array($data) || is_object($data)) {
                    foreach ($data as $key => $item) {
                        Log::info(gettype($data0) . ": {$key} => " . json_encode($item, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                        // Log::info("------------------------------------------------------------");
                        // Log::info(gettype($data0) . ": {$key} => " . $item);
                        // Log::info("------------------------------------------------------------");
                    }
                } else {
                    // اگر مقدار ساده بود
                    Log::info($data);
                }
                Log::info("===============================================================");
            }
        } catch (\Throwable $th) {
            Log::info('error in logging data');
        }
    }
}


/*
use App\Helpers\Logger;
use Illuminate\Support\Collection;

مقدار ساده
Logger::clg("Single value", "Hello World");

آرایه
Logger::clg("Array data", ["id" => 1, "name" => "Admin"]);

Collection
Logger::clg("Collection data", collect([['id' => 1], ['id' => 2]]));

آبجکت
$user = (object)["id" => 1, "name" => "Test"];
Logger::clg("Object data", $user);
*/