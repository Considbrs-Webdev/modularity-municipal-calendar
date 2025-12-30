<?php 


if (function_exists('acf_add_local_field_group')) {

    acf_add_local_field_group(array(
    'key' => 'group_municipal_event_post_data',
    'title' => __('Municipal Event Data', 'modularity-municipal-calendar'),
    'fields' => array(
        0 => array(
            'key' => 'field_municipal_event_start_date',
            'label' => __('Start Date', 'modularity-municipal-calendar'),
            'name' => 'start_date',
            'aria-label' => '',
            'type' => 'date_time_picker',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'display_format' => 'Y-m-d H:i:s',
            'return_format' => 'Y-m-d H:i:s',
            'first_day' => 1,
            'default_to_current_date' => 0,
            'allow_in_bindings' => 0,
        ),
        1 => array(
            'key' => 'field_municipal_event_duration',
            'label' => __('Duration', 'modularity-municipal-calendar'),
            'name' => 'duration',
            'aria-label' => '',
            'type' => 'number',
            'instructions' => __('Duration in hours (e.g., 1.5 for 1 hour 30 minutes)', 'modularity-municipal-calendar'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => __('hours', 'modularity-municipal-calendar'),
            'min' => 0,
            'max' => '',
            'step' => 0.25,
            'allow_in_bindings' => 0,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'municipal_event',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'side',
    'style' => 'default',
    'label_placement' => 'left',
    'instruction_placement' => 'label',
    'hide_on_screen' => array(
        0 => 'discussion',
        1 => 'comments',
        2 => 'revisions',
        3 => 'author',
        4 => 'format',
        5 => 'page_attributes',
        6 => 'tags',
        7 => 'send-trackbacks',
    ),
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
