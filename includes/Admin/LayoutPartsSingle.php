<?php

namespace DOT\Core\Admin;

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

        // Validate before save
        add_filter('acf/validate_field_group', array($this, 'validate_layout_part'), 10, 1);
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
        $component_slug = acf_maybe_get($field_group, 'dot_layout_part_slug') ? sanitize_title($field_group['dot_layout_part_slug']) : '';

        // Slug
        acf_render_field_wrap(
            array(
                'label' => __('Layout Part Slug', 'dotcore'),
                'name' => 'dot_layout_part_slug',
                'prefix' => 'acf_field_group',
                'type' => 'text',
                'instructions' => '',
                'value' => $component_slug,
                'placeholder' => __('layout-part-slug', 'dotcore'),
                'required' => false,
            )
        );

        $field_group_choices = array();
        $layout_parts = dot_get_layout_parts();
        if(!empty($layout_parts)) {
            foreach($layout_parts as $layout_part) {
                $field_group_choices[$layout_part['key']] = $layout_part['name'];
            }
        }

        // Field Group
        acf_render_field_wrap(array(
            'label'             => __('Select','acf'),
            'instructions'      => __('Instructions','acf'),
            'type'              => 'select',
            'name'              => 'hwk_select',
            'prefix'            => 'acf_field_group',
            'value'             => (isset($group['hwk_select'])) ? $group['hwk_select'] : '',
            'toggle'            => false,
            'choices'           => $field_group_choices,
            'allow_null'        => true, // true | false
            'multiple'          => false, // true | false
            'ui'                => true, // true | false
            'ajax'              => false // true | false
        ));

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

    /**
     * validate_layout part
     *
     * validate layout part slug and generate it if needed
     *
     * @param $field_group
     * @return void
     */
    public function validate_layout_part($layout_part) {
        $slug = acf_maybe_get($layout_part, 'dot_layout_part_slug');

        if(!empty($slug))
            return $layout_part;

        $slug = sanitize_title($layout_part['title']);

        $layout_part['dot_layout_part_slug'] = $slug;

        return $layout_part;
    }
}