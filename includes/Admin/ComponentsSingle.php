<?php

namespace DOT\Core\Admin;

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
        add_action('load-post-new.php', array($this, 'load_new'));

        // Validate before save
        add_filter('acf/validate_field_group', array($this, 'validate_component'), 10, 1);
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

        // Meta box: Component settings
        add_meta_box(
            'dot_component_settings',
            __('Component settings', 'dotcore'),
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
        $component_slug = acf_maybe_get($field_group, 'dot_component_slug') ? sanitize_title($field_group['dot_component_slug']) : '';

        acf_render_field_wrap(
            array(
                'label' => __('Component Slug', 'dotcore'),
                'name' => 'dot_component_slug',
                'prefix' => 'acf_field_group',
                'type' => 'text',
                'instructions' => '',
                'value' => $component_slug,
                'placeholder' => __('component-slug', 'dotcore'),
                'required' => false,
            )
        );

        // Layout settings
        acf_render_field_wrap(
            array(
                'label' => '',
                'name' => 'dot_is_component',
                'prefix' => 'acf_field_group',
                'type' => 'acfe_hidden',
                'instructions' => '',
                'value' => 1,
                'required' => false,
            )
        );
    }

    /**
     * validate_layout
     *
     * validate layout slug and generate it if needed
     *
     * @param $field_group
     * @return void
     */
    public function validate_component($component) {
        $component_slug = acf_maybe_get($component, 'dot_component_slug');

        if(!empty($component_slug))
            return $component;

        $slug = sanitize_title($component['title']);

        $component['dot_component_slug'] = $slug;

        return $component;
    }
}