<?php

namespace DOT\Core\Admin\Components;

class ComponentsSingle {

    public function __construct() {
        add_action('current_screen', array($this, 'current_screen'));
    }

    public function current_screen() {
        if (!dot_is_component_screen()) {
            return;
        }

        // Single
        add_action('load-post.php', array($this, 'load_single'));
        add_action('load-post-new.php', array($this, 'load_single'));

        // New
//        add_action('load-post-new.php', array($this, 'load_new'));

        add_action('edit_form_after_title', array($this, 'add_edit_instructions'));
    }

    public function load_single() {
        add_action('add_meta_boxes', array($this, 'metaboxes'));

    }

    public function add_edit_instructions($post) {
        global $typenow;
        if ($typenow !== \DOT\Core\Components::$post_type)
            return;

        $field_group_id = false;

        $field_groups = acf_get_field_groups();
        foreach ($field_groups as $field_group) {
            // Get first location rule only
            $location = $field_group['location'][0][0];
            if ($location['param'] === \DOT\Core\Components::$post_type && $location['value'] == $post->ID) {
                $field_group_id = $field_group['ID'];
            }
        }

        $card = '<div class="card">';

        if (!$field_group_id) {
            $card .= __('Please target this component from a field group for it to be editable.', 'dotcore');
            $card .= '<a href="edit.php?post_type=acf-field-group">' . __('Go to field groups', 'dotcore') . '</a>';
        } else {
            $card .= '<a href="post.php?post=' . $field_group_id . '&action=edit">' . __('Edit component\'s field group', 'dotcore') . '</a>';
        }

        $card .= '</div>';
        echo $card;
    }

    public function metaboxes() {
        // Remove Yoast metabox
        remove_meta_box('wpseo_meta', get_current_screen(), 'normal');

//        add_meta_box(
//            'dot_layout_part_settings',
//            __('Layout Part Settings', 'dotcore'),
//            array($this, 'render_meta_box_main'),
//            get_current_screen(),
//            'normal',
//            'high',
//        );

    }

    public
    function render_meta_box_main() {


//        acf_render_field_wrap(array(
//            'label'             => __('Field Group','acf'),
//            'instructions'      => __('Choose a field group to assign to this component or <a href = "' . get_admin_url() . '/post-new.php?post_type=acf-field-group" > create a field group </a > ','dotcore'),
//            'type'              => 'select',
//            'name'              => 'dot_field_group',
//            'prefix'            => 'acf_field_group',
//            'value'             => (isset($group['dot_field_group'])) ? $group['dot_field_group'] : '',
//            'toggle'            => false,
//            'choices'           => $field_group_choices,
//            'allow_null'        => true, // true | false
//            'multiple'          => false, // true | false
//            'ui'                => true, // true | false
//            'ajax'              => false // true | false
//        ));
    }
}