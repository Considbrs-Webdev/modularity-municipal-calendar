<?php 


if (function_exists('acf_add_local_field_group')) {

    acf_add_local_field_group(array(
    'key' => 'group_municipal_event_taxonomy_settings',
    'title' => __('Municipal Event Taxonomy Settings', 'modularity-municipal-calendar'),
    'fields' => array(
        0 => array(
            'key' => 'field_municipal_event_taxonomy_icon',
            'label' => __('Icon', 'modularity-municipal-calendar'),
            'name' => 'icon',
            'aria-label' => '',
            'type' => 'icon',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'allow_in_bindings' => 0,
            'default_value' => '',
            'placeholder' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'taxonomy',
                'operator' => '==',
                'value' => 'event_place',
            ),
        ),
        1 => array(
            0 => array(
                'param' => 'taxonomy',
                'operator' => '==',
                'value' => 'event_administration',
            ),
        ),
        2 => array(
            0 => array(
                'param' => 'taxonomy',
                'operator' => '==',
                'value' => 'event_type',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'left',
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
