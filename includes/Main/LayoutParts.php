<?php

namespace DOT\Core\Main;

class LayoutParts {
    use HasTemplateFiles;

    /**
     * Get the registered layout parts
     *
     * @return array
     */
    public function get_layout_parts(): array {
        $field_groups = acf_get_field_groups();
        $layout_parts = [];

        foreach ($field_groups as $field_group) {
            if (!$this->is_layout_part($field_group)) {
                continue;
            }

            $layout_parts[] = $field_group;
        }

        return $layout_parts;
    }

    /**
     * Render layout part template
     *
     * @param string $type
     * @param string|null $selector
     * @return void
     */
    public function the_layout_part(string $slug, string $selector = null) {


        // Store preview post ID
        $instance = acf_get_instance('ACF_Local_Meta');
        $previous_id = $instance->post_id;

        $fields = get_sub_field($selector) ?: [];

        if($fields) {
            $field_key = 'field_component_wrapper_' . $slug;
            // Get sub fields
            $sub_fields = array();
            foreach ($fields as $key => $value) {
                $sub_fields[] = array(
                    'key' => $key,
                    'type' => 'text',
                );
            }

            // Create fake field
            acf_add_local_field(
                array(
                    'key' => $field_key,
                    'type' => 'group',
                    'sub_fields' => $sub_fields,
                )
            );

            acf_setup_meta($fields, 'dot_layout_part', true);
        }

        // Enqueue styles and script
        $this->enqueue($slug, 'layout_part');

        // Render template
        $this->render($slug, 'layout_part', $selector);

        if ($fields) {
            acf_reset_meta('dot_layout_part');
            $instance->post_id = $previous_id;
        }
    }

    /**
     * Check whether a field group is a layout part
     *
     * @param $field_group
     * @return mixed|null
     */
    public function is_layout_part($field_group) {

        // If $field_group is an ID
        if (!is_array($field_group)) {
            $field_group = acf_get_field_group($field_group);
        }

        return acf_maybe_get($field_group, 'dot_is_lpart');
    }

    /**
     * Check whether current screen is a layout part admin screen
     *
     * @return bool
     */
    public function is_layout_part_screen(): bool {
        global $typenow;

        // If not field groups page, return
        if ($typenow !== 'acf-field-group') {
            return false;
        }

        // Get screens
        $is_layout_list = acf_is_screen('edit-acf-field-group') && acf_maybe_get_GET('layout_parts') === '1';
        $is_layout_single = acf_is_screen('acf-field-group');

        if ($is_layout_list) {

            // Layout list
            return true;

        } elseif ($is_layout_single) {

            // Check if layout single page
            $is_layout_part_single_new = acf_maybe_get_GET('layout_part') === '1';
            $is_layout_part_single_edit = $this->is_layout_part(acf_maybe_get_GET('post'));
            $is_layout_part_single_save = isset($_REQUEST['acf_field_group']['dot_is_lpart']);

            // Layout single
            if ($is_layout_part_single_new || $is_layout_part_single_edit || $is_layout_part_single_save) {
                return true;
            }
        }

        return false;
    }
}