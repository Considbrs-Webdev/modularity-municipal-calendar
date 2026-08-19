<?php

declare(strict_types=1);

namespace ModularityMunicipalCalendar\PostStatus;

/**
 * Registers the custom `archived` post status for municipal events that have ended.
 *
 * A post has exactly one status at a time: it is either `publish` or `archived`.
 *
 * Flags match modularity-simpleview-events so either plugin can register last.
 * `public` is false so default archive/search queries exclude archived posts.
 * Single municipal event URLs stay reachable via App::allowArchivedMunicipalEventSingular().
 */
class ArchivedPostStatus
{
    public function register(): void
    {
        register_post_status('archived', [
            'label' => _x('Archived', 'post status', 'modularity-municipal-calendar'),
            'public' => false,
            'publicly_queryable' => false,
            'internal' => false,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop(
                'Archived <span class="count">(%s)</span>',
                'Archived <span class="count">(%s)</span>',
                'modularity-municipal-calendar'
            ),
        ]);
    }
}
