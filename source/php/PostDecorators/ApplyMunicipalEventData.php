<?php

declare(strict_types=1);

namespace ModularityMunicipalCalendar\PostDecorators;

use Municipio\Helper\AcfService;
use Municipio\Helper\WpService;
use ModularityMunicipalCalendar\Helper\TaxonomyIcons;
use WP_Post;

/**
 * Decorator to enrich municipal_event posts with event data
 */
class ApplyMunicipalEventData
{
    public function apply(WP_Post $post): WP_Post
    {
        // Only process municipal_event post type
        if ($post->post_type !== 'municipal_event') {
            return $post;
        }

        $wpService = WpService::get();
        $acfService = AcfService::get();
        $postId = $post->ID;


        // Get start date and duration using ACF service
        $startDate = $acfService->getField('start_date', $postId);
        $duration = $acfService->getField('duration', $postId);

        // Calculate time range
        $timeRange = null;
        if ($startDate) {
            $startTimestamp = is_numeric($startDate) ? (int) $startDate : strtotime($startDate);
            if ($startTimestamp !== false) {
                $endTimestamp = $startTimestamp;
                if ($duration && is_numeric($duration) && $duration > 0) {
                    $durationSeconds = (float) $duration * 3600;
                    $endTimestamp = (int) ($startTimestamp + $durationSeconds);
                }

                // Format as range: "25 feb 2026, 08:30 - 17:00"
                $startFormatted = $wpService->dateI18n('j M Y, H:i', $startTimestamp);
                $endFormatted = $wpService->dateI18n('H:i', $endTimestamp);
                if ($startTimestamp == $endTimestamp) {
                    $timeRange = $startFormatted;
                } else {
                    $timeRange = $startFormatted . ' - ' . $endFormatted;
                }
            }
        }

        // Get place data using WP service
        $places = $wpService->getTheTerms($postId, 'event_place');
        $place = ($places && !is_wp_error($places) && is_array($places) && !empty($places)) ? $places[0] : null;
        $placeIcon = $place ? TaxonomyIcons::getTermIcon($postId, 'event_place') : null;

        // Get type data using WP service
        $types = $wpService->getTheTerms($postId, 'event_type');
        $type = ($types && !is_wp_error($types) && is_array($types) && !empty($types)) ? $types[0] : null;
        $typeIcon = $type ? TaxonomyIcons::getTermIcon($postId, 'event_type') : null;

        // Get administration data using WP service
        $administrations = $wpService->getTheTerms($postId, 'event_administration');
        $administration = ($administrations && !is_wp_error($administrations) && is_array($administrations) && !empty($administrations)) ? $administrations[0] : null;
        $administrationIcon = $administration ? TaxonomyIcons::getTermIcon($postId, 'event_administration') : null;

        // Build aria-label for accessibility
        $ariaLabelParts = [$post->post_title];
        if ($timeRange) {
            $ariaLabelParts[] = $timeRange;
        }
        if ($place && isset($place->name)) {
            $ariaLabelParts[] = $place->name;
        }
        $ariaLabel = implode(', ', $ariaLabelParts);

        // Attach computed data to the post object
        $post->municipalEventData = (object) [
            'startDate' => $startDate,
            'duration' => $duration,
            'timeRange' => $timeRange,
            'place' => $place,
            'placeIcon' => $placeIcon,
            'type' => $type,
            'typeIcon' => $typeIcon,
            'administration' => $administration,
            'administrationIcon' => $administrationIcon,
            'ariaLabel' => $ariaLabel,
            'title' => $post->post_title,
        ];
        return $post;
    }
}
