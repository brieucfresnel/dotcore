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
        add_action('save_post', array($this, 'save_post'), 12, 3);
    }

    public function load_new() {
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

    public function save_post($post_id, $post, $update) {
        // Fire only once
        if ( !$post->post_content ) {
            return;
        }

        $field_group = acf_get_field_group( $post_id );

        // Bail early if field group not found
        if ( !$field_group ) {
            return;
        }

        // Create layout files
        $this->generate_directory_files( $field_group );
    }

    /**
     * Create layout folder with corresponding files
     *
     * @param $field_group
     */
    public function generate_directory_files($field_group) {

        $layout_slug = acf_slugify($field_group['title']);

        // Create layout folder if doesn't exists
        if (!file_exists(DOT_THEME_LAYOUT_PARTS_PATH . $layout_slug)) {
            wp_mkdir_p(DOT_THEME_LAYOUT_PARTS_PATH . $layout_slug);
        }

        // Create template file if doesn't exists
        if (!file_exists(DOT_THEME_LAYOUT_PARTS_PATH . $layout_slug . '/' . $layout_slug . '.php')) {
            touch(DOT_THEME_LAYOUT_PARTS_PATH . $layout_slug . '/' . $layout_slug . '.php');
        }
    }

}