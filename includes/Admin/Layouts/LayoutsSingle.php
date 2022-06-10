<?php

namespace DOT\Core\Admin\Layouts;


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

        // Edit
        add_action('load-post-edit.php', array($this, 'load_edit'));

        // Validate before save
//        add_filter('acf/validate_field_group', array($this, 'validate_layout'), 10, 1);

        add_action('acfe/prepare_field_group', array($this, 'prepare_field_group'));
    }

    public function load_single() {
        add_action('acf/field_group/admin_head', array($this, 'metaboxes'));
        add_action('save_post', array($this, 'save_post'), 12, 3);
    }

    public function load_new() {
        add_filter('acf/validate_field_group', array($this, 'validate_new'), 10, 1);
    }

    public function load_edit() {
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
        if (!file_exists(DOT_THEME_LAYOUTS_PATH . $layout_slug)) {
            wp_mkdir_p(DOT_THEME_LAYOUTS_PATH . $layout_slug);
        }

        // Create template file if doesn't exists
        if (!file_exists(DOT_THEME_LAYOUTS_PATH . $layout_slug . '/' . $layout_slug . '.php')) {
            touch(DOT_THEME_LAYOUTS_PATH . $layout_slug . '/' . $layout_slug . '.php');
        }
    }

    /**
     * Set new layout locations
     *
     * @param $layout
     * @return void
     */
    public function validate_new($layout) {
        $layout['location'] = array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '!=',
                    'value' => 'all',
                ),
            ),
        );

        return $layout;
    }

    public function metaboxes() {

        // Remove Yoast metabox
        remove_meta_box('wpseo_meta', 'post', 'normal');

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
        $layout_slug = acf_maybe_get($field_group, 'dot_layout_slug') ? acf_slugify($field_group['dot_layout_slug']) : acf_slugify(get_the_title());

        acf_render_field_wrap(
            array(
                'label' => __('Slug', 'dotcore'),
                'name' => 'dot_layout_slug',
                'prefix' => 'acf_field_group',
                'type' => 'text',
                'instructions' => '',
                'value' => $layout_slug,
                'placeholder' => __('layout-slug', 'dotcore'),
                'required' => false,
            )
        );

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