<?php

declare(strict_types=1);

namespace ModularityMunicipalCalendar\PostStatus;

/**
 * Registers the custom `archived` post status for municipal events that have ended.
 *
 * A post has exactly one status at a time: it is either `publish` or `archived`, never both.
 * When archived, `post_status` becomes `archived` (the previous published revision remains in history).
 *
 * `public` + `publicly_queryable` let single/event URLs resolve on the front end. `exclude_from_search`
 * keeps archived posts out of site search. The post type archive still lists only `publish` via
 * `pre_get_posts` in App.
 */
class ArchivedPostStatus
{
    public function register(): void
    {
        register_post_status('archived', [
            'label' => _x('Archived', 'post status', 'modularity-municipal-calendar'),
            'public' => true,
            'internal' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => true,
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
