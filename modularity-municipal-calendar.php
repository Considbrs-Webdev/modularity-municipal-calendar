<?php

/**
 * Plugin Name:       Modularity Municipal Calendar
 * Plugin URI:        https://github.com/helsingborg-stad/modularity-municipal-calendar
 * Description:       A municipal-calendar for creating Modularity modules.
 * Version: 1.0.0
 * Author:            Starter
 * Author URI:        https://github.com/helsingborg-stad
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       modularity-municipal-calendar
 * Domain Path:       /languages
 */

// Protect against direct file access
if (! defined('WPINC')) {
    die;
}

define('MODULARITYMUNICIPALCALENDAR_PATH', plugin_dir_path(__FILE__));
define('MODULARITYMUNICIPALCALENDAR_URL', plugins_url('', __FILE__));
define('MODULARITYMUNICIPALCALENDAR_MODULE_VIEW_PATH', plugin_dir_path(__FILE__) . 'source/php/Module/views');
define('MODULARITYMUNICIPALCALENDAR_MODULE_PATH', MODULARITYMUNICIPALCALENDAR_PATH . 'source/php/Module/');

// Load text domain
add_action('init', function () {
    load_plugin_textdomain('modularity-municipal-calendar', false, plugin_basename(dirname(__FILE__)) . '/languages');
});

// Autoload from plugin
if (file_exists(MODULARITYMUNICIPALCALENDAR_PATH . 'vendor/autoload.php')) {
    require_once MODULARITYMUNICIPALCALENDAR_PATH . 'vendor/autoload.php';
}

// ACF auto import and export
add_action('acf/init', function () {
    $acfExportManager = new \AcfExportManager\AcfExportManager();
    $acfExportManager->setTextdomain('modularity-municipal-calendar');
    $acfExportManager->setExportFolder(MODULARITYMUNICIPALCALENDAR_PATH . 'source/php/AcfFields/');
    $acfExportManager->autoExport(array(
        'municipal-calendar-module' => 'group_municipal-calendar_module',
        'post-data'                 => 'group_municipal_event_post_data',
        'taxonomy-settings'         => 'group_municipal_event_taxonomy_settings',
        'general-settings'          => 'group_municipal_calendar_general_settings',
    ));
    $acfExportManager->import();
});

// Modularity 3.0 ready - ViewPath for Component library
add_filter('/Modularity/externalViewPath', function ($arr) {
    $arr['mod-municipal-calendar'] = MODULARITYMUNICIPALCALENDAR_MODULE_VIEW_PATH;
    return $arr;
}, 10, 3);

// Flush rewrite rules on plugin activation
register_activation_hook(__FILE__, function () {
    // Load autoloader first
    if (file_exists(MODULARITYMUNICIPALCALENDAR_PATH . 'vendor/autoload.php')) {
        require_once MODULARITYMUNICIPALCALENDAR_PATH . 'vendor/autoload.php';
    }
    
    // Register post type and taxonomies
    if (class_exists('ModularityMunicipalCalendar\PostType\MunicipalEvent')) {
        $postType = new ModularityMunicipalCalendar\PostType\MunicipalEvent();
        $postType->registerPostType();
        $postType->registerTaxonomies();
    }
    
    flush_rewrite_rules();
});

// Flush rewrite rules on plugin deactivation
register_deactivation_hook(__FILE__, function () {
    flush_rewrite_rules();
});

// Start application
new ModularityMunicipalCalendar\App();

