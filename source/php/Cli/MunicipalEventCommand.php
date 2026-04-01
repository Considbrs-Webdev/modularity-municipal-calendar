<?php

declare(strict_types=1);

namespace ModularityMunicipalCalendar\Cli;

/**
 * WP-CLI commands for municipal events (e.g. archive past meetings).
 */
class MunicipalEventCommand
{
    /**
     * Set municipal events to `archived` when the event end time has passed.
     *
     * End time = start_date + duration (hours). Missing duration is treated as 0.
     *
     * ## OPTIONS
     *
     * [--dry-run]
     * : List IDs that would be archived without updating.
     *
     * ## EXAMPLES
     *
     *     wp municipal-event archive-past
     *     wp municipal-event archive-past --dry-run
     *
     * @subcommand archive-past
     *
     * @param array<int, string> $args Positional arguments
     * @param array<string, mixed> $assoc_args Associative arguments
     */
    public function archive_past(array $args, array $assoc_args): void
    {
        $dryRun = !empty($assoc_args['dry-run']);

        if (!get_post_status_object('archived')) {
            \WP_CLI::error(
                'Post status "archived" is not registered. Ensure the plugin is loaded.'
            );
        }

        $now = time();
        $query = new \WP_Query([
            'post_type' => 'municipal_event',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'no_found_rows' => true,
        ]);

        $ids = is_array($query->posts) ? $query->posts : [];
        $archived = 0;
        $skipped = 0;

        foreach ($ids as $postId) {
            $endTimestamp = $this->getEventEndTimestamp((int) $postId);

            if ($endTimestamp === null) {
                ++$skipped;
                \WP_CLI::warning(sprintf('Post %d: could not determine end time, skipped.', $postId));
                continue;
            }

            if ($endTimestamp >= $now) {
                continue;
            }

            if ($dryRun) {
                \WP_CLI::line(sprintf('Would archive post %d (end: %s)', $postId, gmdate('c', $endTimestamp)));
                ++$archived;
                continue;
            }

            $result = wp_update_post([
                'ID' => (int) $postId,
                'post_status' => 'archived',
            ], true);

            if (is_wp_error($result)) {
                \WP_CLI::warning(sprintf('Post %d: %s', $postId, $result->get_error_message()));
                continue;
            }

            ++$archived;
        }

        if ($dryRun) {
            \WP_CLI::success(sprintf('Dry run: %d would be archived, %d skipped (no end time).', $archived, $skipped));
            return;
        }

        \WP_CLI::success(sprintf('Archived %d post(s), %d skipped (no end time).', $archived, $skipped));
    }

    /**
     * Unix timestamp for event end, or null if start_date is missing/invalid.
     */
    private function getEventEndTimestamp(int $postId): ?int
    {
        $startRaw = function_exists('get_field') ? get_field('start_date', $postId) : null;
        if ($startRaw === null || $startRaw === '') {
            return null;
        }

        $startTimestamp = is_numeric($startRaw) ? (int) $startRaw : strtotime((string) $startRaw);
        if ($startTimestamp === false) {
            return null;
        }

        $duration = function_exists('get_field') ? get_field('duration', $postId) : null;
        $endTimestamp = $startTimestamp;

        if ($duration !== null && $duration !== '' && is_numeric($duration) && (float) $duration > 0) {
            $endTimestamp = (int) ($startTimestamp + ((float) $duration * 3600));
        }

        return $endTimestamp;
    }
}
