<?php

namespace App\Utils\Helpers;

class SessionHelper
{
    public static function getDeviceType($userAgent)
    {
        if (stripos($userAgent, 'mobile') !== false) {
            return 'mobile';
        } elseif (stripos($userAgent, 'tablet') !== false) {
            return 'tablet';
        }
        return 'desktop';
    }

    public static function getDeviceName($userAgent)
    {
        $userAgent = strtolower($userAgent);

        if (str_contains($userAgent, 'android')) return 'Android Device';
        if (str_contains($userAgent, 'iphone')) return 'iPhone';
        if (str_contains($userAgent, 'ipad')) return 'iPad';
        if (str_contains($userAgent, 'windows')) return 'Windows PC';
        if (str_contains($userAgent, 'macintosh') || str_contains($userAgent, 'mac os')) return 'Mac';
        if (str_contains($userAgent, 'linux')) return 'Linux Machine';

        return 'Unknown Device';
    }

    public static function getBrowserName($userAgent)
    {
        if (stripos($userAgent, 'chrome') !== false) return 'Chrome';
        if (stripos($userAgent, 'firefox') !== false) return 'Firefox';
        if (stripos($userAgent, 'safari') !== false && stripos($userAgent, 'chrome') === false) return 'Safari';
        if (stripos($userAgent, 'edge') !== false) return 'Edge';
        if (stripos($userAgent, 'msie') !== false || stripos($userAgent, 'trident') !== false) return 'Internet Explorer';
        return 'Unknown Browser';
    }
}
