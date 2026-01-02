<?php

declare(strict_types=1);

namespace ModularityMunicipalCalendar\Controller;

use Municipio\Helper\AcfService;
use Municipio\Helper\WpService;
use ModularityMunicipalCalendar\Helper\TaxonomyIcons;

/**
 * Controller for enriching posts-list view data
 */
class PostsListController
{
    /**
     * Enrich posts with municipal event data
     * 
     * @param array $data The view data
     * @param string|null $template The template name
     * @return array Modified view data
     */
    public function enrichMunicipalEventPosts(array $data, $template = null): array
    {
        // Only process for municipal_event archives
        if (empty($data['postType']) || $data['postType'] !== 'municipal_event') {
            return $data;
        }

        // Preserve template variable for parent templates
        if ($template !== null) {
            $data['template'] = $template;
        }

        // Process each post and add computed data
        if (!empty($data['posts']) && is_array($data['posts'])) {
            $enrichedPosts = [];
            
            $wpService = WpService::get();
            $acfService = AcfService::get();
            
            foreach ($data['posts'] as $post) {
                $postId = $post->getId();
                
                // Get start date using ACF service
                $startDate = $acfService->getField('start_date', $postId);
                $formattedDate = null;
                if ($startDate) {
                    $timestamp = is_numeric($startDate) ? (int) $startDate : strtotime($startDate);
                    if ($timestamp !== false) {
                        $formattedDate = $wpService->dateI18n('j M Y, H:i', $timestamp);
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
                $ariaLabelParts = [$post->getTitle()];
                if ($formattedDate) {
                    $ariaLabelParts[] = $formattedDate;
                }
                if ($place && isset($place->name)) {
                    $ariaLabelParts[] = $place->name;
                }
                $ariaLabel = implode(', ', $ariaLabelParts);
                
                // Store computed data on the post object (or create a wrapper)
                $post->municipalEventData = (object) [
                    'startDate' => $startDate,
                    'formattedDate' => $formattedDate,
                    'place' => $place,
                    'placeIcon' => $placeIcon,
                    'type' => $type,
                    'typeIcon' => $typeIcon,
                    'administration' => $administration,
                    'administrationIcon' => $administrationIcon,
                    'ariaLabel' => $ariaLabel,
                ];
                
                $enrichedPosts[] = $post;
            }
            
            $data['posts'] = $enrichedPosts;
        }
        
        return $data;
    }
}

