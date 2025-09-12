<?php
// app/Helpers/AlertHelper.php

namespace App\Helpers;

class AlertHelper
{
    public static function alert(string $message, string|null $redirectUrl)
    {
        $script = "<script>alert('" . addslashes($message) . "');";
        
        if ($redirectUrl) {
            $script .= "window.location.href = '" . $redirectUrl . "';";
        } else {
            $script .= "window.history.back();";
        }
        
        $script .= "</script>";
        
        return response($script);
    }
}