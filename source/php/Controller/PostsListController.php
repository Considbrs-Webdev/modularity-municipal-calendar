<?php

namespace ModularityMunicipalCalendar\Controller;

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

        // Process each post and add computed data
        if (!empty($data['posts']) && is_array($data['posts'])) {
            $enrichedPosts = [];
            
            foreach ($data['posts'] as $post) {
                $postId = $post->getId();
                
                // Get start date
                $startDate = get_field('start_date', $postId);
                $formattedDate = $startDate ? date_i18n('j M Y, H:i', strtotime($startDate)) : null;
                
                // Get place data
                $places = get_the_terms($postId, 'event_place');
                $place = ($places && !is_wp_error($places)) ? $places[0] : null;
                $placeIcon = $place ? TaxonomyIcons::getTermIcon($postId, 'event_place') : null;
                
                // Get type data
                $types = get_the_terms($postId, 'event_type');
                $type = ($types && !is_wp_error($types)) ? $types[0] : null;
                $typeIcon = $type ? TaxonomyIcons::getTermIcon($postId, 'event_type') : null;
                
                // Get administration data (if needed)
                $administrations = get_the_terms($postId, 'event_administration');
                $administration = ($administrations && !is_wp_error($administrations)) ? $administrations[0] : null;
                $administrationIcon = $administration ? TaxonomyIcons::getTermIcon($postId, 'event_administration') : null;
                
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
                ];
                
                $enrichedPosts[] = $post;
            }
            
            $data['posts'] = $enrichedPosts;
        }
        
        return $data;
    }
}

