<?php

namespace DOT\Core\Admin;

class ElementsSingle {

    public function __construct() {
        add_action('current_screen', array($this, 'current_screen'));
    }

    public function current_screen() {
        if (!dot_is_element_screen()) {
            return;
        }

        // Single
        add_action('load-post.php', array($this, 'load_single'));
        add_action('load-post-new.php', array($this, 'load_single'));

        // New
//        add_action('load-post-new.php', array($this, 'load_new'));

    }

    public function load_single() {
        add_action('add_meta_boxes', array($this, 'metaboxes'));
    }

    public function metaboxes() {
        // Remove Yoast metabox
        remove_meta_box('wpseo_meta', get_current_screen(),'normal');

        add_meta_box(
            'dot_layout_part_settings',
            __('Layout Part Settings', 'dotcore'),
            array($this, 'render_meta_box_main'),
            get_current_screen(),
            'normal',
            'high',
        );

    }

    public function render_meta_box_main() {
        $field_group_choices = array();
        $field_groups = acf_get_field_groups();
        foreach($field_groups as $field_group) {
            if(dot_is_layout($field_group) || dot_is_layout_part($field_group))
                continue;

            $field_group_choices[$field_group['key']] = $field_group['title'];
        }

        acf_render_field_wrap(array(
            'label'             => __('Field Group','acf'),
            'instructions'      => __('Choose a field group to assign to this element or <a href="' . get_admin_url() . '/post-new.php?post_type=acf-field-group">create a field group</a>','dotcore'),
            'type'              => 'select',
            'name'              => 'dot_field_group',
            'prefix'            => 'acf_field_group',
            'value'             => (isset($group['dot_field_group'])) ? $group['dot_field_group'] : '',
            'toggle'            => false,
            'choices'           => $field_group_choices,
            'allow_null'        => true, // true | false
            'multiple'          => false, // true | false
            'ui'                => true, // true | false
            'ajax'              => false // true | false
        ));
    }
}