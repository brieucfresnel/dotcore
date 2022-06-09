<?php

namespace DOT\Core;

class LayoutParts {

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
     * @param string $selector
     * @return void
     */
    public function the_layout_part(string $slug, string $selector) {
        global $is_preview;
        // Enqueue styles and script
        $this->layout_part_enqueue($slug);

        // Render template
        $this->render_template($slug, $selector);
    }

    public function layout_part_enqueue(string $slug) {
        global $is_preview;
        $handle = 'layout-part-' . $slug;

        $style = DOT_THEME_LAYOUT_PARTS_PATH . $slug . '/' . $slug . '.css';
        $script = DOT_THEME_LAYOUT_PARTS_PATH . $slug . '/' . $slug . '.js';

        // Check
        if (!empty($style)) {

            // URL starting with current domain
            if (stripos($style, home_url()) === 0) {

                $style = str_replace(home_url(), '', $style);

            }

            // Locate
            $style_file = acfe_locate_file_url($style);

            // Front-end
            if (!empty($style_file)) {

                wp_enqueue_style($handle, $style_file, array(), false, 'all');

            }

        }

        // Check
        if (!empty($script)) {

            // URL starting with current domain
            if (stripos($script, home_url()) === 0) {

                $script = str_replace(home_url(), '', $script);

            }

            // Locate
            $script_file = acfe_locate_file_url($script);


            // Front-end
            if (!$is_preview || (stripos($script, 'http://') === 0 || stripos($script, 'https://') === 0 || stripos($script, '//') === 0)) {

                if (!empty($script_file)) {

                    wp_enqueue_script($handle, $script_file, array(), false, true);

                }

            } else {

                $path = pathinfo($script);
                $extension = $path['extension'];

                $script_preview = substr($script, 0, -strlen($extension) - 1);
                $script_preview .= '-preview.' . $extension;

                $script_preview = acfe_locate_file_url($script_preview);

                // Enqueue
                if (!empty($script_preview)) {

                    wp_enqueue_script($handle . '-preview', $script_preview, array(), false, true);

                } elseif (!empty($script_file)) {

                    wp_enqueue_script($handle, $script_file, array(), false, true);

                }

            }

        }
    }

    public function render_template(string $slug, string $selector) {
        $file = DOT_THEME_LAYOUT_PARTS_PATH . $slug . '/' . $slug . '.php';
        $file_found = acfe_locate_file_path($file);

        if (!empty($file_found)) {
            $fields = get_sub_field($selector);
            include($file_found);
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