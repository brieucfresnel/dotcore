<?php

namespace DOT\Core\Admin;


class LayoutsSingle {
    public function __construct() {
        add_action('current_screen', array($this, 'current_screen'));
    }

    public function current_screen() {
        if (!dot_is_layout_screen()) {
            return;
        }

        // Single
        add_action('load-post.php', array($this, 'load_single'));
        add_action('load-post-new.php', array($this, 'load_single'));

        // New
        add_action('load-post-new.php', array($this, 'load_new'));
    }

    public function load_single() {
//        add_filter('acf/validate_field_group', array($this, 'validate_single'), 20);
        add_action('acf/field_group/admin_head', array($this, 'metaboxes'));
    }

    public function load_new() {
        add_action('acf/save_post', 'save_field_group', 5);
    }

    public function metaboxes() {

        // Get current field group
        global $field_group;

        // Meta box: Layout settings
        add_meta_box(
            'dot_layout_settings',
            __('Layout settings', 'dotcore'),
            array($this, 'render_meta_box_main'),
            'acf-field-group',
            'normal',
            'high',
            array('field_group' => $field_group)
        );
    }

    public function render_meta_box_main($post, $meta_box) {

        // Get field group
        $field_group = $meta_box['args']['field_group'];
        $layout_slug = acf_maybe_get($field_group, 'dot_layout_slug') ? sanitize_title($field_group['dot_layout_slug']) : 'layout';

        // Layout settings
        acf_render_field_wrap(
            array(
                'label' => 'Layout',
                'type' => 'tab',
            )
        );

//        // Layout slug
//        acf_render_field_wrap(
//            array(
//                'label' => __('Layout slug', 'dotcore'),
//                'instructions' => __('Layout slug and layout folder name', 'dotcore'),
//                'type' => 'text',
//                'name' => 'dot_layout_slug',
//                'prefix' => 'acf_field_group',
//                'placeholder' => 'layout',
//                'required' => 1,
//                'value' => isset($field_group['dot_layout_slug']) ? $field_group['dot_layout_slug'] : $layout_slug,
//            )
//        );

        // Layout settings
        acf_render_field_wrap(
            array(
                'label' => '',
                'name' => 'dot_is_layout',
                'prefix' => 'acf_field_group',
                'type' => 'acfe_hidden',
                'instructions' => '',
                'value' => 1,
                'required' => false,
            )
        );
    }
}