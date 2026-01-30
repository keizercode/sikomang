<?php

if (!function_exists('encode_id')) {
    /**
     * Encode ID untuk keamanan
     */
    function encode_id($id)
    {
        if (empty($id)) {
            return null;
        }
        return base64_encode($id * 12345);
    }
}

if (!function_exists('decode_id')) {
    /**
     * Decode ID
     */
    function decode_id($encoded)
    {
        if (empty($encoded)) {
            return null;
        }
        return base64_decode($encoded) / 12345;
    }
}

if (!function_exists('dateTime')) {
    /**
     * Format datetime
     */
    function dateTime($date, $format = 'd M Y H:i')
    {
        if (empty($date)) {
            return '-';
        }
        return date($format, strtotime($date));
    }
}

if (!function_exists('formatBytes')) {
    /**
     * Format bytes to human readable
     */
    function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('setting')) {
    /**
     * Get setting value
     */
    function setting($key, $default = null)
    {
        // TODO: Implement settings table
        return $default;
    }
}

if (!function_exists('hasAccess')) {
    /**
     * Check if user has access to module
     */
    function hasAccess($module, $permission = 'is_read')
    {
        // TODO: Implement access control
        return true;
    }
}
