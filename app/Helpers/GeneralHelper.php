<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

function is_active_submenu(string|array $route): string
{
    $activeClass = ' submenu-open';

    if (is_string($route)) {
        if (request()->is(substr($route . '*', 1))) {
            return $activeClass;
        }

        if (request()->is(str($route)->slug() . '*')) {
            return $activeClass;
        }

        if (request()->segment(2) === str($route)->before('/')) {
            return $activeClass;
        }

        if (request()->segment(3) === str($route)->after('/')) {
            return $activeClass;
        }
    }

    if (is_array($route)) {
        foreach ($route as $value) {
            $actualRoute = str($value)->remove(' view')->plural();

            if (request()->is(substr($actualRoute . '*', 1))) {
                return $activeClass;
            }

            if (request()->is(str($actualRoute)->slug() . '*')) {
                return $activeClass;
            }

            if (request()->segment(2) === $actualRoute) {
                return $activeClass;
            }

            if (request()->segment(3) === $actualRoute) {
                return $activeClass;
            }
        }
    }

    return '';
}

function get_setting()
{
    return DB::table('setting')->first();
}

function encryptShort($string)
{
    return str_replace('=', '', base64_encode(gzcompress($string, 9)));
}


function decryptShort($string)
{
    try {
        $decoded = base64_decode($string, true);

        if ($decoded === false) {
            abort(404);
        }

        $uncompressed = @gzuncompress($decoded);

        if ($uncompressed === false) {
            abort(404);
        }

        return $uncompressed;
    } catch (\Exception $e) {
        abort(404);
    }
}
