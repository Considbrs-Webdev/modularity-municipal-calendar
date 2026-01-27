<?php

namespace ModularityMunicipalCalendar\Helper;

/**
 * Class Utils
 * 
 * Example helper class demonstrating how to add utility functions
 * to your Modularity plugin.
 * 
 * @package ModularityMunicipalCalendar\Helper
 */
class Utils
{
    /**
     * Example: Sanitize and format a string
     *
     * @param string $string
     * @return string
     */
    public static function sanitizeString(string $string): string
    {
        return sanitize_text_field($string);
    }

    /**
     * Example: Get a plugin option with default fallback
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getOption(string $key, $default = null)
    {
        return get_option('modularity_municipal-calendar_' . $key, $default);
    }
}
