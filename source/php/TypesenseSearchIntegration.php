<?php

declare(strict_types=1);

namespace ModularityMunicipalCalendar;

use ModularityMunicipalCalendar\Helper\TaxonomyIcons;

/**
 * Integrates Municipal Calendar events with Typesense search indexing.
 *
 * - Adds event-specific fields (administration_name, time_range, place_name, place_icon, event_type_name, type_icon).
 * - Registers hit template, post type mapping, and placeholder mappings for the municipal event card.
 */
class TypesenseSearchIntegration
{
    private const POST_TYPE = 'municipal_event';
    private const TEMPLATE_KEY = 'municipal-event';

    public function __construct()
    {
        add_filter(
            'Municipio/TypesenseSearch/DocumentBuilder/build',
            [$this, 'enrichMunicipalEventDocument'],
            10,
            2
        );
        add_filter('Municipio/TypesenseSearch/hitTemplates', [$this, 'addHitTemplate']);
        add_filter('Municipio/TypesenseSearch/hitTemplateView', [$this, 'resolveHitTemplateView'], 10, 2);
        add_filter('Municipio/TypesenseSearch/postTypeToTemplate', [$this, 'mapPostTypesToTemplate']);
        add_filter('Municipio/TypesenseSearch/placeholderMappings', [$this, 'addPlaceholderMappings']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueStylesOnSearch'], 20);
    }

    /**
     * Enqueue municipal calendar styles on search page so hit templates render correctly.
     */
    public function enqueueStylesOnSearch(): void
    {
        if (!is_search()) {
            return;
        }
        $styleFile = Helper\CacheBust::name('css/modularity-municipal-calendar.css');
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
     * @param string[] $templates
     * @return string[]
     */
    public function addHitTemplate(array $templates): array
    {
        $templates[] = self::TEMPLATE_KEY;
        return $templates;
    }

    /**
     * @param string $view
     * @param string $key
     * @return string
     */
    public function resolveHitTemplateView(string $view, string $key): string
    {
        if ($key === self::TEMPLATE_KEY) {
            return 'templates.hits.municipal-event';
        }
        return $view;
    }

    /**
     * @param array<string, string> $mapping
     * @return array<string, string>
     */
    public function mapPostTypesToTemplate(array $mapping): array
    {
        $mapping[self::POST_TYPE] = self::TEMPLATE_KEY;
        return $mapping;
    }

    /**
     * @param array<string, string> $mappings
     * @return array<string, string>
     */
    public function addPlaceholderMappings(array $mappings): array
    {
        return array_merge($mappings, [
            'SEARCH_HIT_ADMINISTRATION' => 'administration_name',
            'SEARCH_HIT_TIME_RANGE'      => 'time_range',
            'SEARCH_HIT_PLACE'          => 'place_name',
            'SEARCH_HIT_PLACE_ICON'     => 'place_icon',
            'SEARCH_HIT_TYPE'           => 'event_type_name',
            'SEARCH_HIT_TYPE_ICON'      => 'type_icon',
        ]);
    }

    /**
     * Enrich the Typesense document with Municipal Event data.
     *
     * @param array<string, mixed> $document The Typesense document array.
     * @param \WP_Post             $post    The source post.
     * @return array<string, mixed>
     */
    public function enrichMunicipalEventDocument(array $document, \WP_Post $post): array
    {
        if ($post->post_type !== self::POST_TYPE) {
            return $document;
        }

        $acfService = class_exists(\Municipio\Helper\AcfService::class)
            ? \Municipio\Helper\AcfService::get()
            : null;

        $startDate = $acfService ? $acfService->getField('start_date', $post->ID) : get_field('start_date', $post->ID);
        $duration = $acfService ? $acfService->getField('duration', $post->ID) : get_field('duration', $post->ID);

        $timeRange = '';
        if ($startDate) {
            $startTimestamp = is_numeric($startDate) ? (int) $startDate : strtotime((string) $startDate);
            if ($startTimestamp !== false) {
                $endTimestamp = $startTimestamp;
                if ($duration && is_numeric($duration) && $duration > 0) {
                    $durationSeconds = (float) $duration * 3600;
                    $endTimestamp = (int) ($startTimestamp + $durationSeconds);
                }
                $startFormatted = date_i18n('j M Y, H:i', $startTimestamp);
                $endFormatted = date_i18n('H:i', $endTimestamp);
                $timeRange = $startFormatted . ' - ' . $endFormatted;
            }
        }

        $places = get_the_terms($post->ID, 'event_place');
        $place = ($places && !is_wp_error($places) && is_array($places) && !empty($places)) ? $places[0] : null;
        $placeIcon = $place ? TaxonomyIcons::getTermIcon($post->ID, 'event_place') : null;

        $types = get_the_terms($post->ID, 'event_type');
        $type = ($types && !is_wp_error($types) && is_array($types) && !empty($types)) ? $types[0] : null;
        $typeIcon = $type ? TaxonomyIcons::getTermIcon($post->ID, 'event_type') : null;

        $administrations = get_the_terms($post->ID, 'event_administration');
        $administration = ($administrations && !is_wp_error($administrations) && is_array($administrations) && !empty($administrations))
            ? $administrations[0]
            : null;

        $adminName = $administration && isset($administration->name) ? (string) $administration->name : '';
        $document['administration_name'] = $adminName !== '' ? $adminName : (string) $post->post_title;
        $document['time_range'] = $timeRange;
        $document['place_name'] = $place && isset($place->name) ? (string) $place->name : '';
        $document['place_icon'] = $this->normalizeIcon($placeIcon);
        // Use event_type_name so we don't overwrite type_name (post type label used by content type facet).
        $document['event_type_name'] = $type && isset($type->name) ? (string) $type->name : '';
        $document['type_icon'] = $this->normalizeIcon($typeIcon);

        return $document;
    }

    /**
     * Normalize ACF icon field (string or array) to icon name string.
     */
    private function normalizeIcon(mixed $icon): string
    {
        if (is_string($icon) && $icon !== '') {
            return $icon;
        }
        if (is_array($icon)) {
            $name = $icon['icon_material_icon'] ?? $icon['icon'] ?? null;
            return is_string($name) ? $name : 'fa-solid fa-map-marker-alt';
        }
        return 'fa-solid fa-map-marker-alt';
    }

}
