<?php

namespace ModularityMunicipalCalendar\PostType;

/**
 * Class MunicipalEvent
 * 
 * Registers the Municipal Event custom post type and its taxonomies.
 * 
 * @package ModularityMunicipalCalendar\PostType
 */ 
class MunicipalEvent
{
    public function __construct()
    {
        // Use acf/init so ACF options (display_name, slug) are available when registering.
        // Priority 20 ensures AcfExportManager has imported field groups (runs at 10).
        if (function_exists('acf_add_local_field_group')) {
            if (did_action('acf/init')) {
                $this->registerPostType();
                $this->registerTaxonomies();
            } else {
                add_action('acf/init', [$this, 'registerPostType'], 20);
                add_action('acf/init', [$this, 'registerTaxonomies'], 21);
            }
        } else {
            add_action('init', [$this, 'registerPostType'], 20);
            add_action('init', [$this, 'registerTaxonomies'], 21);
        }
    }

    /**
     * Register the Municipal Event custom post type
     * 
     * @return void
     */
    public function registerPostType(): void
    {
        $slug = get_field('slug', 'municipal-calendar-settings') ?: 'municipal-event';
        
        // Get display name from settings (for frontend: archive title, breadcrumbs)
        $displayName = get_field('display_name', 'municipal-calendar-settings');
        $pluralName = !empty($displayName) ? $displayName : __('Municipal Events', 'modularity-municipal-calendar');
        $singularName = !empty($displayName) ? $displayName : __('Municipal Event', 'modularity-municipal-calendar');
        
        $labels = [
            // Frontend labels (use custom display name)
            'name'                  => $pluralName,
            'singular_name'         => $singularName,
            'archives'              => $pluralName,
            
            // Admin labels (keep fixed for plugin identity)
            'menu_name'             => __('Municipal Events', 'modularity-municipal-calendar'),
            'name_admin_bar'        => __('Municipal Event', 'modularity-municipal-calendar'),
            'add_new'               => __('Add New', 'modularity-municipal-calendar'),
            'add_new_item'          => __('Add New Municipal Event', 'modularity-municipal-calendar'),
            'new_item'              => __('New Municipal Event', 'modularity-municipal-calendar'),
            'edit_item'             => __('Edit Municipal Event', 'modularity-municipal-calendar'),
            'view_item'             => __('View Municipal Event', 'modularity-municipal-calendar'),
            'all_items'             => __('All Municipal Events', 'modularity-municipal-calendar'),
            'search_items'          => __('Search Municipal Events', 'modularity-municipal-calendar'),
            'parent_item_colon'     => __('Parent Municipal Event:', 'modularity-municipal-calendar'),
            'not_found'             => __('No municipal events found.', 'modularity-municipal-calendar'),
            'not_found_in_trash'    => __('No municipal events found in Trash.', 'modularity-municipal-calendar'),
            'featured_image'        => __('Featured Image', 'modularity-municipal-calendar'),
            'set_featured_image'    => __('Set featured image', 'modularity-municipal-calendar'),
            'remove_featured_image' => __('Remove featured image', 'modularity-municipal-calendar'),
            'use_featured_image'    => __('Use as featured image', 'modularity-municipal-calendar'),
            'insert_into_item'      => __('Insert into municipal event', 'modularity-municipal-calendar'),
            'uploaded_to_this_item' => __('Uploaded to this municipal event', 'modularity-municipal-calendar'),
            'filter_items_list'     => __('Filter municipal events list', 'modularity-municipal-calendar'),
            'items_list_navigation' => __('Municipal events list navigation', 'modularity-municipal-calendar'),
            'items_list'            => __('Municipal events list', 'modularity-municipal-calendar'),
        ];

        $args = [
            'labels'             => $labels,
            'description'        => __('Municipal calendar events', 'modularity-municipal-calendar'),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [
                'slug' => $slug,
                'with_front' => false,
            ],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-calendar-alt',
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
            'show_in_rest'       => true,
        ];

        register_post_type('municipal_event', $args);
    }

    /**
     * Register the taxonomies for Municipal Events
     * 
     * @return void
     */
    public function registerTaxonomies(): void
    {
        // Register Place taxonomy
        $this->registerTaxonomy(
            'event_place',
            'event_places',
            __('Place', 'modularity-municipal-calendar'),
            __('Places', 'modularity-municipal-calendar'),
            __('Place', 'modularity-municipal-calendar'),
            __('Places', 'modularity-municipal-calendar'),
            'event-place'
        );

        // Register Administration taxonomy
        $this->registerTaxonomy(
            'event_administration',
            'event_administrations',
            __('Administration', 'modularity-municipal-calendar'),
            __('Administrations', 'modularity-municipal-calendar'),
            __('Förvaltning', 'modularity-municipal-calendar'),
            __('Förvaltningar', 'modularity-municipal-calendar'),
            'event-administration'
        );

        // Register Type taxonomy
        $this->registerTaxonomy(
            'event_type',
            'event_types',
            __('Type', 'modularity-municipal-calendar'),
            __('Types', 'modularity-municipal-calendar'),
            __('Typ', 'modularity-municipal-calendar'),
            __('Typer', 'modularity-municipal-calendar'),
            'event-type'
        );
    }

    /**
     * Register a single taxonomy
     * 
     * @param string $taxonomy_slug The taxonomy slug
     * @param string $taxonomy_plural The plural form for labels
     * @param string $singular_label Singular label
     * @param string $plural_label Plural label
     * @param string $singular_label_sv Swedish singular label
     * @param string $plural_label_sv Swedish plural label
     * @param string $rewrite_slug Rewrite slug
     * @return void
     */
    private function registerTaxonomy(
        string $taxonomy_slug,
        string $taxonomy_plural,
        string $singular_label,
        string $plural_label,
        string $singular_label_sv,
        string $plural_label_sv,
        string $rewrite_slug
    ): void {
        $labels = [
            'name'                       => $plural_label,
            'singular_name'              => $singular_label,
            'menu_name'                  => $plural_label,
            'all_items'                  => sprintf(
                /* translators: %s: Taxonomy label (plural). */
                __('All %s', 'modularity-municipal-calendar'),
                $plural_label
            ),
            'parent_item'                => sprintf(
                /* translators: %s: Taxonomy label (singular). */
                __('Parent %s', 'modularity-municipal-calendar'),
                $singular_label
            ),
            'parent_item_colon'          => sprintf(
                /* translators: %s: Taxonomy label (singular). */
                __('Parent %s:', 'modularity-municipal-calendar'),
                $singular_label
            ),
            'new_item_name'              => sprintf(
                /* translators: %s: Taxonomy label (singular). */
                __('New %s Name', 'modularity-municipal-calendar'),
                $singular_label
            ),
            'add_new_item'               => sprintf(
                /* translators: %s: Taxonomy label (singular). */
                __('Add New %s', 'modularity-municipal-calendar'),
                $singular_label
            ),
            'edit_item'                  => sprintf(
                /* translators: %s: Taxonomy label (singular). */
                __('Edit %s', 'modularity-municipal-calendar'),
                $singular_label
            ),
            'update_item'                => sprintf(
                /* translators: %s: Taxonomy label (singular). */
                __('Update %s', 'modularity-municipal-calendar'),
                $singular_label
            ),
            'view_item'                  => sprintf(
                /* translators: %s: Taxonomy label (singular). */
                __('View %s', 'modularity-municipal-calendar'),
                $singular_label
            ),
            'separate_items_with_commas' => sprintf(
                /* translators: %s: Taxonomy label (plural, lowercase). */
                __('Separate %s with commas', 'modularity-municipal-calendar'),
                strtolower($plural_label)
            ),
            'add_or_remove_items'        => sprintf(
                /* translators: %s: Taxonomy label (plural, lowercase). */
                __('Add or remove %s', 'modularity-municipal-calendar'),
                strtolower($plural_label)
            ),
            'choose_from_most_used'      => __('Choose from the most used', 'modularity-municipal-calendar'),
            'popular_items'              => sprintf(
                /* translators: %s: Taxonomy label (plural). */
                __('Popular %s', 'modularity-municipal-calendar'),
                $plural_label
            ),
            'search_items'               => sprintf(
                /* translators: %s: Taxonomy label (plural). */
                __('Search %s', 'modularity-municipal-calendar'),
                $plural_label
            ),
            'not_found'                  => __('Not Found', 'modularity-municipal-calendar'),
            'no_terms'                   => sprintf(
                /* translators: %s: Taxonomy label (plural, lowercase). */
                __('No %s', 'modularity-municipal-calendar'),
                strtolower($plural_label)
            ),
            'items_list'                 => sprintf(
                /* translators: %s: Taxonomy label (plural). */
                __('%s list', 'modularity-municipal-calendar'),
                $plural_label
            ),
            'items_list_navigation'      => sprintf(
                /* translators: %s: Taxonomy label (plural). */
                __('%s list navigation', 'modularity-municipal-calendar'),
                $plural_label
            ),
        ];

        $args = [
            'labels'            => $labels,
            'description'       => sprintf(
                /* translators: %s: Taxonomy label (plural). */
                __('%s for municipal events', 'modularity-municipal-calendar'),
                $plural_label
            ),
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud'     => true,
            'show_in_rest'      => true,
            'rewrite'           => ['slug' => $rewrite_slug],
        ];

        register_taxonomy($taxonomy_slug, ['municipal_event'], $args);
    }
}

