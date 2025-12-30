<?php

namespace ModularityMunicipalCalendar;

use ModularityMunicipalCalendar\Helper\CacheBust;
use ModularityMunicipalCalendar\PostType\MunicipalEvent;
use ModularityMunicipalCalendar\Module\MunicipalCalendar;
use ModularityMunicipalCalendar\Admin\Settings;

/**
 * Class App
 * 
 * Main application bootstrap class.
 * Initialize your plugin components here.
 * 
 * @package ModularityMunicipalCalendar
 */
class App
{
    public function __construct()
    {
        // Initialize settings page
        new Settings();

        // Initialize custom post type
        new MunicipalEvent();

        // Register module with Modularity
        add_action('init', [$this, 'registerModule']);

        // Enqueue styles
        add_action('wp_enqueue_scripts', [$this, 'enqueueStyles']);

        // Remove advanced term settings for our taxonomies
        add_filter('acf/load_field_group', [$this, 'removeAdvancedTermSettings']);
    }

    /**
     * Enqueue styles
     * 
     * @return void
     */
    public function enqueueStyles(): void
    {
        $styleFile = CacheBust::name('css/modularity-municipal-calendar.css');

        if ($styleFile) {
            wp_enqueue_style(
                'modularity-municipal-calendar',
                MODULARITYMUNICIPALCALENDAR_URL . '/assets/dist/' . $styleFile,
                [],
                null
            );
        }
    }

    /**
     * Register the module with Modularity
     * 
     * @return void
     */
    public function registerModule(): void
    {
        if (function_exists('modularity_register_module')) {
            modularity_register_module(
                MODULARITYMUNICIPALCALENDAR_MODULE_PATH,
                'MunicipalCalendar',
            );
        }
    }

    /**
     * Remove Municipio's advanced term settings for our taxonomies
     * 
     * @param array $field_group The ACF field group
     * @return array|false The field group or false to remove it
     */
    public function removeAdvancedTermSettings($field_group)
    {
        // Target Municipio's advanced term settings field group
        if ($field_group['key'] !== 'group_63e6002cc129c') {
            return $field_group;
        }

        // Only on taxonomy edit screens
        if (!is_admin() || empty($_GET['taxonomy'])) {
            return $field_group;
        }

        $taxonomy = sanitize_text_field($_GET['taxonomy']);

        // Disable for our taxonomies
        $our_taxonomies = ['event_place', 'event_administration', 'event_type'];
        if (in_array($taxonomy, $our_taxonomies)) {
            return false; // Remove the field group completely
        }

        return $field_group;
    }
}
