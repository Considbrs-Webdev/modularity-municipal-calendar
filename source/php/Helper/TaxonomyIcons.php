<?php

namespace ModularityMunicipalCalendar\Helper;

/**
 * Helper class for getting taxonomy icons
 */
class TaxonomyIcons
{
    /**
     * Get icon for a taxonomy term
     * 
     * @param int $postId The post ID
     * @param string $taxonomy The taxonomy slug
     * @return string|null The icon name (e.g., 'fa-solid fa-trash') or null
     */
    public static function getTermIcon(int $postId, string $taxonomy): ?string
    {
        $terms = get_the_terms($postId, $taxonomy);
        
        if (!$terms || is_wp_error($terms)) {
            return null;
        }
        
        // Get the first term
        $firstTerm = reset($terms);
        
        // Get icon from ACF field (same pattern as ServiceInfo)
        $iconName = get_field('icon', $taxonomy . '_' . $firstTerm->term_id);
        
        return $iconName ?: null;
    }
}

