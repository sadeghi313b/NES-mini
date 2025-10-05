<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait CookieHelper
{
    protected function getPaginationCookie(Request $request = null)
    {
        $cookiePagination = $_COOKIE['pagination'] ?? null;
        $cookiePagination = json_decode($cookiePagination, true);

        return [
            'page' => $cookiePagination['page'] ?? null,
            'perPage' => $cookiePagination['perPage'] ?? null,
        ];
    }

    protected function getCriteriaCookie()
    {
        $decode = $_COOKIE['criteria'] ?? null;
        if ($decode) {
            $decode = json_decode($decode, true);
            if (is_array($decode)) {
                array_walk_recursive($decode, function (&$val) {
                    $val = strip_tags((string) $val);
                });
            }
        }
        return $decode;
    }
}