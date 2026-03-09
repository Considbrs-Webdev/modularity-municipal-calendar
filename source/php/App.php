<?php

namespace ModularityMunicipalCalendar;

use ModularityMunicipalCalendar\Helper\CacheBust;
use ModularityMunicipalCalendar\PostDecorators\ApplyMunicipalEventData;
use ModularityMunicipalCalendar\PostType\MunicipalEvent;
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

        // Enqueue styles
        add_action('wp_enqueue_scripts', [$this, 'enqueueStyles']);

        // Remove advanced term settings for our taxonomies
        add_filter('acf/load_field_group', [$this, 'removeAdvancedTermSettings']);

        // Add view paths for custom templates (single/archive pages)
        add_filter('Municipio/viewPaths', [$this, 'addViewPaths'], 999);

        // Add our view path to Component Library Blade (used by PostsList async pagination and block render)
        add_filter('ComponentLibrary/ViewPaths', [$this, 'addComponentLibraryViewPaths'], 9999, 1);

        // Override Posts module view path to include our custom card template
        add_filter('/Modularity/externalViewPath', [$this, 'addPostsModuleViewPath']);

        // Typesense search integration
        new TypesenseSearchIntegration();

        add_filter('Municipio/DecoratePostObject', function ($postObject) {
            if (!method_exists($postObject, 'getPostType') || $postObject->getPostType() !== 'municipal_event') {
                return $postObject;
            }

            // Get the underlying WP_Post ID
            $postId = $postObject->getId();
            $wpPost = get_post($postId);

            if (!$wpPost || $wpPost->post_type !== 'municipal_event') {
                return $postObject;
            }

            // Apply decorator to WP_Post
            $decorator = new ApplyMunicipalEventData();
            $decoratedPost = $decorator->apply($wpPost);

            // Copy municipalEventData to PostObjectInterface
            // PostObjectInterface supports dynamic properties via __get/__set
            if (isset($decoratedPost->municipalEventData)) {
                $postObject->municipalEventData = $decoratedPost->municipalEventData;
            }

            return $postObject;
        }, 10, 1);
    }

    /**
     * Add plugin view paths to Municipio for custom templates
     *
     * @param array $paths The existing view paths
     * @return array The modified view paths
     */
    public function addViewPaths(array $paths): array
    {
        if ($this->isMunicipalEventContext()) {
            $paths[] = MODULARITYMUNICIPALCALENDAR_PATH . 'views';
        }
        return $paths;
    }

    /**
     * Determine whether the current request should use plugin templates.
     * 
     * Safe to call even before WordPress query is initialized.
     *
     * @return bool
     */
    private function isMunicipalEventContext(): bool
    {
        // Check if query is ready - if so, use get_query_var()
        if (did_action('wp') || !empty($GLOBALS['wp_query'])) {
            $queriedPostType = get_query_var('post_type');
            if ($queriedPostType === 'municipal_event') {
                return true;
            }
            if (is_post_type_archive('municipal_event') || is_singular('municipal_event') || is_search()) {
                return true;
            }
        }

        // Check REST API / AJAX context via request parameters
        // PostsList AJAX passes postType in the query parameters (set by exposePostTypeFromRestAttributes)
        if (!empty($_GET['postType']) && $_GET['postType'] === 'municipal_event') {
            return true;
        }
        if (!empty($_POST['postType']) && $_POST['postType'] === 'municipal_event') {
            return true;
        }
        // For archive pages on initial load: check URL path
        if (!empty($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'municipal_event') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Add plugin view path to Component Library Blade view paths.
     * Only in municipal_event context so we don't override simpleview (or other) post cards.
     *
     * @param array $paths Paths from BladeServiceFactory (internal + external, e.g. PostsList)
     * @return array Modified paths
     */
    public function addComponentLibraryViewPaths(array $paths): array
    {
        if (!$this->isMunicipalEventContext()) {
            return $paths;
        }

        $ourPath = rtrim(MODULARITYMUNICIPALCALENDAR_PATH . 'views', DIRECTORY_SEPARATOR);
        if (is_dir($ourPath)) {
            array_unshift($paths, $ourPath . DIRECTORY_SEPARATOR);
        }
        return $paths;
    }

    /**
     * Add our view path to the Posts module view paths
     *
     * @param array $externalViewPaths Module post_type => view path mapping
     * @return array Modified mapping
     */
    public function addPostsModuleViewPath(array $externalViewPaths): array
    {
        if (!$this->isMunicipalEventContext()) {
            return $externalViewPaths;
        }

        if (!defined('MODULARITY_PATH')) {
            return $externalViewPaths;
        }

        // Get the original Posts module view path
        $postsModuleViewPath = MODULARITY_PATH . 'source/php/Module/Posts/views';

        // Return array with our path last (will be prepended last = checked first)
        $externalViewPaths['mod-posts'] = [
            $postsModuleViewPath,
            MODULARITYMUNICIPALCALENDAR_PATH . 'views',
        ];

        return $externalViewPaths;
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
