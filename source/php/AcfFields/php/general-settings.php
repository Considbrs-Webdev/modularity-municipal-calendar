<?php 


if (function_exists('acf_add_local_field_group')) {

    acf_add_local_field_group(array(
    'key' => 'group_municipal_calendar_general_settings',
    'title' => __('Municipal Calendar Settings', 'modularity-municipal-calendar'),
    'fields' => array(
        0 => array(
            'key' => 'field_municipal_calendar_display_name',
            'label' => __('Display Name', 'modularity-municipal-calendar'),
            'name' => 'display_name',
            'aria-label' => '',
            'type' => 'text',
            'instructions' => __('The name shown on the frontend (archive title, breadcrumbs). Leave empty to use the default \'Municipal Events\'.', 'modularity-municipal-calendar'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'maxlength' => '',
            'allow_in_bindings' => 0,
            'placeholder' => 'Municipal Events',
            'prepend' => '',
            'append' => '',
        ),
        1 => array(
            'key' => 'field_municipal_calendar_slug',
            'label' => __('Slug', 'modularity-municipal-calendar'),
            'name' => 'slug',
            'aria-label' => '',
            'type' => 'text',
            'instructions' => __('The URL slug for the archive and posts. Auto-generated from Display Name, but can be customized. Don\'t forget to flush permalinks after changing (Settings > Permalinks > Save).', 'modularity-municipal-calendar'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'maxlength' => '',
            'allow_in_bindings' => 0,
            'placeholder' => 'municipal-event',
            'prepend' => '',
            'append' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'municipal-calendar-settings',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
    'display_title' => '',
    'acfe_autosync' => array(
        0 => 'json',
    ),
    'acfe_form' => 0,
    'acfe_meta' => '',
    'acfe_note' => '',
));

}
