<?php

namespace DOT\Core\Admin\LayoutParts;

class LayoutPartsSingle {
    public function __construct() {
        add_action('current_screen', array($this, 'current_screen'));
    }

    public function current_screen() {
        if (!dot_is_layout_part_screen()) {
            return;
        }

        // Single
        add_action('load-post.php', array($this, 'load_single'));
        add_action('load-post-new.php', array($this, 'load_single'));

        // New
        add_action('load-post-new.php', array($this, 'load_new'));
    }

    public function load_single() {
        add_action('acf/field_group/admin_head', array($this, 'metaboxes'));
    }

    public function load_new() {
        add_action('acf/save_post', 'save_field_group', 5);
    }

    public function metaboxes() {

        // Remove Yoast metabox
        remove_meta_box('wpseo_meta','post','normal');

        // Remove field group categories metabox
        remove_meta_box('acf-field-group-categorydiv', 'acf-field-group', 'side');

        // Get current field group
        global $field_group;

        // Meta box: Layout Part settings
        add_meta_box(
            'dot_layout_part_settings',
            __('Layout Part Settings', 'dotcore'),
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
        $layout_part_slug = acf_maybe_get($field_group, 'dot_layout_part_slug') ? acf_slugify($field_group['dot_layout_part_slug']) : acf_slugify(get_the_title());

        // Slug
        acf_render_field_wrap(
            array(
                'label' => __('Layout Part Slug', 'dotcore'),
                'name' => 'dot_layout_part_slug',
                'prefix' => 'acf_field_group',
                'type' => 'text',
                'instructions' => '',
                'value' => $layout_part_slug,
                'placeholder' => __('layout-part-slug', 'dotcore'),
                'required' => false,
            )
        );

        // Is Layout Part (hidden, needs to be true for field group to be recognized as a layout part)
        acf_render_field_wrap(
            array(
                'label' => '',
                'name' => 'dot_is_lpart',
                'prefix' => 'acf_field_group',
                'type' => 'acfe_hidden',
                'instructions' => '',
                'value' => 1,
                'required' => false,
            )
        );
    }

}