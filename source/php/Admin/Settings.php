<?php

namespace ModularityMunicipalCalendar\Admin;

/**
 * Class Settings
 * 
 * Registers the ACF options page for Municipal Calendar settings.
 * 
 * @package ModularityMunicipalCalendar\Admin
 */
class Settings
{
    public function __construct()
    {
        add_action('acf/init', [$this, 'registerOptionsPage']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);
        add_action('wp_ajax_municipal_calendar_sanitize_slug', [$this, 'ajaxSanitizeSlug']);
    }

    /**
     * Register ACF options page for Municipal Calendar
     * 
     * @return void
     */
    public function registerOptionsPage(): void
    {
        if (function_exists('acf_add_options_sub_page')) {
            acf_add_options_sub_page([
                'page_title'  => __('Municipal Calendar Settings', 'modularity-municipal-calendar'),
                'menu_title'  => __('Municipal Calendar', 'modularity-municipal-calendar'),
                'menu_slug'   => 'municipal-calendar-settings',
                'parent_slug' => 'options-general.php',
                'post_id'     => 'municipal-calendar-settings',
                'capability'  => 'manage_options',
            ]);
        }
    }

    /**
     * AJAX handler to sanitize slug using WordPress sanitize_title()
     * 
     * @return void
     */
    public function ajaxSanitizeSlug(): void
    {
        check_ajax_referer('municipal_calendar_sanitize_slug', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
        
        // Use WordPress's sanitize_title for proper slug generation
        $slug = sanitize_title($title);

        wp_send_json_success(['slug' => $slug]);
    }

    /**
     * Enqueue admin scripts for the settings page
     * 
     * @param string $hook The current admin page hook
     * @return void
     */
    public function enqueueAdminScripts(string $hook): void
    {
        // Only on our settings page
        if ($hook !== 'settings_page_municipal-calendar-settings') {
            return;
        }

        add_action('admin_footer', [$this, 'printAutoSlugScript']);
    }

    /**
     * Print the auto-slug JavaScript in admin footer
     * 
     * @return void
     */
    public function printAutoSlugScript(): void
    {
        $nonce = wp_create_nonce('municipal_calendar_sanitize_slug');
        $ajaxUrl = admin_url('admin-ajax.php');
        ?>
        <script>
        (function() {
            var ajaxUrl = <?php echo json_encode($ajaxUrl); ?>;
            var nonce = <?php echo json_encode($nonce); ?>;
            var debounceTimer;

            function initAutoSlug() {
                var displayNameField = document.querySelector('input[name="acf[field_municipal_calendar_display_name]"]');
                var slugField = document.querySelector('input[name="acf[field_municipal_calendar_slug]"]');
                
                if (!displayNameField || !slugField) return;
                
                var slugManuallyEdited = slugField.value.length > 0;
                
                // Mark as manually edited if user types in slug field
                slugField.addEventListener('input', function() {
                    slugManuallyEdited = true;
                });
                
                // Auto-generate slug from display name using WordPress sanitize_title
                displayNameField.addEventListener('input', function() {
                    if (slugManuallyEdited && slugField.value.length > 0) return;
                    
                    var title = displayNameField.value;
                    if (!title) {
                        slugField.value = '';
                        return;
                    }

                    // Debounce to avoid too many AJAX calls
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function() {
                        var formData = new FormData();
                        formData.append('action', 'municipal_calendar_sanitize_slug');
                        formData.append('nonce', nonce);
                        formData.append('title', title);

                        fetch(ajaxUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then(function(response) { return response.json(); })
                        .then(function(data) {
                            if (data.success && data.data.slug) {
                                slugField.value = data.data.slug;
                                slugManuallyEdited = false;
                            }
                        })
                        .catch(function(error) {
                            console.error('Error sanitizing slug:', error);
                        });
                    }, 300);
                });
            }
            
            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initAutoSlug);
            } else {
                initAutoSlug();
            }
        })();
        </script>
        <?php
    }
}
